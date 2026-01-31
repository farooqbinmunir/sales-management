<?php 
    require_once(FBM_PLUGIN_DIR . 'inc/functions.php');
    $limit_top_selling_product = -1; // -1 means all
    $limit_most_profitable_products = -1; // -1 means all
    $limit_low_stock_alerts = -1; // -1 means all
    $limit_recent_sales = 5; // last 5
 ?>
<div class="wrap">
    
    <div class="fbm-analytics-grid">
        <!-- Top Selling Products -->
        <div class="fbm-analytics-card">
            <h3>🏆 Top Selling Products</h3>
            <?php fbm_display_top_selling_products($limit_top_selling_product); ?>
        </div>
        
        <!-- Most Profitable Products -->
        <div class="fbm-analytics-card">
            <h3>💰 Most Profitable Products</h3>
            <?php fbm_display_most_profitable_products($limit_most_profitable_products); ?>
        </div>
        
        <!-- Low Stock Alerts -->
        <div class="fbm-analytics-card">
            <h3>⚠️ Low Stock Alerts</h3>
            <?php fbm_display_low_stock_products($limit_low_stock_alerts); ?>
        </div>

        <div class="fbm-analytics-card">
            <h3>🔄 Recent Sales</h3>
            <?php fbm_display_recent_sales($limit_recent_sales); ?>
        </div>

        <div class="fbm-chart-row">
            <div class="fbm-chart-container fbm-analytics-card">
                <h3>📊 Monthly Sales</h3>
                <canvas id="fbmMonthlySalesChart" height="250"></canvas>
            </div>
            <div class="fbm-chart-container fbm-analytics-card">
                <h3>📈 Product Performance</h3>
                <canvas id="fbmProductPerformanceChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>