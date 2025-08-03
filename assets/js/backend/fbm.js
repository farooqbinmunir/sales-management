jQuery(document).ready(function ($) {
	var ajaxUrl = fbm_ajax.url,
		nonce = fbm_ajax.nonce,
		path = fbm_ajax.path;

	var notice = $('#fbm_notice');
	// Custom function for scrolling to specific element, first offset param is required, second is option with default value of 0
	const scrollTo = (offsetSelector, offsetToMinus = 0) => {
		$('html, body').animate({
			scrollTop: Math.floor($(offsetSelector).offset().top - offsetToMinus)
		}, 'slow', 'linear');
	};


	let printIvoiceBtn = $('#printIvoiceBtn');

	printIvoiceBtn.click(function (e) {

		e.preventDefault();
		const allQntField = document.querySelectorAll('.quantity input');
		let allFilled = true;
		allQntField.forEach((v, i, a) => {
			if (!v.value.trim()) {
				allFilled = false;
				v.classList.add('empty_field');
				alert("Sorry please fill the empty field.");
				return;
			}
		});
		if (allFilled) {
			let customer_nameElement = $('#customer-name'),
				cname = customer_nameElement.val().trim(),
				grossTable = $('#grossTotalTable');

			if (cname != '') {
				// Remove error border if field is not empty
				customer_nameElement.css('border-color', 'green');

				// Get all required values
				let invoice_no = Number($('#sale_invoice').val()),
					cemail = `${cname.replace(' ', '').toLowerCase()}${invoice_no}@gmail.com`,
					grossTotalVal = Number(grossTable.find('.gross-total').text().trim()),
					discount = Number(grossTable.find('#discount').val()),
					grossNetTotalVal = Number(grossTable.find('.net-total').text().trim()),
					saleType = grossTable.find('#salesType').val(),
					paymentMethod = grossTable.find('#paymentMethod').val(),
					quantity = 0;

				const payload = {
					invoice_data: {},
					customer_data: {},
					sale_data: {},
				};

				let invoiceTable = $('#invoiceTable'),
					selectedItems = invoiceTable.find('.selected_items_container tr');
				let invoice_data = {};
				selectedItems.each((i, v) => {
					let tr = $(v);
					let prod_id = tr.data('id'),
						prod_name = tr.find('.item-name').text().trim(),
						prod_quantity = Number(tr.find('.quantity input').val()),
						prod_total_amount = Number(tr.find('.items-price').text().trim()),
						prod_type = tr.find('.item-type select').val().trim();

					invoice_data[i] = {
						prod_id: prod_id,
						prod_name: prod_name,
						prod_quantity: prod_quantity,
						prod_total_amount: prod_total_amount,
						prod_type: prod_type
					};
					quantity += prod_quantity;
				});
				// Invoice data
				payload.invoice_data = {
					invoice_no: invoice_no,
					data: JSON.stringify(invoice_data),
				};

				// Customer data
				payload.customer_data = {
					customer_name: cname,
					customer_email: cemail,
				};

				// Sale data
				payload.sale_data = {
					quantity: quantity,
					gross_total: grossTotalVal,
					discount: discount,
					net_total: grossNetTotalVal,
					sale_type: saleType,
					payment_method: paymentMethod,
					payment_status: 'paid',
				};

				// Payload is ready, go ahead
				$.ajax({
					url: ajaxUrl,
					type: 'POST',
					data: {
						action: 'save_sale',
						payload: payload,
					}
				})
					.done(function (res) {
						if (res.success) {
							notice.removeClass('notice-error').addClass('notice-success').text('Sale has been stored successfully.').slideDown();

							// Sale has been saved successfully in the database, now Print the Invoice
							let customerName = $('#customer-name');
							if (customerName.val() != '') {
								let oldBody = $('body').html();
								let customerNameValue = customerName.val().trim();
								let invoiceNo = $('#sale_invoice').val().trim();
								const grossTotalTable = $('#grossTotalTable');
								let totalTbody = grossTotalTable.find('tbody'),
									grossPrice = totalTbody.find('.gross-total').text().trim(),
									discount = totalTbody.find('#discount').val().trim(),
									netPrice = totalTbody.find('.net-total').text().trim(),
									salesType = totalTbody.find('#salesType').val().trim().charAt(0).toUpperCase() + $('#salesType').val().slice(1),
									paymentMethod = $('#paymentMethod').val().trim().charAt(0).toUpperCase() + $('#paymentMethod').val().slice(1);


								const invoiceTable = $('#invoiceTable');
								let invoiceTbody = invoiceTable.find('tbody');
								let items = invoiceTbody.find('.item-name');
								let itemsNames = [];
								for (let x of items) {
									let itemsName = $(x).text().trim();
									itemsNames.push(itemsName);

								}

								const salePrice = invoiceTbody.find('.sale-price');
								let salePriceArry = [];
								for (let x of salePrice) {
									let allSalePrice = $(x).text().trim();
									salePriceArry.push(allSalePrice);
								}

								const quantity = invoiceTbody.find('.quantity input');
								let quantities = [];
								for (let x of quantity) {
									let itemQuantity = $(x).val().trim();
									quantities.push(itemQuantity);
								}

								const prices = invoiceTbody.find('.items-price');
								let itemPrices = [];
								for (let x of prices) {
									let itemPrice = $(x).text().trim();
									itemPrices.push(itemPrice);

								}

								const productType = invoiceTbody.find('select');
								let productTypes = [];
								for (let x of productType) {
									let itemType = $(x).val().trim().charAt(0).toUpperCase() + $(x).val().slice(1);
									productTypes.push(itemType);

								}

								let totalItems = items.length;
								let d = new Date(),
									currentDate = d.getDate();
								let currentMonth = d.getMonth() + 1;
								let currentYear = d.getFullYear();
								let billDate = "Date : " + currentDate + "/" + currentMonth + "/" + currentYear;

								let currentHours = d.getHours();
								let currentMinutes = d.getMinutes().toString().padStart(2, '0');
								let currentSeconds = d.getSeconds().toString().padStart(2, '0');
								const ampm = currentHours >= 12 ? 'PM' : 'AM';
								currentHours = currentHours % 12 || 12; // Convert to 12-hour format, making 0 into 12
								let billTime = "Time : " + currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + ampm;
								let sales_Type = document.querySelector("#salesType");

								let invoiceData = `<div id="print-area" style="width=80mm;">
											<div style="padding-bottom: 10px; display: flex; gap:170px;">
												<h3 style="font-size: 14px; font-weight: 600;"><em>${billDate}</em></h3>
												<h3 style="font-size: 14px; font-weight: 600;"><em>${billTime}</em></h3>
											</div>
											<div style="padding-bottom: 10px;padding-top: 10px;border-bottom: 1px solid black;">
												<h1 style="font-size: 20px; text-transform: uppercase;"><strong>Shahzad shopping Center</strong></h1>
												<h3 style="font-size: 16px;"><strong>Address: </strong><em>Nadirabad, Bedian road, Lahore</em></h3>
												<h3 style="font-size: 16px; margin-bottom: 0;"><strong>Phone: </strong><em style="font-weight: 600;">+92 305 4144952</em></h3>
											</div>
											<div style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">
												<table style="table-layout: fixed;">
													<tbody>
														<tr>
															<th style="padding: 0 20px 0 0; font-weight: 900; margin: 0;"><strong>Customer Name</strong></th>
															<td style="padding: 0 20px 0 0; font-weight: 600;">${customerNameValue}<td>
														</tr>
														<tr>
															<th style="padding: 0 20px 0 0; font-weight: 900;"><strong>Invoice No</strong></th>
															<td style="padding: 0 20px 0 0; font-weight: 600; margin: 0;">${invoiceNo}<td>
														</tr>
													</tbody>
												</table>
											</div>
											<div style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">
												<table style="table-layout: fixed;">
													<thead>
														<tr>
															<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Sr</strong></th>
															<th style="padding: 0 20px 5px 0;font-weight: 900; margin: 0; width:100px;"><strong>Items</strong></th>
															<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Unit Price</strong></th>
															<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Qty</strong></th>
															<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Amount</strong></th>
														</tr>
													</thead>
													<tbody>`;
								let srNm = 1;
								for (let i = 0; i < totalItems; i++) {
									invoiceData += `<tr>
																				<td style="font-weight: 600;">${srNm++}</td>
																				<td style="font-weight: 600; width:120px;">${itemsNames[i]}</td>
																				<td style="font-weight: 600;">${salePriceArry[i]}</td>
																				<td style="font-weight: 600;">${quantities[i]}</td>
																				<td style="font-weight: 600;">${itemPrices[i]}</td>
																			</tr>`;
								}
								invoiceData += `</tbody>
												</table>
											</div>
											<div style="padding-top: 10px;padding-bottom: 10px;">
												<table style="table-layout: fixed;">
													<tbody>
														<tr>
															<th style="padding: 0 110px 0 0; font-weight: 900; margin: 0;"><strong>Total Items:</strong></th>
															<td style="padding: 0 20px 0 0; font-weight: 600;">${totalItems}<td>
														</tr>
														<tr>
															<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Gross Total:</strong></th>
															<td style="padding: 0 20px 0 0; font-weight: 600;">${grossPrice}<td>
														</tr>`;
								if (discount > 0) {
									invoiceData += `<tr>
																<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Discount</strong></th>
																<td style="padding: 0 20px 0 0; font-weight: 600;">${discount}<td>
															</tr>`;
								}
								invoiceData += `<tr>
															<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Net Price:</strong></th>
															<td style="padding: 0 20px 0 0; font-weight: 600;">${netPrice}<td>
														</tr>
														<tr>
															<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Sales Type</strong></th>
															<td style="padding: 0 20px 0 0; font-weight: 600;">${salesType}<td>
														</tr>`;
									if (!(sales_Type.value == "credit-sales")) {
									invoiceData += `
														<tr>
															<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Payment Method</strong></th>
															<td style="padding: 0 20px 0 0; font-weight: 600; margin: 0;">${paymentMethod}<td>
														</tr>`
													};
									invoiceData += `</tbody>
												</table>
											</div>
									</div>`;
								$('body').html(invoiceData);
								// Trigger the print
								window.print();
								// Restore the original content
								$('body').html(oldBody);
								location.reload(true);
							} else {
								customerName.css('border-color', 'red');
							}

							// setTimeout(() => location.reload(), 1500);
						} else {
							notice.removeClass('notice-success').addClass('notice-error').text('Failed to add sale entry.').slideDown();
						}
					})
					.fail(function () {
						console.log("error");
					});
			} else {
				customer_nameElement.css('border-color', 'red');
				scrollTo('#fbm_notice', 100);
			}

		}

	});

	// Delete Single Item
	function remove(delBtnCls, confirmMessage, table_name, idColName) {
		$(document).on('click', `.${delBtnCls}`, function () {
			let id = $(this).data('id');
			let delete_product_confirmation_message = confirmMessage ? confirmMessage : "Are you sure to delete this?";
			if (confirm(delete_product_confirmation_message)) {
				$.ajax({
					url: ajaxUrl,
					type: 'POST',
					data: {
						action: 'sms_delete',
						id: id,
						table_name: table_name,
						id_col_name: idColName,
					},
					success: resp => {
						if (resp.success) {
							notice.removeClass('notice-error').addClass('notice-success').text('Product deleted successfully.').slideDown();
							scrollTo('#fbm_notice', 100);
							setTimeout(() => location.reload(), 1500);
						} else {
							notice.removeClass('notice-success').addClass('notice-error').text('Failed to delete product').slideDown();
							scrollTo('#fbm_notice', 100);
						}
					}
				});
			}
		});
	}
	// remove('delete_product_btn', 'Are you sure to delete this product?', 'sms_products', 'product_id');

	// Edit Single Item
	$(document).on('click', '.edit_product_btn, .edit_purchase_btn', quick_edit);
	function quick_edit() {
		let $this = $(this);
		let requiredEditForm = $this.closest('tr').next().find('.quick_edit_form');
		requiredEditForm.slideToggle();
	}
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
			product_location = currentProduct.find('#product_location').val(),
			product_min_quantity = currentProduct.find('#add_min_quantity').val();
			
		const payload = {
			product_name: product_name,
			product_purchase_price: product_purchase_price,
			product_sale_price: product_sale_price,
			product_vendor: product_vendor,
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


	// Processing returns
	jQuery('#returnForm').on('submit', function (e) {
		e.preventDefault();
		let productIdElement = jQuery('select[name="product_id"]'),
			quantityElement = jQuery('input[name="quantity"]'),
			returnReasonElement = jQuery('textarea[name="return_reason"]');
			returnInvoiceElement = jQuery('input#add_invoice_no');


		let productId = Number(productIdElement.val()),
			quantity = Number(quantityElement.val()),
			reason = returnReasonElement.val().trim(),
			invoice_no = returnInvoiceElement.val().trim();
			console.log(invoice_no.length, ' invoice_no');
		// Error handling
		if (productId != null && productId != 0 && quantity != '' && quantity > 0 && invoice_no.length >= 10) {
			$('#product_id, #return_quantity').each((i, v) => {
				$(v).css('border-color', 'green');
			});
			var data = {
				action: 'handle_product_return',
				product_id: productId,
				quantity: quantity,
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

			if (quantity == '' || quantity == 0) {
				quantityElement.css('border', '2px solid red');
			} else {
				quantityElement.css('border', '2px solid green');
			}
		}

	});

	// Print the invoice on invoice details page
	let invoicePrintBtn = $('#invoicePrintBtn');

	invoicePrintBtn.on('click', function (e) {

		e.preventDefault();
		let oldBody = $('body').html();
		let customerName = $('#inv_customer_name').text().toUpperCase();
		let invoiceNo = $('#inv_invoice_no').text();
		let grossPrice = $('#inv_gross_total').text();
		let discount = $('#inv_discount').text();
		let netPrice = $('#inv_net_total').text();
		let salesType = $('#inv_sale_type').text();
		let paymentMethod = $('#inv_payment_method').text();


		const invoiceTable = $('#inv_products');
		let invoiceTbody = invoiceTable.find('tbody');
		let items = invoiceTbody.find('.inv_product_name');
		let itemsNames = [];
		for (let x of items) {
			let itemsName = $(x).text().trim();
			itemsNames.push(itemsName);

		}

		const salePrice = invoiceTbody.find('.inv_product_unit_price');
		let salePriceArry = [];
		for (let x of salePrice) {
			let allSalePrice = $(x).text().trim();
			salePriceArry.push(allSalePrice);
		}

		const quantity = invoiceTbody.find('.inv_product_quantity');
		let quantities = [];
		for (let x of quantity) {
			let itemQuantity = $(x).text().trim();
			quantities.push(itemQuantity);
		}

		const prices = invoiceTbody.find('.inv_product_amount');
		let itemPrices = [];
		for (let x of prices) {
			let itemPrice = $(x).text().trim();
			itemPrices.push(itemPrice);

		}

		let totalItems = items.length;
		let d = new Date(),
			currentDate = d.getDate();
		let currentMonth = d.getMonth() + 1;
		let currentYear = d.getFullYear();
		let billDate = "Date : " + currentDate + "/" + currentMonth + "/" + currentYear;

		let currentHours = d.getHours();
		let currentMinutes = d.getMinutes().toString().padStart(2, '0');
		let currentSeconds = d.getSeconds().toString().padStart(2, '0');
		const ampm = currentHours >= 12 ? 'PM' : 'AM';
		currentHours = currentHours % 12 || 12; // Convert to 12-hour format, making 0 into 12
		let billTime = "Time : " + currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + ampm;

		let invoiceData = `<div id="print-area" style="width=80mm;">
					<div style="padding-bottom: 10px; display: flex; gap:170px;">
						<h3 style="font-size: 14px; font-weight: 600;"><em>${billDate}</em></h3>
						<h3 style="font-size: 14px; font-weight: 600;"><em>${billTime}</em></h3>
					</div>
					<div style="padding-bottom: 10px;padding-top: 10px;border-bottom: 1px solid black;">
						<h1 style="font-size: 20px; text-transform: uppercase;"><strong>Shahzad shopping Center</strong></h1>
						<h3 style="font-size: 16px;"><strong>Address: </strong><em>Nadirabad, Bedian road, Lahore</em></h3>
						<h3 style="font-size: 16px; margin-bottom: 0;"><strong>Phone: </strong><em style="font-weight: 600;">+92 305 4144952</em></h3>
					</div>
					<div style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">
						<table style="table-layout: fixed;">
							<tbody>
								<tr>
									<th style="padding: 0 20px 0 0; font-weight: 900; margin: 0;"><strong>Customer Name</strong></th>
									<td style="padding: 0 20px 0 0; font-weight: 600;">${customerName}<td>
								</tr>
								<tr>
									<th style="padding: 0 20px 0 0; font-weight: 900;"><strong>Invoice No</strong></th>
									<td style="padding: 0 20px 0 0; font-weight: 600; margin: 0;">${invoiceNo}<td>
								</tr>
							</tbody>
						</table>
					</div>
					<div style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">
						<table style="table-layout: fixed;">
							<thead>
								<tr>
									<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Sr</strong></th>
									<th style="padding: 0 20px 5px 0;font-weight: 900; margin: 0; width:100px;"><strong>Items</strong></th>
									<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Unit Price</strong></th>
									<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Qty</strong></th>
									<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Amount</strong></th>
								</tr>
							</thead>
							<tbody>`;
		let srNm = 1;
		for (let i = 0; i < totalItems; i++) {
			invoiceData += `<tr>
														<td style="font-weight: 600;">${srNm++}</td>
														<td style="font-weight: 600; width:120px;">${itemsNames[i]}</td>
														<td style="font-weight: 600;">${salePriceArry[i]}</td>
														<td style="font-weight: 600;">${quantities[i]}</td>
														<td style="font-weight: 600;">${itemPrices[i]}</td>
													</tr>`;
		}
		invoiceData += `</tbody>
						</table>
					</div>
					<div style="padding-top: 10px;padding-bottom: 10px;">
						<table style="table-layout: fixed;">
							<tbody>
								<tr>
									<th style="padding: 0 110px 0 0; font-weight: 900; margin: 0;"><strong>Total Items:</strong></th>
									<td style="padding: 0 20px 0 0; font-weight: 600;">${totalItems}<td>
								</tr>
								<tr>
									<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Gross Total:</strong></th>
									<td style="padding: 0 20px 0 0; font-weight: 600;">${grossPrice}<td>
								</tr>`;
								if (discount > 0) {
									invoiceData += `<tr>
														<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Discount</strong></th>
														<td style="padding: 0 20px 0 0; font-weight: 600;">${discount}<td>
													</tr>`;
								}
								invoiceData += `<tr>
													<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Net Price:</strong></th>
													<td style="padding: 0 20px 0 0; font-weight: 600;">${netPrice}<td>
												</tr>
												<tr>
													<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Sales Type</strong></th>
													<td style="padding: 0 20px 0 0; font-weight: 600;">${salesType}<td>
												</tr>`;
								invoiceData += `<tr>
									<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Payment Method</strong></th>
									<td style="padding: 0 20px 0 0; font-weight: 600; margin: 0;">${paymentMethod}<td>
								</tr>`;
			invoiceData += `</tbody>
						</table>
					</div>
			</div>`;
		$('body').html(invoiceData);
		// Trigger the print
		window.print();
		// Restore the original content
		$('body').html(oldBody);
		location.reload(true);

	});

	// Returns Page, add invoice no field
	let addInvoiceInput = $('input#add_invoice_no');
	if(addInvoiceInput){
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

	$('select#product_id').on('change', function(){
		let product_id = + $(this).val();
		let quantity = + $(this).find(`option[value="${product_id}"]`).attr('data-quantity');
		$('input#return_quantity').attr({
			max: quantity,
			placeholder: `1-${quantity}`
		});
	});

	// %%%%%%%%%%%%%%%%%%%%
	// SELECT2 init on add new purchase form on field(selectbox) SELECT PRODUCT
	// %%%%%%%%%%%%%%%%%%%%
	let purchaseProductSelectbox = jQuery('#add_stock_form .purchase_product_select select#product_id');
	purchaseProductSelectbox.select2({
		width: '100%',
	});
	// %%%%%%%%%%%%%%%%%%%%

	// %%%%%%%%%%%%%%%%%%%%
	// Updating Product rate on form ADD NEW PURCHASE on PURCHASES page
	// %%%%%%%%%%%%%%%%%%%%
	// Update toggler
	let btnUpdateRateToggler = jQuery('#btnUpdateRateToggler');
	btnUpdateRateToggler.on('click', function(){
		let $this = $(this),
			wrapper = $this.closest('.purchase_product_rate_wrapper'),
			input = wrapper.find('input'),
			updateRateBtn = wrapper.find('#btnUpdateRate');
		input.prop('readonly', false).css('border-color', 'inherit');
		updateRateBtn.slideDown();
		wrapper.addClass('rate_modifying').removeClass('rate_updated').find('.notice').remove();
	});

	// Update product rate
	let btnUpdateRate = $('#btnUpdateRate');
	btnUpdateRate.on('click', function(){
		let button = $(this),
			buttonText = button.text(),
			wrapper = button.closest('.purchase_product_rate_wrapper'),
			input = wrapper.find('input'),
			product_id = input.data('product_id'),
			newRate = +input.val();
		if(product_id && newRate){
			wrapper.find('.notice').remove();
			button.text('Updating...');
			wrapper.addClass('rate_updating');
			
			$.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					action: 'update_product_rate',
					product_id: product_id,
					new_rate: newRate,
				},
				success: res => {
					console.log(res , ' res');
					if(res.success){
						wrapper.find('.error-update-failed').remove();
						input.prop('readonly', true).attr('value', newRate);
						button.text(buttonText).slideUp();
						wrapper.removeClass('rate_updating rate_modifying').addClass('rate_updated');
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

			if(!newRate){
				if(!wrapper.find('.error-empty-new-rate').length){
					wrapper.append(`<p class="notice notice-error error-empty-new-rate" style="font-size: 12px;">Rate field is empty!</p>`);
				}
				input.css('border-color', 'red');
			}else{
				input.css('border-color', 'inherit');
				wrapper.find('.error-empty-new-rate').remove();
			}
		}
	});
	// %%%%%%%%%%%%%%%%%%%%

});