<?php
if (!defined('ABSPATH')) { exit; }

/** Create a due record from a sale (call this when a sale is saved if paid < total) */
function fbm_dues_create_from_sale($referer_id, $customer_saler_id, $total_amount, $paid_amount, $due_type) {
    global $wpdb;
    $table_dues = $wpdb->prefix . 'sms_dues';
    $table_payments = $wpdb->prefix . 'sms_dues_payments';

    $remaining = max(0, (float)$total_amount - (float)$paid_amount);
    if ($remaining <= 0) { return 0; }

    $due_args = [
        'referer_id'          => (int)$referer_id,
        'customer_saler_id'    => sanitize_text_field($customer_saler_id), // storing ID not name of customer/sales man
        'total_amount'     => (float)$total_amount,
        'paid_amount'      => (float)$paid_amount,
        'remaining_amount' => (float)$remaining,
        'status'           => $remaining > 0 ? 'open' : 'closed',
        'due_type'         =>  $due_type,
        'created_at'       => current_time('mysql'),
        'updated_at'       => current_time('mysql'),
    ];
    // return $due_args;
    // Insert into dues table
    // $dues_sql = $wpdb->prepare('');
    $wpdb->insert($table_dues, $due_args);

    $due_id = $wpdb->insert_id;

    // ✅ Insert the upfront payment as the first payment record
    if ($paid_amount > 0) {
        $wpdb->insert($table_payments, [
            'due_id'         => $due_id,
            'payment_amount' => $paid_amount,
            'payment_date'   => current_time('mysql'),
            'note'           => "Initial payment at the time of {$due_type}",
        ]);
    }

    return $due_id;
}


/** Fetch dues with optional status filter */
// function fbm_dues_get_all($status = 'open', $search = '', $due_type = '') {
//     global $wpdb;
//     $table_dues     = $wpdb->prefix . "sms_dues";
//     $table_customer = $wpdb->prefix . "sms_customers";

//     $where = "1=1";
//     $params = [];

//     // Status filter
//     if ($status !== 'all') {
//         $where .= " AND d.status = %s";
//         $params[] = $status;
//     }

//     // Search filter
//     if (!empty($search)) {
//         $like = '%' . $wpdb->esc_like($search) . '%';
//         $where .= " AND (c.name LIKE %s OR d.referer_id LIKE %s)";
//         $params[] = $like;
//         $params[] = $like;
//     }

//     // Due Type filter
//     if ($due_type !== '') {
//         $where .= " AND d.due_type = %s";
//         $params[] = $due_type;
//     }

//     $sql = "
//         SELECT d.*, c.name, c.phone
//         FROM $table_dues d
//         LEFT JOIN $table_customer c ON d.customer_saler_id = c.customer_id
//         WHERE $where
//         ORDER BY d.id DESC
//     ";

//     return !empty($params)
//         ? $wpdb->get_results($wpdb->prepare($sql, $params))
//         : $wpdb->get_results($sql);
// }

// function fbm_dues_get_all($status = 'open', $search = '', $due_type = '') {
//     global $wpdb;

//     $table_dues      = $wpdb->prefix . "sms_dues";
//     $table_customer  = $wpdb->prefix . "sms_customers";
//     $table_salemans  = $wpdb->prefix . "sms_salemans";

//     $where  = "1=1";
//     $params = [];

//     // Filter: status
//     if ($status !== 'all') {
//         $where .= " AND d.status = %s";
//         $params[] = $status;
//     }

//     // Filter: search by customer OR salesman
//     if (!empty($search)) {
//         $like = '%' . $wpdb->esc_like($search) . '%';

//         $where .= " AND (
//             c.name LIKE %s
//             OR s.name LIKE %s
//         )";

//         $params[] = $like; // customer name
//         $params[] = $like; // salesman name
//     }

//     // Filter: due type
//     if ($due_type !== '') {
//         $where .= " AND d.due_type = %s";
//         $params[] = $due_type;
//     }

//     // Query
//     $sql = "
//         SELECT 
//             d.*,
//             c.name AS customer_name,
//             c.phone AS customer_phone,
//             s.name AS salesman_name,
//             s.phone AS salesman_phone
//         FROM $table_dues d
//         LEFT JOIN $table_customer c ON d.customer_saler_id = c.customer_id
//         LEFT JOIN $table_salemans s ON d.customer_saler_id = s.id
//         WHERE $where
//         ORDER BY d.id DESC
//     ";

//     return !empty($params)
//         ? $wpdb->get_results($wpdb->prepare($sql, $params))
//         : $wpdb->get_results($sql);
// }

