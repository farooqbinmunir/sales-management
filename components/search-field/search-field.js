jQuery(document).ready(function($) {
    // Search products in the product list table
    $(document).on('keyup', '#search-product', function () {
        const value = $(this).val().toLowerCase();
        const searchColumn = $(this).attr('data-search-column'); // Get the target column from data attribute
        const $context = $(this).closest('.fbm_ui_body, #page-pending_payments, .customers-page, .sales-page, .purchase-page, .returns-page, .stock-page');
        let $table = $context.find('.table-wrap table').first();
        if(!$table.length){
            $table = $('.table-wrap table').first();
        }
        let rowsToSkip = $(this).attr('data-rows-to-skip') || $(this).attr('data-row-to-skip'); // Backward compatible
        let rowsSelector = 'tbody tr';
        if (rowsToSkip) {
            rowsSelector += `:not(.${rowsToSkip})`; // Exclude rows with the specified class
        }
        $table.find(rowsSelector).each(function () {
            $(this).toggle($(this).children('.' + searchColumn).text().toLowerCase().includes(value));
        });
        resetTableFocus($table);
    });
});
