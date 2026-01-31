jQuery(document).ready($ => {

    // Global variable
    window.ajaxUrl = fbm_ajax.url;
    window.nonce = fbm_ajax.nonce;
    window.path = fbm_ajax.path;
    window.currentUser = fbm_ajax.current_user.toUpperCase();
    
    window.notice = $('#fbm_notice');

    // Scroll to function
    window.scrollTo = (offsetSelector, offsetToMinus = 0) => {
        $('html, body').animate({
            scrollTop: Math.floor($(offsetSelector).offset().top - offsetToMinus)
        }, 'slow', 'linear');
    };

	// Function for setting up search based on input/search item
	window.setupSearch = function (event, inputSelector, tableBodySelector, rowSelector, searchColumnSelector) {
		$(document).on(event, inputSelector, function () {
			let searchTerm = $(this).val().trim().toLowerCase();
			const allRows = $(tableBodySelector).find(rowSelector);
			if (allRows.length) {
				allRows.each((i, row) => {
					let cellValue = $(row).find(searchColumnSelector).text().trim().toLowerCase();
					if (!$(row).hasClass('edit_form')) {
						if (cellValue.includes(searchTerm)) {
							$(row).removeClass('not_found_in_search').addClass('found_in_search');
						} else {
							$(row).removeClass('found_in_search').addClass('not_found_in_search');
						}
					}
				});
			}
		});
	};

    // Filter sales functions
    window.activeSaleType = "";
    window.activeShift = "";
    window.filterSales = function (filterTerm, filterType = 'saleType') {

        // Update the active filters
        if (filterType === 'saleType') {
            window.activeSaleType = filterTerm;
        } else if (filterType === 'shift') {
            window.activeShift = filterTerm;
        }

        let rows = $('.sales-page .sale_table > tbody > tr');
        if (!rows.length) return;

        rows.each(function () {
            let row = $(this),
                rowShift = row.find('.shift').text(),
                rowSaleType = row.find('.sales_type').text(),
                dueAmount = +row.find('.due_amount').text();

            let showRow = true;

            // --- SALE TYPE FILTER ---
            if (window.activeSaleType) {
                if (window.activeSaleType === 'Due Payment') {
                    if (!(dueAmount > 0)) showRow = false;
                } else if (window.activeSaleType !== rowSaleType) {
                    showRow = false;
                }
            }

            // --- SHIFT FILTER ---
            if (window.activeShift) {
                if (window.activeShift !== rowShift) showRow = false;
            }

            // Final action
            showRow ? row.show() : row.hide();
        });
    };

    // Print the bill - Function
    window.printBill = function(){
        let oldBody = $('body').html();
        // let customerNameValue = $(`#customer-name`).val().trim() ? $(`#customer-name`).val().trim() : '--WALKING-CUSTOMER--';
        let invoiceNo = $('#sale_invoice').val().trim();
        const grossTotalTable = $('#grossTotalTable');
        let totalTbody = grossTotalTable.find('tbody'),
            grossPrice = totalTbody.find('.gross-total').text().trim(),
            discount = totalTbody.find('#discount').val().trim(),
            netPrice = totalTbody.find('.net-total').text().trim(),
            salesType = totalTbody.find('#salesType').val().trim(),
            paymentMethod = $('#paymentMethod').val().trim(),
            salesPerson = $('#printIvoiceBtn').data('user_name'),

            paidAmountInput = $(`input#paidAmount`),
            paidAmount = + paidAmountInput.val() ?? 0,

            dueAmountContainer = $(`#due-amount`),
            dueAmount = + dueAmountContainer.text() ?? 0;

        let tempCustomer = $('#customer-name'),
            customerNameValue = '';
        if(salesType === 'Credit Sale' || salesType === 'Partially Paid'){
            let cNameInput = $('#customer-name-credit');

            // Validate Customer name field before printing and saving sale
            if(cNameInput.val()){
                cNameInput.css('border-color', 'green');
                customerNameValue = cNameInput.val();
            }else{
                cNameInput.css('border-color', 'red');
                scrollTo('#fbm_notice', 100);
                return;
            }
            
        }else{
            if(tempCustomer.val()){
                customerNameValue = tempCustomer.val();
            }else{
                customerNameValue = '--WALKING-CUSTOMER--';
            }
        }
        if(salesType === 'Partially Paid' && paidAmount <= 0){
            paidAmountInput.css('border-color', 'red');
            return;
        }else if(salesType === 'Partially Paid' && paidAmount > 0){
            paidAmountInput.css('border-color', 'green');
        }


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
                        <h3 style="font-size: 16px; margin-bottom: 0;"><strong>Sales Person: </strong><em style="font-weight: 600;">${salesPerson}</em></h3>
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
                            <th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Sale Type</strong></th>
                            <td style="padding: 0 20px 0 0; font-weight: 600;">${salesType}<td>
                        </tr>`;
        if(salesType === 'Partially Paid' || salesType === 'Credit Sale'){
            invoiceData += `<tr>
                                <th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Paid Amount:</strong></th>
                                <td style="padding: 0 20px 0 0; font-weight: 600;">${paidAmount}<td>
                            </tr>
                            <tr>
                                <th style="padding: 0 110px 0 0; font-weight: 900;"><strong>Due Amount</strong></th>
                                <td style="padding: 0 20px 0 0; font-weight: 600;">${dueAmount}<td>
                            </tr>`; 
        }
        
        if (!(sales_Type.value == "Credit Sale")) {
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
        location.reload(true);
    };
    
    // Delete Single Item
    window.remove = function (delBtnCls, confirmMessage, table_name, idColName) {
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

    // Quick edit product
    window.quick_edit = function () {
        let $this = $(this);
        let requiredEditForm = $this.closest('tr').next().find('.quick_edit_form');
        requiredEditForm.slideToggle();
    }

});
