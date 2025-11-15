<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php if ( function_exists('wp_body_open') ) { wp_body_open(); } ?>

<header class="site-header">
  <div class="en-header">
    <div class="en-header__inner">

      <!-- Burger (mobile) -->
      <button class="en-menu-toggle" aria-controls="en-qlist" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle menu','exam-nibbles'); ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18v2H3zM3 11h18v2H3zM3 16h18v2H3z"/></svg>
      </button>

      <!-- Logo (no extra anchor wrapper) -->
      <div class="en-logo" role="banner">
        <?php if ( has_custom_logo() ) : ?>
          <?php the_custom_logo(); ?>
        <?php else : ?>
          <a class="en-site-title" href="<?php echo esc_url( home_url('/') ); ?>"><?php bloginfo('name'); ?></a>
        <?php endif; ?>
      </div>


        <!-- Search icon (mobile) -->
        <button class="en-search-toggle" aria-controls="en-search" aria-expanded="false"
          aria-label="<?php esc_attr_e('Toggle search','exam-nibbles'); ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"
              fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="M21 21l-4.35-4.35"></path>
          </svg>
        </button>

      <!-- Search (drops below top bar on phones) -->
      <div class="en-searchwrap" id="en-search">
        <?php get_search_form(); ?>
      </div>

      <!-- Quick links / Menu -->
      <nav class="en-quicklinks" id="en-qlist" aria-label="Quick links" data-open="false">
        <ul>
          <li><a href="<?php echo esc_url( home_url('/vendors/') ); ?>"><?php esc_html_e('Vendors','exam-nibbles'); ?></a></li>
          <li>
            <a class="en-cart" href="<?php echo function_exists('wc_get_cart_url') ? esc_url( wc_get_cart_url() ) : esc_url( home_url('/cart/') ); ?>">
              <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm10 0a2 2 0 1 0-.001 3.999A2 2 0 0 0 17 18ZM6.2 6l.5 2H21l-2 7H8l-2.3-9H3V4h3.8l-.2.8Z"/></svg>
              <span><?php esc_html_e('Cart','exam-nibbles'); ?></span>
              <?php if ( function_exists('WC') && WC()->cart ) : ?>
                <em class="en-cart-count"><?php echo (int) WC()->cart->get_cart_contents_count(); ?></em>
              <?php endif; ?>
            </a>
          </li>
          <li>
            <?php $account_url = function_exists('wc_get_page_id') ? get_permalink( wc_get_page_id('myaccount') ) : wp_login_url(); ?>
            <a class="en-login" href="<?php echo esc_url($account_url); ?>">
              <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.4 0-8 2.2-8 5v1h16v-1c0-2.8-3.6-5-8-5Z"/></svg>
              <span><?php echo is_user_logged_in() ? esc_html__('My Account','exam-nibbles') : esc_html__('Log in','exam-nibbles'); ?></span>
            </a>
          </li>
        </ul>
      </nav>

    </div>
  </div>
</header>
<script>
/* Tiny vanilla toggles for mobile menu & search */
(function(){
  var menuBtn   = document.querySelector('.en-menu-toggle');
  var nav       = document.getElementById('en-qlist');
  var searchBtn = document.querySelector('.en-search-toggle');
  var searchBox = document.getElementById('en-search');

  function setMenu(open){
    if(!nav) return;
    nav.setAttribute('data-open', open ? 'true' : 'false');
    menuBtn && menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  function setSearch(open){
    if(!searchBox) return;
    searchBox.setAttribute('data-open', open ? 'true' : 'false');
    searchBtn && searchBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    if(open){
      setTimeout(function(){
        var input = searchBox.querySelector('.search-field');
        if(input){ try{ input.focus(); }catch(e){} }
      }, 0);
    }
  }

  menuBtn && menuBtn.addEventListener('click', function(){
    var isOpen = nav && nav.getAttribute('data-open') === 'true';
    setMenu(!isOpen);
    setSearch(false);
  });

  searchBtn && searchBtn.addEventListener('click', function(){
    var isOpen = searchBox && searchBox.getAttribute('data-open') === 'true';
    setSearch(!isOpen);
    setMenu(false);
  });

  // Close on outside click
  document.addEventListener('click', function(e){
    if(nav && !nav.contains(e.target) && !menuBtn?.contains(e.target)) setMenu(false);
    if(searchBox && !searchBox.contains(e.target) && !searchBtn?.contains(e.target)) setSearch(false);
  });

  // Close on ESC
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){ setMenu(false); setSearch(false); }
  });

  // Close menu after link tap
  nav && nav.addEventListener('click', function(e){
    if(e.target.closest('a')) setMenu(false);
  });
})();
</script>

<main class="site-container">
<!-- Your page content starts here -->
