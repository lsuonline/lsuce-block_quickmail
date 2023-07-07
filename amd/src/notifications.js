define(['jquery', 'core/notification'], function($, notification) {
    'use strict';

    return {

        /**
         * A simple way to call the Moodle core notification system.
         * Type can be either: success, warning, info, error
         *  Example:
         *  noti.callNoti({
         *      message: "This is a success test",
         *      type: "success"
         *  });
         * @param {obj} data A simple object with the 'message' and 'type' of notification.
         * @return void
         */
        callNoti: function(data) {
            if (!data.hasOwnProperty('message')) {
                console.log("ERROR -> Notification was called but with no message, aborting.");
            }
            if (!data.hasOwnProperty('type')) {
                // default to info
                data.type = "info";
            }
            notification.addNotification(data);
        },
        /**
         * Store the reponse object to showcase a message after reload.
         * @param {obj} data Server Response {'success', 'data', 'msg'}
         *
         * @return void
         */
        storeMsg: function(data) {
            // Save data to sessionStorage
            if (data.hasOwnProperty('success')) {
                sessionStorage.setItem('sent_delete_success', data.success);
                sessionStorage.setItem('sent_delete_msg', data.msg);
            } else {
                console.log("NOTI -> Error: There was an error with the data from the server, please contact Moodle Dev Team.");
            }
        },

        /**
         *  If a message is stored then show the notification and remove it.
         * @return void
         */
        showMsg: function() {
            // Save data to sessionStorage
            if (sessionStorage.getItem('sent_delete_msg')) {
                this.callNoti({
                    message: sessionStorage.getItem('sent_delete_msg'),
                    type: sessionStorage.getItem('sent_delete_success')
                });
                // Remove saved data from sessionStorage
                sessionStorage.removeItem('sent_delete_msg');
                sessionStorage.removeItem('sent_delete_success');
            }
        }
    };
});