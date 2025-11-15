<?php
/**
 * Exam Nibbles Minimal WooCommerce functions
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// -----------------------------------------------------------------------------
// Theme setup
// -----------------------------------------------------------------------------
add_action( 'after_setup_theme', function() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/main.min.css' );
} );

// -----------------------------------------------------------------------------
// Enqueue styles
// -----------------------------------------------------------------------------
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'exam-nibbles-main', get_stylesheet_directory_uri() . '/assets/main.min.css', [], '1.3.0' );
}, 11 );

// -----------------------------------------------------------------------------
// Performance: disable cart fragments except on cart/checkout/customizer
// -----------------------------------------------------------------------------
add_action( 'wp_enqueue_scripts', function() {
    if ( is_admin() ) { return; }
    if ( is_cart() || is_checkout() || is_customize_preview() ) { return; }
    wp_dequeue_script( 'wc-cart-fragments' );
    wp_deregister_script( 'wc-cart-fragments' );
}, 100 );

// -----------------------------------------------------------------------------
// Mobile "Jump to Price" bar
// -----------------------------------------------------------------------------
add_action( 'wp_footer', function () {
    if ( ! is_product() ) return;
    
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?>
    <a class="en-jump-bar" href="#en-purchase-box" aria-label="<?php echo esc_attr__( 'Jump to pricing and buy', 'exam-nibbles-minimal' ); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?>">
      <?php esc_html_e( 'View Price & Buy', 'exam-nibbles-minimal' ); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?>
    </a>
    <?php
}, 20 );

// -----------------------------------------------------------------------------
// Digital-only checkout (hide shipping + unnecessary fields)
// -----------------------------------------------------------------------------
add_filter( 'woocommerce_cart_needs_shipping', '__return_false' );
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );
add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    $keep = [ 'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone' ];
    foreach ( $fields['billing'] as $key => $field ) {
        if ( ! in_array( $key, $keep, true ) ) {
            unset( $fields['billing'][ $key ] );
        }
    }
    if ( isset( $fields['shipping'] ) ) { $fields['shipping'] = []; }
    if ( isset( $fields['billing']['billing_phone'] ) ) { $fields['billing']['billing_phone']['required'] = false; }
    return $fields;
}, 20 );
add_filter( 'woocommerce_is_sold_individually', function(){ return true; } );

// -----------------------------------------------------------------------------
// "Demo File (URL)" meta + button
// -----------------------------------------------------------------------------
add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'en_demo_file',
        __( 'Demo File (URL)', 'exam-nibbles-minimal' ),
        function( $post ) {
            $value = get_post_meta( $post->ID, '_en_demo_file_url', true );
            wp_nonce_field( 'en_save_demo', 'en_demo_nonce' );
            echo '<p><label for="en_demo_file_url">' . esc_html__( 'Paste a direct URL to your demo file (PDF/ZIP/etc.)', 'exam-nibbles-minimal' ) . '</label></p>';
            echo '<input type="url" style="width:100%" placeholder="https://..." id="en_demo_file_url" name="en_demo_file_url" value="' . esc_attr( $value ) . '">';
        },
        'product', 'side', 'high'
    );
} );

add_action( 'save_post_product', function( $post_id ) {
    if ( ! isset( $_POST['en_demo_nonce'] ) || ! wp_verify_nonce( $_POST['en_demo_nonce'], 'en_save_demo' ) ) { return; }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
    $url = isset( $_POST['en_demo_file_url'] ) ? esc_url_raw( trim( $_POST['en_demo_file_url'] ) ) : '';
    if ( $url ) { update_post_meta( $post_id, '_en_demo_file_url', $url ); }
    else { delete_post_meta( $post_id, '_en_demo_file_url' ); }
} );

// Remove product tabs (we show description inline)
add_filter( 'woocommerce_product_tabs', '__return_empty_array', 99 );
add_filter( 'wc_product_sku_enabled', function(){ return false; } );

// -----------------------------------------------------------------------------
// "Update Info" meta (Last Update Check + Latest Q&A) + frontend output
// -----------------------------------------------------------------------------
add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'en_update_info',
        __( 'Exam Nibbles – Update Info', 'exam-nibbles-minimal' ),
        function( $post ) {
            $date  = get_post_meta( $post->ID, '_en_last_update_check', true );
            $count = get_post_meta( $post->ID, '_en_latest_qa_count', true );
            wp_nonce_field( 'en_save_update_info', 'en_update_info_nonce' );
            echo '<p><label for="en_last_update_check"><strong>' . esc_html__( 'Last Update Check (date)', 'exam-nibbles-minimal' ) . '</strong></label></p>';
            echo '<input type="date" id="en_last_update_check" name="en_last_update_check" value="' . esc_attr( $date ) . '" style="width:100%;">';
            echo '<p style="margin-top:10px;"><label for="en_latest_qa_count"><strong>' . esc_html__( 'Latest Questions & Answers (count)', 'exam-nibbles-minimal' ) . '</strong></label></p>';
            echo '<input type="number" min="0" step="1" id="en_latest_qa_count" name="en_latest_qa_count" value="' . esc_attr( $count ) . '" style="width:100%;">';
        },
        'product', 'side', 'default'
    );
} );

add_action( 'save_post_product', function( $post_id ) {
    if ( ! isset( $_POST['en_update_info_nonce'] ) || ! wp_verify_nonce( $_POST['en_update_info_nonce'], 'en_save_update_info' ) ) { return; }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

    $date  = isset( $_POST['en_last_update_check'] ) ? sanitize_text_field( $_POST['en_last_update_check'] ) : '';
    if ( $date && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) { $date = ''; }
    $count = isset( $_POST['en_latest_qa_count'] ) ? intval( $_POST['en_latest_qa_count'] ) : '';
    if ( $count !== '' && $count < 0 ) $count = 0;

    if ( $date ) { update_post_meta( $post_id, '_en_last_update_check', $date ); } else { delete_post_meta( $post_id, '_en_last_update_check' ); }
    if ( $count !== '' ) { update_post_meta( $post_id, '_en_latest_qa_count', $count ); } else { delete_post_meta( $post_id, '_en_latest_qa_count' ); }
} );

function en_output_update_block() {
    if ( ! is_product() ) return;
    global $product; if ( ! $product ) return;
    $date_raw  = get_post_meta( $product->get_id(), '_en_last_update_check', true );
    $count_raw = get_post_meta( $product->get_id(), '_en_latest_qa_count', true );
    if ( empty( $date_raw ) && ( $count_raw === '' || $count_raw === null ) ) return;
    $date_display = '';
    if ( $date_raw ) {
        $ts = strtotime( $date_raw );
        if ( $ts ) { $date_display = date_i18n( get_option( 'date_format' ), $ts ); }
    }
    $count_display = ( $count_raw !== '' && $count_raw !== null ) ? intval( $count_raw ) : '';
    echo '<section class="en-update-block" aria-label="' . esc_attr__( 'Product update information', 'exam-nibbles-minimal' ) . '">';
    echo '  <div class="en-update-row"><span class="en-update-label">' . esc_html__( 'Last Update Check', 'exam-nibbles-minimal' ) . '</span>';
    echo '  <span class="en-update-value">' . ( $date_display ? esc_html( $date_display ) : esc_html__( '—', 'exam-nibbles-minimal' ) ) . '</span></div>';
    echo '  <div class="en-update-row"><span class="en-update-label">' . esc_html__( 'Latest Questions & Answers', 'exam-nibbles-minimal' ) . '</span>';
    echo '  <span class="en-update-value">' . ( $count_display !== '' ? esc_html( number_format_i18n( $count_display ) ) : esc_html__( '—', 'exam-nibbles-minimal' ) ) . '</span></div>';
    echo '</section>';
}

// -----------------------------------------------------------------------------
// Admin: Columns + Quick Edit + Bulk Edit for Demo/Update/Q&A
// -----------------------------------------------------------------------------
add_filter( 'manage_edit-product_columns', function( $cols ) {
    $cols['en_demo'] = __( 'Demo URL', 'exam-nibbles-minimal' );
    $cols['en_last_update'] = __( 'Last Update', 'exam-nibbles-minimal' );
    $cols['en_qa'] = __( 'Q&A', 'exam-nibbles-minimal' );
    return $cols;
}, 20 );

add_action( 'manage_product_posts_custom_column', function( $col, $post_id ) {
    if ( $col === 'en_demo' ) {
        $v = get_post_meta( $post_id, '_en_demo_file_url', true );
        if ( $v ) echo '<a href="' . esc_url( $v ) . '" target="_blank" rel="noreferrer">link</a>';
    } elseif ( $col === 'en_last_update' ) {
        $v = get_post_meta( $post_id, '_en_last_update_check', true );
        echo $v ? esc_html( $v ) : '—';
    } elseif ( $col === 'en_qa' ) {
        $v = get_post_meta( $post_id, '_en_latest_qa_count', true );
        echo $v !== '' ? intval( $v ) : '—';
    }

    // Hidden inline data for Quick Edit
    if ( in_array( $col, ['name','title'], true ) ) {
        $demo  = esc_attr( get_post_meta( $post_id, '_en_demo_file_url', true ) );
        $date  = esc_attr( get_post_meta( $post_id, '_en_last_update_check', true ) );
        $qa    = esc_attr( get_post_meta( $post_id, '_en_latest_qa_count', true ) );
        echo '<div class="hidden" id="en-inline-' . intval( $post_id ) . '" data-demo="' . $demo . '" data-date="' . $date . '" data-qa="' . $qa . '"></div>';
    }
}, 10, 2 );

// Quick Edit box
add_action( 'quick_edit_custom_box', function( $col, $post_type ) {
    if ( $post_type !== 'product' || $col !== 'en_demo' ) return;
    
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?>
    <fieldset class="inline-edit-col-left">
      <div class="inline-edit-col">
        <label>
          <span class="title"><?php esc_html_e('Demo URL','exam-nibbles-minimal'); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?></span>
          <span class="input-text-wrap"><input type="url" name="en_quick_demo" value=""></span>
        </label>
        <label>
          <span class="title"><?php esc_html_e('Last Update (YYYY-MM-DD)','exam-nibbles-minimal'); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?></span>
          <span class="input-text-wrap"><input type="text" name="en_quick_date" value=""></span>
        </label>
        <label>
          <span class="title"><?php esc_html_e('Latest Q&A (count)','exam-nibbles-minimal'); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?></span>
          <span class="input-text-wrap"><input type="number" name="en_quick_qa" value=""></span>
        </label>
      </div>
    </fieldset>
    <?php
}, 10, 2 );

// Save on Quick Edit (inline save)
add_action( 'save_post_product', function( $post_id, $post, $update ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( isset( $_REQUEST['en_quick_demo'] ) || isset( $_REQUEST['en_quick_date'] ) || isset( $_REQUEST['en_quick_qa'] ) ) {
        if ( isset($_REQUEST['en_quick_demo']) ) {
            $val = esc_url_raw( trim( wp_unslash( $_REQUEST['en_quick_demo'] ) ) );
            if ( $val ) update_post_meta( $post_id, '_en_demo_file_url', $val ); else delete_post_meta( $post_id, '_en_demo_file_url' );
        }
        if ( isset($_REQUEST['en_quick_date']) ) {
            $date = sanitize_text_field( wp_unslash( $_REQUEST['en_quick_date'] ) );
            if ( $date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ) update_post_meta( $post_id, '_en_last_update_check', $date );
        }
        if ( isset($_REQUEST['en_quick_qa']) ) {
            $qa = intval( $_REQUEST['en_quick_qa'] );
            update_post_meta( $post_id, '_en_latest_qa_count', $qa );
        }
    }
}, 10, 3 );

// Bulk Edit UI
add_action( 'bulk_edit_custom_box', function( $col, $post_type ) {
    if ( $post_type !== 'product' || $col !== 'en_demo' ) return;
    
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?>
    <fieldset class="inline-edit-col-left">
      <div class="inline-edit-col">
        <label>
          <span class="title"><?php esc_html_e('Demo URL (set for selected)','exam-nibbles-minimal'); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?></span>
          <span class="input-text-wrap"><input type="url" name="en_bulk_demo" value=""></span>
        </label>
        <label>
          <span class="title"><?php esc_html_e('Last Update (YYYY-MM-DD)','exam-nibbles-minimal'); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?></span>
          <span class="input-text-wrap"><input type="text" name="en_bulk_date" value=""></span>
        </label>
        <label>
          <span class="title"><?php esc_html_e('Latest Q&A (count)','exam-nibbles-minimal'); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?></span>
          <span class="input-text-wrap"><input type="number" name="en_bulk_qa" value=""></span>
        </label>
      </div>
    </fieldset>
    <?php
}, 10, 2 );

// Admin assets for Quick/Bulk Edit (prefill & bulk AJAX)
add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( 'edit.php' !== $hook || ( isset($_GET['post_type']) && $_GET['post_type'] !== 'product' ) ) return;
    wp_enqueue_script( 'en-admin-inline', get_stylesheet_directory_uri() . '/assets/admin-inline.js', [ 'jquery','inline-edit-post' ], '1.3.0', true );
    wp_localize_script( 'en-admin-inline', 'enInline', [
        'nonce' => wp_create_nonce('en-bulk'),
        'ajax'  => admin_url('admin-ajax.php')
    ] );
} );

add_action( 'wp_ajax_en_bulk_edit', function() {
    if ( ! current_user_can('edit_products') ) wp_send_json_error('cap');
    check_ajax_referer('en-bulk', 'nonce');
    $ids = isset($_POST['ids']) ? array_map('intval', (array) $_POST['ids']) : [];
    $demo = isset($_POST['demo']) ? esc_url_raw( trim( wp_unslash($_POST['demo']) ) ) : '';
    $date = isset($_POST['date']) ? sanitize_text_field( wp_unslash($_POST['date']) ) : '';
    $qa   = isset($_POST['qa']) ? intval($_POST['qa']) : null;

    foreach ( $ids as $id ) {
        if ( $demo ) update_post_meta( $id, '_en_demo_file_url', $demo );
        if ( $date && preg_match('/^\d{4}-\d{2}-\d{2}$/',$date) ) update_post_meta( $id, '_en_last_update_check', $date );
        if ( $qa !== null && $qa !== '' ) update_post_meta( $id, '_en_latest_qa_count', $qa );
    }
    wp_send_json_success(['updated'=>count($ids)]);
} );


/** Frontend interactions (variation radios, sticky ATC) */
add_action( 'wp_enqueue_scripts', function() {
    if ( ! is_product() ) return;
    wp_enqueue_script( 'en-frontend', get_stylesheet_directory_uri() . '/assets/frontend.js', [ 'jquery' ], '1.4.0', true );
}, 12 );


