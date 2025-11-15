<?php
/**
 * 404 template – Exam Nibbles Minimal WooCommerce
 */
defined('ABSPATH') || exit;
get_header();
?>

<main id="primary" class="site-main en-404">
  <section class="en-404__hero">
    <h1 class="en-404__title">Oops — page not found</h1>
    <p class="en-404__subtitle">Try searching for the exam, or browse our categories.</p>

    <!-- Product-only search (uses your searchform.php) -->
<!-- Search -->
    <div class="en-searchwrap">
      <?php get_search_form(); ?>
    </div>

    <div class="en-404__actions">
      <?php
        $shop_id  = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : -1;
        $shop_url = $shop_id && $shop_id > 0 ? get_permalink($shop_id) : home_url('');
        $back_url = wp_get_referer();
      ?>
      <?php if ( $back_url ) : ?>
        <a class="en-btn en-btn--ghost" href="<?php echo esc_url($back_url); ?>">Go Back</a>
      <?php endif; ?>
      <a class="en-btn" href="/vendors/">Explore all Exams</a>
      <a class="en-btn en-btn--outline" href="javascript:void(Tawk_API.toggle())">Contact Us</a>
    </div>
  </section>

</main>

<?php get_footer(); ?>
