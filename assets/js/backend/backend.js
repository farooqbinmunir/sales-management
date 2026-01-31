/* Sales Management - Backend Custom JavaScripts */


jQuery(document).ready($ => {
	const ajaxUrl = fbm_ajax.url,
		nonce = fbm_ajax.nonce,
		path = fbm_ajax.path;

	var notice = $('#fbm_notice');




	const cl = (...params) => console.log(...params); // Shortcut for console.log(), now use cl() instead in this file


	const a = (...params) => alert(...params); // Shortcut for alert(), now use a() instead in this file





	/* _______________________ */





	// Custom function for scrolling to specific element, first offset param is required, second is optional with default value of 0


	const scrollTo = (offsetSelector, offsetToMinus = 0) => {


		$('html, body').animate({


			scrollTop: Math.floor($(offsetSelector).offset().top - offsetToMinus)


		}, 'slow', 'linear');


	};

	// Akram js

	let fbm_ui = document.querySelector('#fbm_ui');

	if (fbm_ui) {
		document.querySelector('body').classList.add('fbm_ui');
	}

	// Set the current date in the desired format

	let d = new Date(),
		currentDate = d.getDate();
	let currentMonth = d.getMonth() + 1;
	let currentYear = d.getFullYear();
	let formattedDate = `${currentYear}-${currentMonth}-${currentDate}`;

	// scroll efect with focus row code start here

	let isScrollElement = document.querySelector('.scrollelement');
	let scrollElement = document.querySelector('.scrollelement');
	if (isScrollElement) {

		// let allScrollEleRow = document.querySelectorAll('.scrollelement tbody tr:not(.zero_stock_alert)');
		let allScrollEleRow = document.querySelectorAll('.scrollelement tbody tr');

		// Add focus class when condition is true 
		let productIRowIndex = 0;
		let visibleRows = [];

		// Function to update the focused row
		function updateFocus() {
			visibleRows = Array.from(allScrollEleRow).filter((row) => {
				return row.style.display !== 'none' && !row.classList.contains('edit_form');
			});
			visibleRows.forEach((v, i) => {
				v.classList.toggle('focused', i === productIRowIndex);
			});
		}

		// By presponsesing key up and down, focus moves in product table rows
		/*
		document.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowUp') {
				productIRowIndex = (productIRowIndex > 0) ? productIRowIndex - 1 : 0;
				updateFocus();
			} else if (e.key === 'ArrowDown') {
				productIRowIndex = (productIRowIndex < visibleRows.length - 1) ? productIRowIndex + 1 : visibleRows.length - 1;
				updateFocus();
			}

			// old code

			// Scroll product table code
			// let focusItem = document.querySelector('.focused');
			// if (focusItem) {
			// 	let scrollElementHeight = scrollElement.clientHeight;
			// 	if (focusItem.offsetTop > (scrollElementHeight - focusItem.clientHeight)) {
			// 		scrollElement.scrollTop += focusItem.clientHeight;
			// 	}
			// 	if (focusItem.offsetTop < (scrollElementHeight - (focusItem.clientHeight * 4))) {
			// 		scrollElement.scrollTop -= focusItem.clientHeight;
			// 	}
			// }

			// new code code

			// Scroll product table code
			let focusItem = document.querySelector('.focused');
			if (focusItem) {
				focusItem.scrollIntoView({
					// behavior: 'smooth',
					behavior: 'auto',
					block: 'nearest',
					inline: 'nearest'
				});
			}
		});
		*/

		document.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowUp') {
				productIRowIndex = (productIRowIndex > 0) ? productIRowIndex - 1 : 0;
				updateFocus();
			} else if (e.key === 'ArrowDown') {
				productIRowIndex = (productIRowIndex < visibleRows.length - 1) ? productIRowIndex + 1 : visibleRows.length - 1;
				updateFocus();
			}

			// old code

			// Scroll product table code
			// let focusItem = document.querySelector('.focused');
			// if (focusItem) {
			// 	let scrollElementHeight = scrollElement.clientHeight;
			// 	if (focusItem.offsetTop > (scrollElementHeight - focusItem.clientHeight)) {
			// 		scrollElement.scrollTop += focusItem.clientHeight;
			// 	}
			// 	if (focusItem.offsetTop < (scrollElementHeight - (focusItem.clientHeight * 4))) {
			// 		scrollElement.scrollTop -= focusItem.clientHeight;
			// 	}
			// }

			// new code code

			// Scroll product table code
			// let focusItem = document.querySelector('.focused');
			// if (focusItem) {
			// 	focusItem.scrollIntoView({
			// 		// behavior: 'smooth',
			// 		behavior: 'auto',
			// 		block: 'nearest',
			// 		inline: 'nearest'
			// 	});
			// }
		});

		// here focus first row of prodcut table on page load
		updateFocus(allScrollEleRow);
	}

	// scroll efect with focus row code end here


	let isInventoryPage = document.querySelector('.sales-management-wrap');
	if (isInventoryPage) {
		document.querySelector('.date').innerHTML = "Date : " + formattedDate;
		let no = 0;
		let customerInput = document.querySelector('#customer-name');
		let productInput = document.querySelector('#search-product');
		let showTable = document.querySelector('#showTable');
		let sTtable = document.querySelector('.selected-total-table');
		sTtable.style.display = "none";
		let allItems = document.querySelectorAll('.product-table-wrap tbody .item-name');
		let allInput = document.querySelectorAll('input');
		let selectedTable = document.querySelector('.selected-product-table-wrap tbody');
		let allProductRows = document.querySelectorAll('.product-table-wrap tbody tr:not(.zero_stock_alert)');
		let allRows = document.querySelectorAll('.product-table-wrap tr');

		// let productTableTdName = ['sr-Number', 'item-name', 'stock', 'sale', 'vendor', 'in-stock', 'location'];
		// allRows.forEach((row) => {
		// 	let allRowChildren = row.children;
		// 	Array.from(allRowChildren).forEach((child, index) => {
		// 		if (productTableTdName[index]) {
		// 			child.classList.add(productTableTdName[index]);
		// 		}
		// 	});
		// });


		// addProduct function call here by click
		allProductRows.forEach((row) => {
			row.addEventListener('click', () => addProduct(row));
		});

		// Add product function call here by pressing Enter Button
		// document.addEventListener('keydown', (e) => {
		// 	let focusItem = document.querySelector('.focused:not(.zero_stock_alert)');
		// 	if (focusItem) {
		// 		if (e.key === 'Enter') {
		// 			// addProduct.call(focusItem);
		// 			$('.focused:not(.zero_stock_alert)').click();
		// 		}else{
		// 			return true;
		// 		}
		// 	}
		// });
		$(document).on('keydown', function(e){
			if(e.key === 'Enter'){
				// $('.focused:not(.zero_stock_alert)').trigger('click');
				$('.focused:not(.zero_stock_alert)').click();
			}
		});
		// $(document).on('click', '.focused:not(.zero_stock_alert)', function(){
		// 	// $('.selected-total-table').fadeIn();
		// 	// console.log('Clicked');
		// 	let focusedTr = document.querySelector('.focused:not(.zero_stock_alert)');
		// 	addProduct(focusedTr);
		// });

		// Function to reset and reassign serial numbers after product add/delete
		function resetSerialNumbers() {
			let allSr = document.querySelectorAll('.selected-product-table-wrap tbody .sr-Number');
			allSr.forEach((sr, index) => {
				sr.innerHTML = index + 1;  // Reassign serial number starting from 1
			});
		}

		// addProduct function start and define here

		function addProduct($tr) {
			var $row = $tr;
			console.log('Row', $row);


			no++;
			let product_id = Number($row.getAttribute('data-id'));

			// make sure here product not dublicate  
			let allSelectedTr = selectedTable.childNodes;
			for (let i = 0; i < allSelectedTr.length; i++) {
				let existItem = Number(allSelectedTr[i].dataset.id);
				if (product_id == existItem) {
					return alert('This item already has been added to cart!');
				}
			}
			let purcahseRate = +$row.querySelector('.ppurchase_rate').innerHTML,
				saleRate = +$row.querySelector('.psale_rate').innerHTML;

			let newRow = document.createElement('tr');
			newRow.setAttribute('data-id', product_id);
			newRow.setAttribute('data-purchase_rate', purcahseRate);
			newRow.setAttribute('data-sale_rate', saleRate);

			selectedTable.append(newRow);

			// here we give className to each td and after that we append all td in tr 
			let tdClassName = ['sr-Number', 'item-name', 'sale-price', 'quantity', 'items-price', 'item-type', 'edit'];
			tdClassName.forEach((className, index) => {
				let td = document.createElement('td');
				td.classList.add(className);
				newRow.append(td);
			});

			// Set Serial Number
			let srNo = newRow.querySelector('.sr-Number');
			srNo.innerHTML = no;

			// Set item name
			let selectedItem = newRow.querySelector('.item-name');
			selectedItem.innerText = $row.querySelector('.pname').innerText;

			// unit price
			let salePrice = newRow.querySelector('.sale-price');
			salePrice.innerText = +$row.querySelector('.psale_rate').innerText;

			let inStock = +$row.querySelector('.pin_stock').innerText;

			// Add input fields for quantity
			let numberField = document.createElement('input');
			numberField.type = 'number';
			numberField.min = 0;
			numberField.max = inStock;
			numberField.oninput = function () {
				if (this.value > inStock) this.value = inStock; // enforce max
			}
			newRow.querySelector('.quantity').append(numberField);
			setTimeout(e => numberField.focus(), 1/2 * 1000)

			// type of selected item field
			let itemTypeField = document.createElement('select');
			let options = ['simple', 'solid', 'liquid'];
			options.forEach((option, index) => {
				let opt = document.createElement('option');
				opt.value = option;
				opt.text = option.charAt(0).toUpperCase() + option.slice(1); // Capitalize the first letter
				if (index === 0) opt.selected = true; // Set 'simple' as default
				itemTypeField.appendChild(opt);
			});
			newRow.querySelector('.item-type').append(itemTypeField);

			// Add 'Delete' text to the last column
			let delBtn = document.createElement('button');
			delBtn.innerHTML = "Delete <sapn>&times;</sapn>";
			let editCell = newRow.querySelector('.edit');
			editCell.append(delBtn);

			sTtable.style.display = 'block';

			// Get the Items price for the clicked product
			let sp = Number($row.querySelector('.psale_rate').innerText);

			// Set serial numbers to be updated correctly after adding a product
			resetSerialNumbers();

			// Items price calculation logic
			const allItemsPrice = document.querySelectorAll('.selected-product-table-wrap tbody .items-price');
			const InStock = Number($row.querySelector('.pin_stock').innerText.trim());
			const allPriceInput = document.querySelectorAll('.selected-product-table-wrap tbody .quantity input');
			const grossTotal = document.querySelector('tbody .gross-total');
			const netTotalElement = document.querySelector('.net-total');
			const discountField = document.querySelector('tbody .discount input');

			const calculateTotals = () => {
				let total = Array.from(allItemsPrice).reduce((sum, unit) => sum + Number(unit.innerText), 0);
				grossTotal.innerText = total;

				let discountValue = Number(discountField.value) || 0,
					netTotal = (total - discountValue),
					paidAmount = +$(`input#paidAmount`).val(),
					dueAmount = netTotal - paidAmount;
				netTotalElement.innerText = netTotal;
				$(`#due-amount`).text(dueAmount);

			};
			allPriceInput.forEach((input, index) => {
				

				input.addEventListener('input', e => {
					// Prevent negative sign input
					if (e.key === '-') {
						return 0;
					}

					let quantity = Number(input.value),
						tr = input.closest('tr'),
						unitPrice = +tr.querySelector('.sale-price').innerText,
						amount = +(quantity * unitPrice),
						purcahseRate = +tr.dataset.purchase_rate;
						profit = +(amount - (purcahseRate * quantity));
					tr.setAttribute('data-profit', profit);
					
					// Ensure quantity is positive and at least 1
					if (quantity < 0) {
						alert("Please enter a positive value.");
						input.value = 0;
						allItemsPrice[index].innerText = 0;
						calculateTotals();
					}

					// Validate stock availability
					if (quantity > InStock) {
						alert(`Your available stock is ${InStock}. Please reduce your quantity.`);
						input.value = "";
						allItemsPrice[index].innerText = "";
						calculateTotals();
					}

					allItemsPrice[index].innerText = amount;
					calculateTotals();
				});
			});

			// Bug fix - when quantity added is out of stock then quantity visually becomes empty but it's total gets calculated, it should be 0 when quantity is empty or 0
			// allPriceInput.forEach((input, index) => {
				

			// 	input.addEventListener('keydown', e => {
			// 		// Prevent negative sign input
			// 		if (e.key === '-' || input.value < 0) {
			// 			return;
			// 		}

			// 		let quantity = Number(input.value);
			// 		if(quantity > InStock){
			// 			alert(`Your available stock is ${InStock}. Please reduce your quantity.`);
			// 			// return;
			// 		}
			// 	});
			// });


			// Update totals when discount changes
			discountField.addEventListener('keyup', calculateTotals);




			// Edit functionality for each row (Placeholder for future logic)
			let allSr = document.querySelectorAll('.selected-product-table-wrap tbody .sr-Number');

			let editBtn = document.querySelectorAll('.selected-product-table-wrap tbody .edit');
			editBtn.forEach((v, i) => {
				v.addEventListener('click', function () {
					let startLoop = Number(this.closest('tr').querySelector('.sr-Number').innerHTML);
					// Remove the selected row
					// this.closest('tr').remove();
					(confirm("Do you want to proceed?")) ? this.closest('tr').remove() : '';
					resetSerialNumbers();  // Reset serial numbers after deletion
					recalculateTotals();   // Recalculate totals after deletion
				});
			});

			// Function to recalculate the total after deleting an item
			function recalculateTotals() {

				// Select all remaining item prices after deletion
				let allItemsPrice = document.querySelectorAll('.selected-product-table-wrap tbody .items-price');

				// Recalculate the gross total
				let newGrossTotal = Array.from(allItemsPrice).reduce((sum, unit) => sum + Number(unit.innerHTML), 0);
				document.querySelector('tbody .gross-total').innerHTML = newGrossTotal;

				// Fetch the discount value and calculate net total
				let discountField = document.querySelector('tbody .discount input');
				let discountValue = Number(discountField.value); // Ensure it's a number
				// let discount = (discountValue / 100) * newGrossTotal; // Convert discount to percentage

				// Calculate the new net total
				let netTotal = newGrossTotal - discountValue;
				document.querySelector('.net-total').innerHTML = netTotal; // Update net total

				// Update totals whenever the discount field is modified
				discountField.addEventListener('keyup', function () {
					let discountValue1 = Number(discountField.value); // Ensure it's a number
					// let discount1 = (discountValue1 / 100) * newGrossTotal; // Convert discount to percentage
					let netTotal1 = newGrossTotal - discountValue1;
					document.querySelector('.net-total').innerHTML = netTotal1;
				});

			}
			allProductRows.forEach((v, i) => {
				productInput.value = '';
				allProductRows[i].style.display = '';
			});


			// show listing listing Button
			let spTbody = document.querySelector('.selected_items_container');
			if (spTbody.childNodes.length) {
				showTable.style.display = "block";
			}

			let salesType = document.querySelector("#grossTotalTable #salesType");
			let paymentMethod = document.querySelector("#paymentMethod");
			salesType.addEventListener("change", function () {
				if (salesType.value == "Credit Sale") {
					paymentMethod.value = "---";
					paymentMethod.disabled = true;
				} else {
					paymentMethod.value = "Cash in Hand";
					paymentMethod.disabled = false;
				}
			});

		}


		// addProduct function end



		// show listing producted table 

		showTable.addEventListener('click', function () {
			sTtable.style.display = "block";
		});





		// close selected product table 

		let closeSpBtn = document.querySelector('.close-sp-tbl');

		closeSpBtn.addEventListener('click', function () {

			sTtable.style.display = 'none';

		});

		// document.addEventListener('keydown', function (e) {
		// 	if (e.ctrlKey && e.key === 'a') {
		// 		e.preventDefault();
		// 		let listingItem = document.querySelector('.selected-total-table');
		// 		listingItem.classList.toggle('close-selected-table');
		// 	}
		// });
	}

	$('#product-form').on('submit', function (e) {
		e.preventDefault();
		let product_name = $('#product_name').val(),
			product_purchase_price = $('#product_purchase_price').val(),
			product_sale_price = $('#product_sale_price').val(),
			product_vendor = $('#product_vendor').val(),
			product_manufacturer = $('#product_manufacturer').val(),
			product_location = $('#product_location').val(),
			product_min_quantity = $('#add_min_quantity').val();
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
				required_action: 'add',
			},
			success: function (response) {
				// console.log(response, 'response');
				if (response.success) {
					$('.add-product-page .product-form-wrap').fadeOut();
					notice.removeClass('notice-error').addClass('notice-success').text('New product added successfully.').slideDown();
					scrollTo('#fbm_notice', 100);
					setTimeout(() => location.reload(), 1500);
				} else {
					scrollTo('#fbm_notice', 100);
					notice.removeClass('notice-success').addClass('notice-error').text('Failed to add new product.').slideDown();
				}
				alert('Product added successfully!');
				location.reload(); // Reload the page to show the new product
			}
		});
	});


	//search function call here for Search Product
	// By default or on page load, set search to product name statically
	setupSearch('input', '#search-product', 'tbody#product-table-body', 'tr', 'td.pname');

	// On filter type change do the search filteration
	$(document).on('change', '#searchFilterType', doSearchFilter);
	function doSearchFilter(){
		let searchFilterDropdown = $(this),
			searchFilterType = searchFilterDropdown.val(),
			searchFieldSelector = '',
			searchInput = $('#search-product');
		const searchFilterSelectorsMapping = {
			product_name: 'td.pname',
			product_vendor: 'td.pvendor',
			product_manufacturer: 'td.pmanufacturer',
		};
		let searchedFilterType = searchFilterType.replaceAll('_', ' ');

		let newPlaceholder = `Search ${searchedFilterType}`;
		// Update placeholder
		searchInput.attr('placeholder', newPlaceholder);

		// Remove the old/initial event on product search
		$('#search-product').off('input');

		// Prepare and pass, dynamic search field selector based on selected filter type
		searchFieldSelector = searchFilterSelectorsMapping[searchFilterType];
		setupSearch('input', '#search-product', 'tbody#product-table-body', 'tr', searchFieldSelector);
	}






	let isSalePage = document.querySelector('.sales-page');
	if (isSalePage) {

		let fromDate = document.querySelector('#from_date');
		let toDate = document.querySelector('#to_date');
		let saleAmount = document.querySelector('#sale_amount');
		let creditAmount = document.querySelector('#credit_amount');
		let partReceivedAmount = document.querySelector('#partially_recieved_amount');
		let partRemAmount = document.querySelector('#partially_remaining_amount');
		let totalAmountInput = document.querySelector('#total_amount');
		let allSaleRows = document.querySelectorAll('.sale_table tbody tr');
		let salesType = document.querySelector('#salesType');
		let calcProfit = document.querySelector('#sc_profit');

		// Set default From Date as today
		let today = new Date(),
			formattedDate = today.toISOString().split('T')[0];
		toDate.value = formattedDate;

		// Show today's total without adding any class
		updateTotalOnly(toDate.value);

		// Show total only (no class)
		function updateTotalOnly(dateStr) {
			let todayTotal = 0,
				cashSale = 0,
				creditSale = 0,
				partiallyReceivedTotal = 0, 
				partiallyRemainingTotal = 0, 
				entries = 0,
				profitTotal = 0,

				cashSaleInput = saleAmount,
				creditSaleInput = creditAmount,
				entriesElement = $(`.totalEntriesCount`);
			allSaleRows.forEach(row => {
				let rowDateStr = row.getAttribute('data-date'),
					saleTypeValue = row.querySelector('.sales_type').innerText.trim();

				if (rowDateStr === dateStr) {
					let amt = Number(row.querySelector('.amount').innerText.trim()) ?? 0;
					let remainingAmount = Number(row.querySelector('.due_amount').innerText.trim()) ?? 0;
					let profit = Number(row.querySelector('.profit').innerText.trim()) ?? 0;

					todayTotal += amt;

					// Calculate total profit
					profitTotal += profit;

					// Increment the entry
					entries++;
					if(saleTypeValue == 'Credit Sale'){
						creditSale += amt;
					}else if(saleTypeValue == 'Cash Sale'){
						cashSale += amt;
					}else if(saleTypeValue == 'Partially Paid'){
						partiallyReceivedTotal += amt - remainingAmount;
						partiallyRemainingTotal += remainingAmount;
					}
				}
			});
			totalAmountInput.value = todayTotal;
			saleAmount.value = cashSale;
			creditAmount.value = creditSale;
			entriesElement.text(entries);

			// Set partially paid sale(paid and remaining amount)
			partReceivedAmount.value = partiallyReceivedTotal;
			partRemAmount.value = partiallyRemainingTotal;
			
			partReceivedAmount.setAttribute('value', partiallyReceivedTotal);
			partRemAmount.setAttribute('value', partiallyRemainingTotal);

			// Set profit
			calcProfit.innerText = profitTotal;
		}



		// ya new code hai 

		[fromDate, toDate].forEach(input => {
			input.addEventListener('input', () => {
				let fromVal = fromDate.value,
					toVal = toDate.value;
					todayDate = new Date().toISOString().split(`T`)[0],
					salesBanner = $(`.salesCalculatorBanner`);

				// If both are cleared
				if (!fromVal && !toVal) {
					updateTotalsAndHighlight(); // No filter — show all
				} else {
					updateTotalsAndHighlight(fromVal, toVal || null); // Range-based
				}
				if(!fromVal && (toVal == todayDate)){
					salesBanner.show();
				}else{
					salesBanner.hide();
				}
			});
		});

		function updateTotalsAndHighlight(fromDate = null, toDate = null) {
			let fromTime = fromDate ? new Date(fromDate).getTime() : null,
				toTime = toDate ? new Date(toDate).getTime() : fromTime,
				totalEntriesCountElement = $(`.amount-cell .totalEntriesCount`),
				total = 0, 
				totalProfit = 0,
				cashTotal = 0, 
				creditTotal = 0, 
				partiallyReceivedTotal = 0, 
				partiallyRemainingTotal = 0, 
				entriesTotal = 0,
				todayDate = new Date().toISOString().split(`T`)[0];


			if (fromTime && toTime && fromTime > toTime) [fromTime, toTime] = [toTime, fromTime];

			allSaleRows.forEach(row => {
				let rowDateStr = row.getAttribute('data-date'),
					rowTime = new Date(rowDateStr).getTime(),
					saleType = row.querySelector('.sales_type').innerText.trim(),
					amount = Number(row.querySelector('.amount').innerText.trim()) ?? 0,
					remainingAmount = Number(row.querySelector('.due_amount').innerText.trim()) ?? 0,
					profit = Number(row.querySelector('.profit').innerText.trim()) ?? 0;

					isInRange = true;

				if (fromTime !== null) {
					isInRange = rowTime >= fromTime;
				}
				if (toTime !== null) {
					isInRange = isInRange && rowTime <= toTime;
				}

				// Add/remove helper class
				if (isInRange) {
					row.classList.add('found_in_search');
					row.classList.remove('not_found_in_search');

					total += amount;
					totalProfit += profit;

					if (saleType === "Cash Sale") cashTotal += amount;
					if (saleType === "Credit Sale") creditTotal += amount;
					if (saleType === "Partially Paid") {
						partiallyReceivedTotal += amount - remainingAmount;
						partiallyRemainingTotal += remainingAmount;
					}
					entriesTotal += 1;
				} else {
					console.log('Not in range');
				}
			});

			// Update totals
			totalAmountInput.value = total;
			saleAmount.value = cashTotal;
			creditAmount.value = creditTotal;
			totalEntriesCountElement.text(entriesTotal);

			// Set partially paid sale(paid and remaining amount)
			partReceivedAmount.value = partiallyReceivedTotal;
			partRemAmount.value = partiallyRemainingTotal;
			
			partReceivedAmount.setAttribute('value', partiallyReceivedTotal);
			partRemAmount.setAttribute('value', partiallyRemainingTotal);

			// Set the total profit
			calcProfit.innerText = totalProfit;

			// Apply dropdown filter
			// filterBySalesType();
		}

		// Dropdown filtering
		function filterBySalesType() {
			let selectedType = salesType?.value || '';

			allSaleRows.forEach(row => {
				let saleType = row.querySelector('.sales_type').innerText.trim();
				let isMatched = row.classList.contains('found_in_search');

				let showRow = false;
				if (selectedType === '') {
					showRow = isMatched;
				} else if (selectedType === 'cash-sales' && saleType === 'Cash Sale') {
					showRow = isMatched;
				} else if (selectedType === 'credit-sales' && saleType === 'Credit Sale') {
					showRow = isMatched;
				}

				if (showRow) {
					row.classList.remove('not_found_in_search');
					row.classList.add('found_in_search');
				} else {
					row.classList.remove('found_in_search');
					row.classList.add('not_found_in_search');
				}
			});
		}

		// Apply dropdown filter when changed
		salesType?.addEventListener('change', () => {
			let fromVal = fromDate.value;
			let toVal = toDate.value;
			updateTotalsAndHighlight(fromVal || null, toVal || null);
		});




		// Optional: invoice search
		setupSearch('keyup', '#invoice_number', '.sale_table tbody', 'tr', 'td.invoice_no');

		// Optional: date search for table cell (not for filtering)
		setupSearch('keyup', '#to_date', '.sale_table tbody', 'tr', 'td.sales_date');
	}

	let isAddProductPage = document.querySelector('.add-product-page');

	if (isAddProductPage) {
		setupSearch('keyup', '#search-product', '#products_table_listing_rows', 'tr', 'td:nth-child(2)');
	}

	let purchasePage = document.querySelector('.purchase-page');

	if (purchasePage) {
		setupSearch('keyup', '#search-product', '.purchase-tbody', 'tr', 'td:nth-child(2)');
	}

	// Stock check filter
	let stockChecker = document.querySelector('#stockFilter');

	if (stockChecker) {
		let productTableRows = document.querySelectorAll('#product-table-body tr');

		stockChecker.addEventListener('change', function () {
			let selectedValue = this.value;

			if (selectedValue === 'all-stock') {
				productTableRows.forEach(function (v) {
					v.classList.remove('hide');
					v.classList.add('show');
				});

			} else if (selectedValue === 'near-to-end') {
				productTableRows.forEach(function (v) {
					if (v.classList.contains('low_stock_warning')) {
						v.classList.remove('hide');
						v.classList.add('show');
					} else {
						v.classList.remove('show');
						v.classList.add('hide');
					}
				});

			} else if (selectedValue === 'out-of-stock') {
				productTableRows.forEach(function (v) {
					if (v.classList.contains('zero_stock_alert')) {
						v.classList.remove('hide');
						v.classList.add('show');
					} else {
						v.classList.remove('show');
						v.classList.add('hide');
					}
				});
			}
		});
	}


	// Calculating due amount when paid amount is entered
	$(document).on(`input`, `input#paidAmount`, function(){
		let input = $(this),
			dueAmountContainer = $(`#due-amount`),
			netAmount = +$(`td.net-total`).text().trim(),
			paidAmount = +input.val(),
			dueAmount = netAmount - paidAmount;
		// Now set the due amount
		dueAmountContainer.text(dueAmount);
	});

	$(document).on('change', '#grossTotalTable select#salesType', function(){
		let $this = $(this),
			selectedSaleType = $this.val().trim(),
			salesTable = $this.closest('table'),
			partialPaymentRows = salesTable.find('tr.partial_payment_row'),
			paidAmountInput = salesTable.find('input#paidAmount'),
			dueAmountElement = salesTable.find('#due-amount'),
			netTotalAmount = + salesTable.find('.net-total').text(),
			tempCustomer = $('#customer_register_area > .cform_field'),
			customerRegisterForm = $('#creditCustomerForm');


		if(selectedSaleType === 'Partially Paid'){
			paidAmountInput.val(0).attr('max', netTotalAmount).trigger('input');
			dueAmountElement.text(netTotalAmount);
			partialPaymentRows.slideDown();
			customerRegisterForm.slideDown();
			tempCustomer.prop('inert', true).attr('inert', 'inert');

			// Open the popup
			$('#salesCalculator').fadeIn();
		}else if(selectedSaleType === 'Cash Sale'){
			partialPaymentRows.slideUp();
			paidAmountInput.val(netTotalAmount);
			dueAmountElement.text(0);
			customerRegisterForm.slideUp();
			tempCustomer.prop('inert', false);

			// Close the popup
			$('#salesCalculator').fadeOut();
		}else{
			partialPaymentRows.slideUp();
			paidAmountInput.val(0);
			dueAmountElement.text(netTotalAmount);
			customerRegisterForm.slideDown();
			tempCustomer.prop('inert', true).attr('inert', 'inert');

			// Open the popup
			$('#salesCalculator').fadeIn();
		}
	});

	// Filter sales by sale type or due amount
	$(document).on('change', '.sales-page select#salesType', function(){
		let $this = $(this),
			saleType = $this.val();
		if(typeof window.filterSales != 'undefined'){
			filterSales(saleType);
		}else{
			console.error('filterSales() function not defined.');
		}
	});

	// Filter sales by shift (moring/evening/night)
	$(document).on('change', '.sales-page select#shiftsDropdown', function(){
		let $this = $(this),
			shift = $this.val();
		if(typeof window.filterSales != 'undefined'){
			filterSales(shift, 'shift');
		}else{
			console.error('filterSales() function not defined.');
		}
	});

	// Toggle sales calculator
	$(document).on('click', '#salesCalculatorToggler', function(){
		$('#salesCalculator').fadeIn();
	});

	$(document).on('click', '#salesCalculatorCloser', function(){
		$(this).closest('#salesCalculator').fadeOut();
	});

	$(document).on('click', function(e){
		let targetId = e.target.id,
			salesCalculatorPopup = $('#salesCalculator');
		if(targetId === 'salesCalculator'){
			salesCalculatorPopup.fadeOut();
		}
	});

	$(document).on('keyup', function(e){
		let salesCalculatorPopup = $('#salesCalculator');
		if(e.key === 'Escape'){
			if(salesCalculatorPopup.is(":visible")){
				salesCalculatorPopup.fadeOut();
			}else{
				console.log('Not Visible');
			}
		}
	});

	document.querySelectorAll("#fbm_ui input[type='number']").forEach(input => {
	  input.addEventListener("input", () => {
	    if (input.value < 0) {
	      input.value = "";        // clear the field
	      alert("Negative numbers are not allowed.");
	      return false;
	    }
	  });
	});




});


