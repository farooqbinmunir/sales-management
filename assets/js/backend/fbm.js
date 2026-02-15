jQuery(document).ready(function ($) {


	

	// Edit Single Item
	$(document).on('click', '.edit_product_btn, .edit_purchase_btn', quick_edit);
	
	// Close quick edit of single product item
	$(document).on('click', '.close_quick_edit_popup', function (param) {
		$(this).closest('tr').find('.quick_edit_form').slideUp();
	});

	// Update Single Product
	$(document).on('click', `.update_product`, function () {

		let $this = $(this),
			id = $this.data('id'),
			currentProduct = $this.closest('tr'),
			product_name = currentProduct.find('#product_name').val(),
			product_purchase_price = Number(currentProduct.find('#product_purchase_price').val()),
			product_sale_price = Number(currentProduct.find('#product_sale_price').val()),
			product_vendor = currentProduct.find('#product_vendor').val(),
			product_manufacturer = +currentProduct.find('#product_manufacturer').val(),
			product_location = currentProduct.find('#product_location').val(),
			product_min_quantity = currentProduct.find('#add_min_quantity').val();
			
		const payload = {
			product_name: product_name,
			product_purchase_price: product_purchase_price,
			product_sale_price: product_sale_price,
			product_vendor: product_vendor,
			product_manufacturer: product_manufacturer,
			product_location: product_location,
			product_min_quantity: product_min_quantity,
		};
		$.ajax({
			url: ajaxUrl,
			method: 'POST',
			data: {
				action: 'handle_product',
				payload: JSON.stringify(payload),
				required_action: 'update',
				id: id,
			},
			success: function (response) {
				if (response.success) {
					currentProduct.slideUp();
					notice.removeClass('notice-error').addClass('notice-success').text('Product updated successfully.').slideDown();
					scrollTo('#fbm_notice', 100);
					setTimeout(() => location.reload(), 1500);
				} else {
					scrollTo('#fbm_notice', 100);
					notice.removeClass('notice-success').addClass('notice-error').text('Failed to update product.').slideDown();
				}
			}
		});
	});

	// Update Single Purchase
	$(document).on('click', `.update_purchase`, function () {
		let $this = $(this);
		let purchase_id = $this.data('id');
		let currentPurchaseForm = $this.closest('form');
		let product_id = currentPurchaseForm.find('#product_id'),
			vendor = currentPurchaseForm.find('#purchase_product_vendor'),
			rate = currentPurchaseForm.find('#purchase_rate'),
			quantity = currentPurchaseForm.find('#purchase_quantity'),
			payment = currentPurchaseForm.find('#purchase_payment'),
			payment_status = currentPurchaseForm.find('#purchase_payment_status'),
			payment_method = currentPurchaseForm.find('#purchase_payment_method'),
			description = currentPurchaseForm.find('#purchase_description');

		let product_id_val = Number(product_id.val()),
			vendor_val = vendor.val(),
			rate_val = Number(rate.val()),
			quantity_val = Number(quantity.val()),
			payment_val = Number(payment.val()),
			payment_status_val = payment_status.val(),
			payment_method_val = payment_method.val(),
			description_val = description.val();

		if (product_id_val != null && product_id_val != 0 && quantity_val != '' && quantity_val > 0) {
			$('#product_id, #purchase_quantity').each((i, v) => {
				$(v).css('border-color', 'green');
			});
			const payload = {
				product_id: product_id_val,
				vendor: vendor_val,
				rate: rate_val,
				quantity: quantity_val,
				payment: payment_val,
				payment_status: payment_status_val,
				payment_method: payment_method_val,
				description: description_val,
			};
			$.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					action: 'handle_purchase',
					payload: JSON.stringify(payload),
					required_action: 'update',
					id: purchase_id,
				},
				success: res => {
					if (res.success) {
						let currentProduct = $this.closest('tr').find('.quick_edit_form');
						currentProduct.slideUp();
						notice.removeClass('notice-error').addClass('notice-success').text('Purchase updated successfully.').slideDown();
						scrollTo('#fbm_notice', 100);
						setTimeout(() => location.reload(), 1500);
					} else {
						scrollTo('#fbm_notice', 100);
						notice.removeClass('notice-success').addClass('notice-error').text(res.data).slideDown();
					}
				}
			});
		} else {
			if (product_id_val == null || product_id_val == 0) {
				product_id.css('border', '2px solid red');
			} else {
				product_id.css('border', '2px solid green');
			}


			if (quantity_val == '' || quantity_val <= 0) {
				quantity.css('border', '2px solid red');
			} else {
				quantity.css('border', '2px solid green');
			}


		}


	});

	// %%%%%%%%%%%%%%%%%%%%
	// SELECT2 init on add new purchase form on field(selectbox) SELECT PRODUCT
	// %%%%%%%%%%%%%%%%%%%%
	let purchaseProductSelectbox = jQuery('#addPurchaseForm .purchase_product_select select.product_id').first();
	if(purchaseProductSelectbox.length){
		purchaseProductSelectbox.select2({
			width: '100%',
		});
	}
	// %%%%%%%%%%%%%%%%%%%%



	// %%%%%%%%%%%%%%%%%%%%
	// Updating Product rate on form ADD NEW PURCHASE on PURCHASES page
	// %%%%%%%%%%%%%%%%%%%%
	// Update toggler
	
	$(document).on('click', '.btnUpdateRateToggler', function(){
		let $this = $(this),
			wrapper = $this.closest('.purchase_product_rate'),
			inputPurchaseRate = wrapper.find('input.purchase_rate'),
			needToggleElements = wrapper.find('.needToggle'),
			inputElements = wrapper.find('input');
		needToggleElements.slideDown();
		inputPurchaseRate.prop('readonly', false);
		inputElements.css('border-color', 'inherit');
		wrapper.addClass('rate_modifying').removeClass('rate_updated').find('.notice').remove();
	});

	// Cancelling Update rate modifying
	
	$(document).on('click', '.purchase_product_rate .btnCancelUpdateRate', function(){
		let $cancelBtn = $(this),
			wrapper = $cancelBtn.closest('.purchase_product_rate'),
			inputElements = wrapper.find('input'),
			inputPurchaseRate = wrapper.find('input.purchase_rate'),
			needToggleElements = wrapper.find('.needToggle');
		needToggleElements.slideUp();
		inputPurchaseRate.prop('readonly', true);
		inputElements.css('border-color', 'inherit');
		wrapper.addClass('cancelled_rate_updating').removeClass('rate_modifying').find('.notice').remove();
	});

	// Update product rate
	$(document).on('click', '.btnUpdateRate', function(){
		let button = $(this),
			buttonText = button.text(),
			wrapper = button.closest('.purchase_product_rate'),
			needToggleElements = wrapper.find('.needToggle'),
			inputPurchaseRate = wrapper.find('input.purchase_rate'),
			inputSaleRate = wrapper.find('input.sale_rate'),
			product_id = inputPurchaseRate.data('product_id'),
			purchase_newRate = +inputPurchaseRate.val(),
			sale_newRate = +inputSaleRate.val();
		if(product_id && purchase_newRate && sale_newRate){
			wrapper.find('.notice').remove();
			button.text('Updating...');
			wrapper.removeClass('rate_updated cancelled_rate_updating').addClass('rate_updating');
			
			$.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					action: 'update_product_rate',
					product_id: product_id,
					purchase_new_rate: purchase_newRate,
					sale_new_rate: sale_newRate,
				},
				success: res => {
					if(res.success){
						wrapper.find('.error-update-failed').remove();
						inputPurchaseRate.prop('readonly', true).attr('value', purchase_newRate);
						inputSaleRate.attr('value', sale_newRate);
						button.text(buttonText);
						needToggleElements.slideUp();
						wrapper.removeClass('rate_updating rate_modifying cancelled_rate_updating').addClass('rate_updated');
						if(!wrapper.find('.success-rate-updated').length){
							wrapper.append(`<p class="notice notice-success success-rate-updated" style="font-size: 12px;">${res.data}</p>`);	
						}
					}else{
						wrapper.find('.notice').remove();
						if(!wrapper.find('.error-update-failed').length){
							wrapper.append(`<p class="notice notice-error error-update-failed" style="font-size: 12px;">${res.data}</p>`);	
						}
					}
				}
			});
		}else{
			if(!product_id){
				if(!wrapper.find('.error-missing-product-id').length){
					wrapper.append(`<p class="notice notice-error error-missing-product-id" style="font-size: 12px;">Product not selected!</p>`);	
				}
			}else{
				wrapper.find('.error-missing-product-id').remove();
			}

			if(!purchase_newRate){
				if(!wrapper.find('.error-empty-purchase-new-rate').length){
					wrapper.append(`<p class="notice notice-error error-empty-purchase-new-rate" style="font-size: 12px;">Purchase Rate is empty!</p>`);
				}
				inputPurchaseRate.css('border-color', 'red');
			}else{
				inputPurchaseRate.css('border-color', 'inherit');
				wrapper.find('.error-empty-purchase-new-rate').remove();
			}

			if(!sale_newRate){
				if(!wrapper.find('.error-empty-sale-new-rate').length){
					wrapper.append(`<p class="notice notice-error error-empty-sale-new-rate" style="font-size: 12px;">Sale Rate is empty!</p>`);
				}
				inputSaleRate.css('border-color', 'red');
			}else{
				inputSaleRate.css('border-color', 'inherit');
				wrapper.find('.error-empty-sale-new-rate').remove();
			}
		}
	});
	// %%%%%%%%%%%%%%%%%%%%

	// %%%%%%%%%%%%%%%%%%%%
	// Add new fields group row for adding more products on form ADD NEW PURCHASE on PURCHASES page
	// %%%%%%%%%%%%%%%%%%%%
	$(document).on('click', '#addNewPurchaseFormRow', function () {
		let button = $(this),
			buttonDefaultText = button.data('default-btn-text'),
			formWrapper = button.closest('form#addPurchaseForm'),
			fieldsGroupHtml = formWrapper.find('#hidden_fields_group').html(),
			formFieldsContainer = formWrapper.find('#purchase_form_fields_container'),
			purchaseCart = formWrapper.find('.purchase-sidebar-list'),
			currentForm = formFieldsContainer.find('.purchase_form_fields_group.active'),
			select2Id = currentForm.find('td[data-select2-id]').data('select2-id'),
			productSelectbox = currentForm.find('[name="product_id"]'),
			productRendered = currentForm.find('.select2-selection__rendered'),
			productID = productSelectbox.val(),
			productName = productRendered.attr('title'),
			quantity = currentForm.find('[name="quantity"]'),
			payment = + currentForm.find('[name="payment"]').val(),
			needValidationSelectors = currentForm.find('.needValidation'),
			addStockBtn = formWrapper.find('input#add_stock');
		button.html(buttonDefaultText);
		addStockBtn.prop('disabled', false);
		
		if(productID && (quantity && quantity.val())){
			// Remove any previous error when flow is okay
			needValidationSelectors.css({
				border: 'inherit',
			});
			productRendered.css({
				border: 'inherit',
			});
			let cartItemNameString = `${productName} &times; ${quantity.val()} <br /> <small class="cartItemPayment" data-item-payment="${payment}"><i>Rs. ${formatNumber(payment)}</i></small>`;
			let cartItem = `<li data-select2-id="${select2Id}" class="purchase-sidebar-list-item" data-product_id="${productID}" data-title="${productName}">
								<div class="purchase-sidebar-list-item_wrapper">
									<span class="cartItemName"  title="${productName}">${cartItemNameString}</span>
									<button class="btnRemovePurchaseCartItem" type="button"  title="Remove product from cart">&times;</button>
								</div>
							</li>`;
			if(!purchaseCart.find(`li[data-select2-id="${select2Id}"]`).length){
				purchaseCart.append(cartItem);
			}else{
				purchaseCart.find(`li[data-select2-id="${select2Id}"]`).attr({
					'title':  productName,
					'data-title':  productName,
					'data-product_id':  productID,
				}).find('.cartItemName').html(cartItemNameString);
			}
			currentForm.removeClass('active').slideUp();

			formFieldsContainer.append(fieldsGroupHtml);
			formFieldsContainer.find(':last-child select.product_id').select2({
				width: '100%',
			});
		}else{
			if(!productRendered || !productID){
				productRendered.css({
					border: '2px solid red',
				});
			}else{
				productRendered.css({
					border: 'inherit',
				});
			}

			if(!quantity || !quantity.val()){
				quantity.css({
					border: '2px solid red',
				});
			}else{
				quantity.css({
					border: 'inherit',
				});
			}
		}
	});
	// %%%%%%%%%%%%%%%%%%%%

	function formatNumber(x) {
	    x = x.toString();
	    let lastThree = x.substring(x.length - 3);
	    let otherNumbers = x.substring(0, x.length - 3);
	    if (otherNumbers !== '') {
	        lastThree = ',' + lastThree;
	    }
	    return otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
	}


	// %%%%%%%%%%%%%%%%%%%%
	// Delete last added purchase row
	// %%%%%%%%%%%%%%%%%%%%
	$(document).on('click', '#removeLastPurchaseFormRow', function () {
		let button = $(this),
			formWrapper = button.closest('form#add_stock_form'),
			formFieldsContainer = formWrapper.find('#purchase_form_fields_container'),
			allFormRows = formFieldsContainer.children('.purchase_form_fields_group'),
			lastAddedFormRow = allFormRows.length > 1 ? allFormRows.last() : null;
		if(lastAddedFormRow){
			lastAddedFormRow.remove();
		}
	});
	// %%%%%%%%%%%%%%%%%%%%

	// %%%%%%%%%%%%%%%%%%%%
	// Toggle Add New Purchase Form
	// %%%%%%%%%%%%%%%%%%%%
	let isaddProductPage = $('.add-product-page');
	let isPurchasePage = $('.purchase-page');

	if (isaddProductPage.length || isPurchasePage.length) {
		$(document).on('click', '.add-new, .closeUserForm', function () { 
			let addNewPurchaseForm = $('.product-form-wrap');
			addNewPurchaseForm.slideToggle();
		});
	}
	// %%%%%%%%%%%%%%%%%%%%

	// %%%%%%%%%%%%%%%%%%%%
	// Get & Set product price/rate automatically on change of product in add stock/purchase form
	// %%%%%%%%%%%%%%%%%%%%
	
	$(document).on('change', 'form#addPurchaseForm select.product_id', function () {
		let $this = $(this),
			product_id = Number($this.val()),
			formWrapper = $this.closest('form#addPurchaseForm'),
			wrapper = $this.closest('table.anpProductInfoTable'),
			inputPurchaseRate = wrapper.find('input.purchase_rate'),
			inputSaleRate = wrapper.find('input.sale_rate'),
			inputVendor = wrapper.find('input.vendor'),
			inputQuantity = wrapper.find('input.quantity'),
			quantity = parseInt(inputQuantity.val()) || 1,
			inputPayment = wrapper.find('input.payment'),
			inputManufacturer = wrapper.find('input.manufacturer'),

			fieldsGroupHtml = formWrapper.find('#hidden_fields_group').html(),
			fieldsContainer = formWrapper.find('#purchase_form_fields_container'),
			existsInCart = $(`li.purchase-sidebar-list-item[data-product_id="${product_id}"]`).length;
		if(existsInCart){
			alert('This product already exists in cart.');	
			fieldsContainer.find('.active').remove();
			fieldsContainer.append(fieldsGroupHtml);
			fieldsContainer.find(':last-child select.product_id').select2({
				width: '100%',
			});
		}
		// Set selected product's id on product selectbox/dropdown
		$this.attr('data-product_id', product_id);

		// Item is new, so continue to fetch and manipulate it for current purchase
		$.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action: 'get_product_details',
				product_id: product_id,
			}
		})
		.done(function (response) {

			if (response.success) {
				let data = response.data,
					product = JSON.parse(data.product),
					purchase_rate = Number(product.product_purchase_price),
					sale_rate = Number(product.product_sale_price),
					vendor = product.product_vendor,
					payment = quantity * purchase_rate,
					manufacturerId = data.manufacturer.id;
					manufacturerName = data.manufacturer.name;
				inputPurchaseRate.val(purchase_rate).attr({
					'value': purchase_rate,
					'data-product_id': product_id,
				});
				inputSaleRate.val(sale_rate).attr({
					'value': sale_rate,
					'data-product_id': product_id,
				});
				inputQuantity.val(quantity).attr({
					'value': quantity,
					'data-product_id': product_id,
				});
				
				inputPayment.val(payment).attr({
					'value': payment,
					'data-product_id': product_id,
				});
				inputVendor.val(vendor).attr('value', vendor);
				inputManufacturer.val(manufacturerName).attr({
					'value': manufacturerName,
					'data-manufacturer_id': manufacturerId,
				});

				calculatePurchase();
			} else {
				console.log('Failed to get product information');
			}
		})
		.fail(function () {
			console.log("AJAX ERROR => get_product");
		});

	});
	// %%%%%%%%%%%%%%%%%%%%

	// Function to calculate the purchase
	function calculatePurchase(){
		let form = $('#addPurchaseForm'),
			totalCartAmountInput = form.find('#anpTotalCartAmount'),
			activeFieldsGroup = form.find('.active.purchase_form_fields_group'),
			itemsInCart = form.find('ol.purchase-sidebar-list').children(),
			currentProductPayment = activeFieldsGroup.find('input[name="payment"]').val() || 0,
			totalCartAmount = + currentProductPayment,
			cartItemsTotalAmount = 0;
			itemsInCart.each((i, li) => {
				totalCartAmount += + $(li).find('.cartItemPayment').data('item-payment');
			});
			totalCartAmountInput.val(totalCartAmount).attr('value', totalCartAmount).trigger('input');

	}

	// %%%%%%%%%%%%%%%%%%%%
	// Calculate payment as per selected product on quantity change on add purchase/stock form
	// %%%%%%%%%%%%%%%%%%%%
	
	$(document).on('input', '.purchase_product_quantity input.quantity', function () {
		let $this = $(this),
			wrapper = $this.closest('table.anpProductInfoTable'),
			paymentInput = wrapper.find('input.payment'),
			quantity = Number($this.val()) > 0 ? Number($this.val()) : 1,
			purchaseRate = Number(wrapper.find('.purchase_rate').val()),
			totalPayment = Math.floor(quantity * purchaseRate);
		// Set Totaly payment
		paymentInput.val(totalPayment).attr('value', totalPayment).trigger('input');

		calculatePurchase();

	});
	// %%%%%%%%%%%%%%%%%%%%

	$(document).on('click', 'button.notice-dismiss', function(){
		$(this).closest('.notice').remove();
	});
	// %%%%%%%%%%%%%%%%%%%%

	// %%%%%%%%%%%%%%%%%%%%
	// Edit the product when item clicked from Cart Items List on purchase sidebar
	// %%%%%%%%%%%%%%%%%%%%
	$(document).on('click', 'li.purchase-sidebar-list-item .cartItemName', function(){
		let $this = $(this).closest('li'),
			select2Id = $this.data('select2-id'),
			itemPayment = + $this.find('.cartItemPayment').data('item-payment'),
			purchaseCart = $this.closest('.purchase-sidebar-list'),
			wrapper = $this.closest('#addPurchaseForm'),
			totalPaymentInput = wrapper.find('#anpTotalCartAmount'),
			totalPaymentOld = totalPaymentInput.val(),
			fieldsContainer = wrapper.find('#purchase_form_fields_container'),
			activeFieldsGroup = fieldsContainer.children('.active.purchase_form_fields_group'),
			activeFieldsGroup_select2Id = activeFieldsGroup.find('td[data-select2-id]').data('select2-id'),
			targetForm = fieldsContainer.find(`td[data-select2-id='${select2Id}']`).closest('.purchase_form_fields_group'),
			addMoreBtn = wrapper.find('#addNewPurchaseFormRow'),
			addMoreBtnOriginalText = addMoreBtn.text(),
			addStockBtn = wrapper.find('#add_stock'),
			totalPaymentNew = totalPaymentOld - itemPayment;
			totalPaymentInput.val(totalPaymentNew).attr('value', totalPaymentNew);
		if(activeFieldsGroup_select2Id !== undefined){
			if(activeFieldsGroup_select2Id === select2Id){
				return;
			}else{
				let existsInCart = purchaseCart.find(`li[data-select2-id="${activeFieldsGroup_select2Id}"]`).length;
				if(existsInCart){
					activeFieldsGroup.removeClass('active').slideUp();
					targetForm.addClass('active').slideDown();
				}
			}
		}else{
			activeFieldsGroup.remove();
			targetForm.addClass('active').slideDown();
		}
		addMoreBtn.text('Update Info');
		addStockBtn.prop('disabled', true);
	});
	// %%%%%%%%%%%%%%%%%%%%

	// Toggle next element - common function
	$(document).on('click', '.toggleNextSibling', toggleNextSibling);
	

	// Toggle update manufacturer form
	$(document).on('click', '.btnUpdateManufacturer', function(){
		$(this).closest('tr').next().slideToggle();
	});



	// %%%%%%%%%%%%%%%%%%%%
	// Delete the clicked product form and cart item from Cart Items List on purchase sidebar
	// %%%%%%%%%%%%%%%%%%%%
	$(document).on('click', 'li.purchase-sidebar-list-item button.btnRemovePurchaseCartItem', function() {
		if(confirm('Are you sure! you want to remove this product from cart?')){
			let $this = $(this),
				li = $this.closest('li'),
				select2Id = li.data('select2-id'),
				purchaseCart = li.closest('.purchase-sidebar-list'),
				wrapper = li.closest('#addPurchaseForm'),
				fieldsGroupHtml = wrapper.find('#hidden_fields_group').html(),
				fieldsContainer = wrapper.find('#purchase_form_fields_container'),
				activeFieldsGroup = fieldsContainer.children('.active.purchase_form_fields_group'),
				activeFieldsGroup_select2Id = activeFieldsGroup.find('td[data-select2-id]').data('select2-id'),
				targetForm = fieldsContainer.find(`td[data-select2-id="${select2Id}"]`).closest('.purchase_form_fields_group');
				
			if(activeFieldsGroup_select2Id !== undefined){
				if(activeFieldsGroup_select2Id === select2Id){
					targetForm.remove();
					li.remove();
					fieldsContainer.append(fieldsGroupHtml);
					fieldsContainer.find(':last-child select.product_id').select2({
						width: '100%',
					});
				}else{
					targetForm.remove();
					li.remove();
				}
			}else{
				// activeFieldsGroup.remove();
				// targetForm.addClass('active').slideDown();
				targetForm.remove();
				li.remove();
			}
			calculatePurchase();
		}
	});
	// %%%%%%%%%%%%%%%%%%%%

	// Fading effect on sales page in the section `Sales Calculator`
	setInterval(() => {
		let opacity = $(`.salesCalculatorBanner`).css('opacity');
		if(opacity == 1){
			$(`.salesCalculatorBanner`).css('opacity', 0.2);
		}else{
			$(`.salesCalculatorBanner`).css('opacity', 1);
		}
	}, 1 * 1000);


	// Customer form
	$(document).on('change', 'select#customer_type', function () {
		let $this = $(this),
			customerType = $this.val(),
			customerRegisterForm = $('#customer_register_area');
		if(customerType == 'credit_customer'){
			customerRegisterForm.slideDown();
		}else{
			customerRegisterForm.slideUp();
		}
	});

	// Pending payments history
	$('table.pending_payments_table tr').on('click', function(e){
		if ($(e.target).closest('button').length) return; // skip when clicking buttons
		var row = $(this);
		var next = row.next('.payments-row');
		if (next.length) {
		var dueId = next.find('.payments').data('due');
		if (next.is(':visible')) {
			next.slideUp();
		} else {
			next.slideDown();

			// Load payments only once
			if (!next.data('loaded')) {
			$.get(ajaxurl, { action: 'fbm_get_due_payments', due_id: dueId }, function(res){
				if (res.success) {
				next.find('.payments').html(res.data);
				next.data('loaded', true);
				} else {
				next.find('.payments').html('<em>Error loading payments.</em>');
				}
			});
			}
		}
		}
	});

	// %%%%%%%%%%%%%%%%%%%%
	// SELECT2 init on add new product form on field(selectbox) Manufacturer
	// %%%%%%%%%%%%%%%%%%%%
	let productManufacturerDropdown = $('form#product-form select#product_manufacturer');
	if(productManufacturerDropdown.length){
		productManufacturerDropdown.select2({
			width: '100%',
		});
	}
	// %%%%%%%%%%%%%%%%%%%%


	

});// .ready() closed

