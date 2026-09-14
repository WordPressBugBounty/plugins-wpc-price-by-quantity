<?php
defined( 'ABSPATH' ) || exit;

$active_tab = sanitize_key( wp_unslash( $_GET['tab'] ?? 'settings' ) );
?>
<div class="wrap wpcpq-settings-wrap">
    <div class="wpcpq-settings-header">
        <div class="wpcpq-settings-header-inner">
            <div class="wpcpq-header-left">
                <div class="wpcpq-logo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                </div>
                <div>
                    <h1>
                        <?php echo esc_html__( 'WPC Price by Quantity', 'wpc-price-by-quantity' ) . ' ' . esc_html( WPCPQ_VERSION ); ?>
                        <?php if ( defined( 'WPCPQ_PREMIUM' ) ) : ?>
                            <span class="premium"><?php esc_html_e( 'Premium', 'wpc-price-by-quantity' ); ?></span>
                        <?php endif; ?>
                    </h1>
                    <p class="wpcpq-tagline">
                        <?php esc_html_e( 'Offer quantity-based and tiered pricing for products in your store.', 'wpc-price-by-quantity' ); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if ( isset( $_GET['settings-updated'] ) && sanitize_text_field( wp_unslash( $_GET['settings-updated'] ?? '' ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e( 'Settings updated.', 'wpc-price-by-quantity' ); ?></p>
        </div>
    <?php } ?>

    <div class="wpcpq-admin-nav">
        <div class="wpcpq-nav-container">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-wpcpq&tab=how' ) ); ?>"
               class="wpcpq-nav-item <?php echo $active_tab === 'how' ? 'active' : ''; ?>">
                <?php esc_html_e( 'How to use?', 'wpc-price-by-quantity' ); ?>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-wpcpq&tab=settings' ) ); ?>"
               class="wpcpq-nav-item <?php echo $active_tab === 'settings' ? 'active' : ''; ?>">
                <?php esc_html_e( 'Settings', 'wpc-price-by-quantity' ); ?>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-wpcpq&tab=localization' ) ); ?>"
               class="wpcpq-nav-item <?php echo $active_tab === 'localization' ? 'active' : ''; ?>">
                <?php esc_html_e( 'Localization', 'wpc-price-by-quantity' ); ?>
            </a>
            <?php if ( ! defined( 'WPCPQ_PREMIUM' ) ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-wpcpq&tab=premium' ) ); ?>"
                   class="wpcpq-nav-item wpc-premium <?php echo $active_tab === 'premium' ? 'active' : ''; ?>">
                    <?php esc_html_e( 'Premium Version', 'wpc-price-by-quantity' ); ?>
                </a>
            <?php endif; ?>
            <?php if ( defined( 'WPCPQ_PREMIUM' ) ) : ?>
                <a href="<?php echo esc_url( WPCPQ_SUPPORT ); ?>" class="wpcpq-nav-item" target="_blank">
                    <?php esc_html_e( 'Support', 'wpc-price-by-quantity' ); ?>
                </a>
            <?php endif; ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-kit' ) ); ?>"
               class="wpcpq-nav-item">
                <?php esc_html_e( 'Essential Kit', 'wpc-price-by-quantity' ); ?>
            </a>
        </div>
    </div>

    <div class="wpcpq-tab-content active">
        <?php if ( $active_tab === 'how' ) { ?>
            <div class="wpcpq-card">
                <div class="wpcpq-card-header">
                    <div>
                        <h2 class="wpcpq-card-title"><?php esc_html_e( 'How to use?', 'wpc-price-by-quantity' ); ?></h2>
                        <p class="wpcpq-card-desc"><?php esc_html_e( 'Quick instructions on how to use WPC Price by Quantity.', 'wpc-price-by-quantity' ); ?></p>
                    </div>
                </div>
                <div class="wpcpq-settings-page-content-text">
                    <p>
                        <?php esc_html_e( '1. Global Pricing Table: Under the Settings tab, configure quantity-based pricing tiers for all users or specific roles.', 'wpc-price-by-quantity' ); ?>
                    </p>
                    <p>
                        <?php esc_html_e( '2. Pricing Methods: Choose Volume Pricing (one flat unit price according to the quantity reached) or Tiered Pricing (cumulative prices calculation across tier brackets).', 'wpc-price-by-quantity' ); ?>
                    </p>
                    <p>
                        <?php esc_html_e( '3. Shortcode: Use [wpcpq] to display the pricing table anywhere on product pages.', 'wpc-price-by-quantity' ); ?>
                    </p>
                    <p>
                        <?php esc_html_e( '4. Product Overrides: With the Premium version, configure individual pricing tables per product or variation in the Product Data panel.', 'wpc-price-by-quantity' ); ?>
                    </p>
                </div>
            </div>
        <?php } elseif ( $active_tab === 'settings' ) {
            $position        = Wpcpq_Helper()::get_setting( 'position', 'above' );
            $low_stock       = Wpcpq_Helper()::get_setting( 'low_stock', 'yes' );
            $main_price      = Wpcpq_Helper()::get_setting( 'main_price', 'no' );
            $after_text      = Wpcpq_Helper()::get_setting( 'after_text', 'yes' );
            $click_to_set    = Wpcpq_Helper()::get_setting( 'click_to_set', 'yes' );
            $hide_first_row  = Wpcpq_Helper()::get_setting( 'hide_first_row', 'no' );
            $color_default   = apply_filters( 'wpcpq_active_color_default', '#ffffff' );
            $bgcolor_default = apply_filters( 'wpcpq_active_bgcolor_default', '#cc99c2' );
            ?>
            <form method="post" action="options.php">
                <div class="wpcpq-card">
                    <div class="wpcpq-card-header">
                        <div>
                            <h2 class="wpcpq-card-title"><?php esc_html_e( 'General Settings', 'wpc-price-by-quantity' ); ?></h2>
                            <p class="wpcpq-card-desc"><?php esc_html_e( 'Configure general display options and pricing table behavior.', 'wpc-price-by-quantity' ); ?></p>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Position', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <select name="wpcpq_settings[position]">
                                <option value="above" <?php selected( $position, 'above' ); ?>><?php esc_html_e( 'Above the add to cart button', 'wpc-price-by-quantity' ); ?></option>
                                <option value="under" <?php selected( $position, 'under' ); ?>><?php esc_html_e( 'Under the add to cart button', 'wpc-price-by-quantity' ); ?></option>
                                <option value="under_title" <?php selected( $position, 'under_title' ); ?>><?php esc_html_e( 'Under the title', 'wpc-price-by-quantity' ); ?></option>
                                <option value="under_price" <?php selected( $position, 'under_price' ); ?>><?php esc_html_e( 'Under the price', 'wpc-price-by-quantity' ); ?></option>
                                <option value="under_excerpt" <?php selected( $position, 'under_excerpt' ); ?>><?php esc_html_e( 'Under the excerpt', 'wpc-price-by-quantity' ); ?></option>
                                <option value="under_meta" <?php selected( $position, 'under_meta' ); ?>><?php esc_html_e( 'Under the meta', 'wpc-price-by-quantity' ); ?></option>
                                <option value="under_summary" <?php selected( $position, 'under_summary' ); ?>><?php esc_html_e( 'Under the summary', 'wpc-price-by-quantity' ); ?></option>
                                <option value="no" <?php selected( $position, 'no' ); ?>><?php esc_html_e( 'None (hide it)', 'wpc-price-by-quantity' ); ?></option>
                            </select>
                            <span class="description"><?php printf( /* translators: shortcode */ esc_html__( 'Choose where to display the pricing table on a single product page; use %s shortcode to place it anywhere.', 'wpc-price-by-quantity' ), '<code>[wpcpq]</code>' ); ?></span>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Hide on low-stock products', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <select name="wpcpq_settings[low_stock]">
                                <option value="yes" <?php selected( $low_stock, 'yes' ); ?>><?php esc_attr_e( 'Yes', 'wpc-price-by-quantity' ); ?></option>
                                <option value="no" <?php selected( $low_stock, 'no' ); ?>><?php esc_attr_e( 'No', 'wpc-price-by-quantity' ); ?></option>
                            </select>
                            <span class="description"><?php esc_html_e( 'Hide the pricing table for products with inventory quantities lower than the minimum quantity specified in the pricing options.', 'wpc-price-by-quantity' ); ?></span>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Change price', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <select name="wpcpq_settings[main_price]">
                                <option value="price" <?php selected( $main_price, 'price' ); ?>><?php esc_attr_e( 'Replace by active price', 'wpc-price-by-quantity' ); ?></option>
                                <option value="total" <?php selected( $main_price, 'total' ); ?>><?php esc_attr_e( 'Replace by total', 'wpc-price-by-quantity' ); ?></option>
                                <option value="no" <?php selected( $main_price, 'no' ); ?>><?php esc_attr_e( 'No', 'wpc-price-by-quantity' ); ?></option>
                            </select>
                            <span class="description"><?php esc_html_e( 'Change the main price when updating the quantity.', 'wpc-price-by-quantity' ); ?></span>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Click to set', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <select name="wpcpq_settings[click_to_set]">
                                <option value="yes" <?php selected( $click_to_set, 'yes' ); ?>><?php esc_attr_e( 'Yes', 'wpc-price-by-quantity' ); ?></option>
                                <option value="no" <?php selected( $click_to_set, 'no' ); ?>><?php esc_attr_e( 'No', 'wpc-price-by-quantity' ); ?></option>
                            </select>
                            <span class="description"><?php esc_html_e( 'Change the main quantity when clicking on a pricing row.', 'wpc-price-by-quantity' ); ?></span>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Active color', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_settings[active_color]"
                                   class="wpcpq_color_picker"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::get_setting( 'active_color', $color_default ) ); ?>"/>
                            <span class="description"><?php printf( /* translators: color */ esc_html__( 'Choose the color for the active price, default %s', 'wpc-price-by-quantity' ), '<code>' . esc_html( $color_default ) . '</code>' ); ?></span>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Active background color', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_settings[active_bgcolor]"
                                   class="wpcpq_color_picker"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::get_setting( 'active_bgcolor', $bgcolor_default ) ); ?>"/>
                            <span class="description"><?php printf( /* translators: color */ esc_html__( 'Choose the background color for the active price, default %s', 'wpc-price-by-quantity' ), '<code>' . esc_html( $bgcolor_default ) . '</code>' ); ?></span>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Hide first row', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <select name="wpcpq_settings[hide_first_row]">
                                <option value="yes" <?php selected( $hide_first_row, 'yes' ); ?>><?php esc_attr_e( 'Yes', 'wpc-price-by-quantity' ); ?></option>
                                <option value="no" <?php selected( $hide_first_row, 'no' ); ?>><?php esc_attr_e( 'No', 'wpc-price-by-quantity' ); ?></option>
                            </select>
                            <span class="description"><?php esc_html_e( 'Hide the first row that show the original price.', 'wpc-price-by-quantity' ); ?></span>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'After text', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <select name="wpcpq_settings[after_text]">
                                <option value="yes" <?php selected( $after_text, 'yes' ); ?>><?php esc_attr_e( 'Show', 'wpc-price-by-quantity' ); ?></option>
                                <option value="no" <?php selected( $after_text, 'no' ); ?>><?php esc_attr_e( 'Hide', 'wpc-price-by-quantity' ); ?></option>
                            </select>
                            <span class="description"><?php esc_html_e( 'Show/hide the after text on each price row.', 'wpc-price-by-quantity' ); ?></span>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Default after text', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_settings[after_text_default]"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::get_setting( 'after_text_default' ) ); ?>"/>
                            <span class="description"><?php esc_html_e( 'You can use [p] for percentage discount, [a] for amount discount. E.g: (saved [p])', 'wpc-price-by-quantity' ); ?></span>
                        </div>
                    </div>
                </div>

                <div class="wpcpq-card">
                    <div class="wpcpq-card-header">
                        <div>
                            <h2 class="wpcpq-card-title"><?php esc_html_e( 'Pricing Table', 'wpc-price-by-quantity' ); ?></h2>
                            <p class="wpcpq-card-desc"><?php esc_html_e( 'Set up quantity-based pricing rules for different user roles or storewide.', 'wpc-price-by-quantity' ); ?></p>
                        </div>
                        <div class="wpcpq-card-header-actions">
                            <a href="#" class="wpcpq-action-tool-btn wpcpq_expand_all">
                                <span class="dashicons dashicons-arrow-down-alt2"></span> <?php esc_html_e( 'Expand All', 'wpc-price-by-quantity' ); ?>
                            </a>
                            <a href="#" class="wpcpq-action-tool-btn wpcpq_collapse_all">
                                <span class="dashicons dashicons-arrow-up-alt2"></span> <?php esc_html_e( 'Collapse All', 'wpc-price-by-quantity' ); ?>
                            </a>
                            <button type="button" class="wpcpq-import-export-btn wpclever_export"
                                    data-key="wpcpq_prices"
                                    data-name="rules">
                                <span class="dashicons dashicons-database-export"></span>
                                <?php esc_html_e( 'Import / Export', 'wpc-price-by-quantity' ); ?>
                            </button>
                        </div>
                    </div>

                    <div class="wpcpq_settings">
                        <div class="wpcpq-items-wrapper">
                            <div class="wpcpq-items wpcpq-roles">
                                <?php
                                $active       = true;
                                $name         = '';
                                $is_variation = false;
                                $product_id   = 0;
                                $prices       = Wpcpq_Helper()::get_prices();
                                $prices       = ( is_array( $prices ) && ! empty( $prices ) ) ? Wpcpq_Helper()::format_prices( $prices ) : [
                                        'all' => [
                                                'tiers' => [
                                                        [
                                                                'quantity' => '',
                                                                'price'    => '',
                                                        ]
                                                ]
                                        ]
                                ];

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
                    </div>
                </div>

                <div class="wpcpq-submit-row">
                    <?php
                    settings_fields( 'wpcpq_settings' );
                    submit_button( '', 'primary', 'submit', false );

                    if ( function_exists( 'wpc_last_saved' ) ) {
                        wpc_last_saved( Wpcpq_Helper()::get_settings() );
                    }
                    ?>
                </div>
            </form>
        <?php } elseif ( $active_tab == 'localization' ) { ?>
            <form method="post" action="options.php">
                <div class="wpcpq-card">
                    <div class="wpcpq-card-header">
                        <div>
                            <h2 class="wpcpq-card-title"><?php esc_html_e( 'Default Table', 'wpc-price-by-quantity' ); ?></h2>
                            <p class="wpcpq-card-desc"><?php esc_html_e( 'Leave blank to use the default text and its equivalent translation in multiple languages.', 'wpc-price-by-quantity' ); ?></p>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Quantity', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_localization[quantity]" class="regular-text"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::localization( 'quantity' ) ); ?>"
                                   placeholder="<?php esc_attr_e( 'Quantity', 'wpc-price-by-quantity' ); ?>"/>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Price', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_localization[price]" class="regular-text"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::localization( 'price' ) ); ?>"
                                   placeholder="<?php esc_attr_e( 'Price', 'wpc-price-by-quantity' ); ?>"/>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Tier Total', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_localization[row_total]" class="regular-text"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::localization( 'row_total' ) ); ?>"
                                   placeholder="<?php esc_attr_e( 'Tier Total', 'wpc-price-by-quantity' ); ?>"/>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Min outliers', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_localization[min]" class="regular-text"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::localization( 'min' ) ); ?>"
                                   placeholder="<?php esc_attr_e( '<{min}', 'wpc-price-by-quantity' ); ?>"/>
                            <span class="description"><?php esc_html_e( 'Values smaller than min.', 'wpc-price-by-quantity' ); ?></span>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Single value', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_localization[single]" class="regular-text"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::localization( 'single' ) ); ?>"
                                   placeholder="<?php esc_attr_e( '{single}', 'wpc-price-by-quantity' ); ?>"/>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Values between min and max', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_localization[from_to]" class="regular-text"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::localization( 'from_to' ) ); ?>"
                                   placeholder="<?php esc_attr_e( '{from} - {to}', 'wpc-price-by-quantity' ); ?>"/>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Max & outliers', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_localization[max]" class="regular-text"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::localization( 'max' ) ); ?>"
                                   placeholder="<?php esc_attr_e( '{max}+', 'wpc-price-by-quantity' ); ?>"/>
                            <span class="description"><?php esc_html_e( 'Max quantity and larger values.', 'wpc-price-by-quantity' ); ?></span>
                        </div>
                    </div>
                </div>

                <div class="wpcpq-card">
                    <div class="wpcpq-card-header">
                        <div>
                            <h2 class="wpcpq-card-title"><?php esc_html_e( 'Quick Buy Table', 'wpc-price-by-quantity' ); ?></h2>
                            <p class="wpcpq-card-desc"><?php esc_html_e( 'Leave blank to use the default text and its equivalent translation in multiple languages.', 'wpc-price-by-quantity' ); ?></p>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Item quantity (singular)', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_localization[qb_item_qty_singular]"
                                   class="regular-text"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::localization( 'qb_item_qty_singular' ) ); ?>"
                                   placeholder="<?php /* translators: qty */ esc_attr_e( '%s item', 'wpc-price-by-quantity' ); ?>"/>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Item quantity (plural)', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_localization[qb_item_qty_plural]"
                                   class="regular-text"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::localization( 'qb_item_qty_plural' ) ); ?>"
                                   placeholder="<?php /* translators: qty */ esc_attr_e( '%s items', 'wpc-price-by-quantity' ); ?>"/>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Item price', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_localization[qb_item_price]"
                                   class="regular-text"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::localization( 'qb_item_price' ) ); ?>"
                                   placeholder="<?php /* translators: price */ esc_attr_e( '%s for each product', 'wpc-price-by-quantity' ); ?>"/>
                        </div>
                    </div>

                    <div class="wpcpq-settings-row">
                        <div class="wpcpq-settings-label">
                            <strong><?php esc_html_e( 'Add to cart', 'wpc-price-by-quantity' ); ?></strong>
                        </div>
                        <div class="wpcpq-settings-field">
                            <input type="text" name="wpcpq_localization[qb_item_atc]"
                                   class="regular-text"
                                   value="<?php echo esc_attr( Wpcpq_Helper()::localization( 'qb_item_atc' ) ); ?>"
                                   placeholder="<?php esc_attr_e( 'Add to cart', 'wpc-price-by-quantity' ); ?>"/>
                        </div>
                    </div>
                </div>

                <div class="wpcpq-submit-row">
                    <?php
                    settings_fields( 'wpcpq_localization' );
                    submit_button( '', 'primary', 'submit', false );

                    if ( function_exists( 'wpc_last_saved' ) ) {
                        wpc_last_saved( get_option( 'wpcpq_localization', [] ) );
                    }
                    ?>
                </div>
            </form>
        <?php } elseif ( $active_tab == 'premium' ) { ?>
            <div class="wpcpq-card">
                <div class="wpcpq-card-header">
                    <div>
                        <h2 class="wpcpq-card-title"><?php esc_html_e( 'Premium Version', 'wpc-price-by-quantity' ); ?></h2>
                        <p class="wpcpq-card-desc"><?php esc_html_e( 'Unlock powerful features with WPC Price by Quantity Premium.', 'wpc-price-by-quantity' ); ?></p>
                    </div>
                </div>
                <div class="wpcpq-settings-page-content-text">
                    <p>
                        <?php esc_html_e( 'Get the Premium Version just $29!', 'wpc-price-by-quantity' ); ?>
                        <a href="https://wpclever.net/downloads/wpc-price-by-quantity/?utm_source=pro&utm_medium=wpcpq&utm_campaign=wporg"
                           target="_blank">https://wpclever.net/downloads/wpc-price-by-quantity/</a>
                    </p>
                    <p><strong><?php esc_html_e( 'Extra features for Premium Version:', 'wpc-price-by-quantity' ); ?></strong></p>
                    <ul class="wpcpq-mb-0">
                        <li>- <?php esc_html_e( 'Setup pricing on a product/variation basis.', 'wpc-price-by-quantity' ); ?></li>
                        <li>- <?php esc_html_e( 'Get the lifetime update & premium support.', 'wpc-price-by-quantity' ); ?></li>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