/** Desktop Sticky Add-to-Cart (CTA scroll-to-price) */
add_action( 'wp_footer', function () {
    if ( ! is_product() ) return;
    global $product;
    $price_html = function_exists('woocommerce_template_single_price') ? $product->get_price_html() : '';
    
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?>
    <div class="en-sticky-atc" role="region" aria-label="<?php echo esc_attr__('Sticky add to cart', 'exam-nibbles-minimal'); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?>">
      <div class="en-sticky-atc__title"><?php echo esc_html( get_the_title() ); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?></div>
      <div class="en-sticky-atc__price"><?php echo wp_kses_post( $price_html ); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?></div>
      <a class="en-sticky-atc__btn" href="#en-purchase-box"><?php esc_html_e('Add to Cart', 'exam-nibbles-minimal'); 
// === EN: Hide 'Choose package' label/placeholder for pa_package (Applied) ===
if ( ! function_exists( 'en_filter_hide_package_label' ) ) {
  add_filter('woocommerce_attribute_label', function($label, $name, $product=null){
    if ($name === 'pa_package') { return ''; }
    return $label;
  }, 10, 3);
  add_filter('woocommerce_dropdown_variation_attribute_options_args', function($args){
    if (!empty($args['attribute']) && strpos($args['attribute'], 'pa_package') !== false) {
      $args['show_option_none'] = false;
    }
    return $args;
  });
}

?></a>
    </div>
    <?php
}, 35 );



