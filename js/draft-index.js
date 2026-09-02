$(function() {
    // Handle change of "select course filter".
    var originalSelectValue = $('#select_course_filter').val();
    var sesskey = $('input#sesskey').val();
    var actions = {
        'delete': {'confirmMessage': 'Delete this draft?'},
        'duplicate': {'confirmMessage': 'Duplicate this draft?'},
    };

    // When selected course id changes.
    $('#select_course_filter').change(function(e) {
        e.preventDefault();

        // If the value actually changed, redirect to the correct page.
        if (originalSelectValue !== this.value) {
            window.location.href = 'drafts.php?courseid=' + this.value;
        }
    });

    $(document).click(function(e) {
        var btnAction = $(e.target).data('action') ?? false;
        if (btnAction && actions[btnAction]) {
            e.preventDefault();
            if (confirm(actions[btnAction].confirmMessage)) {
                $.ajax({
                    url: '/blocks/quickmail/drafts.php',
                    method: 'POST',
                    data: {
                        action: btnAction,
                        id: $(e.target).data('id'),
                        sesskey: sesskey,
                    },
                })
                .done(() => {
                    window.location.reload();
                });
            }
        }
    });
});
