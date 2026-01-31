jQuery(document).ready($ => {

	// Print the invoice on invoice details page
	$(document).on('click', '#purchaseInvoicePrintBtn', function (e) {
		e.preventDefault();
		// Store old body markup/html to restore later as per need
		let oldBody = $('body').html();

		
		let invoiceNo = $('#inv_invoice_no').text();
		let totalPayment = $('#inv_total_payment').text();
		let paymentStatus = $('#inv_payment_status').text();
		let paymentMethod = $('#inv_payment_method').text();

		// let paidAmount = +$(`#inv_paid_amount`).text().trim(),
		// 	duePayment = +$(`#inv_due_payment`).text().trim();


		const invoiceTable = $('#inv_products'),
			invoiceTbody = invoiceTable.find('tbody'),
			invoiceTableRows = invoiceTbody.children('tr'),
			purchaseInvoiceRowsValues = [];


		invoiceTableRows.each((i, row) => {
			const invoiceItem = {
				product: $(row).find('.inv_product_name').text().trim(),
				manufacturer: $(row).find('.inv_product_manufacturer').text().trim(),
				vendor: $(row).find('.inv_product_vendor').text().trim(),
				rate: $(row).find('.inv_product_unit_price').text().trim(),
				quantity: $(row).find('.inv_product_quantity').text().trim(),
				amount: $(row).find('.inv_product_amount').text().trim()
			};
			purchaseInvoiceRowsValues.push(invoiceItem);
		});

		let totalItems = purchaseInvoiceRowsValues.length;	
		let d = new Date(),
			currentDate = d.getDate();
		let currentMonth = d.getMonth() + 1;
		let currentYear = d.getFullYear();
		let billDate = "Date : " + currentDate + "/" + currentMonth + "/" + currentYear;

		let currentHours = d.getHours();
		let currentMinutes = d.getMinutes().toString().padStart(2, '0');
		let currentSeconds = d.getSeconds().toString().padStart(2, '0');
		const ampm = currentHours >= 12 ? 'PM' : 'AM';
		currentHours = currentHours % 12 || 12; // Convert to 12-hour format, making 0 to 12
		let billTime = "Time : " + currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + ampm;

		let purchaseInvoiceData = `<div id="print-area" style="width=80mm;">
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
									<th style="padding: 0 20px 0 0; font-weight: 900; margin: 0;"><strong>Invoice Type</strong></th>
									<td style="padding: 0 20px 0 0; font-weight: 600;">Purchase Invoice<td>
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
									<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Manufacturer</strong></th>
									<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Vendor</strong></th>
									<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Rate</strong></th>
									<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Qty</strong></th>
									<th style="padding: 0 20px 5px 0;font-weight: 900;"><strong>Amount</strong></th>
								</tr>
							</thead>
							<tbody>`;
		$.each(purchaseInvoiceRowsValues, (i, item) => {
			purchaseInvoiceData += `<tr>
								<td style="font-weight: 600; min-width:120px;">${item.product}</td>
								<td style="font-weight: 600;">${item.manufacturer}</td>
								<td style="font-weight: 600;">${item.vendor}</td>
								<td style="font-weight: 600;">${item.rate}</td>
								<td style="font-weight: 600;">${item.quantity}</td>
								<td style="font-weight: 600;">${item.amount}</td>
							</tr>`;
		});
		purchaseInvoiceData += `</tbody>
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
									<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Total Payment</strong></th>
									<td style="padding: 0 20px 0 0; font-weight: 600;">${totalPayment}<td>
								</tr>
								<tr>
									<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Payment Status</strong></th>
									<td style="padding: 0 20px 0 0; font-weight: 600;">${paymentStatus}<td>
								</tr>`;
		if(paymentStatus.toLowerCase() != 'unpaid'){
			purchaseInvoiceData += `<tr>
										<th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Payment Method</strong></th>
										<td style="padding: 0 20px 0 0; font-weight: 600; margin: 0;">${paymentMethod}<td>
									</tr>`;
		}

	purchaseInvoiceData += `</tbody>
						</table>
					</div>
			</div>`;
		$('body').html(purchaseInvoiceData);
		// Trigger the print
		window.print();
		// Restore the original content
		$('body').html(oldBody);
		location.reload(true);

	});

}); // .ready() closed