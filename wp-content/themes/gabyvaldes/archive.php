<?php get_header(); ?>

		<section class="main">


			<!-- <section class="content-main" style="background: #fffefe; min-height: 500px;"> -->

				<div class="container" style="width:inherit;">


					<div class="row">
					  <div class="col-xs-12 col-sm-6 col-md-8 content-main reflexiones-col-left" id="col-cart-1">

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

												<img src="<?php echo catch_first_post_image() ?>" alt="imagen curso"></a>
												 <?php the_excerpt(); ?> 
											
												
											</div>
		
										</div>

									<?php endwhile; ?>

									<?php include (TEMPLATEPATH . '/inc/nav.php' ); ?>
									
								<?php else : ?>

									<h2>No hay cursos</h2>

							<?php endif; ?>

					  </div>
					  <div class="col-xs-6 col-md-4 content-main float-right reflexiones-col-right" id="col-cart-2">

					  	<?php get_sidebar(); ?>

					  </div>
					</div>
			
			<!-- </section> -->
			<!-- /content-main -->

			</div>
			<!-- /container -->

				
					


					

		</section>
		<!-- /section main -->

<?php get_footer(); ?>


 <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/gv.js"></script>

<script type="text/javascript">



	$j=jQuery.noConflict();
	$j(document).ready(function() {
		
		resizeCols();
		//alert(".edd_purchase_submit_wrapper");
		//$j(".edd_purchase_submit_wrapper").on( "click", columnResize );
		// $j( ".reflexiones-col-right" ).on( "click", columnResize );

	});

</script>