// Demo button renderer (appears just before the Add to Cart button)
function en_render_demo_button() {
    global $product; if ( ! $product ) return;
    $url = get_post_meta( $product->get_id(), '_en_demo_file_url', true );
    if ( ! $url ) {
        foreach ( [ '_demo_file_url','enibbles_demo_url','get_demo_url' ] as $k ) {
            $maybe = get_post_meta( $product->get_id(), $k, true );
            if ( $maybe ) { $url = $maybe; break; }
        }
    }
    if ( $url ) {
        echo '<a class="button en-demo-button" href="' . esc_url( $url ) . '" target="_blank" rel="noopener nofollow">' . esc_html__( 'Download Demo', 'exam-nibbles-minimal' ) . '</a>';
    }
}
add_action( 'woocommerce_before_add_to_cart_button', 'en_render_demo_button', 3 );

// === EN: Add block supports (Applied) ===
add_action('after_setup_theme', function(){
  add_theme_support('wp-block-styles');
  add_theme_support('align-wide');
});


// === EN: Change Add to Cart text to "Buy Now"
add_filter('woocommerce_product_single_add_to_cart_text', function( $text ) {
    return 'Buy Now';
});

// Keep archives sensible: show "Buy Now" for simple products,
// keep "Select options" for variable products so users pick a variant.
add_filter('woocommerce_product_add_to_cart_text', function( $text, $product ) {
    if ( $product && $product->is_type( array( 'simple', 'downloadable', 'virtual' ) ) ) {
        return 'Buy Now';
    }
    return $text; // variable/external/grouped keep their normal labels
}, 10, 2);


