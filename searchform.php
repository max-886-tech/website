<?php
/**
 * Product-only search form
 */
?>
<form role="search" method="get" class="search-form en-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
  <label class="screen-reader-text" for="en-s"><?php echo esc_html_x( 'Search for:', 'label', 'exam-nibbles' ); ?></label>
  <input type="search"
         id="en-s"
         class="search-field"
         placeholder="<?php echo esc_attr__( 'Search exams....', 'exam-nibbles' ); ?>"
         value="<?php echo esc_attr( get_search_query() ); ?>"
         name="s"
         autocomplete="off" />
  <input type="hidden" name="post_type" value="product" />
  <button type="submit" class="search-submit" aria-label="<?php echo esc_attr__( 'Search', 'exam-nibbles' ); ?>">
    <!-- simple search icon -->
    <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 18a8 8 0 1 1 5.3-14.05A8 8 0 0 1 10 18zm0-2a6 6 0 1 0 0-12 6 6 0 0 0 0 12Zm8.61 3.39-4.24-4.24 1.41-1.41 4.24 4.24-1.41 1.41Z"/></svg>
  </button>
</form>
