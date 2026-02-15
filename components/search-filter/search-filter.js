jQuery(document).ready(function($) {    
	// On filter type change do the search filteration
	$(document).on('change', '#searchFilterType', function(){
		let selectedFilter = $(this).val(),
			searchInput = $('#search-product');
        console.log('Selected filter', selectedFilter);
		searchInput.attr('data-search-column', selectedFilter); // Store the selected filter type in data attribute of search input
		searchInput.focus().trigger('input'); // Trigger input event to apply the search filter immediately based on the new filter type
	});
});