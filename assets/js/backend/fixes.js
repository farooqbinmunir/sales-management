jQuery(document).ready(function($) {
    // Fixes for the backend

    // Search products in the product list table
    $('#search-product').on('keyup', function () {
        const value = $(this).val().toLowerCase();
        const $table = $('.table-wrap table');

        $table.find('tbody tr').each(function () {
            console.table(value, $(this).children('.pname').text().toLowerCase());
            $(this).toggle($(this).children('.pname').text().toLowerCase().includes(value));
        });

        resetTableFocus($table);
    });

    // Sales type change
    $(document).on('change', '#salesType', function(){
        const type = $(this).val();
        handleSalesTypeChange(type);
    });

    // When user fills paid amount in partial sale
    $(document).on('blur', '.partial_payment_row input', function(){

        const paid = parseFloat($(this).val()) || 0;
        const netTotal = parseFloat($('.net-total').text()) || 0;

        if(paid > 0 && paid <= netTotal){

            const $popup = $('#customer_register_area #salesCalculator');
            const $printActions = $('#print-actions');
            const $popupPrintContainer = $('.salesCalculatorWrapper');

            // Move buttons inside popup
            $printActions.appendTo($popupPrintContainer);

            $popup.fadeIn();
        }

    });

    // Bind ONLY to partial payment input
    $(document).on('input', '.partial_payment_row input', handlePaidInput);


});