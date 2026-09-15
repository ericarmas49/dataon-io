<!-- /*  
*
* Template Name: Home
*
*
*/ -->


<?php get_header(); ?>

<main id="main" class="main main-home">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <section id="post-<?php the_ID(); ?>" <?php post_class(); ?> aria-label="<?php echo esc_attr( get_the_title() ); ?>">
        <div class="entry-content" itemprop="mainContentOfPage">
            <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) ); } ?>
            <?php
            $blocks = parse_blocks( get_the_content( null, false, get_the_ID() ) );
            $output = '';

            foreach ( $blocks as $block ) {
                $output .= render_block( $block );
            }

            if ( $output ) {
                // Render blocks directly — avoid the_content filters (wpautop strips layout / wraps cards in <p>).
                echo $output;
            } else {
                the_content();
            }
            ?>
            <div class="entry-links"><?php wp_link_pages(); ?></div>
        </div>
    </section>
    <?php if ( comments_open() && !post_password_required() ) { comments_template( '', true ); } ?>
    <?php endwhile; endif; ?>

<?php get_template_part('section', 'home-cta'); ?>
</main>




<?php get_footer(); ?>
