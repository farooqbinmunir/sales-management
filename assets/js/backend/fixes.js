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

    // Close customer popup without forcing sale type reset
    $(document).on('click', '#customer_register_area #salesCalculatorCloser', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();
        fbmCloseCustomerPopup();
    });

    // If customer popup closed by clicking outside or pressing ESC
    $(document).on('fbm:customerPopupClosed', function(){
        // Keep print actions visible even after popup closes
        $('#print-actions').appendTo('.sales-main-actions');
    });

}); // close .ready()
