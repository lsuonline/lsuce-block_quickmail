<?php
/**
 * ************************************************************************
 *                            QuickMail
 * ************************************************************************
 * Web Service to allow users to remove messages in the "Sent Message History"
 * This does NOT actually delete the msg record but marks it as "deleted".
 
 * @package    block - Quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Update by David Lowe
 */

class sent_messages_ctrl
{
    public function remove_sent_messages($data) {
        global $USER;
        // Authentication.
        require_login();

        $success = "success";
        $fail_msg = "The messages have been successfully removed";
        foreach ($data->ids as $id) {

            // Check that the message has not been deleted.
            if (!$message = \block_quickmail\persistents\message::find_or_null($id)) {
                $success = "error";
                $fail_msg = "Cannot find this sent message";
            }

            // Check that the user can delete this message.
            if ($message->get('user_id') !== $USER->id) {
                $success = "error";
                $fail_msg = "This user cannot delete the sent message(s)";
            }

            $message->mark_as_deleted();
        }

        return array(
            'success' => $success,
            'msg' => $fail_msg
        );
    }
}
