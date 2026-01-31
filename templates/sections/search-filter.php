<?php 
    $screen = get_current_screen();
    $current_page_id = $screen->id;
    $pendingPaymentsPageId = 'inventory-system_page_pending-payments';
    $inventry_page_id = 'toplevel_page_inventory-system';
?>

<?php if($current_page_id === $pendingPaymentsPageId): ?>

    <div id="pendingPaymentsFilterArea">
        <div>Filter by Due Type:</div>
        <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
            <input type="hidden" name="action" value="get_filtered_pending_payments" />
            <select id="searchFilterType" name="due_type">
                <option value="">All Due Types</option>
                <option value="Purchase">Purchase</option>
                <option value="Sale">Sale</option>
            </select>
            <input type="submit" value="Filter" />
        </form>        
    </div>

<?php elseif ($current_page_id === $inventry_page_id) : ?>
    
    <div id="searchFilter">
        <div>Search by:</div>
        <select id="searchFilterType">
            <option value="product_name">Product Name</option>
            <option value="product_manufacturer">Product Manufacturer</option>
        </select>
        <input type="search" name="" id="search-product" placeholder="Search Product Name">
    </div>    

<?php endif; ?>
