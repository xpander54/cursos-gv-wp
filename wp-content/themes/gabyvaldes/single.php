<?php get_header(); ?>

	<section class="main">


			<section class="content-main" style="background: #fffefe; min-height: 500px;">

				<!-- <div class="container"> -->
					


					<div class="row">
						



						<!-- <div class="col-md-8"> -->
						<div class="col-xs-12 col-sm-8 col-md-8">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

								<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
									
									<h2><?php the_title(); ?> </h2>
									
									<?php include (TEMPLATEPATH . '/inc/meta.php' ); ?>

									<div class="entry">
										
										<?php the_content(); ?>

										<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>
										
										<?php the_tags( 'Tags: ', ', ', ''); ?>

									</div>
									
									<?php/*edit_post_link('Editar curso','','.');*/ ?>
									
								</div>

							<?php/* comments_template();*/ ?>

						<?php endwhile; endif; ?>
	
					</div> 

			
						<!-- <div class="col-xs-4 col-md-4"> -->
						<div class="col-xs-4 col-md-4 f-right padding-2em">
								
							<?php get_sidebar(); ?>

						</div>


					
					</div>

				<!-- </div> -->
				

			</section>
					<!-- /section content-main -->

		</section>
		<!-- /section main -->

<?php get_footer(); ?>