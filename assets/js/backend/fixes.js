jQuery('#printIvoiceBtn, #printBillBtn').off('click');
jQuery(document).ready(function($) {
    const ajaxUrl = fbm_ajax.url,
        nonce = fbm_ajax.nonce,
        path = fbm_ajax.path;

    var notice = $('#fbm_notice');

    // Fixes for the backend

    // Sales type change
    $(document).on('change', '#salesType', function(){
        const type = $(this).val();
        handleSalesTypeChange(type);
    });

    // Bind ONLY to partial payment input
    $(document).on('input', '.partial_payment_row input', handlePaidInput);

    // If close button clicked
    $(document).on('click', '#customer_register_area #salesCalculatorCloser', function(){
        resetToCashSale();
    });


    // If customer popup closed by clicking outside or pressing ESC
    $(document).on('fbm:customerPopupClosed', function(){
        const currentType = $('#salesType').val();
        if(currentType === 'Credit Sale' || currentType === 'Partially Paid') {
            // Reset to cash only if popup closed manually
            resetToCashSale();
        }
    });

}); // close .ready()