function fbm_dues_get_all($status = 'open', $search = '', $due_type = '') {
    global $wpdb;

    $table_dues      = $wpdb->prefix . "sms_dues";
    $table_customer  = $wpdb->prefix . "sms_customers";
    $table_salemans  = $wpdb->prefix . "sms_salemans";

    $where  = "1=1";
    $params = [];

    // Status filter
    if ($status !== 'all') {
        $where .= " AND d.status = %s";
        $params[] = $status;
    }

    // Search only customer name and salesman name
    if (!empty($search)) {
        $like = '%' . $wpdb->esc_like($search) . '%';

        $where .= " AND (
            c.name LIKE %s
            OR s.name LIKE %s
        )";

        $params[] = $like; // customer name
        $params[] = $like; // salesman name
    }

    // Due type
    if ($due_type !== '') {
        $where .= " AND d.due_type = %s";
        $params[] = $due_type;
    }

    // Final query
    $sql = "
        SELECT 
            d.*,
            c.name AS customer_name,
            c.phone AS customer_phone,
            s.name AS salesman_name,
            s.phone AS salesman_phone
        FROM $table_dues d
        LEFT JOIN $table_customer c ON d.customer_saler_id = c.customer_id
        LEFT JOIN $table_salemans s ON d.customer_saler_id = s.id
        WHERE $where
        ORDER BY d.id DESC
    ";
    $query = !empty($params) ? $wpdb->prepare($sql, $params) : $sql;
    $dues = $wpdb->get_results($query);
    return $dues;
}




/** Get a single due */
function fbm_dues_get($due_id){
    global $wpdb; $table = $wpdb->prefix . 'sms_dues';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $due_id));
}
// Get the due by sale_id
function fbm_get_due_by_referer_id($sale_id){
    global $wpdb; $table = $wpdb->prefix . 'sms_dues';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE referer_id = %d", $sale_id));
}

/** Record a payment against a due */
function fbm_dues_add_payment($due_id, $amount, $note = '', $due_type = 'sale'){
    global $wpdb;
    $due_table = $wpdb->prefix . 'sms_dues';
    $pay_table = $wpdb->prefix . 'sms_dues_payments';
    $table_purchases = $wpdb->prefix . 'sms_purchases';

    $due = fbm_dues_get($due_id);
    if (!$due){ return new WP_Error('not_found', 'Due not found'); }

    $amount = (float)$amount;
    if ($amount <= 0){ return new WP_Error('invalid_amount', 'Payment must be greater than 0'); }

    // Insert payment row
    $ok = $wpdb->insert($pay_table, [
        'due_id'         => (int)$due_id,
        'payment_amount' => $amount,
        'payment_date'   => current_time('mysql'),
        'note'           => sanitize_textarea_field($note)
    ], ["%d","%f","%s","%s"]);

    if (!$ok){ return new WP_Error('db', 'Could not insert payment'); }

    // Update master due
    $new_paid = (float)$due->paid_amount + $amount;
    $new_remaining = max(0, (float)$due->total_amount - $new_paid);
    $new_status = $new_remaining <= 0 ? 'closed' : 'open';

    $wpdb->update($due_table, [
            'paid_amount'      => $new_paid,
            'remaining_amount' => $new_remaining,
            'status'           => $new_status,
            'updated_at'       => current_time('mysql'),
        ], 
        [
            'id' => (int)$due_id
        ], 
        ["%f","%f","%s","%s"], 
        ["%d"]
    );

    // Update purchase table when received payment is Purchase's due payment
    if($due_type === 'purchase'):
        $purchase_id = $due->referer_id;
        $purchase = get_purchase_by_id($purchase_id);
        if (!$purchase){ return new WP_Error('not_found', 'Purchase not found'); }

        $new_paid = (int)$purchase->paid + $amount;
        $new_remaining = max(0, (int)$purchase->total_payment - $new_paid);
        $old_status = $purchase->payment_status;
        $new_status = $new_remaining <= 0 ? 'Paid' : $old_status;

        $wpdb->update($table_purchases, [
                'paid'      => $new_paid,
                'due' => $new_remaining,
                'payment_status'           => $new_status,
            ], 
            [
                'purchase_id' => (int)$purchase_id
            ], 
            ["%d","%d","%s","%s"], 
            ["%d"]
        );
    endif;
    return true;
}

/** Get payment history for a due */
function fbm_dues_get_payments($due_id){
    global $wpdb; $table = $wpdb->prefix . 'sms_dues_payments';
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE due_id = %d ORDER BY id DESC", $due_id));
}


add_action('admin_post_fbm_receive_due_payment', 'fbm_handle_receive_due_payment');
function fbm_handle_receive_due_payment(){
    if (!current_user_can('manage_options')){ wp_die('Permission denied'); }

    $nonce_ok = isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'fbm_receive_due_payment');
    if (!$nonce_ok){ wp_die('Security check failed'); }

    $due_id = isset($_POST['due_id']) ? (int)$_POST['due_id'] : 0;
    $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
    $due_type = isset($_POST['due_type']) ? sanitize_text_field($_POST['due_type']) : 'sale';
    $note   = isset($_POST['note']) ? sanitize_textarea_field($_POST['note']) : '';

    if ($due_id <= 0 || $amount <= 0){
        wp_redirect(add_query_arg(['page'=>'pending-payments','status'=>'open','msg'=>'invalid'], admin_url('admin.php')));
        exit;
    }

    $res = fbm_dues_add_payment($due_id, $amount, $note, $due_type);
    if (is_wp_error($res)){
        wp_redirect(add_query_arg(['page'=>'pending-payments','status'=>'open','msg'=>$res->get_error_code()], admin_url('admin.php')));
        exit;
    }

    wp_redirect(add_query_arg(['page'=>'pending-payments','status'=>'open','msg'=>'saved'], admin_url('admin.php')));
    exit;
}