// === EN: Redirect to Cart after Add to Cart (server-side for non-AJAX)
add_filter( 'woocommerce_add_to_cart_redirect', function( $url ) {
    return wc_get_cart_url();
}, 10, 1 );

// === EN: Redirect to Cart after AJAX add_to_cart (archives/shop)
add_action( 'wp_enqueue_scripts', function(){
    // make sure WooCommerce add-to-cart script is present
    if ( wp_script_is( 'wc-add-to-cart', 'registered' ) ) {
        wp_enqueue_script( 'wc-add-to-cart' );
        $js = "jQuery(function($){ $(document.body).on('added_to_cart', function(){ window.location.href = '".esc_url_raw( wc_get_cart_url() )."'; }); });";
        wp_add_inline_script( 'wc-add-to-cart', $js, 'after' );
    } else {
        // Fallback: print inline in footer
        add_action('wp_footer', function(){
            echo '<script>jQuery(function($){ $(document.body).on("added_to_cart", function(){ window.location.href = "'.esc_url( wc_get_cart_url() ).'"; }); });</script>';
        }, 100);
    }
}, 20);

// Ensure product searches render with WooCommerce's archive template
add_filter( 'template_include', function( $template ) {
    if ( is_search() && 'product' === get_query_var( 'post_type' ) ) {
        if ( function_exists( 'wc_locate_template' ) ) {
            $woo_tpl = wc_locate_template( 'archive-product.php' );
            if ( $woo_tpl ) return $woo_tpl;
        }
    }
    return $template;
} );

// Safety: force front-end searches to products if the search form is missing the hidden field
add_action( 'pre_get_posts', function( $q ) {
    if ( $q->is_main_query() && $q->is_search() && ! is_admin() ) {
        $post_type = $q->get( 'post_type' );
        if ( empty( $post_type ) ) {
            $q->set( 'post_type', [ 'product' ] );
        }
        // Optional: order by relevance then date
        $q->set( 'orderby', 'relevance' );
    }
} );

add_filter('woocommerce_add_to_cart_fragments', function($fragments){
  ob_start(); ?>
  <em class="en-cart-count"><?php echo (int) WC()->cart->get_cart_contents_count(); ?></em>
  <?php $fragments['.en-cart-count'] = ob_get_clean();
  return $fragments;
});


add_action('wp_head', function(){
  if ( is_404() ) {
    echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
  }
}, 5);

