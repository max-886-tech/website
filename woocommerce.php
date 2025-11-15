<?php
/**
 * Template Name: WooCommerce
 * Description: Minimal wrapper for WooCommerce pages.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header( 'shop' ); ?>
<?php woocommerce_content(); ?>
<?php get_footer( 'shop' ); ?>
