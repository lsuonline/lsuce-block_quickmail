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
 * @copyright  2008-2017 Louisiana State University
 * @copyright  2008-2017 Adam Zapletal, Chad Mazilly, Philip Cali, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once $CFG->libdir . '/formslib.php';

class course_configuration_form extends moodleform {

    public $quickmail;

    public function definition() {

        $mform =& $this->_form;

        // get this specific quickmail plugin instatiation
        $this->quickmail = $this->_customdata['plugin'];

        ////////////////////////////////////////////////////////////
        ///  allow students (select)
        ////////////////////////////////////////////////////////////
        if ($this->should_show_allow_students()) {
            $mform->addElement('select', 'allowstudents', block_quickmail_plugin::_s('allowstudents'), $this->get_allow_student_options());
            $mform->setDefault('allowstudents', $this->quickmail->config['allowstudents']);
        }

        ////////////////////////////////////////////////////////////
        ///  role selection (select)
        ////////////////////////////////////////////////////////////
        $mform->addElement('select', 'roleselection', block_quickmail_plugin::_s('select_roles'), $this->quickmail->all_available_roles())->setMultiple(true);
        $mform->getElement('roleselection')->setSelected($this->get_selected_role_ids_array());
        $mform->addRule('roleselection', null, 'required');

        ////////////////////////////////////////////////////////////
        ///  prepend class (select)
        ////////////////////////////////////////////////////////////
        $mform->addElement('select', 'prepend_class', block_quickmail_plugin::_s('prepend_class'), $this->get_prepend_class_options());
        $mform->setDefault('prepend_class', $this->quickmail->config['prepend_class']);

        ////////////////////////////////////////////////////////////
        ///  receipt (select)
        ////////////////////////////////////////////////////////////
        $mform->addElement('select', 'receipt', block_quickmail_plugin::_s('receipt'), $this->get_receipt_options());
        $mform->setDefault('receipt', $this->quickmail->config['receipt']);

        ////////////////////////////////////////////////////////////
        ///  buttons
        ////////////////////////////////////////////////////////////
        $buttons = [
            $mform->createElement('submit', 'save', block_quickmail_plugin::_s('save_configuration')),
            $mform->createElement('submit', 'reset', block_quickmail_plugin::_s('reset')),
            $mform->createElement('cancel', 'cancel', block_quickmail_plugin::_s('cancel'))
        ];
        
        $mform->addGroup($buttons, 'actions', '&nbsp;', array(' '), false);
    }

    /**
     * Reports whether or not the course configuration form should display "allow students" option (based on global configuration)
     * 
     * @return bool
     */
    private function should_show_allow_students()
    {
        return $this->quickmail->config['allowstudents'] == 1;
    }

    /**
     * Returns the options for the course "allow students" setting
     * 
     * @return array
     */
    private function get_allow_student_options()
    {
        return [
            0 => get_string('no'), 
            1 => get_string('yes')
        ];
    }

    /**
     * Returns the options for "prepend class" setting
     * 
     * @return array
     */
    private function get_prepend_class_options()
    {
        return [
            0 => get_string('none'),
            'idnumber' => get_string('idnumber'),
            'shortname' => get_string('shortname')
        ];
    }

    /**
     * Returns the options for the course "receipt" setting
     * 
     * @return array
     */
    private function get_receipt_options()
    {
        return [
            0 => get_string('no'), 
            1 => get_string('yes')
        ];
    }

    /**
     * Returns the currently selected role ids as array
     * 
     * @return array
     */
    private function get_selected_role_ids_array()
    {
        return explode(',', $this->quickmail->config['roleselection']);
    }

}