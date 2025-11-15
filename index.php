<?php if ( ! defined( 'ABSPATH' ) ) { exit; } get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <!-- <h1><?php the_title(); ?></h1> -->
    <div class="entry-content"><?php the_content(); ?></div>
  </article>
<?php endwhile; else: ?>
  <p><?php esc_html_e( 'No content found.', 'exam-nibbles-minimal' ); ?></p>
<?php endif; ?>
<?php get_footer(); ?>
