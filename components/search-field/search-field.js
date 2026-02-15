jQuery(document).ready(function($) {
    // Search products in the product list table
    $(document).on('keyup', '#search-product', function () {
        const value = $(this).val().toLowerCase();
        const searchColumn = $(this).attr('data-search-column'); // Get the target column from data attribute
        const $table = $('.table-wrap table');
        let rowsToSkip = $(this).attr('data-rows-to-skip'); // Get the class of rows to skip from data attribute
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