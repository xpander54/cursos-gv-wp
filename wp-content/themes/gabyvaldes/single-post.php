<?php
/*
Template Name Posts: testimonios
*/
?>

<?php get_header(); ?>

	<section class="main">


			<!-- <section class="content-main" style="background: #fffefe; min-height: 500px;"> -->

				<!-- <div class="container"> -->
					
		
		<div class="container" style="width:inherit;">


					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12 content-testimonios content-main">
					  <!-- <div class="col-xs-12 col-sm-6 col-md-8 content-main reflexiones-col-left" id="col-cart-1"> -->

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

								<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
									
									<h2><?php the_title(); ?> </h2>
									
									<?php include (TEMPLATEPATH . '/inc/meta.php' ); ?>

									<div class="entry">
										
										<?php the_content(); ?>

										<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>
										
										<?php the_tags( 'Tags: ', ', ', ''); ?>

									</div>
									
									<?php edit_post_link('Editar Post','','.'); ?>
									
								</div>

							<?php comments_template();?>

							<?php endwhile; endif; ?>
					
						</div>

					  <!-- <div class="col-xs-6 col-md-4 content-main float-right reflexiones-col-right" id="col-cart-2">
					  
					  	<?php// get_sidebar(); ?>
					  
					  </div> -->
					</div>
		</div>


		</section>
		<!-- /main		 -->

		

					

<?php get_footer(); ?>

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/gv.js"></script>
