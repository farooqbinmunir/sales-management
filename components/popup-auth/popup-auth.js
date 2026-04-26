jQuery(document).ready(function($) {
    const ajaxUrl = fbm_ajax.url,
        nonce = fbm_ajax.nonce,
        path = fbm_ajax.path;

    var notice = $('#fbm_notice');
    const authErrorEl = $('#auth_error_message');

    const clearAuthError = () => {
        authErrorEl.text('').hide();
        $('#auth_pincode').css('border-color', '#ccc');
        $('#auth_user_id').css('border-color', '#ccc');
    };

    const showAuthError = (message, field = 'pincode') => {
        authErrorEl.text(message).show();
        if (field === 'user') {
            $('#auth_user_id').css('border-color', '#d63638').focus();
        } else {
            $('#auth_pincode').css('border-color', '#d63638').focus();
        }
    };

    // Handle print button clicks with authentication
    let pendingPrintAction = null;    
    $(document).on('click', '#printIvoiceBtn, #printBillBtn', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();

        const clickedAction = $(this).attr('id');

        // Validate sale/customer data before opening auth popup
        if (typeof window.preValidateSaleBeforeAuth === 'function') {
            const isValidBeforeAuth = window.preValidateSaleBeforeAuth(clickedAction);
            if (!isValidBeforeAuth) {
                return false;
            }
        }

        pendingPrintAction = clickedAction;

        const openAuthPopup = () => {
            clearAuthError();
            $('#printAuthOverlay').fadeIn(function(){
                $('#auth_pincode').focus();
            });
        };

        const $customerPopup = $('#customer_register_area #salesCalculator');
        if ($customerPopup.length && $customerPopup.is(':visible')) {
            if (typeof window.fbmCloseCustomerPopup === 'function') {
                window.fbmCloseCustomerPopup();
                setTimeout(openAuthPopup, 250);
            } else {
                openAuthPopup();
            }
        } else {
            openAuthPopup();
        }

        return false;
    });

    // Handle authentication confirmation
    $(document).on('click', '#authConfirm', function(){
        const pincode = $('#auth_pincode').val().trim();
        const userId = $('#auth_user_id').val();
        const userName = $('#auth_user_id option:selected').text();

        if(!userId){
            showAuthError('Please select salesman.', 'user');
            return;
        }
        if(!pincode){
            showAuthError('Please enter pincode.', 'pincode');
            return;
        }
        clearAuthError();
        const payload = {
            action: 'fbm_verify_user',
            pincode: pincode,
            user_id: userId,
            nonce: nonce
        };
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: payload,
            success: function(response){
                if(response.success){
                    window.isPrintAuthenticated = true;
                    window.authSalesmanId = Number(userId);
                    window.authSalesmanName = userName;
                    $('#printIvoiceBtn').data('user_id', userId);
                    $('#printIvoiceBtn').data('user_name', userName);
                    $('#printAuthOverlay').fadeOut(function(){
                        $('#auth_pincode').val('');
                    });

                    // Proceed with original action
                    if(pendingPrintAction === 'printIvoiceBtn'){
                        doSaveSaleAndPrintBill();
                    }else if(pendingPrintAction === 'printBillBtn'){
                        printBill();
                    }
                }else{
                    const errorMessage = (response && response.data) ? response.data : 'Invalid pincode';
                    showAuthError(errorMessage, 'pincode');
                }
            }
        });
    });

    // Toggle password visibility in the authentication popup
    $(document).on('click', '#toggleAuthPassword', function(){

        const $pincodeInput = $('#auth_pincode');
        const currentType = $pincodeInput.attr('type');

        if(currentType === 'password'){
            $pincodeInput.attr('type', 'text');
            $(this)
                .removeClass('fa-eye')
                .addClass('fa-eye-slash');
        }else{
            $pincodeInput.attr('type', 'password');
            $(this)
                .removeClass('fa-eye-slash')
                .addClass('fa-eye');
        }

    });

    // Cancel button
    $(document).on('click', '#authCancel, #closeAuthPopup', function(){
        $('#printAuthOverlay').fadeOut(function(){
            $('#auth_pincode').val('');
            clearAuthError();
        });
    });

    // Click outside modal closes it
    $(document).on('click', '#printAuthOverlay', function(e){
        if($(e.target).is('#printAuthOverlay')){
            $('#printAuthOverlay').fadeOut(function(){
                $('#auth_pincode').val('');
                clearAuthError();
            });
        }
    });

    $(document).on('input change', '#auth_pincode, #auth_user_id', function(){
        clearAuthError();
    });

});
