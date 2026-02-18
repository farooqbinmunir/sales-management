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
	
	$(document).on('click', '#add_stock', handleAddPurchaseFormSubmit);

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