jQuery(document).ready($ => {
    const ajaxUrl = fbm_ajax.url,
        nonce = fbm_ajax.nonce,
        path = fbm_ajax.path;

    var notice = $('#fbm_notice');

    // ── Payment Status change handler ──────────────────────────────────────
    $(document).on('change', 'table.anpPurchaseInfoTable select.payment_status', function(e){
        let $this = $(this),
            paymentStatus = $this.val(),
            rowContainer = $this.closest('tr').parent(),
            totalPayment = +rowContainer.find('input#anpTotalCartAmount').val(),
            paymentMethodRow = rowContainer.children('tr.anpRowPaymentMethod'),
            paymentMethodSelect = paymentMethodRow.find('select.payment_method'),
            paymentMethodWrap = paymentMethodRow.find('.anpFieldWrap'),
            partiallyPaid = rowContainer.children('tr.anpRowPartiallyPaid').find('.anpFieldsContainer');

        if('Partially Paid' === paymentStatus){
            partiallyPaid.slideDown();
            paymentMethodWrap.slideDown();
            // Enable payment method
            paymentMethodSelect.prop('disabled', false).css('opacity', '1');
        } else if('unpaid' === paymentStatus.toLowerCase()){
            partiallyPaid.slideUp();
            // Hide AND disable payment method for Unpaid
            paymentMethodWrap.slideUp();
            paymentMethodSelect.prop('disabled', true).css('opacity', '0.5');

            partiallyPaid.find('input#anpPaid').val(0).attr('value', 0);
            partiallyPaid.find('input#anpRemaining').val(totalPayment).attr('value', totalPayment);
        } else if('Paid' === paymentStatus){
            partiallyPaid.slideUp();
            paymentMethodWrap.slideDown();
            // Enable payment method
            paymentMethodSelect.prop('disabled', false).css('opacity', '1');

            partiallyPaid.find('input#anpPaid').val(totalPayment).attr('value', totalPayment);
            partiallyPaid.find('input#anpRemaining').val(0).attr('value', 0);
        }
    });

    // ── Show P = X, S = Y rate badges when product is selected ────────────
    $(document).on('change', '.purchase_form_fields_group select.product_id', function(){
        let $this = $(this),
            productId = $this.val(),
            $group = $this.closest('.purchase_form_fields_group');

        if(!productId) return;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_product_rates',
                nonce: nonce,
                product_id: productId
            },
            success: function(res){
                if(res.success && res.data){
                    let pRate = res.data.purchase_rate || '—';
                    let sRate = res.data.sale_rate || '—';
                    $group.find('.badge_p_val').text(pRate);
                    $group.find('.badge_s_val').text(sRate);
                    $group.find('input.purchase_rate').val(pRate);
                }
            }
        });
    });

    // ── Auto-generate Expiry Batch when Expiry Date changes ───────────────
    $(document).on('change', '.purchase_form_fields_group input.expiry_date', function(){
        let $this = $(this),
            $group = $this.closest('.purchase_form_fields_group'),
            dateVal = $this.val(); // format: YYYY-MM

        if(dateVal){
            // Format: EXP-YYYYMM-XXXX (random 4-digit suffix)
            let suffix = Math.floor(1000 + Math.random() * 9000);
            let batchNo = 'EXP-' + dateVal.replace('-', '') + '-' + suffix;
            $group.find('input.expiry_batch').val(batchNo);
        } else {
            $group.find('input.expiry_batch').val('');
        }
    });

    // ── Adding new purchase ────────────────────────────────────────────────
    $(document).on('click', '#add_stock', handleAddPurchaseFormSubmit);

    // ── Prevent overpayment + update remaining ─────────────────────────────
    $(document).on('input', 'input#anpPaid', function(e){
        let $this = $(this),
            parentTable = $this.closest('table'),
            totalPayment = parentTable.find('input#anpTotalCartAmount').val(),
            paidAmount = $this.val();

        preventOverpayment($this, paidAmount, totalPayment);
        let remainingAmountInput = parentTable.find('input#anpRemaining');
        paidAmount = $this.val();
        let remainingAmount = totalPayment - paidAmount;
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