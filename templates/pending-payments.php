<?php 
if (!defined('ABSPATH')) { exit; }
if (!current_user_can('manage_options')){ wp_die('You do not have permission.'); }

$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'open';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$dues = fbm_dues_get_all($status, $search, '');
$nonce_action = 'fbm_receive_due_payment';
?>
<div class="wrap">
  <p>Manage customers who have remaining balances from previous sales.</p>
  <form method="get" action="">
      <input type="hidden" name="page" value="pending-payments">
      <input type="text" name="s" value="<?php echo isset($_GET['s']) ? esc_attr($_GET['s']) : ''; ?>" placeholder="Search by Customer or Invoice..." style="width:300px;">
      <button type="submit" class="button">Search</button>
  </form>
  <br>
  <div class="table-top">
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
                $due_type = null;
                if(isset($_POST['dueFilterSubmit'])):
                    $due_type = sanitize_text_field($_POST['due_type']);
                    if($due_type):
                        $dues = fbm_dues_get_all($status, $search, $due_type);
                    endif;
                endif;

            ?>
            <select id="searchFilterType" name="due_type">
                <option value="" <?php echo $due_type === null ? 'selected' : '' ?>>All Due Types</option>
                <option value="Purchase" <?php echo $due_type === 'Purchase' ? 'selected' : '' ?>>Purchase</option>
                <option value="Sale" <?php echo $due_type === 'Sale' ? 'selected' : '' ?>>Sale</option>
            </select>
            <input type="submit" name="dueFilterSubmit" value="Filter" />
        </form>        
    </div>
    <!-- End search filter -->
  </div>
  <table class="pending_payments_table widefat striped" style="margin-top:12px;">
    <thead>
      <tr>
        <th>#</th>
        <th>Customer/Sales Man</th>
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
      <tr><td colspan="9">No records found.</td></tr>
    <?php else: $i = 1; foreach ($dues as $d): 
        $id = $d->id;
        // $phone = $d->phone == 0 ? "N/A" : $d->phone;
        $phone = $due_type === 'Sale'
                ? ($d->customer_phone ?: "N/A")
                : ($d->salesman_phone ?: "N/A");

        $customer_saler_id = $d->customer_saler_id;
        $total = number_format((float)$d->total_amount, 2);
        $paid_amount = number_format((float)$d->paid_amount, 2);
        $remaining_amount = number_format((float)$d->remaining_amount, 2);
        $status = ucfirst($d->status);
        $updated_date = $d->updated_at;
        $due_type = $d->due_type;
        
        $customer_saler_name = $due_type === 'sale' ? get_customer($customer_saler_id)->name : get_saleman($customer_saler_id)->name;
      ?>
      <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo $customer_saler_name; ?></td>
        <td><?php echo $phone; ?></td>
        <td><?php echo $total; ?></td>
        <td><?php echo $paid_amount; ?></td>
        <td><strong><?php echo $remaining_amount; ?></strong></td>
        <td><?php echo esc_html($status); ?></td>
        <td><?php echo esc_html($updated_date); ?></td>
        <td><?php echo ucwords(esc_html($due_type)); ?></td>

        <td>
          <?php if ($status === 'Open'): ?>
          <button class="button button-primary" type="button" onclick="fbmOpenReceiveModal(<?php echo $id; ?>, <?php echo esc_js((float)$d->remaining_amount); ?>, '<?php echo $due_type; ?>')">Receive Payment</button>
          <?php else: ?>
          <em>Closed</em>
          <?php endif; ?>
        </td>
      </tr>
      <tr class="payments-row" id="payments-row-<?php echo $id; ?>" style="display:none;">
        <td colspan="9">
          <h6><strong>Payment History</strong></h6>
          <div class="payments" data-due="<?php echo $id; ?>"></div>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<div id="receive-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:9999;">
  <div style="background:#fff; width:480px; margin:10% auto; padding:20px; border-radius:8px;">
    <h2>Receive Payment</h2>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
      <input type="hidden" name="action" value="fbm_receive_due_payment"/>
      <input type="hidden" name="due_id" id="fbm_due_id" value=""/>
      <input type="hidden" name="due_type" id="fbm_due_type" value=""/>
      <?php wp_nonce_field($nonce_action, '_wpnonce'); ?>
      <p>
        <label>Amount to receive now</label><br/>
        <input type="number" min="0" name="amount" id="fbm_amount" class="regular-text" oninput="this.value = Math.abs(this.value)" required />
      </p>
      <p>
        <label>Note (optional)</label><br/>
        <textarea name="note" class="large-text" rows="3"></textarea>
      </p>
      <p>
        <button type="submit" class="button button-primary">Save Payment</button>
        <button type="button" class="button" onclick="document.getElementById('receive-modal').style.display='none'">Cancel</button>
      </p>
    </form>
  </div>
</div>

<script>
function fbmOpenReceiveModal(dueId, remaining, dueType) {
  document.getElementById('fbm_due_id').value = dueId;
  var input = document.getElementById('fbm_amount');
  input.value = remaining;
  input.setAttribute('max', remaining);
  input.setAttribute('min', 1);
  document.getElementById('fbm_due_type').value = dueType;
  document.getElementById('receive-modal').style.display = 'block';
}
</script>