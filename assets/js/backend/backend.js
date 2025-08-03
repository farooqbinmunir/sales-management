/* Wiselogix - Custom JavaScripts */


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
		let allProductIRow = document.querySelectorAll('.product-table-wrap tbody tr:not(.zero_stock_alert)');
		let allRows = document.querySelectorAll('.product-table-wrap tr');
		let productTableTdName = ['sr-Number', 'item-name', 'stock', 'sale', 'vendor', 'in-stock', 'location'];

		allRows.forEach((row) => {
			let allRowChildren = row.children;
			Array.from(allRowChildren).forEach((child, index) => {
				if (productTableTdName[index]) {
					child.classList.add(productTableTdName[index]);
				}
			});
		});


		// addProduct function call here by click
		allProductIRow.forEach((row) => {
			row.addEventListener('click', addProduct);
		});

		// Add product function call here by pressing Enter Button
		document.addEventListener('keydown', (e) => {
			let focusItem = document.querySelector('.focused:not(.zero_stock_alert)');
			if (focusItem) {
				if (e.key === 'Enter' && sTtable.style.display === 'none') {
					addProduct.call(focusItem);
				}
			}
		})

		// Function to reset and reassign serial numbers after product add/delete
		function resetSerialNumbers() {
			let allSr = document.querySelectorAll('.selected-product-table-wrap tbody .sr-Number');
			allSr.forEach((sr, index) => {
				sr.innerHTML = index + 1;  // Reassign serial number starting from 1
			});
		}

		// addProduct function start and define here

		function addProduct() {


			no++;
			let product_id = Number(this.getAttribute('data-id'));

			// make sure here product not dublicate  
			let allSelectedTr = selectedTable.childNodes;
			for (let i = 0; i < allSelectedTr.length; i++) {
				let existItem = Number(allSelectedTr[i].dataset.id);
				if (product_id == existItem) {
					return alert('This item already has been added to cart!');
				}
			}


			let newRow = document.createElement('tr');
			newRow.setAttribute('data-id', product_id);

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
			selectedItem.innerText = this.querySelector('.item-name').innerText;

			// unit price
			let salePrice = newRow.querySelector('.sale-price');
			salePrice.innerText = this.querySelector('.sale').innerText;

			// Add input fields for quantity
			let numberField = document.createElement('input');
			numberField.type = 'number';
			numberField.min = 0;
			newRow.querySelector('.quantity').append(numberField);
			setTimeout(() => {
				numberField.focus();
			}, 0);

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

			// Add input fields for discount
			// let numberField1 = document.createElement('input');
			// numberField1.type = 'number';
			// newRow.querySelector('.item-discount').append(numberField1);

			// Hide the selected product item table and empty product search feild
			let allSpInput = document.querySelectorAll('.selected-total-table-inner tbody input');
			allSpInput[allSpInput.length - 1].addEventListener('keydown', function (e) {
				if (e.key == 'Tab') {
					e.preventDefault();
					sTtable.style.display = 'none';
					productInput.focus();
				}
			});

			// Add 'Delete' text to the last column
			let delBtn = document.createElement('button');
			delBtn.innerHTML = "Delete <sapn>&times;</sapn>";
			let editCell = newRow.querySelector('.edit');
			editCell.append(delBtn);

			sTtable.style.display = 'block';

			// Get the Items price for the clicked product
			let sp = Number(this.querySelector('.sale').innerText);

			// Set serial numbers to be updated correctly after adding a product
			resetSerialNumbers();

			// Items price calculation logic
			const allItemsPrice = document.querySelectorAll('.selected-product-table-wrap tbody .items-price');
			const InStock = Number(this.querySelector('.in-stock').innerText.trim());
			const allPriceInput = document.querySelectorAll('.selected-product-table-wrap tbody .quantity input');
			const grossTotal = document.querySelector('tbody .gross-total');
			const netTotalElement = document.querySelector('.net-total');
			const discountField = document.querySelector('tbody .discount input');

			const calculateTotals = () => {
				let total = Array.from(allItemsPrice).reduce((sum, unit) => sum + Number(unit.innerText), 0);
				grossTotal.innerText = total;

				let discountValue = Number(discountField.value) || 0;
				// let discount = (discountValue / 100) * total;
				netTotalElement.innerText = (total - discountValue);
			};

			allPriceInput.forEach((input, index) => {
				// Prevent negative sign input
				input.addEventListener('keyup', (e) => {
					if (e.key === '-') {
						e.preventDefault();
					}
				});

				input.addEventListener('keyup', () => {
					let quantity = Number(input.value);

					// Ensure quantity is positive and at least 1
					if (quantity < 0) {
						alert("Please enter a positive value.");
						input.value = "";
						allItemsPrice[index].innerText = "";
						calculateTotals();
						return;
					}

					// Validate stock availability
					if (quantity > InStock) {
						alert(`Your available stock is ${InStock}. Please reduce your quantity.`);
						input.value = "";
						allItemsPrice[index].innerText = "";
						calculateTotals();
						return;
					}

					// Update unit price and total
					// allItemsPrice[index].innerText = (quantity * sp);
					// calculateTotals();
					let currentRow = input.closest('tr');
					let unitPrice = Number(currentRow.querySelector('.sale-price').innerText);

					allItemsPrice[index].innerText = (quantity * unitPrice);
					calculateTotals();
				});
			});


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
			allProductIRow.forEach((v, i) => {
				productInput.value = '';
				allProductIRow[i].style.display = '';
			});


			// show listing listing Button
			let spTbody = document.querySelector('.selected_items_container');
			if (spTbody.childNodes.length) {
				showTable.style.display = "Block";
			}

			let salesType = document.querySelector("#salesType");
			let paymentMethod = document.querySelector("#paymentMethod");
			salesType.addEventListener("change", function () {
				if (salesType.value == "credit-sales") {
					paymentMethod.value = "---";
					paymentMethod.disabled = true;
				} else {
					paymentMethod.value = "cash-in-hand";
					paymentMethod.disabled = false;
				}
			});

		}


		// addProduct function end



		// show listing producted table 

		showTable.addEventListener('click', function () {
			sTtable.style.display = "Block";
		});





		// close selected product table 

		let closeSpBtn = document.querySelector('.close-sp-tbl');

		closeSpBtn.addEventListener('click', function () {

			sTtable.style.display = 'none';

		});

		document.addEventListener('keydown', function (e) {
			// console.log(e.key);
			if (e.ctrlKey && e.key === 'a') {
				e.preventDefault();
				let listingItem = document.querySelector('.selected-total-table');
				listingItem.classList.toggle('close-selected-table');
			}
		});
	}

	$('#product-form').on('submit', function (e) {
		e.preventDefault();
		let product_name = $('#product_name').val(),
			product_purchase_price = $('#product_purchase_price').val(),
			product_sale_price = $('#product_sale_price').val(),
			product_vendor = $('#product_vendor').val(),
			product_location = $('#product_location').val(),
			product_min_quantity = $('#add_min_quantity').val();
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


	// Adding new purchase
	let addPurchaseBtn = $('#add_stock');
	addPurchaseBtn.click(addNewPurchase);
	function addNewPurchase(e) {
		e.preventDefault();
		let product_id = $('#product_id'),
			vendor = $('#purchase_product_vendor'),
			rate = $('#purchase_rate'),
			quantity = $('#purchase_quantity'),
			payment = $('#purchase_payment'),
			payment_status = $('#purchase_payment_status'),
			payment_method = $('#purchase_payment_method'),
			description = $('#purchase_description');

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
					required_action: 'add',
				},
				success: res => {
					if (res.success) {
						$('.purchase-page .product-form-wrap').fadeOut();
						notice.removeClass('notice-error').addClass('notice-success').text('New stock added successfully.').slideDown();
						scrollTo('#fbm_notice', 100);
						setTimeout(() => location.reload(), 1500);
					} else {
						scrollTo('#fbm_notice', 100);
						notice.removeClass('notice-success').addClass('notice-error').text('Failed to add new stock.').slideDown();
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
	}


	let isaddProductPage = document.querySelector('.add-product-page');
	let isPurchasePage = document.querySelector('.purchase-page');

	if (isaddProductPage || isPurchasePage) {
		let actionBtn = document.querySelector('.add-new');
		let closeUserFormBtn = document.querySelector('.closeUserForm');
		let showEle = document.querySelector('.product-form-wrap');
		showFormFun(actionBtn, showEle);
		closeUserFormBtn.addEventListener('click', function () {
			showEle.style.display = 'none';
		});

		// let formsBtn = document.querySelector('.save-btn');

		// formsBtn .addEventListener('click', function(){
		// 	showEle.style.display = 'none';
		// });

	}

	function showFormFun(actionBtn, showEle) {
		actionBtn.addEventListener('click', function () {
			showEle.style.display = 'block';
		});
	}


	// Get & Set product price/rate automatically on change of product in add stock/purchase form
	let purchase_product = $('#add_stock_form #product_id');
	purchase_product.on('change', function () {
		let product_id = Number($(this).val());
		$.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action: 'get_product_rate',
				product_id: product_id,
			}
		})
			.done(function (response) {
				if (response.success) {
					let product = JSON.parse(response.data),
						rate = Number(product.product_purchase_price),
						vendor = product.product_vendor;
					$('#purchase_rate').val(rate).attr({
						'value': rate,
						'data-product_id': product_id,
					});
					$('#purchase_product_vendor').val(vendor).attr('value', vendor);
				} else {
					alert('Failed to get product information');
				}
			})
			.fail(function () {
				console.log("AJAX ERROR => get_product");
			});

	});



	// Calculate payment as per selected product on quantity change on add purchase/stock form
	let purchase_quantity_selectboxes = $('.purchase_quantity');
	purchase_quantity_selectboxes.each((i, purchase_quantity) => {
		$(purchase_quantity).on('input', function () {
			let $this = $(this);
			let currentForm = $this.closest('form');
			let quantity = Number($this.val()),
				rate = Number(currentForm.find('#purchase_rate').val()),
				total_payment = Math.floor(quantity * rate);
			// Set Totaly payment
			currentForm.find('#purchase_payment').val(total_payment).attr('value', total_payment);

		});
	});

	//search function call here for Search Product

	setupSearch('keyup', '#search-product', 'tbody#product-table-body', 'tr', 'td.item-name');






	let isSalePage = document.querySelector('.sales-page');
	if (isSalePage) {

		let fromDate = document.querySelector('#from_date');
		let toDate = document.querySelector('#to_date');
		let saleAmount = document.querySelector('#sale_amount');
		let creditAmount = document.querySelector('#credit_amount');
		let totalAmountInput = document.querySelector('#total_amount');
		let allSaleRows = document.querySelectorAll('.sale_table tbody tr');
		let salesType = document.querySelector('#salesType');

		// Set default From Date as today
		let today = new Date();
		let formattedDate = today.toISOString().split('T')[0];
		fromDate.value = formattedDate;

		// To Date is empty with placeholder
		toDate.value = '';
		toDate.placeholder = 'YYYY-MM-DD';

		// Show today's total without adding any class
		updateTotalOnly(fromDate.value);

		// Show total only (no class)
		function updateTotalOnly(dateStr) {
			let total = 0;
			allSaleRows.forEach(row => {
				let rowDateStr = row.getAttribute('data-date');
				if (rowDateStr === dateStr) {
					let amt = Number(row.querySelector('.amount').innerText.trim()) || 0;
					total += amt;
				}
			});
			totalAmountInput.value = total;
		}

		// Show total fromdate to todate
		// [fromDate, toDate].forEach(date => {
		// 	date.addEventListener('change', () => {
		// 		fromDateToTodate(fromDate.value, toDate.value);
		// 	});
		// });

		// function fromDateToTodate(fDate, tDate) {
		// 	let total = 0;
		// 	if (!fDate || !tDate) return; // agar date missing hai to kuch na karo
		// 	let from = new Date(fDate);
		// 	let to = new Date(tDate);
		// 	allSaleRows.forEach(row => {
		// 		let rowDateStr = row.getAttribute('data-date');
		// 		let rowDate = new Date(rowDateStr);
		// 		if (rowDate >= from && rowDate <= to) {
		// 			let amt = Number(row.querySelector('.amount').innerText.trim()) || 0;
		// 			total += amt;
		// 		}
		// 	});
		// 	totalAmountInput.value = total;
		// }



		// ya old code hai 

		// // On user input, now apply filter + add classes
		// [fromDate, toDate].forEach(input => {
		// 	input.addEventListener('input', () => {
		// 		let fromVal = fromDate.value;
		// 		let toVal = toDate.value;

		// 		if (fromVal && toVal) {
		// 			updateTotalsAndHighlight(fromVal, toVal);
		// 		} else {
		// 			updateTotalsAndHighlight(fromVal, null); // show only that date
		// 		}
		// 	});
		// });

		// // Show total + apply classes for filtering (only on user input)
		// function updateTotalsAndHighlight(from, to = null) {
		// 	let fromTime = new Date(from).getTime();
		// 	let toTime = to ? new Date(to).getTime() : fromTime;
		// 	let total = 0;

		// 	allSaleRows.forEach(row => {
		// 		let rowDateStr = row.getAttribute('data-date');
		// 		let rowTime = new Date(rowDateStr).getTime();

		// 		// Remove old classes first
		// 		row.classList.remove('found_in_search', 'not_found_in_search');

		// 		if (rowTime >= fromTime && rowTime <= toTime) {
		// 			row.classList.add('found_in_search');
		// 			let amt = Number(row.querySelector('.amount').innerText.trim()) || 0;
		// 			total += amt;
		// 		} else {
		// 			row.classList.add('not_found_in_search');
		// 		}
		// 	});

		// 	totalAmountInput.value = total;
		// }



		// ya new code hai 

		[fromDate, toDate].forEach(input => {
			input.addEventListener('input', () => {
				let fromVal = fromDate.value;
				let toVal = toDate.value;

				// If both are cleared
				if (!fromVal && !toVal) {
					updateTotalsAndHighlight(); // No filter — show all
				} else {
					updateTotalsAndHighlight(fromVal, toVal || null); // Range-based
				}
			});
		});

		function updateTotalsAndHighlight(from = null, to = null) {
			let fromTime = from ? new Date(from).getTime() : null;
			let toTime = to ? new Date(to).getTime() : fromTime;

			if (fromTime && toTime && fromTime > toTime) [fromTime, toTime] = [toTime, fromTime];

			let total = 0, cashTotal = 0, creditTotal = 0;

			allSaleRows.forEach(row => {
				let rowDateStr = row.getAttribute('data-date');
				let rowTime = new Date(rowDateStr).getTime();
				let saleType = row.querySelector('.sales_type').innerText.trim();
				let amount = Number(row.querySelector('.amount').innerText.trim()) || 0;

				let isInRange = true;

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
					if (saleType === "Cash Sale") cashTotal += amount;
					if (saleType === "Credit Sale") creditTotal += amount;
				} else {
					row.classList.remove('found_in_search');
					row.classList.add('not_found_in_search');
				}
			});

			// Update totals
			totalAmountInput.value = total;
			saleAmount.value = cashTotal;
			creditAmount.value = creditTotal;

			// Apply dropdown filter
			filterBySalesType();


			// document.querySelectorAll('.focused').forEach(row => {
			// 	row.classList.remove('focused');
			// });

			// // Apply focus to first found_in_search row
			// let firstVisible = document.querySelector('.found_in_search');
			// if (firstVisible) {
			// 	firstVisible.classList.add('focused');
			// 	firstVisible.scrollIntoView({
			// 		behavior: 'auto',
			// 		block: 'nearest',
			// 		inline: 'nearest'
			// 	});
			// }
		}

		// Dropdown filtering
		function filterBySalesType() {
			let selectedType = salesType?.value || 'all-sales';

			allSaleRows.forEach(row => {
				let saleType = row.querySelector('.sales_type').innerText.trim();
				let isMatched = row.classList.contains('found_in_search');

				let showRow = false;
				if (selectedType === 'all-sales') {
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

	// jQuery helper function
	function setupSearch(event, inputSelector, tableBodySelector, rowSelector, searchColumnSelector) {
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


	// Stock check filter

	// let salesType = document.querySelector('#salesType');

	// if (salesType) {
	// 	let productTableRows = document.querySelectorAll('.sale_table tbody tr');
	// 	salesType.addEventListener('change', function () {
	// 		let selectedValue = this.value;
	// 		if (selectedValue === 'all-sales') {
	// 			productTableRows.forEach(function (v) {
	// 				v.classList.remove('hide');
	// 				v.classList.add('show');
	// 			});
	// 		} else if (selectedValue === 'cash-sales') {
	// 			productTableRows.forEach(function (v) {
	// 				let typeOfSsale = v.querySelector('.sales_type').innerText;
	// 				if (typeOfSsale == "Cash Sale") {
	// 					v.classList.remove('hide');
	// 					v.classList.add('show');
	// 				} else {
	// 					v.classList.remove('show');
	// 					v.classList.add('hide');
	// 				}
	// 			});
	// 		} else if (selectedValue === 'credit-sales') {
	// 			productTableRows.forEach(function (v) {
	// 				let typeOfSsale = v.querySelector('.sales_type').innerText;
	// 				if (typeOfSsale == "Credit Sale") {
	// 					v.classList.remove('hide');
	// 					v.classList.add('show');
	// 				} else {
	// 					v.classList.remove('show');
	// 					v.classList.add('hide');
	// 				}
	// 			});
	// 		}
	// 	});
	// }


});


