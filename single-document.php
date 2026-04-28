<?php get_header(); ?>

<style>

.entry-content  
{
	display: flex;
}

p 
{
	flex: 5;
	margin-left: 3rem;
}

article .mod-featured-img
{
	flex: 1;
	width: 250px;
	height: 250px;
}

.single-document #wrapper {
	min-height: 100vh;
	display: flex;
	flex-direction: column;
}

.single-document #container-fluid {
	flex: 1 0 auto;
}

.single-document #footer {
	margin-top: auto;
}

.document-download {
	margin: 1.5rem 0;
}

.document-download a {
	display: inline-block;
	background-color: #00adef;
	color: #ffffff;
	font-size: 20px;
	padding: 20px;
	border-radius: 4px;
	font-weight: 600;
	text-decoration: none;
	transition: background-color 0.2s ease;
}

.document-download a:hover,
.document-download a:focus {
	background-color: #008fc6;
	color: #ffffff;
}

.document-preview {
	margin-top: 1.5rem;
}

.document-preview iframe {
	width: 100%;
	min-height: 900px;
	border: 1px solid #d9d9d9;
}

</style>



<main id="main" class="main main-single document">
	<div class="container">
		<div class="row">
			<?php $postLayout = get_field('post_layout'); ?>
			<!-- <div class="col-12 <?php echo ($postLayout === 'sidebar' || $postLayout === '') ? 'col-lg-9' : ''; ?>"> -->

			<div class="col-12">

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'entry' ); ?>

					<?php
					$document_url = get_field( 'document' );
					$document_ext = strtolower( pathinfo( (string) $document_url, PATHINFO_EXTENSION ) );
					$is_pdf = ( $document_ext === 'pdf' );
					?>

					<?php if ( !empty( $document_url ) ): ?>
    					<div class="document-download">
							<a href="<?php echo esc_url( $document_url ); ?>" target="_blank" rel="noopener">Download Document</a>
						</div>

						<?php if ( $is_pdf ): ?>
							<div class="document-preview">
								<h3>Preview</h3>
								<iframe src="<?php echo esc_url( $document_url ); ?>#toolbar=1&navpanes=0" title="Document preview" loading="lazy"></iframe>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				<?php endwhile; endif; ?>
			</div>

			<?php // if($postLayout === 'sidebar' || $postLayout === '') : ?>
				<!-- <div class="col-12 col-lg-3"> -->
					<?php // get_sidebar(); ?>
				<!-- </div> -->
			<?php // endif; ?>

		</div>
	</div>
</main>

<?php get_footer(); ?>