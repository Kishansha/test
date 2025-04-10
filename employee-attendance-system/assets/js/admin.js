jQuery(document).ready(function($) {
    // Show modal when edit button clicked
    $(document).on('click', '.edit-record', function() {
        $('#admin-edit-modal').show();
        $('#admin-edit-id').val($(this).data('id'));
        $('#admin-edit-check-in').val($(this).data('checkin'));
        $('#admin-edit-check-out').val($(this).data('checkout') || '');
    });

    // Close modal when clicking outside
    $(document).on('click', function(e) {
        if ($(e.target).is('#admin-edit-modal')) {
            $('#admin-edit-modal').hide();
        }
    });

    // Close modal with cancel button
    $('.eas-admin-cancel-edit').on('click', function() {
        $('#admin-edit-modal').hide();
    });

    // Handle form submission
    $('#admin-edit-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        $form.find('button').prop('disabled', true);
        
        $.ajax({
            url: easAdminData.ajaxurl,
            type: 'POST',
            data: {
                action: 'eas_admin_edit',
                id: $('#admin-edit-id').val(),
                check_in: $('#admin-edit-check-in').val(),
                check_out: $('#admin-edit-check-out').val(),
                nonce: easAdminData.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Failed to update record');
                }
            },
            complete: function() {
                $form.find('button').prop('disabled', false);
            }
        });
    });
});