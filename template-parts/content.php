<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package LBWD-2024
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php
				lbwd2024_posted_on();
				lbwd2024_posted_by();
				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</div><!-- .entry-header -->

<div class="post-content">
    <?php
		if ( is_singular() ) : ?>
    	<div class="entry-content">

		<?php
		the_content( sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'lbwd2024' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		) );

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'lbwd2024' ),
			'after'  => '</div>',
		) );
		?>
	</div><!-- .entry-content -->
			
		<?php else :?>
    
    <?php if ( has_post_thumbnail() ) { ?>
       <div class="entry-thumbnail">
        <?php the_post_thumbnail('feat-thumb'); ?>
    </div>
    <?php }
    else {

    }
    ?>

	<div class="entry-content">

		<?php
		the_excerpt();

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'lbwd2024' ),
			'after'  => '</div>',
		) );
		?>
	</div><!-- .entry-content -->
<?php	endif;?>
    
</div>

	<footer class="entry-footer">
		<?php lbwd2024_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->

