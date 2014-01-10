<?php get_header(); ?>

		<section class="main">


			<section class="content-main" style="background: #fffefe; min-height: 500px;">

				<!-- <div class="container"> -->
					


					<div class="row">
						



						<!-- <div class="col-md-8"> -->
						<div class="col-xs-12 col-sm-8 col-md-8">
						
							<?php if (have_posts()) : ?>

					 			<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>

								<?php /* If this is a category archive */ if (is_category()) { ?>
									<!-- <h2>Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2> -->

								<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
									<h2>Cursos dentro del tag &#8216;<?php single_tag_title(); ?>&#8217;</h2>

								<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
									<h2>Cursos del día  <?php the_time('F jS, Y'); ?></h2>

								<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
									<h2>Cursos del mes <?php the_time('F, Y'); ?></h2>

								<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
									<h2>Cursos en el año <?php the_time('Y'); ?></h2>

								<?php /* If this is an author archive */ } elseif (is_author()) { ?>
									<h2>Cursos del Autor</h2>

								<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
									<h2>Archivo</h2>
								
								<?php } ?>

								<?php include (TEMPLATEPATH . '/inc/nav.php' ); ?>

								<?php while (have_posts()) : the_post(); ?>
								
									<div <?php post_class() ?>>
									
										<h2 class="posts-h2" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>"><?php the_title(); ?></h2>
										
										<!-- <?php // include (TEMPLATEPATH . '/inc/meta.php' ); ?> -->

										<div class="entry">

											<img src="<?php echo catch_first_post_image() ?>" alt="imagen curso">
											 <?php the_excerpt(); ?> </a>
										
											
										</div>
	
									</div>

								<?php endwhile; ?>

								<?php include (TEMPLATEPATH . '/inc/nav.php' ); ?>
								
							<?php else : ?>

								<h2>Nothing found</h2>

							<?php endif; ?>

						</div> 

			
						<!-- <div class="col-xs-4 col-md-4"> -->
						<div class="col-xs-4 col-md-4 f-right">
								
							<?php get_sidebar(); ?>

						</div>


					
					</div>

				<!-- </div> -->
				

			</section>
					<!-- /section content-main -->

		</section>
		<!-- /section main -->

<?php get_footer(); ?>

