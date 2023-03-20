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
         * @param {obj} A simple object with the 'message' and 'type' of notification.
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
         * @param {obj} Server Response {'success', 'data', 'msg'}
         *
         * @return void
         */
        storeMsg: function(data) {
            // Save data to sessionStorage
            sessionStorage.setItem('sent_delete_success', data.success);
            sessionStorage.setItem('sent_delete_msg', data.msg);
        },
        /**
         *  If a message is stored then show the notification and remove it.
         * @param void
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