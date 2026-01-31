jQuery(document).ready($ => {
	const ajaxUrl = fbm_ajax.url,
		nonce = fbm_ajax.nonce,
		path = fbm_ajax.path;

	var notice = $('#fbm_notice');

	// Hide payment method when payment status 'Unpaid' is selected on Add New Purchase form
	$(document).on('change', 'table.anpPurchaseInfoTable select.payment_status', function(e){
		let $this = $(this),
			paymentStatus = $this.val(),
			rowContainer = $this.closest('tr').parent(),
			totalPayment = +rowContainer.find('input#anpTotalCartAmount').val(),
			paymentMethod = rowContainer.children('tr.anpRowPaymentMethod').find('.anpFieldWrap'),
			partiallyPaid = rowContainer.children('tr.anpRowPartiallyPaid').find('.anpFieldsContainer');

		// Hide Paid and Remaining amount fields when payment status is selected other than Partially Paid
		if('Partially Paid' === paymentStatus){
			partiallyPaid.slideDown();
			paymentMethod.slideDown();
		}else if('Unpaid' === paymentStatus){
			partiallyPaid.slideUp();
			paymentMethod.slideUp();

			partiallyPaid.find('input#anpPaid').val(0).attr('value', 0);
			partiallyPaid.find('input#anpRemaining').val(totalPayment).attr('value', totalPayment);
		}else if('Paid' === paymentStatus){
			partiallyPaid.slideUp();
			paymentMethod.slideDown();

			partiallyPaid.find('input#anpPaid').val(totalPayment).attr('value', totalPayment);
			partiallyPaid.find('input#anpRemaining').val(0).attr('value', 0);
		}
	});

	// %%%%%%%%%%%%%%%%%%%%
	// Adding new purchase
	// %%%%%%%%%%%%%%%%%%%%
	
	$(document).on('click', '#add_stock', function(e) {
		e.preventDefault();
		let button = $(this),
			buttonText = button.text(),
			wrapper = button.closest('.product-form-wrap'),
			form = wrapper.find('form#addPurchaseForm'),
			formFieldGroups = form.find('#purchase_form_fields_container .purchase_form_fields_group'),
			notice = $('#fbm_notice'),
			purchaseInfoTable = form.find('table.anpPurchaseInfoTable'),
			purchaseInvoice = + purchaseInfoTable.find('[name="purchase_invoice"]').val(),
			purchaseTotalPayment = Math.abs(purchaseInfoTable.find('[name="purchase_total_payment"]').val()),
			purchasePaymentStatus = purchaseInfoTable.find('[name="payment_status"]').val(),
			purchasePaymentMethod = purchaseInfoTable.find('[name="payment_method"]').val(),
			description = purchaseInfoTable.find('[name="description"]').val().trim(),

			// Paid amount
			paymentPaidInput = purchaseInfoTable.find('#anpPaid'),
			paymentPaid = +paymentPaidInput.val(),
			maxAmountToPay = +paymentPaidInput.attr('max'),

			// Remaining amount
			paymentRemainingInput = purchaseInfoTable.find('#anpRemaining'),
			paymentRemaining = +paymentRemainingInput.val();

		if(paymentPaid <= maxAmountToPay){
			paymentPaidInput.attr('title', 'Good to go').css('border-color', 'green');
			const purchaseInfo = {
				invoice: purchaseInvoice,
				totalPayment: purchaseTotalPayment,
				paymentStatus: purchasePaymentStatus,
				paymentMethod: purchasePaymentMethod,
				description,
				paymentPaid,
				paymentRemaining
			};

			// Prepare the payload
			const payload = [];
			if(formFieldGroups.length){
				for (const fieldGroup of formFieldGroups) {
					// Get FORM VALUES and prepare PAYLOAD for submission to ADD NEW PURCHASE
					if(fieldGroup){
						// Getting Form Elements
						let productId = $(fieldGroup).find(`[name="product_id"]`),
							manufacturer = $(fieldGroup).find(`[name="manufacturer"]`),
							vendor = $(fieldGroup).find(`[name="vendor"]`),
							purchaseRate = $(fieldGroup).find(`[name="purchase_rate"]`),
							saleRate = $(fieldGroup).find(`[name="sale_rate"]`),
							quantity = $(fieldGroup).find(`[name="quantity"]`),
							payment = $(fieldGroup).find(`[name="payment"]`);

						// Form Values
						let productIdVal = Number(productId.val()),
							manufacturerId = manufacturer.data('manufacturer_id'),
							vendorVal = vendor.val(),
							purchaseRateVal = Number(purchaseRate.val()),
							saleRateVal = Number(saleRate.val()),
							quantityVal = Number(quantity.val()),
							paymentVal = Number(payment.val());

						// Preparing purcahse data object for payload
						if(productIdVal){
							const singlePurchaseData = {
								product_id: productIdVal,
								manufacturer_id: manufacturerId,
								vendor: vendorVal,
								rate: purchaseRateVal,
								quantity: quantityVal,
								payment: paymentVal,
							};
							payload.push(singlePurchaseData);
						}
						
					}else{
						alert('Payload data missing');
					}
				}
			}
			if(payload.length){
				button.text('Adding...');
				$.ajax({
					url: ajaxUrl,
					type: 'POST',
					data: {
						action: 'handle_purchase',
						payload: JSON.stringify(payload),
						purchase_info: JSON.stringify(purchaseInfo),
					},
					success: res => {
						button.text(buttonText);
						let ajaxSuccess = res.success,
							resMessages = res.data,
							noticeHtml = ``;
						if(resMessages.length){
							$.each(resMessages, (i, message) => {
								noticeHtml += `<div class="notice notice-${ajaxSuccess === true ? 'success' : 'error'} is-dismissible">
													<p>${message}</p>
													<button type="button" class="notice-dismiss"></button>
												</div>`;
							});
						}
						wrapper.slideUp();
						$(noticeHtml).insertBefore('.fbm_ui_body');
						scrollTo('#fbm_notice', 100);
						setTimeout(() => location.reload(), 10 * 1000); // 10 seconds
					}
				});
			}else{
				alert('Payload is empty!');
			}
		}else{
			paymentPaidInput.attr('title', 'Entered amount is either zero or bigger than total amount.').css('border-color', 'red');
		}
	});

	// Calculate and set remaining amount field when entring in paid amount field
	$(document).on('input', 'input#anpPaid', function(e){
		let $this = $(this),
			parentTable = $this.closest('table'),
			totalPayment = parentTable.find('input#anpTotalCartAmount').val(),
			remainingAmountInput = parentTable.find('input#anpRemaining'),
			paidAmount = $this.val(),
			remainingAmount = +totalPayment - +paidAmount;
		remainingAmountInput.val(remainingAmount).attr('value', remainingAmount);
	});

	$(document).on('input', 'input#anpTotalCartAmount', function(){
		let $this = $(this),
			parentTable = $this.closest('table'),
			totalPayment = $this.val(),
			paidAmountInput = parentTable.find('input#anpPaid'),
			remainingAmountInput = parentTable.find('input#anpRemaining');
		paidAmountInput.attr('max', totalPayment);
		remainingAmountInput.attr('max', totalPayment);
	});



}); // .ready() closed