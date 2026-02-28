<?php 
if (!defined('ABSPATH')) { exit; }
if (!current_user_can('manage_options')){ wp_die('You do not have permission.'); }

$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'open';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$dues = fbm_dues_get_all($status, $search, '');
$nonce_action = 'fbm_receive_due_payment';
?>
<div id="page-pending_payments" class="wrap">
  <p>Manage open dues for both sales (customer recoveries) and purchases (vendor payments).</p>
  <?php
    include_component('search-filter', [
      'options' => [
        'customer_name' => 'Customer Name',
        'vendor_name' => 'Vendor',
        'invoice_no' => 'Invoice',
        'phone' => 'Phone',
      ],
      'row_to_skip' => 'payments-row' // Add a class to identify rows to skip during search
    ]);
   ?>
  <br>
  <div class="table-top d-flex justify-content-between">
    <ul class="subsubsub">
      <li><a href="<?php echo admin_url('admin.php?page=pending-payments&status=open'); ?>" class="<?php echo $status==='open'?'current':''; ?>">Open</a> | </li>
      <li><a href="<?php echo admin_url('admin.php?page=pending-payments&status=closed'); ?>" class="<?php echo $status==='closed'?'current':''; ?>">Closed</a> | </li>
      <li><a href="<?php echo admin_url('admin.php?page=pending-payments&status=all'); ?>" class="<?php echo $status==='all'?'current':''; ?>">All</a></li>
    </ul>
    <!-- Search filter -->
    <div id="pendingPaymentsFilterArea">
        <div>Filter by Due Type:</div>
        <form method="post">
            <?php
                $due_type_filter = '';
                if(isset($_POST['dueFilterSubmit'])):
                    $due_type_filter = strtolower(sanitize_text_field($_POST['due_type']));
                    if($due_type_filter !== ''):
                        $dues = fbm_dues_get_all($status, $search, $due_type_filter);
                    endif;
                endif;

            ?>
            <select id="pendingDueTypeFilter" name="due_type">
                <option value="" <?php echo $due_type_filter === '' ? 'selected' : '' ?>>All Due Types</option>
                <option value="purchase" <?php echo $due_type_filter === 'purchase' ? 'selected' : '' ?>>Purchase</option>
                <option value="sale" <?php echo $due_type_filter === 'sale' ? 'selected' : '' ?>>Sale</option>
            </select>
            <input type="submit" name="dueFilterSubmit" value="Filter" />
        </form>        
    </div>
    <!-- End search filter -->
  </div>
  <div class="table-wrap">
    <table class="pending_payments_table widefat striped" style="margin-top:12px;">
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Vendor</th>
          <th>Invoice</th>
          <th>Phone</th>
          <th>Total</th>
          <th>Paid</th>
          <th>Remaining</th>
          <th>Status</th>
          <th>Updated</th>
          <th>Due Type</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$dues): ?>
        <tr><td colspan="12">No records found.</td></tr>
      <?php else: $i = 1; foreach ($dues as $d):
          $id = $d->id;
          $record_due_type = strtolower((string)$d->due_type);
          $customer_name = $record_due_type === 'sale' ? ($d->customer_name ?: "--") : "--";
          $vendor_name = $record_due_type === 'purchase' ? ($d->purchase_vendor ?: "--") : "--";
          $invoice_no = '--';
          $invoice_link = '';
          if ($record_due_type === 'sale') {
              $invoice_no = $d->sale_invoice_no ?: '--';
              $customer_id = $d->sale_customer_id ? intval($d->sale_customer_id) : 0;
              if ($invoice_no !== '--' && $customer_id > 0) {
                  $invoice_link = admin_url('admin.php?page=invoice_details&invoice_no=' . $invoice_no . '&customer_id=' . $customer_id);
              }
          } else if ($record_due_type === 'purchase') {
              $invoice_no = $d->purchase_invoice_no ?: '--';
              if ($invoice_no !== '--') {
                  $invoice_link = admin_url('admin.php?page=purchase_invoice_details&invoice_no=' . $invoice_no);
              }
          }
          $phone = $record_due_type === 'sale' ? ($d->customer_phone ?: "N/A") : "N/A";
          $total = number_format((float)$d->total_amount, 2);
          $paid_amount = number_format((float)$d->paid_amount, 2);
          $remaining_amount = number_format((float)$d->remaining_amount, 2);
          $record_status = ucfirst($d->status);
          $updated_date = $d->updated_at;
        ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td class="customer_name"><?php echo esc_html($customer_name); ?></td>
          <td class="vendor_name"><?php echo esc_html($vendor_name); ?></td>
          <td class="invoice_no">
            <?php if ($invoice_link): ?>
              <a href="<?php echo esc_url($invoice_link); ?>"><?php echo esc_html($invoice_no); ?></a>
            <?php else: ?>
              <?php echo esc_html($invoice_no); ?>
            <?php endif; ?>
          </td>
          <td class="phone"><?php echo esc_html($phone); ?></td>
          <td><?php echo $total; ?></td>
          <td><?php echo $paid_amount; ?></td>
          <td><strong><?php echo $remaining_amount; ?></strong></td>
          <td><?php echo esc_html($record_status); ?></td>
          <td><?php echo esc_html($updated_date); ?></td>
          <td><?php echo ucwords(esc_html($record_due_type)); ?></td>
          <td>
            <?php if ($record_status === 'Open'): ?>
            <button class="button button-primary" type="button" onclick="fbmOpenReceiveModal(<?php echo $id; ?>, <?php echo esc_js((float)$d->remaining_amount); ?>, '<?php echo esc_js($record_due_type); ?>')"><?php echo $record_due_type === 'purchase' ? "Pay" : "Receive"; ?> Payment</button>
            <?php else: ?>
            <em>Closed</em>
            <?php endif; ?>
          </td>
        </tr>
        <tr id="payments-row-<?php echo $id; ?>" class="payments-row" style="display:none;">
          <td colspan="12">
            <h6><strong>Payment History</strong></h6>
            <div class="payments" data-due="<?php echo $id; ?>"></div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div id="receive-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:9999;">
  <div style="background:#fff; width:480px; margin:10% auto; padding:20px; border-radius:8px;">
    <h2 id="receive-modal-title">Receive Payment</h2>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
      <input type="hidden" name="action" value="fbm_receive_due_payment"/>
      <input type="hidden" name="due_id" id="fbm_due_id" value=""/>
      <input type="hidden" name="due_type" id="fbm_due_type" value=""/>
      <?php wp_nonce_field($nonce_action, '_wpnonce'); ?>
      <p>
        <label id="fbm_amount_label">Amount to receive now</label><br/>
        <input type="number" min="0" name="amount" id="fbm_amount" class="regular-text" required />
      </p>
      <p>
        <label>Note (optional)</label><br/>
        <textarea name="note" class="large-text" rows="3"></textarea>
      </p>
      <p>
        <button type="submit" id="fbm_submit_btn" class="button button-primary">Save Payment</button>
        <button type="button" class="button" onclick="document.getElementById('receive-modal').style.display='none'">Cancel</button>
      </p>
    </form>
  </div>
</div>

<script>
function fbmOpenReceiveModal(dueId, remaining, dueType) {
  var normalizedDueType = String(dueType || '').toLowerCase();
  var isPurchase = normalizedDueType === 'purchase';

  document.getElementById('fbm_due_id').value = dueId;
  var input = document.getElementById('fbm_amount');
  input.value = remaining;
  input.setAttribute('max', remaining);
  input.setAttribute('min', 1);
  document.getElementById('fbm_due_type').value = normalizedDueType;
  document.getElementById('receive-modal-title').textContent = isPurchase ? 'Pay Vendor' : 'Receive Payment';
  document.getElementById('fbm_amount_label').textContent = isPurchase ? 'Amount to pay now' : 'Amount to receive now';
  document.getElementById('fbm_submit_btn').textContent = isPurchase ? 'Save Payment' : 'Save Recovery';
  document.getElementById('receive-modal').style.display = 'block';
}
</script>
