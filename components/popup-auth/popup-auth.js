jQuery(document).ready(function($) {
    const ajaxUrl = fbm_ajax.url,
        nonce = fbm_ajax.nonce,
        path = fbm_ajax.path;

    var notice = $('#fbm_notice');

    // Handle print button clicks with authentication
    let pendingPrintAction = null;    
    $(document).on('click', '#printIvoiceBtn, #printBillBtn', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();

        pendingPrintAction = $(this).attr('id');

        $('#printAuthOverlay').fadeIn(function(){
            $('#auth_password').focus();
        });

        return false;
    });

    // Handle authentication confirmation
    $(document).on('click', '#authConfirm', function(){
        const password = $('#auth_password').val().trim();
        if(!password){
            alert('Please enter password');
            return;
        }
        const payload = {
            action: 'fbm_verify_user',
            password: password,
            nonce: nonce
        };
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: payload,
            success: function(response){
                if(response.success){
                    window.isPrintAuthenticated = true;
                    $('#printAuthOverlay').fadeOut();

                    // Proceed with original action
                    if(pendingPrintAction === 'printIvoiceBtn'){
                        doSaveSaleAndPrintBill();
                    }else if(pendingPrintAction === 'printBillBtn'){
                        printBill();
                    }
                }else{
                    alert('Invalid credentials');
                }
            }
        });
    });

    // Toggle password visibility in the authentication popup
    $(document).on('click', '#toggleAuthPassword', function(){

        const $passwordInput = $('#auth_password');
        const currentType = $passwordInput.attr('type');

        if(currentType === 'password'){
            $passwordInput.attr('type', 'text');
            $(this)
                .removeClass('fa-eye')
                .addClass('fa-eye-slash');
        }else{
            $passwordInput.attr('type', 'password');
            $(this)
                .removeClass('fa-eye-slash')
                .addClass('fa-eye');
        }

    });

    // Cancel button
    $(document).on('click', '#authCancel, #closeAuthPopup', function(){
        $('#printAuthOverlay').fadeOut();
    });

    // Click outside modal closes it
    $(document).on('click', '#printAuthOverlay', function(e){
        if($(e.target).is('#printAuthOverlay')){
            $('#printAuthOverlay').fadeOut();
        }
    });

});