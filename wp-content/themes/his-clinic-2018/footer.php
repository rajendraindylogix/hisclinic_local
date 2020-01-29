<?php
	$sm = get_field('social_media', 'option');
	$content = get_field('footer_content', 'option');

	// No need to check for empty
	$column_1 = $content[0]['column_1'][0];
	$column_2 = $content[0]['column_2'][0];
	$column_3 = $content[0]['column_3'][0];
	$column_4 = $content[0]['column_4'][0];
?>
	<footer class="main-footer">
		<div class="container">
			<?php if ( $sm ) : ?>
				<div class="line-1">
					<div class="row">
						<div class="col-sm-6 sm">
								<?php foreach ($sm as $s): ?>
									<a href="<?php echo $s['url'] ?>" target="_blank">
										<img src="<?php echo $s['icon']['url'] ?>" alt="<?php echo $s['name'] ?>">
									</a>
								<?php endforeach; ?>
						</div>
						<!-- <div class="col-sm-6">
							<div class="newsletter-signup">
								<div id="mc_embed_signup">
									<form action="https://hisclinic.us19.list-manage.com/subscribe/post?u=51fe2ebc7592adc47d2357f71&amp;id=ede6b98756" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
										<div id="mc_embed_signup_scroll">
											<div class="mc-field-group field input-text">
												<input type="email" name="EMAIL" id="mce-EMAIL" class="required email" placeholder="Your Email">
												<button type="submit" class="btn filled" id="mc-embedded-subscribe"><span>Subscribe</span></button>
											</div>

											<div id="mce-responses" class="clear">
												<div class="response" id="mce-error-response" style="display:none"></div>
												<div class="response" id="mce-success-response" style="display:none"></div>
											</div>

											<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_51fe2ebc7592adc47d2357f71_ede6b98756" tabindex="-1" value=""></div>
										</div>
									</form>
								</div>

								<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
								<script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[3]='ADDRESS';ftypes[3]='address';fnames[4]='PHONE';ftypes[4]='phone';fnames[5]='BIRTHDAY';ftypes[5]='birthday';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
							</div>
						</div> -->
					</div>
				</div>
			<?php endif; ?>

			<div class="line-2">
				<div class="row">
					<div class="col-sm-3 col-sm-push-4 column-3">
						<?php if ($column_3['title']): ?>
							<h3><?php echo $column_3['title'] ?></h3>
						<?php endif; ?>

						<?php echo $column_3['content'] ?>
					</div>
					<div class="col-sm-3 col-sm-push-4 column-4">
						<?php if ($column_4['title']): ?>
							<h3><?php echo $column_4['title'] ?></h3>
						<?php endif; ?>

						<?php echo $column_4['content'] ?>
					</div>
					<div class="col-sm-3 col-sm-push-4 column-2">
						<?php if ($column_2['title']): ?>
							<h3><?php echo $column_2['title'] ?></h3>
						<?php endif; ?>

						<?php echo $column_2['content'] ?>
					</div>
					<div class="col-sm-3 col-sm-pull-9 column-1">
						<?php if ($column_1['title']): ?>
							<h3><?php echo $column_1['title'] ?></h3>
						<?php endif; ?>

						<?php echo $column_1['content'] ?>
					</div>
				</div>
				<p><small>Website by <a href="https://jaywing.com.au/" target="_blank">Jaywing</a></small></p>
			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>

	<!-- <script type="text/javascript">
		(function(d, src, c) { var t=d.scripts[d.scripts.length - 1],s=d.createElement('script');s.id='la_x2s6df8d';s.async=true;s.src=src;s.onload=s.onreadystatechange=function(){var rs=this.readyState;if(rs&&(rs!='complete')&&(rs!='loaded')){return;}c(this);};t.parentElement.insertBefore(s,t.nextSibling);})(document, 'https://hisclinic.ladesk.com/scripts/track.js', function(e){ LiveAgent.createButton('7b0d4bcb', e); });
	</script> -->
<!-- Start of hisclinic Zendesk Widget script -->
<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=ec633711-27f3-4835-86e7-77afcf6c19b5"> </script>
<!-- End of hisclinic Zendesk Widget script -->
<script type="text/javascript">
var downicon = document.querySelectorAll(".download-icon");
for (i = 0; i < downicon.length; ++i) {
  downicon[i].firstElementChild.style.display = "none";
  downicon[i].innerHTML = "Download Fact Sheet";
}
</script>
</body>
</html>
