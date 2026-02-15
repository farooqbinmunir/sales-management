jQuery(document).ready(function($) {

    $(document).on('keydown', '.partial_payment_row input', function(e){
        if(e.key === 'Enter'){
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            tryOpenCustomerPopup($(this));
        }
    });

	// Trigger click on focused product row by pressing Enter Button
	$(document).on('keydown', (e) => {
		let focusItem = $('.focused:not(.zero_stock_alert)');
		if (focusItem.length) {
			if (e.key === 'Enter') {
				// addProduct.call(focusItem);
				$('.focused:not(.zero_stock_alert)').click();
			}else{
				return true;
			}
		}

		if(e.key === 'Enter' && $('#printAuthOverlay').is(':visible')){
            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();
            $('#authConfirm').trigger('click');
        }
	});

    $(document).on('keyup', function(e){
		let salesCalculatorPopup = $('#salesCalculator');
		const $popup = $('#customer_register_area #salesCalculator');
		if(e.key === 'Escape'){
			if(salesCalculatorPopup.is(":visible")){
				salesCalculatorPopup.fadeOut();
			}else{            
				console.log('Not Visible');
			}

			if($popup.is(':visible')){
				fbmCloseCustomerPopup();
            }
		}
	});

    // Close sales panel on pressing enter after adding quantity
	$(document).on('keydown', '.selected-product-table-wrap .quantity input', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            $('span.close-sp-tbl').trigger('click');

            // Move focus away from product list
            $(this).blur();
        }
    });

    $(document).on('input', '.selected-product-table-wrap tbody .quantity input', function (e) {
        // Prevent negative sign input
        if (e.key === '-') {
            return 0;
        }
    });

    // Add focus class when condition is true 
    let productIRowIndex = 0;
    $(document).on('keydown', (e) => {
        let allScrollEleRow = $('.scrollelement tbody tr');
        let visibleRows = Array.from(allScrollEleRow).filter((row) => {
            return row.style.display !== 'none' && !row.classList.contains('edit_form');
        });

        if (e.key === 'ArrowUp') {
            productIRowIndex = (productIRowIndex > 0) ? productIRowIndex - 1 : 0;
            updateFocus(productIRowIndex);
        } else if (e.key === 'ArrowDown') {
            productIRowIndex = (productIRowIndex < visibleRows.length - 1) ? productIRowIndex + 1 : visibleRows.length - 1;
            updateFocus(productIRowIndex);
        }
    });

    $(document).on('keydown', '#auth_password', function(e){
        if(e.key === 'Enter'){
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            $('#authConfirm').trigger('click');
        }
    });

}); /* .ready() close */