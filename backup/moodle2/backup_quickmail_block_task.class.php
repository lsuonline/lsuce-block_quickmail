<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/quickmail/backup/moodle2/backup_quickmail_stepslib.php');
require_once($CFG->dirroot . '/lib/blocklib.php');

class backup_quickmail_block_task extends backup_block_task {
    protected function define_my_settings() {
        global $OUTPUT, $SESSION;
        
        // If a user has multiple tabs open when adding blocks to their course page it's possible
        // to add multiple instances of the Quickmail block which breaks the backup process.
        // This will remvoe the extras.
        if ($this->check_block_instances()) {
            // Because a looped call is coming from backup/moodle2/backup_plan_builder.class.php
            // the session variable is used to keep track.
            if ($SESSION->qm_multi_block_inst->counter == 1) {
                echo $OUTPUT->notification(get_string('multi_block_checker', 'block_quickmail'), 'notifywarning');
                $SESSION->qm_multi_block_inst->counter++;
            } else {

                if ($SESSION->qm_multi_block_inst->counter == $SESSION->qm_multi_block_inst->count) {
                    // We've hit the last block_instance_id, let's now cleanup.
                    foreach ($SESSION->qm_multi_block_inst->remove as $r) {
                        blocks_delete_instance($r);
                    }
                    unset($SESSION->qm_multi_block_inst);
                    return;
                }

                $SESSION->qm_multi_block_inst->counter++;
                return;
            }
        }
        $includehistory = new backup_generic_setting('include_quickmail_log', base_setting::IS_BOOLEAN, false);
        $includehistory->get_ui()->set_label(get_string('backup_history', 'block_quickmail'));
        $this->add_setting($includehistory);

        $this->plan->get_setting('users')->add_dependency($includehistory);
        $this->plan->get_setting('blocks')->add_dependency($includehistory);

        $includeconfigsettings = new backup_generic_setting('include_quickmail_config', base_setting::IS_BOOLEAN, true);
        $includeconfigsettings->get_ui()->set_label(get_string('backup_block_configuration', 'block_quickmail'));
        $this->add_setting($includeconfigsettings);

        $this->plan->get_setting('blocks')->add_dependency($includeconfigsettings);
    }

    protected function define_my_steps() {
        // TODO: Additional steps for drafts and alternate emails.
        $this->add_step(new backup_quickmail_block_structure_step('quickmail_structure', 'emaillogs_and_block_configuration.xml'));
    }

    public function get_fileareas() {
        return array();
    }

    public function get_configdata_encoded_attributes() {
        return array();
    }

    public static function encode_content_links($content) {
        // TODO: Perhaps needing this when moving away from email zip attaches.
        return $content;
    }
    /**
     * A check to see if there are multiple block instances 
    */
    public function check_block_instances() {
        global $DB, $SESSION;

        $now = time();
        // During backup each call should be microseconds,
        // check if it's greater than 10 seconds for older, possible, sessions.
        if (isset($SESSION->qm_multi_block_inst)  && ($now - $SESSION->qm_multi_block_inst->stamp) < 10) {
            // Yup we are doing a backup and there are multiple block instances.
            return true;
        } else {

            // Get how many block instances there are.
            $sql = "SELECT bi.*
            FROM
                {block_instances} bi
            JOIN
                {context} c
                    ON c.id = bi.parentcontextid
            WHERE
                c.contextlevel = 50
                AND c.instanceid = ".$this->plan->get_courseid().
                " AND bi.blockname = 'quickmail'
            ORDER BY
                bi.id";

            $result = $DB->get_records_sql($sql);

            // If there's only, get outta here and make sure any sessions are dead.
            if (count($result) <= 1) {
                if (isset($SESSION->qm_multi_block_inst)) {
                    unset($SESSION->qm_multi_block_inst);
                }
                return false;
            }
            
            $temp = new stdClass();
            $temp->stamp = time();
            $temp->count = count($result);
            $temp->counter = 1;
            $temp->backupid = $this->plan->get_backupid();

            // Skip the first block instance and keep track of the rest.
            $first = true;
            foreach ($result as $b) {
                if ($first) {
                    $first = false;
                    continue;
                }
                $temp->remove[] = $b;
            }

            $SESSION->qm_multi_block_inst = $temp;
            return true;
        }
    }
}
