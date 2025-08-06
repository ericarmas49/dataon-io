<div class="entry-content" itemprop="mainEntityOfPage">
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="mod-featured-img" style="background-image: url(<?php the_post_thumbnail_url( 'full' ); ?>);"></div>

    <?php endif; ?>

    <meta itemprop="description" content="<?php echo esc_html( wp_strip_all_tags( get_the_excerpt(), true ) ); ?>" />
        <?php the_content(); ?>
    <div class="entry-links">
        <?php wp_link_pages(); ?>
    </div>
    
</div>
