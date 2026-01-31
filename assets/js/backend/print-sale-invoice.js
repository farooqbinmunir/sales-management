jQuery(document).ready($ => {

	// Print the invoice on invoice details page
	$(document).on('click', '#invoicePrintBtn', printSaleInvoice);
	function printSaleInvoice(e) {
		e.preventDefault();
		let oldBody = $('body').html();
		let customerName = $('#inv_customer_name').text().toUpperCase();
		let invoiceNo = $('#inv_invoice_no').text();
		let grossPrice = $('#inv_gross_total').text();
		let discount = $('#inv_discount').text();
		let netPrice = $('#inv_net_total').text();
		let salesType = $('#inv_sale_type').text();
		let paymentMethod = $('#inv_payment_method').text();

		let paidAmount = +$(`#inv_paid_amount`).text().trim(),
			duePayment = +$(`#inv_due_payment`).text().trim();



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

		// Get Pending/Due payments history
		let paidPaymentRows = $('table.due_payment_history_table tbody tr');
		let paidPayments = [];
		paidPaymentRows.each((i, row) => {
			let amount = $(row).find('.dph_amount').text(),
				date = $(row).find('.dph_date').text(),
				note = $(row).find('.dph_note').text();
			paidPayments.push([amount, date, note]);
		});

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
								<td style="font-weight: 600; min-width:120px;">${itemsNames[i]}</td>
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
												<tr>`;
								if(paidPayments.length){
									invoiceData += `<tr>
														<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Paid Payments:</strong></th>
														<td style="padding: 0 20px 0 0; font-weight: 600;">`;
											invoiceData += `<table style="border: 1px solid black;">
			                                                    <tbody>`;
	                                        	$.each(paidPayments, (i, payment, a) => {
	                                        		invoiceData += `<tr>
		                                                                <td class="dph_amount" style="border: 1px solid black; padding: 10px;">${payment[0]}</td>
		                                                                <td class="dph_date" style="border: 1px solid black; padding: 10px;">${payment[1]}</td>
		                                                                <td class="dph_note" style="border: 1px solid black; padding: 10px;">${payment[2]}</td>
		                                                            </tr>`;
	                                        	});
			                                    invoiceData += `</tbody>
			                                                </table>`;
										invoiceData += `<td>
													</tr>
													<tr>
														<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Remaining Amount</strong></th>
														<td style="padding: 0 20px 0 0; font-weight: 600;">${duePayment}<td>
													</tr>
													<tr>
														<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Account Status</strong></th>
														<td style="padding: 0 20px 0 0; font-weight: 600;">${duePayment == 0 ? 'Closed' : 'Open'}<td>
													</tr>`;
								}

								invoiceData += `<tr>
													<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Sales Type:</strong></th>
													<td style="padding: 0 20px 0 0; font-weight: 600;">${salesType}<td>
												</tr>`;
								if(salesType != 'Credit Sale'){
								invoiceData += `<tr>
													<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Payment Method</strong></th>
													<td style="padding: 0 20px 0 0; font-weight: 600; margin: 0;">${paymentMethod}<td>
												</tr>`;
								}
								
			invoiceData += `</tbody>
						</table>
					</div>
			</div>`;
		$('body').html(invoiceData);

		// Trigger the print
		window.print();

		// Restore the original content
		$('body').html(oldBody);

		// Reoload to reflect updates/changes
		location.reload(true);
	}

}); // .ready() closed