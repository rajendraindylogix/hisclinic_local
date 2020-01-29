<div class="container">
	<div class="left-column">
		<div id="sticky-anchor"></div>
		<div id="menusticky" class="menu-container">
		<?php wp_nav_menu( array( 'menu' => 'SEO Sidebar Menu' ) ); ?>
		</div>
	</div>
	<div class="right-column">
		<div class="section-1">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-12">
					<h1 class="title"><?php the_title(); ?></h1>
					<div class="text-container">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</div>
		<?php if ( has_post_thumbnail()) : ?>
			<div class="featured-image-section">
				<div class="container">
					<div class="row">
						<div class="col-lg-2 col-md-2 col-12"></div>
						<div class="col-lg-8 col-md-8 col-12">	
							<?php the_post_thumbnail(); ?>
						</div>
						<div class="col-lg-2 col-md-2 col-12"></div>
					</div>
				</div>
			</div>	
		<?php endif; ?>
		<?php if( get_field('section_2_content') ): ?>
			<div class="section-2">
				<div class="container">
					<div class="row">
						<div class="col-lg-2 col-md-2 col-12"></div>
						<div class="col-lg-8 col-md-8 col-12">
							<?php the_field('section_2_content'); ?>
						</div>
						<div class="col-lg-2 col-md-2 col-12"></div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if( have_rows('boxes_section') ): ?>
			<div class="section-3">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-12">
							<div class="text-container">			
								<?php while( have_rows('boxes_section') ): the_row(); 
									$image = get_sub_field('image');
									$content = get_sub_field('text');				
									?>			
									<div class="box">				
										<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt'] ?>" height="509" width="531" />
									    <p><?php echo $content; ?></p>			
									</div>				
								<?php endwhile; ?>	
							<div class="clear"></div>			
							</div>	
						</div>
					</div>
			</div>			
		<?php endif; ?>
		<?php if( have_rows('sections') ): ?>
			<?php while( have_rows('sections') ): the_row(); 		
				$title = get_sub_field('section_title');
				$content = get_sub_field('section_content');
				$link = get_sub_field('section_link');
				$id = get_sub_field('section_id');
			?>
			<div class="seo-section" id="<?php echo $id; ?>">
				
					<div class="row">		
						<div class="col-lg-12 col-md-12 col-12">
							<h2 class="section-title"><a href="<?php echo $link; ?>"><?php echo $title; ?></a></h2>
						    <?php echo $content; ?>
							<a href="<?php echo $link; ?>" class="read-more">Read More</a>
						</div>		
					</div>
				
			</div>
			<?php endwhile; ?>	
		<?php endif; ?>
		<div class="learn-more-section">
			
				<div class="row">
					<div class="col-lg-12 col-md-12 col-12 cards-container">
						<?php if( get_field('learn_more_section_intro') ): ?>
							<h2><?php the_field('learn_more_section_intro'); ?></h2>
						<?php endif; ?>
						<?php if( have_rows('learn_more_cards') ): ?>
							<?php while( have_rows('learn_more_cards') ): the_row(); 		
								$title = get_sub_field('title');
								$image = get_sub_field('image');
								$link = get_sub_field('link'); ?>
								<div class="col-lg-6 col-md-6 col-sm-6 col-6 desktop">
									<div class="card" style="background-image:url(<?php echo $image['url']; ?>);">
										<a href="<?php echo $link; ?>">
											<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt'] ?>" height="754" width="498" />
											<h3><?php echo $title; ?></h3>
										</a>
									</div>
								</div>
							<?php endwhile; ?>	
						<?php endif; ?>		
						<?php if( have_rows('learn_more_cards') ): ?>
							<div class="autoplay mobile-devices">
							<?php while( have_rows('learn_more_cards') ): the_row(); 		
								$title = get_sub_field('title');
								$image = get_sub_field('image');
								$link = get_sub_field('link'); ?>
								<div class="card" style="background-image:url(<?php echo $image['url']; ?>);">
									<a href="<?php echo $link; ?>">
										<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt'] ?>" height="754" width="498" />
										<h3><?php echo $title; ?></h3>
									</a>
								</div>
							<?php endwhile; ?>	
							</div>
						<?php endif; ?>			
					</div>
				</div>
			
		</div>
	</div>
	<div class="clear"></div>
</div>
<div id="menu-stops" class="get-started-section">
	<div class="container">
		<div class="row">
			<div class="col-lg-2 col-md-2 col-12"></div>
			<div class="col-lg-8 col-md-8 col-12">
				<h2>Get started</h2>
				<p>We’re here to help you treat your ED in a way that’s quick, simple, and discreet. Talk with a friendly member of our all-male customer service team now or check your eligibility.</p>
				<a href="#" class="check">Check Eligibility</a>
			</div>
			<div class="col-lg-2 col-md-2 col-12"></div>
		</div>
	</div>
</div>