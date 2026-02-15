jQuery(document).ready(function($) {
    // Search products in the product list table
    $(document).on('keyup', '#search-product', function () {
        const value = $(this).val().toLowerCase();
        const searchColumn = $(this).attr('data-search-column'); // Get the target column from data attribute
        const $table = $('.table-wrap table');
        $table.find('tbody tr').each(function () {
            $(this).toggle($(this).children('.' + searchColumn).text().toLowerCase().includes(value));
        });
        resetTableFocus($table);
    });
});