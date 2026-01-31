jQuery(document).ready($ => {

	// Returns Page, add invoice no field
	let addInvoiceInput = $('input#add_invoice_no');
	if(addInvoiceInput.length){
		addInvoiceInput.on('input', function(){
			let enteredInvoiceNo = $(this).val();
			let invoiceNoMinLength = 10;
			if(enteredInvoiceNo && enteredInvoiceNo.length >= invoiceNoMinLength){
				jQuery.ajax({
				  url: ajaxUrl,
				  type: 'POST',
				  data: {
				  	action: 'get_invoiced_products',
				  	invoice_no: enteredInvoiceNo,
				  },
				  success: function(response, textStatus, xhr) {
				  	if(response){
				  		$('#invoice_no_error').slideUp();
				  		$('#returnForm_body').slideDown();
				  		let invoiced_products = `<option disabled selected>Select Product</option>`;
				  		$.each(response, function(index, product) {
				  			invoiced_products += `<option value="${product.prod_id}" data-quantity="${product.prod_quantity}">${product.prod_name}</option>`;
				  		});
				  		$('select#product_id').html(invoiced_products);
				  	}else{
				  		$('#invoice_no_error').slideDown();
				  		$('#returnForm_body').slideUp();
				  	}
				  }
				});
				
			}
		});
	}
	// Returns page, add max return quantity on product selection
	$(document).on('change', '#returnForm select#product_id', function(){
		let $this = $(this),
			productId = $this.val(),
			productQuantity = +$this.find(`[value="${productId}"]`).data('quantity'),
			returnQuantityInput = $('#return_quantity');
		returnQuantityInput.attr({
			placeholder: `Enter quantity to return (max ${productQuantity})`,
			max: productQuantity,
		});
	});

	// Calculate amount to return on quantity to return input
	$(document).on('input', '#returnForm #return_quantity', function(e){
		let selectedProductId = $('#product_id').val() ? $('#product_id').val() : null;
		if(!selectedProductId) return;

		let quantity = +$(this).val(),
			rate = 12,
			amount = quantity * rate,
			amountToReturnInput = $('#returnForm #return_amount');
		amountToReturnInput.val(amount).attr('value', amount);
	});

	// Process the sale return
		// Processing returns
	jQuery('#returnForm').on('submit', function (e) {
		e.preventDefault();
		let productIdElement = jQuery('select[name="product_id"]'),
			quantityElement = jQuery('input[name="quantity"]'),
			amountElement = jQuery('input[name="amount"]'),
			returnReasonElement = jQuery('textarea[name="return_reason"]');
			returnInvoiceElement = jQuery('input#add_invoice_no');


		let productId = Number(productIdElement.val()),
			quantity = Number(quantityElement.val()),
			quantityMaxLimit = +quantityElement.attr('max'),
			reason = returnReasonElement.val().trim(),
			invoice_no = returnInvoiceElement.val().trim();

		if(quantity && quantity > 0 && quantity <= quantityMaxLimit){
			quantityElement.css('border-color', 'green');
			// Error handling
			if (productId != null && productId != 0 && invoice_no.length >= 10) {
				productIdElement.css('border-color', 'green');
				returnInvoiceElement.css('border-color', 'green');
				let amount = +amountElement.val();
				var data = {
					action: 'handle_product_return',
					product_id: productId,
					quantity: quantity,
					amount: amount,
					return_reason: reason,
					invoice_no: invoice_no,
				};

				jQuery.post(ajaxurl, data, function (response) {
					if (response.success) {
						location.reload(); // Reload page to reflect updated return
					} else {
						alert(response.data.message);
					}
				});
			} else {
				if (!invoice_no || invoice_no.length < 10) {
					returnInvoiceElement.css('border', '2px solid red');
				} else {
					returnInvoiceElement.css('border', '2px solid green');
				}

				if (productId == null || productId == 0) {
					productIdElement.css('border', '2px solid red');
				} else {
					productIdElement.css('border', '2px solid green');
				}
			}
		}else{
			quantityElement.css('border', '2px solid red');
		}



	});

}); // .ready() closed