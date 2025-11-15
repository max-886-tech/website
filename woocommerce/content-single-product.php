<?php
/**
 * Single Product content template with 3-column layout:
 *  - 20%: Images
 *  - 50%: Description
 *  - 30%: Pricing/Add to Cart (+ Demo button via hook)
 */
defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

if ( post_password_required() ) {
    // Let WooCommerce / WordPress render the password form.
    echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    return;
}
?>

<div id="product-<?php the_ID(); ?>"
     <?php wc_product_class( 'en-product en-product--three-col', $product ); ?>
     itemscope
     itemtype="https://schema.org/Product">

  <?php do_action( 'woocommerce_before_single_product' ); ?>

  <header class="en-product-header">
    <?php if ( function_exists( 'woocommerce_breadcrumb' ) ) : ?>
      <nav class="en-product-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'woocommerce' ); ?>">
        <?php woocommerce_breadcrumb(); ?>
      </nav>
    <?php endif; ?>

    <h1 class="product_title entry-title" itemprop="name">
      <?php the_title(); ?>
    </h1>
  </header>

  <div class="en-product-grid">
    <div class="en-col en-col--media" itemprop="image">
      <?php
      /**
       * Hook: woocommerce_before_single_product_summary
       *
       * Typically outputs sale flash + product images.
       */
      do_action( 'woocommerce_before_single_product_summary' );
      ?>
    </div>

    <div class="en-col en-col--description">
      <?php
      // Optional update / notice block (e.g., "Updated on…" or feature badges).
      if ( function_exists( 'en_output_update_block' ) ) {
          en_output_update_block();
      }
      ?>

      <div class="en-short-description entry-summary">
        <?php
        // Short description (excerpt)
        woocommerce_template_single_excerpt();
        ?>
      </div>

      <div class="en-long-description" itemprop="description">
        <?php the_content(); ?>
      </div>

      <?php
      /**
       * Custom hook to append extra information below the long description
       * (e.g., FAQs, changelog, related docs).
       */
      do_action( 'en_after_product_description', $product );
      ?>
    </div>

    <aside class="en-col en-col--purchase" id="en-purchase-box" aria-label="<?php esc_attr_e( 'Purchase options', 'woocommerce' ); ?>">
      <?php
      /**
       * Custom hook before the purchase box content.
       * Good for trust badges, countdowns, etc.
       */
      do_action( 'en_before_purchase_box', $product );

      // Rating (stars)
      woocommerce_template_single_rating();

      // Price
      woocommerce_template_single_price();

      // Compact update block variant (if you want it near the price).
      if ( function_exists( 'en_output_update_block' ) ) {
          echo '<div class="en-update-block en-update-block--compact">';
          en_output_update_block();
          echo '</div>';
      }

      // Stock status (e.g., "In stock", "Out of stock")
      echo '<div class="en-product-stock">';
      echo wp_kses_post( wc_get_stock_html( $product ) );
      echo '</div>';

      // Add to Cart (Demo button can be hooked into 'woocommerce_before_add_to_cart_button', etc.)
      woocommerce_template_single_add_to_cart();

      // Meta (SKU/Categories) – SKU can be suppressed via filter in functions.php
      woocommerce_template_single_meta();

      /**
       * Custom hook after the purchase box.
       * Use for guarantees, extra contact options, etc.
       */
      do_action( 'en_after_purchase_box', $product );
      ?>
    </aside>
  </div>

  <?php do_action( 'woocommerce_after_single_product' ); ?>
</div>
