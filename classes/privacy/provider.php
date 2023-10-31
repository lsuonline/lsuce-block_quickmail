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
 * Privacy Subsystem implementation for block_quickmail.
 *
 * @package    block_quickmail
 * @author     Jwalit Shah <jwalitshah@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

defined('MOODLE_INTERNAL') || die();

/**
 * The block_quickmail.
 *
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'block_quickmail_signatures',
            [
                'user_id' => 'privacy:metadata:block_quickmail_signatures:user_id',
                'title' => 'privacy:metadata:block_quickmail_signatures:title',
                'signature' => 'privacy:metadata:block_quickmail_signatures:signature',
                'usermodified' => 'privacy:metadata:block_quickmail_signatures:usermodified',
                'timecreated' => 'privacy:metadata:block_quickmail_signatures:timecreated',
                'timemodified' => 'privacy:metadata:block_quickmail_signatures:timemodified',
                'timedeleted' => 'privacy:metadata:block_quickmail_signatures:timedeleted',
            ],
            'privacy:metadata:block_quickmail_signatures'
        );

        $collection->add_database_table(
            'block_quickmail_messages',
            [
                'user_id' => 'privacy:metadata:block_quickmail_messages:user_id',
                'notification_id' => 'privacy:metadata:block_quickmail_messages:notification_id',
                'alternate_email_id' => 'privacy:metadata:block_quickmail_messages:alternate_email_id',
                'signature_id' => 'privacy:metadata:block_quickmail_messages:signature_id',
                'subject' => 'privacy:metadata:block_quickmail_messages:subject',
                'body' => 'privacy:metadata:block_quickmail_messages:body',
                'sent_at' => 'privacy:metadata:block_quickmail_messages:sent_at',
                'to_send_at' => 'privacy:metadata:block_quickmail_messages:to_sent_at',
                'usermodified' => 'privacy:metadata:block_quickmail_messages:usermodified',
                'timecreated' => 'privacy:metadata:block_quickmail_messages:timecreated',
                'timemodified' => 'privacy:metadata:block_quickmail_messages:timemodified',
                'timedeleted' => 'privacy:metadata:block_quickmail_messages:timedeleted',
            ],
            'privacy:metadata:block_quickmail_messages'
        );

        $collection->add_database_table(
            'block_quickmail_msg_recips',
            [
                'user_id' => 'privacy:metadata:block_quickmail_msg_recips:user_id',
                'sent_at' => 'privacy:metadata:block_quickmail_msg_recips:sent_at',
                'moodle_message_id' => 'privacy:metadata:block_quickmail_msg_recips:moodle_message_id',
                'usermodified' => 'privacy:metadata:block_quickmail_msg_recips:usermodified',
                'timecreated' => 'privacy:metadata:block_quickmail_msg_recips:timecreated',
                'timemodified' => 'privacy:metadata:block_quickmail_msg_recips:timemodified',
            ],
            'privacy:metadata:block_quickmail_msg_recips'
        );

        $collection->add_database_table(
            'block_quickmail_draft_recips',
            [
                'message_id' => 'privacy:metadata:block_quickmail_draft_recips:message_id',
                'recipient_type' => 'privacy:metadata:block_quickmail_draft_recips:recipient_type',
                'recipient_id' => 'privacy:metadata:block_quickmail_draft_recips:recipient_id',
                'recipient_filter' => 'privacy:metadata:block_quickmail_draft_recips:recipient_filter',
                'timecreated' => 'privacy:metadata:block_quickmail_draft_recips:timecreated',
                'timemodified' => 'privacy:metadata:block_quickmail_draft_recips:timemodified',
            ],
            'privacy:metadata:block_quickmail_draft_recips'
        );

        $collection->add_database_table(
            'block_quickmail_msg_ad_email',
            [
                'message_id' => 'privacy:metadata:block_quickmail_msg_ad_email:message_id',
                'email' => 'privacy:metadata:block_quickmail_msg_ad_email:email',
                'sent_at' => 'privacy:metadata:block_quickmail_msg_ad_email:sent_at',
                'usermodified' => 'privacy:metadata:block_quickmail_msg_ad_email:usermodified',
                'timecreated' => 'privacy:metadata:block_quickmail_msg_ad_email:timecreated',
                'timemodified' => 'privacy:metadata:block_quickmail_msg_ad_email:timemodified',
            ],
            'privacy:metadata:block_quickmail_msg_ad_email'
        );

        $collection->add_database_table(
            'block_quickmail_msg_attach',
            [
                'message_id' => 'privacy:metadata:block_quickmail_msg_attach:message_id',
                'path' => 'privacy:metadata:block_quickmail_msg_attach:path',
                'filename' => 'privacy:metadata:block_quickmail_msg_attach:filename',
                'usermodified' => 'privacy:metadata:block_quickmail_msg_attach:usermodified',
                'timecreated' => 'privacy:metadata:block_quickmail_msg_attach:timecreated',
                'timemodified' => 'privacy:metadata:block_quickmail_msg_attach:timemodified',
            ],
            'privacy:metadata:block_quickmail_msg_attach'
        );

        $collection->add_database_table(
            'block_quickmail_alt_emails',
            [
                'setup_user_id' => 'privacy:metadata:block_quickmail_alt_emails:setup_user_id',
                'course_id' => 'privacy:metadata:block_quickmail_alt_emails:course_id',
                'user_id' => 'privacy:metadata:block_quickmail_alt_emails:user_id',
                'email' => 'privacy:metadata:block_quickmail_alt_emails:setup_email',
                'firstname' => 'privacy:metadata:block_quickmail_alt_emails:firstname',
                'lastname' => 'privacy:metadata:block_quickmail_alt_emails:lastname',
                'allowed_role_ids' => 'privacy:metadata:block_quickmail_alt_emails:allowed_role_ids',
                'is_validated' => 'privacy:metadata:block_quickmail_alt_emails:is_validated',
                'usermodified' => 'privacy:metadata:block_quickmail_alt_emails:usermodified',
                'timecreated' => 'privacy:metadata:block_quickmail_alt_emails:timecreated',
                'timemodified' => 'privacy:metadata:block_quickmail_alt_emails:timemodified',
                'timedeleted' => 'privacy:metadata:block_quickmail_alt_emails:timedeleted',
            ],
            'privacy:metadata:block_quickmail_alt_emails'
        );

        $collection->add_database_table(
            'block_quickmail_notifs',
            [
                'user_id' => 'privacy:metadata:block_quickmail_notifs:user_id',
                'alternate_email_id' => 'privacy:metadata:block_quickmail_notifs:alternate_email_id',
                'subject' => 'privacy:metadata:block_quickmail_notifs:subject',
                'signature_id' => 'privacy:metadata:block_quickmail_notifs:signature_id',
                'body' => 'privacy:metadata:block_quickmail_notifs:body',
                'usermodified' => 'privacy:metadata:block_quickmail_notifs:usermodified',
                'timecreated' => 'privacy:metadata:block_quickmail_notifs:timecreated',
                'timemodified' => 'privacy:metadata:block_quickmail_notifs:timemodified',
                'timedeleted' => 'privacy:metadata:block_quickmail_notifs:timedeleted',
            ],
            'privacy:metadata:block_quickmail_notifs'
        );

        $collection->add_database_table(
            'block_quickmail_event_notifs',
            [
                'notification_id' => 'privacy:metadata:block_quickmail_event_notifs:notification_id',
                'model' => 'privacy:metadata:block_quickmail_event_notifs:model',
                'usermodified' => 'privacy:metadata:block_quickmail_event_notifs:usermodified',
                'timecreated' => 'privacy:metadata:block_quickmail_event_notifs:timecreated',
                'timemodified' => 'privacy:metadata:block_quickmail_event_notifs:timemodified',
                'timedeleted' => 'privacy:metadata:block_quickmail_event_notifs:timedeleted',
            ],
            'privacy:metadata:block_quickmail_event_notifs'
        );

        $collection->add_database_table(
            'block_quickmail_schedules',
            [
                'unit' => 'privacy:metadata:block_quickmail_schedules:unit',
                'amount' => 'privacy:metadata:block_quickmail_schedules:amount',
                'begin_at' => 'privacy:metadata:block_quickmail_schedules:begin_at',
                'end_at' => 'privacy:metadata:block_quickmail_schedules:end_at',
                'usermodified' => 'privacy:metadata:block_quickmail_schedules:usermodified',
                'timecreated' => 'privacy:metadata:block_quickmail_schedules:timecreated',
                'timemodified' => 'privacy:metadata:block_quickmail_schedules:timemodified',
                'timedeleted' => 'privacy:metadata:block_quickmail_schedules:timedeleted',
            ],
            'privacy:metadata:block_quickmail_schedules'
        );

        $collection->add_database_table(
            'block_quickmail_rem_notifs',
            [
                'notification_id' => 'privacy:metadata:block_quickmail_rem_notifs:notification_id',
                'model' => 'privacy:metadata:block_quickmail_rem_notifs:model',
                'object_id' => 'privacy:metadata:block_quickmail_rem_notifs:object_id',
                'schedule_id' => 'privacy:metadata:block_quickmail_rem_notifs:schedule_id',
                'usermodified' => 'privacy:metadata:block_quickmail_rem_notifs:usermodified',
                'timecreated' => 'privacy:metadata:block_quickmail_rem_notifs:timecreated',
                'timemodified' => 'privacy:metadata:block_quickmail_rem_notifs:timemodified',
                'timedeleted' => 'privacy:metadata:block_quickmail_rem_notifs:timedeleted',
            ],
            'privacy:metadata:block_quickmail_rem_notifs'
        );

        $collection->add_database_table(
            'block_quickmail_event_recips',
            [
                'user_id' => 'privacy:metadata:block_quickmail_event_recips:user_id',
                'event_notification_id' => 'privacy:metadata:block_quickmail_event_recips:event_notification_id',
                'notified_at' => 'privacy:metadata:block_quickmail_event_recips:notified_at',
            ],
            'privacy:metadata:block_quickmail_event_recips'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $params = [
            'contextlevel'  => CONTEXT_USER,
            'userid'        => $userid,
            'usermodified'  => $userid
        ];

        $sql = "SELECT c.id
                  FROM {block_quickmail_signatures} bqs
                  JOIN {context} c ON c.instanceid = bqs.user_id AND c.contextlevel = :contextlevel
                 WHERE bqs.user_id = :userid OR bqs.usermodified = :usermodified
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT c.id
                  FROM {block_quickmail_messages} bqm
                  JOIN {context} c ON c.instanceid = bqm.user_id AND c.contextlevel = :contextlevel
                 WHERE bqm.user_id = :userid OR bqm.usermodified = :usermodified
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT c.id
                  FROM {block_quickmail_msg_recips} bqmr
                  JOIN {context} c ON c.instanceid = bqmr.user_id AND c.contextlevel = :contextlevel
                 WHERE bqmr.user_id = :userid OR bqmr.usermodified = :usermodified
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT c.id
                  FROM {block_quickmail_draft_recips} bqdr
                  JOIN {context} c ON c.instanceid = bqdr.recipient_id AND c.contextlevel = :contextlevel
                 WHERE bqdr.recipient_id = :userid
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT c.id
                  FROM {block_quickmail_msg_ad_email} bqmae
                  JOIN {context} c ON c.instanceid = bqmae.usermodified AND c.contextlevel = :contextlevel
                 WHERE bqmae.usermodified = :userid
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT c.id
                  FROM {block_quickmail_msg_attach} bqma
                  JOIN {context} c ON c.instanceid = bqma.usermodified AND c.contextlevel = :contextlevel
                 WHERE bqma.usermodified = :userid
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT c.id
                  FROM {block_quickmail_alt_emails} bqae
                  JOIN {context} c ON c.instanceid = bqae.user_id AND c.contextlevel = :contextlevel
                 WHERE bqae.user_id = :userid OR bqae.usermodified = :usermodified
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT c.id
                  FROM {block_quickmail_notifs} bqn
                  JOIN {context} c ON c.instanceid = bqn.user_id AND c.contextlevel = :contextlevel
                 WHERE bqn.user_id = :userid OR bqn.usermodified = :usermodified
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT c.id
                  FROM {block_quickmail_event_notifs} bqen
                  JOIN {context} c ON c.instanceid = bqen.usermodified AND c.contextlevel = :contextlevel
                 WHERE bqen.usermodified = :usermodified
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT c.id
                  FROM {block_quickmail_schedules} bqs
                  JOIN {context} c ON c.instanceid = bqs.usermodified AND c.contextlevel = :contextlevel
                 WHERE bqs.usermodified = :usermodified
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT c.id
                  FROM {block_quickmail_rem_notifs} bqrn
                  JOIN {context} c ON c.instanceid = bqrn.usermodified AND c.contextlevel = :contextlevel
                 WHERE bqrn.usermodified = :usermodified
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        $sql = "SELECT c.id
                  FROM {block_quickmail_event_recips} bqer
                  JOIN {context} c ON c.instanceid = bqer.user_id AND c.contextlevel = :contextlevel
                 WHERE bqer.user_id = :userid
              GROUP BY c.id";

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        // TODO: Implement export_user_data() method.
        global $DB;
        $contexts = $contextlist->get_contexts();
        if (count($contexts) == 0) {
            return;
        }
        $context = reset($contexts);

        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;

        $sql1 = "SELECT bqs.*,
                  FROM {block_quickmail_signatures} bqs
                 WHERE bqs.user_id = :userid OR bqs.usermodified = :userid
              ORDER BY bqs.id";

        $sql2 = "SELECT bqm.*,
                  FROM {block_quickmail_messages} bqm
                 WHERE bqm.user_id = :userid OR bqm.usermodified = :userid
              ORDER BY bqm.id";

        $sql3 = "SELECT bqmr.*,
                  FROM {block_quickmail_msg_recips} bqmr
                 WHERE bqmr.user_id = :userid OR bqmr.usermodified = :userid
              ORDER BY bqmr.id";

        $sql4 = "SELECT bqdr.*,
                  FROM {block_quickmail_draft_recips} bqdr
                 WHERE bqdr.recipient_id = :userid
              ORDER BY bqdr.id";

        $sql5 = "SELECT bqmae.*,
                  FROM {block_quickmail_msg_ad_email} bqmae
                 WHERE bqmae.usermodified = :userid
              ORDER BY bqmae.id";

        $sql6 = "SELECT bqma.*,
                  FROM {block_quickmail_msg_attach} bqma
                 WHERE bqma.usermodified = :userid
              ORDER BY bqma.id";

        $sql7 = "SELECT bqae.*,
                  FROM {block_quickmail_alt_emails} bqae
                 WHERE bqae.user_id = :userid OR bqae.usermodified = :userid
              ORDER BY bqma.id";

        $sql8 = "SELECT bqn.*,
                  FROM {block_quickmail_notifs} bqn
                 WHERE bqn.user_id = :userid OR bqn.usermodified = :userid
              ORDER BY bqn.id";

        $sql9 = "SELECT bqen.*,
                  FROM {block_quickmail_event_notifs} bqen
                 WHERE bqen.usermodified = :userid
              ORDER BY bqen.id";

        $sql10 = "SELECT bqs.*,
                  FROM {block_quickmail_schedules} bqs
                 WHERE bqs.usermodified = :userid
              ORDER BY bqs.id";

        $sql11 = "SELECT bqrn.*,
                  FROM {block_quickmail_rem_notifs} bqrn
                 WHERE bqrn.usermodified = :userid
              ORDER BY bqrn.id";

        $sql12 = "SELECT bqer.*,
                  FROM {block_quickmail_event_recips} bqer
                 WHERE bqer.user_id = :userid
              ORDER BY bqer.id";

        $params = [
            'userid' => $userid
        ];

        $quickmailbqs = $DB->get_records_sql($sql1, $params);
        $quickmailbqm = $DB->get_records_sql($sql2, $params);
        $quickmailbqmr = $DB->get_records_sql($sql3, $params);
        $quickmailbqdr = $DB->get_records_sql($sql4, $params);
        $quickmailbqmae = $DB->get_records_sql($sql5, $params);
        $quickmailbqma = $DB->get_records_sql($sql6, $params);
        $quickmailbqae = $DB->get_records_sql($sql7, $params);
        $quickmailbqn = $DB->get_records_sql($sql8, $params);
        $quickmailbqen = $DB->get_records_sql($sql9, $params);
        $quickmailbqs = $DB->get_records_sql($sql10, $params);
        $quickmailbqrn = $DB->get_records_sql($sql11, $params);
        $quickmailbqer = $DB->get_records_sql($sql12, $params);

        $data = (object) [
            'signatures' => $quickmailbqs,
            'messages' => $quickmailbqm,
            'msg_recips' => $quickmailbmr,
            'draft_recips' => $quickmailbqdr,
            'msg_ad_email' => $quickmailbqmae,
            'msg_attach' => $quickmailbqma,
            'alt_emails' => $quickmailbqae,
            'notifs' => $quickmailbn,
            'event_notifs' => $quickmailbqen,
            'schedules' => $quickmailbqs,
            'rem_notifs' => $quickmailbqrn,
            'event_recips' => $quickmailbqer,
        ];

        $subcontext = [
            get_string('pluginname', 'block_quickmail'),
        ];

        writer::with_context($context)->export_data($subcontext, $data);

        $quickmailbqs->close();
        $quickmailbqm->close();
        $quickmailbqmr->close();
        $quickmailbqdr->close();
        $quickmailbqmae->close();
        $quickmailbqma->close();
        $quickmailbqae->close();
        $quickmailbqn->close();
        $quickmailbqen->close();
        $quickmailbqs->close();
        $quickmailbqrn->close();
        $quickmailbqer->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;

        $DB->delete_records('block_quickmail_signatures', ['user_id' => $userid]);
        $DB->delete_records('block_quickmail_signatures', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_messages', ['user_id' => $userid]);
        $DB->delete_records('block_quickmail_messages', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_msg_recips', ['user_id' => $userid]);
        $DB->delete_records('block_quickmail_msg_recips', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_draft_recips', ['recipient_id' => $userid]);
        $DB->delete_records('block_quickmail_msg_ad_email', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_msg_attach', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_alt_emails', ['user_id' => $userid]);
        $DB->delete_records('block_quickmail_alt_emails', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_notifs', ['user_id' => $userid]);
        $DB->delete_records('block_quickmail_notifs', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_event_notifs', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_schedules', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_rem_notifs', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_event_recips', ['user_id' => $userid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // TODO: Implement delete_data_for_user() method.
        global $DB;

        $contexts = $contextlist->get_contexts();
        if (count($contexts) == 0) {
            return;
        }
        $context = reset($contexts);

        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;

        $DB->delete_records('block_quickmail_signatures', ['user_id' => $userid]);
        $DB->delete_records('block_quickmail_signatures', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_messages', ['user_id' => $userid]);
        $DB->delete_records('block_quickmail_messages', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_msg_recips', ['user_id' => $userid]);
        $DB->delete_records('block_quickmail_msg_recips', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_draft_recips', ['recipient_id' => $userid]);
        $DB->delete_records('block_quickmail_msg_ad_email', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_msg_attach', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_alt_emails', ['user_id' => $userid]);
        $DB->delete_records('block_quickmail_alt_emails', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_notifs', ['user_id' => $userid]);
        $DB->delete_records('block_quickmail_notifs', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_event_notifs', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_schedules', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_rem_notifs', ['usermodified' => $userid]);
        $DB->delete_records('block_quickmail_event_recips', ['user_id' => $userid]);
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_course) {
            return;
        }

        $sql = "SELECT * FROM {block_quickmail_messages}";
        $userlist->add_from_sql('user_id', $sql, ['courseid' => $context->instanceid]);

        $sql = "SELECT * FROM {block_quickmail_alt_emails}";
        $userlist->add_from_sql('user_id', $sql, ['courseid' => $context->instanceid]);

        $sql = "SELECT * FROM {block_quickmail_notifs}";
        $userlist->add_from_sql('user_id', $sql, ['courseid' => $context->instanceid]);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param \core_privacy\local\request\approved_userlist $userlist
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $users = $userlist->get_users();
        foreach ($users as $user) {
            // Create a contextlist with only system context.
            $contextlist = new approved_contextlist($user, 'block_quickmail', [\context_user::instance($user->id)->id]);
            self::delete_data_for_user($contextlist);
        }
    }
}
