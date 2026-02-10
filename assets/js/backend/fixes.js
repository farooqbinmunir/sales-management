jQuery(document).ready(function($) {
    // Fixes for the backend
    // Fixes the issue with the media library not showing the correct images after uploading

    $('#search-product').on('keyup', function () {
        const value = $(this).val().toLowerCase();
        const $table = $('.table-wrap table');

        $table.find('tbody tr').each(function () {
            console.table(value, $(this).children('.pname').text().toLowerCase());
            $(this).toggle($(this).children('.pname').text().toLowerCase().includes(value));
        });

        resetTableFocus($table);
    });

});