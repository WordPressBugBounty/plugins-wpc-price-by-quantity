<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wpcpq_Backend' ) ) {
    class Wpcpq_Backend {
        protected static $instance = null;

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct() {
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

            // Settings
            add_action( 'admin_init', [ $this, 'register_settings' ] );
            add_filter( 'pre_update_option', [ $this, 'last_saved' ], 10, 2 );
            add_action( 'admin_menu', [ $this, 'admin_menu' ] );

            // Links
            add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );
            add_filter( 'plugin_row_meta', [ $this, 'row_meta' ], 10, 2 );

            // AJAX
            add_action( 'wp_ajax_wpcpq_add_role_price', [ $this, 'ajax_add_role_price' ] );

            // Search Term
            add_action( 'wp_ajax_wpcpq_search_term', [ $this, 'ajax_search_term' ] );

            // Single Product
            add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_data_tabs' ] );
            add_action( 'woocommerce_product_data_panels', [ $this, 'product_data_panels' ] );

            // Variation
            add_action( 'woocommerce_product_after_variable_attributes', [ $this, 'variation_settings' ], 99, 3 );

            // WPC Variation Duplicator
            add_action( 'wpcvd_duplicated', [ $this, 'duplicate_variation' ], 99, 2 );

            // WPC Variation Bulk Editor
            add_action( 'wpcvb_bulk_update_variation', [ $this, 'bulk_update_variation' ], 99, 2 );

            // Product columns
            add_filter( 'manage_edit-product_columns', [ $this, 'product_columns' ] );
            add_action( 'manage_product_posts_custom_column', [ $this, 'custom_column' ], 10, 2 );

            // Overview
            add_action( 'wp_ajax_wpcpq_overview', [ $this, 'ajax_overview' ] );

            // Export
            add_filter( 'woocommerce_product_export_meta_value', [ $this, 'export_process' ], 10, 3 );

            // Import
            add_filter( 'woocommerce_product_import_pre_insert_product_object', [ $this, 'import_process' ], 10, 2 );
        }
        public function product_data_tabs( $tabs ) {
            $tabs['wpcpq'] = [
                    'label'  => esc_html__( 'Price by Quantity', 'wpc-price-by-quantity' ),
                    'target' => 'wpcpq_settings'
            ];

            return $tabs;
        }

        public function product_data_panels() {
            global $post, $thepostid, $product_object;

            if ( $product_object instanceof WC_Product ) {
                $product_id = $product_object->get_id();
            } elseif ( is_numeric( $thepostid ) ) {
                $product_id = $thepostid;
            } elseif ( $post instanceof WP_Post ) {
                $product_id = $post->ID;
            } else {
                $product_id = 0;
            }

            if ( ! $product_id ) {
                ?>
                <div id='wpcpq_settings' class='panel woocommerce_options_panel wpcpq_settings'>
                    <p style="padding: 0 12px; color: #c9356e"><?php esc_html_e( 'Product wasn\'t returned.', 'wpc-price-by-quantity' ); ?></p>
                </div>
                <?php
                return;
            }

            self::product_settings( $product_id );
        }

        function variation_settings( $loop, $variation_data, $variation ) {
            $variation_id = absint( $variation->ID );
            ?>
            <div class="form-row form-row-full wpcpq-variation-settings">
                <label><?php esc_html_e( 'WPC Price by Quantity', 'wpc-price-by-quantity' ); ?></label>
                <div class="wpcpq-variation-wrap wpcpq-variation-wrap-<?php echo esc_attr( $variation_id ); ?>">
                    <?php self::product_settings( $variation_id, true ); ?>
                </div>
            </div>
            <?php
        }

        function product_settings( $product_id, $is_variation = false ) {
            $enable = get_post_meta( $product_id, 'wpcpq_enable', true ) ?: ( $is_variation ? 'parent' : 'global' );
            $prices = (array) get_post_meta( $product_id, 'wpcpq_prices', true );
            $prices = ! empty( $prices ) ? Wpcpq_Helper()::format_prices( $prices ) : [
                    'all' => [
                            'tiers' => [
                                    [
                                            'quantity' => '',
                                            'price'    => '',
                                    ]
                            ]
                    ]
            ];

            $name  = '';
            $id    = 'wpcpq_settings';
            $class = 'panel woocommerce_options_panel wpcpq_settings';

            if ( $is_variation ) {
                $name  = '_v[' . $product_id . ']';
                $id    = '';
                $class = 'wpcpq_settings';
            }
            ?>
            <div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
                <?php do_action( 'wpcpq_product_settings_before', $product_id, $is_variation ); ?>
                <div class="wpcpq_settings_enable">
                    <div class="wpcpq_settings_enable_inner">
                        <span><?php esc_html_e( 'Price by Quantity', 'wpc-price-by-quantity' ); ?></span> <label>
                            <select name="<?php echo esc_attr( 'wpcpq_enable' . $name ); ?>" class="wpcpq_enable">
                                <option value="global" <?php selected( $enable, 'global' ); ?>><?php esc_html_e( 'Global', 'wpc-price-by-quantity' ); ?></option>
                                <option value="disable" <?php selected( $enable, 'disable' ); ?>><?php esc_html_e( 'Disable', 'wpc-price-by-quantity' ); ?></option>
                                <?php if ( $is_variation ) { ?>
                                    <option value="parent" <?php selected( $enable, 'parent' ); ?>><?php esc_html_e( 'Parent', 'wpc-price-by-quantity' ); ?></option>
                                <?php } ?>
                                <option value="override" <?php selected( $enable, 'override' ); ?>><?php esc_html_e( 'Override', 'wpc-price-by-quantity' ); ?></option>
                            </select> </label>
                    </div>
                    <div style="color: #c9356e; padding-left: 12px; padding-right: 12px;">
                        Settings at a product/variation basis only available on the Premium Version.
                        <a href="https://wpclever.net/downloads/wpc-price-by-quantity/?utm_source=pro&utm_medium=wpcpq&utm_campaign=wporg"
                           target="_blank">Click here</a> to buy, just $29!
                    </div>
                </div>
                <div class="wpcpq_settings_override"
                     style="display: <?php echo esc_attr( $enable === 'override' ? 'block' : 'none' ); ?>;">
                    <?php do_action( 'wpcpq_product_settings_override_before', $product_id, $is_variation ); ?>
                    <div class="wpcpq-items-wrapper">
                        <div class="wpcpq-items wpcpq-roles">
                            <?php
                            $active = true;

                            if ( is_array( $prices ) && ! empty( $prices ) ) {
                                if ( isset( $prices['all'] ) ) {
                                    // move old 'all' to the last for old version < 3.0
                                    $all = $prices['all'];
                                    unset( $prices['all'] );
                                    $prices['all'] = $all;
                                }

                                foreach ( $prices as $key => $price ) {
                                    include WPCPQ_DIR . 'includes/templates/role-price.php';
                                    $active = false;
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php include WPCPQ_DIR . 'includes/templates/add-new.php'; ?>
                    <?php do_action( 'wpcpq_product_settings_override_after', $product_id, $is_variation ); ?>
                </div>
                <?php do_action( 'wpcpq_product_settings_after', $product_id, $is_variation ); ?>
            </div>
            <?php
        }

        function duplicate_variation( $old_variation_id, $new_variation_id ) {
            if ( $enable = get_post_meta( $old_variation_id, 'wpcpq_enable', true ) ) {
                update_post_meta( $new_variation_id, 'wpcpq_enable', $enable );
            }

            if ( $rules = get_post_meta( $old_variation_id, 'wpcpq_prices', true ) ) {
                update_post_meta( $new_variation_id, 'wpcpq_prices', $rules );
            }
        }

        function bulk_update_variation( $variation_id, $fields ) {
            if ( ! empty( $fields['wpcpq_enable_v'] ) && ( $fields['wpcpq_enable_v'] !== 'wpcvb_no_change' ) ) {
                update_post_meta( $variation_id, 'wpcpq_enable', sanitize_text_field( $fields['wpcpq_enable_v'] ) );
            }

            if ( ! empty( $fields['wpcpq_enable_v'] ) && ( $fields['wpcpq_enable_v'] === 'override' ) && ! empty( $fields['wpcpq_prices_v'] ) ) {
                update_post_meta( $variation_id, 'wpcpq_prices', Wpcpq_Helper()::sanitize_array( $fields['wpcpq_prices_v'] ) );
            }
        }

        function register_settings() {
            // settings
            register_setting( 'wpcpq_settings', 'wpcpq_settings', [
                    'type'              => 'array',
                    'sanitize_callback' => [ 'Wpcpq_Helper', 'sanitize_array' ],
            ] );
            register_setting( 'wpcpq_settings', 'wpcpq_prices', [
                    'type'              => 'array',
                    'sanitize_callback' => [ 'Wpcpq_Helper', 'sanitize_array' ],
            ] );

            // localization
            register_setting( 'wpcpq_localization', 'wpcpq_localization', [
                    'type'              => 'array',
                    'sanitize_callback' => [ 'Wpcpq_Helper', 'sanitize_array' ],
            ] );
        }

        function last_saved( $value, $option ) {
            if ( $option == 'wpcpq_settings' || $option == 'wpcpq_localization' ) {
                $value['_last_saved']    = current_time( 'timestamp' );
                $value['_last_saved_by'] = get_current_user_id();
            }

            return $value;
        }

        public function admin_menu() {
            add_submenu_page( 'wpclever', 'WPC Price by Quantity', 'Price by Quantity', 'manage_options', 'wpclever-wpcpq', [
                    $this,
                    'admin_menu_content'
            ] );
        }

        public function admin_menu_content() {
            include WPCPQ_DIR . 'includes/templates/settings.php';
        }

        function action_links( $links, $file ) {
            static $plugin;

            if ( ! isset( $plugin ) ) {
                $plugin = plugin_basename( WPCPQ_FILE );
            }

            if ( $plugin === $file ) {
                $settings             = '<a href="' . esc_url( admin_url( 'admin.php?page=wpclever-wpcpq&tab=settings' ) ) . '">' . esc_html__( 'Settings', 'wpc-price-by-quantity' ) . '</a>';
                //$links['wpc-premium'] = '<a href="' . esc_url( admin_url( 'admin.php?page=wpclever-wpcpq&tab=premium' ) ) . '">' . esc_html__( 'Premium Version', 'wpc-price-by-quantity' ) . '</a>';
                array_unshift( $links, $settings );
            }

            return (array) $links;
        }

        function row_meta( $links, $file ) {
            static $plugin;

            if ( ! isset( $plugin ) ) {
                $plugin = plugin_basename( WPCPQ_FILE );
            }

            if ( $plugin === $file ) {
                $row_meta = [
                        'support' => '<a href="' . esc_url( WPCPQ_DISCUSSION ) . '" target="_blank">' . esc_html__( 'Community support', 'wpc-price-by-quantity' ) . '</a>',
                ];

                return array_merge( $links, $row_meta );
            }

            return (array) $links;
        }

        function ajax_add_role_price() {
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'wpcpq-security' ) ) {
                die( 'Permissions check failed!' );
            }

            $role      = sanitize_text_field( wp_unslash( $_POST['role'] ?? 'all' ) );
            $apply     = sanitize_text_field( wp_unslash( $_POST['apply'] ?? 'all' ) );
            $apply_val = sanitize_text_field( wp_unslash( $_POST['apply_val'] ?? '' ) );
            $method    = sanitize_text_field( wp_unslash( $_POST['method'] ?? 'volume' ) );
            $layout    = sanitize_text_field( wp_unslash( $_POST['layout'] ?? 'default' ) );
            $tiers     = isset( $_POST['tiers'] )
                ? map_deep( wp_unslash( $_POST['tiers'] ?? '' ), 'sanitize_text_field' )
                : [ [ 'quantity' => '', 'price' => '', 'text' => '' ] ];

            // variation id > 0
            $product_id   = absint( sanitize_text_field( wp_unslash( $_POST['id'] ?? 0 ) ) );
            $is_variation = $product_id > 0;
            $name         = $is_variation ? '_v[' . $product_id . ']' : '';

            if ( ! empty( $role ) ) {
                $key    = Wpcpq_Helper()::generate_key();
                $active = true;
                $price  = [
                        'role'      => $role,
                        'apply'     => $apply,
                        'apply_val' => $apply_val,
                        'method'    => $method,
                        'layout'    => $layout,
                        'tiers'     => $tiers
                ];
                include WPCPQ_DIR . 'includes/templates/role-price.php';
            }

            die;
        }

        function ajax_search_term() {
            if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_REQUEST['nonce'] ) ), 'wpcpq-security' ) ) {
                die( 'Permissions check failed!' );
            }

            $return = [];
            $args   = [
                    'taxonomy'   => sanitize_text_field( wp_unslash( $_REQUEST['taxonomy'] ?? '' ) ),
                    'orderby'    => 'id',
                    'order'      => 'ASC',
                    'hide_empty' => false,
                    'fields'     => 'all',
                    'name__like' => sanitize_text_field( wp_unslash( $_REQUEST['q'] ?? '' ) ),
            ];

            $terms = get_terms( $args );

            if ( count( $terms ) ) {
                foreach ( $terms as $term ) {
                    $return[] = [ $term->slug, $term->name ];
                }
            }

            wp_send_json( $return );
        }

        public function enqueue_scripts() {
            wp_enqueue_style( 'hint', WPCPQ_URI . 'assets/css/hint.css', [], WPCPQ_VERSION );
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'wpcpq-backend', WPCPQ_URI . 'assets/css/backend.css', [ 'woocommerce_admin_styles' ], WPCPQ_VERSION );
            wp_enqueue_script( 'wpcpq-backend', WPCPQ_URI . 'assets/js/backend.js', [
                    'jquery',
                    'wp-color-picker',
                    'jquery-ui-dialog',
                    'jquery-ui-sortable',
                    'wc-enhanced-select',
                    'selectWoo'
            ], WPCPQ_VERSION, true );
            wp_localize_script( 'wpcpq-backend', 'wpcpq_vars', [
                    'nonce'       => wp_create_nonce( 'wpcpq-security' ),
                    'hint_qty'    => esc_attr__( 'Quantity', 'wpc-price-by-quantity' ),
                    'hint_price'  => esc_attr__( 'Price', 'wpc-price-by-quantity' ),
                    'hint_text'   => esc_attr__( 'Leave empty to use the default text.', 'wpc-price-by-quantity' ),
                    'hint_remove' => esc_attr__( 'Remove', 'wpc-price-by-quantity' ),
                    'close'       => esc_attr__( 'Close', 'wpc-price-by-quantity' ),
            ] );
        }

        function product_columns( $columns ) {
            $columns['wpcpq'] = esc_html__( 'Price by Quantity', 'wpc-price-by-quantity' );

            return $columns;
        }

        function custom_column( $column, $postid ) {
            if ( $column === 'wpcpq' ) {
                $enable = get_post_meta( $postid, 'wpcpq_enable', true );

                if ( empty( $enable ) || $enable === 'global' ) {
                    echo '<a href="#" class="wpcpq_overview" data-pid="' . esc_attr( $postid ) . '" data-name="' . esc_attr( get_the_title( $postid ) ) . '" data-type="global"><span class="dashicons dashicons-money-alt"></span></a>';
                }

                if ( $enable === 'disable' ) {
                    echo '<span class="wpcpq_overview_disable"><span class="dashicons dashicons-money-alt"></span> ' . esc_html__( 'Disable', 'wpc-price-by-quantity' ) . '</span>';
                }

                if ( $enable === 'override' ) {
                    echo '<a href="#" class="wpcpq_overview" data-pid="' . esc_attr( $postid ) . '" data-name="' . esc_attr( get_the_title( $postid ) ) . '" data-type="override"><span class="dashicons dashicons-money-alt"></span> ' . esc_html__( 'Override', 'wpc-price-by-quantity' ) . '</a>';
                }
            }
        }

        function ajax_overview() {
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'wpcpq-security' ) ) {
                die( 'Permissions check failed!' );
            }

            $pid     = absint( sanitize_text_field( wp_unslash( $_POST['pid'] ?? 0 ) ) );
            $type    = sanitize_text_field( wp_unslash( $_POST['type'] ?? '' ) );
            $product = wc_get_product( $pid );

            if ( $product ) {
                global $wp_roles;
                $prices = [];

                echo '<div class="wpcpq-overview-content">';
                echo wp_kses_post( '<div class="wpcpq-overview-line">' . esc_html__( 'Product price:', 'wpc-price-by-quantity' ) . ' ' . $product->get_price_html() . '</div>' );

                if ( $type === 'global' ) {
                    $prices = Wpcpq_Helper()::get_prices();
                }

                if ( $type === 'override' && $pid ) {
                    $prices = get_post_meta( $pid, 'wpcpq_prices', true ) ?: [];
                }

                if ( ! empty( $prices ) ) {
                    foreach ( $prices as $price ) {
                        $price     = array_merge( [
                                'role'      => 'all',
                                'apply'     => '',
                                'apply_val' => '',
                                'method'    => '',
                                'layout'    => 'default',
                                'tiers'     => [],
                        ], $price );
                        $role      = $price['role'];
                        $role_name = isset( $wp_roles->roles[ $role ] ) ? $wp_roles->roles[ $role ]['name'] : esc_html__( 'All', 'wpc-price-by-quantity' );

                        echo '<div class="wpcpq-overview-line">';
                        echo esc_html__( 'User role:', 'wpc-price-by-quantity' ) . ' <strong>' . esc_html( $role_name ) . '</strong><br/>';

                        if ( $type === 'global' ) {
                            if ( $price['apply'] === 'all' ) {
                                echo esc_html__( 'Apply for:', 'wpc-price-by-quantity' ) . ' ' . esc_html__( 'all products', 'wpc-price-by-quantity' ) . '<br/>';
                            } else {
                                echo esc_html__( 'Apply for:', 'wpc-price-by-quantity' ) . ' ' . esc_html( $price['apply'] ) . ' (' . esc_html( $price['apply_val'] ) . ')<br/>';
                            }
                        }

                        echo esc_html__( 'Pricing method:', 'wpc-price-by-quantity' ) . ' <u>' . esc_html( $price['method'] ) . '</u><br/>';
                        echo esc_html__( 'Layout:', 'wpc-price-by-quantity' ) . ' ' . esc_html( $price['layout'] ) . '<br/>';

                        if ( ! empty( $price['tiers'] ) ) {
                            echo esc_html__( 'Quantity-based pricing options', 'wpc-price-by-quantity' ) . ':';
                            echo '<table><thead><tr><td>' . esc_html__( 'Qty', 'wpc-price-by-quantity' ) . '</td><td>' . esc_html__( 'Price', 'wpc-price-by-quantity' ) . '</td><td>' . esc_html__( 'Text', 'wpc-price-by-quantity' ) . '</td></tr></thead>';
                            echo '<tbody>';

                            foreach ( $price['tiers'] as $tier ) {
                                $tier = array_merge( [
                                        'quantity' => '',
                                        'price'    => '',
                                        'text'     => '',
                                ], $tier );
                                echo '<tr><td>' . esc_html( $tier['quantity'] ) . '</td><td>' . esc_html( $tier['price'] ) . '</td><td>' . wp_kses_post( $tier['text'] ) . '</td></tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';
                        } else {
                            echo esc_html__( 'Quantity-based pricing options', 'wpc-price-by-quantity' ) . ': ' . esc_html__( 'Not set', 'wpc-price-by-quantity' );
                        }

                        echo '</div>';
                    }
                }

                echo '</div>';
            }

            wp_die();
        }

        function export_process( $value, $meta, $product ) {
            if ( $meta->key === 'wpcpq_prices' ) {
                $ids = get_post_meta( $product->get_id(), 'wpcpq_prices', true );

                if ( ! empty( $ids ) && is_array( $ids ) ) {
                    return json_encode( $ids );
                }
            }

            return $value;
        }

        function import_process( $object, $data ) {
            if ( isset( $data['meta_data'] ) ) {
                foreach ( $data['meta_data'] as $meta ) {
                    if ( $meta['key'] === 'wpcpq_prices' ) {
                        $object->update_meta_data( 'wpcpq_prices', json_decode( $meta['value'], true ) );
                        break;
                    }
                }
            }

            return $object;
        }
    }

    function Wpcpq_Backend() {
        return Wpcpq_Backend::instance();
    }

    Wpcpq_Backend();
}