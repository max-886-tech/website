<?php
/**
 * Single Product content template with 3-column layout:
 *  - 20%: Images
 *  - 50%: Description
 *  - 30%: Pricing/Add to Cart (+ Demo button via hook)
 */
defined( 'ABSPATH' ) || exit;
global $product;
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
  <div class="en-product-header">
    <h1 class="product_title entry-title"><?php the_title(); ?></h1>
  </div>

  <div class="en-product-grid">
    <div class="en-col en-col--media">
      <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
    </div>

    <div class="en-col en-col--description">
<?php if ( function_exists( 'en_output_update_block' ) ) { en_output_update_block(); } ?>

      <?php
      // Short description (excerpt)
      woocommerce_template_single_excerpt();

      // Common Description block is injected by plugin or can be hooked via actions if needed
      ?>
      <div class="en-long-description">
        <?php the_content(); ?>
      </div>
    </div>

    <div class="en-col en-col--purchase" id="en-purchase-box">
      <?php
     // woocommerce_template_single_price();

      if ( function_exists( 'en_output_update_block' ) ) { en_output_update_block(); }

      // Add to Cart (Demo button appears above via our hook)
      woocommerce_template_single_add_to_cart();

      // Meta (SKU/Categories) – SKU suppressed via filter in functions.php
      woocommerce_template_single_meta();
      ?>
    </div>
  </div>
</div>
