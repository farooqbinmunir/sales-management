<?php
if (!defined('ABSPATH')) exit;

class FBM_Assets {

    private $version;

    public function __construct() {

        $this->version = (defined('WP_DEBUG') && WP_DEBUG)
            ? time()
            : FBM_PLUGIN_VERSION;

        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets($hook) {

        // Only load on FBM plugin pages
        if (strpos($hook, 'fbm_') === false) {
            return;
        }

        $this->enqueue_styles();
        $this->enqueue_scripts();
        $this->localize_core();
    }

    /* ----------------------------------
       STYLES
    -----------------------------------*/
    private function enqueue_styles() {

        $styles = [

            // Core
            'fbm-backend' => [
                'src'  => FBM_PLUGIN_URL . '/assets/css/backend/backend.css',
            ],

            'fbm-core' => [
                'src'  => FBM_PLUGIN_URL . '/assets/css/backend/fbm.css',
            ],

            'popup-auth-css' => [
                'src'  => FBM_PLUGIN_URL . '/components/popup-auth/popup-auth.css',
            ],

            // Vendors
            'bootstrap' => [
                'src'  => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
                'ver'  => '5.3.3',
            ],

            'fontawesome' => [
                'src'  => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
                'ver'  => '6.5.1',
            ],

            'select2' => [
                'src'  => FBM_PLUGIN_URL . '/assets/css/backend/select2.css',
                'ver'  => '4.1.0',
            ],
        ];

        foreach ($styles as $handle => $style) {

            wp_enqueue_style(
                $handle,
                $style['src'],
                $style['deps'] ?? [],
                $style['ver'] ?? $this->version,
                $style['media'] ?? 'all'
            );
        }
    }

    /* ----------------------------------
       SCRIPTS
    -----------------------------------*/
    private function enqueue_scripts() {

        $scripts = [

            'fbm-functions' => [
                'src'  => FBM_PLUGIN_URL . '/assets/js/backend/functions.js',
                'deps' => ['jquery'],
            ],

            'fbm-backend' => [
                'src'  => FBM_PLUGIN_URL . '/assets/js/backend/backend.js',
                'deps' => ['jquery', 'fbm-functions'],
            ],

            'fbm-fbm' => [
                'src'  => FBM_PLUGIN_URL . '/assets/js/backend/fbm.js',
                'deps' => ['jquery', 'fbm-functions'],
            ],

            'fbm-key-events' => [
                'src'  => FBM_PLUGIN_URL . '/assets/js/backend/key-events.js',
                'deps' => ['jquery'],
            ],

            'fbm-fixes' => [
                'src'  => FBM_PLUGIN_URL . '/assets/js/backend/fixes.js',
                'deps' => ['jquery'],
            ],

            'popup-auth-js' => [
                'src'  => FBM_PLUGIN_URL . '/components/popup-auth/popup-auth.js',
                'deps' => ['jquery'],
            ],

            'fbm-print-sale' => [
                'src'  => FBM_PLUGIN_URL . '/assets/js/backend/print-sale-invoice.js',
                'deps' => ['jquery'],
            ],

            'fbm-print-purchase' => [
                'src'  => FBM_PLUGIN_URL . '/assets/js/backend/print-purchase-invoice.js',
                'deps' => ['jquery'],
            ],

            'fbm-returns' => [
                'src'  => FBM_PLUGIN_URL . '/assets/js/backend/returns.js',
                'deps' => ['jquery'],
            ],

            'fbm-purchase' => [
                'src'  => FBM_PLUGIN_URL . '/assets/js/backend/purchase.js',
                'deps' => ['jquery'],
            ],

            'search-field' => [
                'src'  => FBM_PLUGIN_URL . '/components/search-field/search-field.js',
                'deps' => ['jquery'],
            ],

            'search-filter' => [
                'src'  => FBM_PLUGIN_URL . '/components/search-filter/search-filter.js',
                'deps' => ['jquery'],
            ],

            'bootstrap' => [
                'src'     => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
                'ver'     => '5.3.3',
                'footer'  => true,
            ],

            'chartjs' => [
                'src'     => 'https://cdn.jsdelivr.net/npm/chart.js',
                'ver'     => '4.4.0',
                'footer'  => true,
            ],

            'select2' => [
                'src'     => FBM_PLUGIN_URL . '/assets/js/backend/select2.js',
                'deps'    => ['jquery'],
                'ver'     => '4.1.0',
                'footer'  => true,
            ],

            'fbm-analytics' => [
                'src'     => FBM_PLUGIN_URL . '/assets/js/backend/analytics-charts.js',
                'deps'    => ['chartjs'],
            ]
        ];

        foreach ($scripts as $handle => $script) {

            wp_enqueue_script(
                $handle,
                $script['src'],
                $script['deps'] ?? [],
                $script['ver'] ?? $this->version,
                $script['footer'] ?? true
            );
        }

        // Localize analytics only if loaded
        if (wp_script_is('fbm-analytics', 'enqueued')) {

            wp_localize_script('fbm-analytics', 'fbmChartData', [
                'monthlySales'      => fbm_get_monthly_sales_data(),
                'productPerformance'=> fbm_get_product_performance_data()
            ]);
        }
    }

    /* ----------------------------------
       LOCALIZATION
    -----------------------------------*/
    private function localize_core() {

        $current_user = wp_get_current_user();

        wp_localize_script('fbm-backend', 'fbm_ajax', [
            'url'         => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce(FBM_PLUGIN_NONCE),
            'siteUrl'     => site_url(),
            'currentUser' => $current_user->display_name,
        ]);
    }
}
