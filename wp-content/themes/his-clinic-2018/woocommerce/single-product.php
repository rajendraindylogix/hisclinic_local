<?php  
/**
 * Product single page
 */
get_header();

$username = '';

if ( is_user_logged_in() ) {        
    $user     = wp_get_current_user();
    $username = $user->user_firstname;
}
$_pf = new WC_Product_Factory(); 

while( have_posts() ) : the_post();

$product = $_pf->get_product( get_the_ID() );
$p_attributes = $product->get_variation_attributes();
$is_product_allowed = is_product_allowed($product);
?>

<?php if ( is_user_logged_in() ) : ?>
    <div class="single-product-wrapper">
        <div class="container">
			<div class="row">
			    <div class="col-md-6">
			        <div class="single-product-image">
			            <?php 
			            //echo woocommerce_show_product_images(); 
			            the_post_thumbnail( 'product-featured' );
			            ?>
			        </div>
			    </div>
			    <div class="col-md-6">
			        <div class="product-description">
			            <?php the_title( '<h2>', '</h2>' ); ?>
			            <?php
			                foreach ( $p_attributes as $attribute_name => $options ) : 
			                    $selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
			                endforeach;
			            ?>
			            
			            <?php if ($is_product_allowed): ?>
			                <p>
			                    <?php printf( __( 'Hi %1$s, the doctor has recommended %2$s, with %3$s tablets as your recommended packet size.', 'woocommerce' ), $username, get_the_title(), $selected ); ?>
			                </p>
			                
			                <div class="product-desc-list">
			                    <?php echo woocommerce_template_single_excerpt(); ?>
			                </div>
			                
			                <!-- product-desc-list -->
			                <div class="start-order">
			                    <div class="order-btn">
			                        <a href="<?php 
			                            $permalink = home_url( '/order-details' );
			                            $permalink = add_query_arg( 'prod_id', get_the_ID(), $permalink );
			
			                            echo esc_url( $permalink );
			                        
			                        ?>"><?php _e( 'Start Your Order', 'woocommerce' ); ?> <img src="<?php echo get_template_directory_uri() ?>/assets/css/img/arrow-white.png"></a>
			                    </div>
			
			                    <p class="price">
			                        <?php echo $product->get_price_html() ?>
			                    </p>
			                </div>
			            <?php else: ?>
			                <p><?php echo get_field('message_for_not_allowed_product', 'option') ?></p>
			
			                <a href="<?php echo home_url('my-account/request-treatment-change') ?>" class="btn filled auto">
			                    <span class="text">Request Treatment Change</span>
			                </a>
			            <?php endif ?>
			        </div>
			    </div>
			</div>
        </div>
    </div>
    <!-- end product section -->

	<div class="product-information">
	    <div class="container">
	        <ul class="nav nav-tabs mobile-nav" id="myTab" role="tablist">
	            <?php 
	                $about_tab = function_exists( 'get_field' ) && ! empty( get_field( 'about_tab' ) ) ? get_field( 'about_tab' ) : false;
	
	                if ( $about_tab ) :
	
	                    $tab_icon  = isset( $about_tab['tab_icon'] ) && ! empty( $about_tab['tab_icon'] ) ? $about_tab['tab_icon'] : false;
	                    $tab_title = isset( $about_tab['tab_title'] ) && ! empty( $about_tab['tab_title'] ) ? $about_tab['tab_title'] : false;
	
	                    if ( $tab_title ) :
	            ?>
	                        <li class="nav-item active">
	                            <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><img class="svg" src="<?php echo esc_url( $tab_icon ); ?>"> <?php echo esc_html( $tab_title ); ?></a>
	                        </li>
	
	                    <?php endif; ?>
	
	            <?php endif; ?>
	
	            <?php 
	                $dosage_protocol = function_exists( 'get_field' ) && ! empty( get_field( 'dosage_protocol' ) ) ? get_field( 'dosage_protocol' ) : false;
	
	                if ( $dosage_protocol ) :
	
	                    $tab_icon  = isset( $dosage_protocol['tab_icon'] ) && ! empty( $dosage_protocol['tab_icon'] ) ? $dosage_protocol['tab_icon'] : false;
	                    $tab_title = isset( $dosage_protocol['tab_title'] ) && ! empty( $dosage_protocol['tab_title'] ) ? $dosage_protocol['tab_title'] : false;
	
	                    if ( $tab_title ) :
	            ?>
	            
	                        <li class="nav-item">
	                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"> <img class="svg" src="<?php echo esc_url( $tab_icon ); ?>"> <?php echo esc_html( $tab_title ); ?></a>
	                        </li>
	
	                    <?php endif; ?>
	
	            <?php endif; ?>
	
	            <?php 
	                $safety_information = function_exists( 'get_field' ) && ! empty( get_field( 'safety_information' ) ) ? get_field( 'safety_information' ) : false;
	
	                if ( $safety_information ) :
	
	                    $tab_icon  = isset( $safety_information['tab_icon'] ) && ! empty( $safety_information['tab_icon'] ) ? $safety_information['tab_icon'] : false;
	                    $tab_title = isset( $safety_information['tab_title'] ) && ! empty( $safety_information['tab_title'] ) ? $safety_information['tab_title'] : false;
	
	                    if ( $tab_title ) :
	            ?>
	            
	                        <li class="nav-item">
	                            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false"> <img class="svg" src="<?php echo esc_url( $tab_icon ); ?>"> <?php echo esc_html( $tab_title ); ?></a>
	                        </li>
	
	                    <?php endif; ?>
	
	            <?php endif; ?>
	
	            
	            <?php 
	                $faq_tab = function_exists( 'get_field' ) && ! empty( get_field( 'faq_tab' ) ) ? get_field( 'faq_tab' ) : false;
	
	                if ( $faq_tab ) :
	
	                    $tab_icon  = isset( $faq_tab['tab_icon'] ) && ! empty( $faq_tab['tab_icon'] ) ? $faq_tab['tab_icon'] : false;
	                    $tab_title = isset( $faq_tab['tab_title'] ) && ! empty( $faq_tab['tab_title'] ) ? $faq_tab['tab_title'] : false;
	
	                    if ( $tab_title ) :
	            ?>
	                        <li class="nav-item">
	                            <a class="nav-link" id="faq-tab" data-toggle="tab" href="#faq" role="tab" aria-controls="faq" aria-selected="false"><img class="svg" src="<?php echo esc_url( $tab_icon ); ?>"> <?php echo esc_html( $tab_title ); ?></a>
	                        </li>
	                    
	                    <?php endif; ?>
	
	                <?php endif; ?>
	
	            <?php 
	                $reviews_tab = function_exists( 'get_field' ) && ! empty( get_field( 'reviews_tab' ) ) ? get_field( 'reviews_tab' ) : false;
	
	                if ( $reviews_tab ) :
	
	                    $tab_icon  = isset( $reviews_tab['tab_icon'] ) && ! empty( $reviews_tab['tab_icon'] ) ? $reviews_tab['tab_icon'] : false;
	                    $tab_title = isset( $reviews_tab['tab_title'] ) && ! empty( $reviews_tab['tab_title'] ) ? $reviews_tab['tab_title'] : false;
	
	                    if ( $tab_title ) :
	            ?>
	                        <li class="nav-item">
	                            <a class="nav-link" id="review-tab" data-toggle="tab" href="#review" role="tab" aria-controls="review" aria-selected="false"><img class="svg" src="<?php echo esc_url( $tab_icon ); ?>"> <?php echo esc_html( $tab_title ); ?></a>
	                        </li>
	
	            <?php endif; ?>
	
	        <?php endif; ?>
	        
	        </ul>
	        <div class="tab-content" id="myTabContent">
	        
	        <?php 
	        
	            if ( $about_tab ) :
	                $tab_content  = isset( $about_tab['tab_content'] ) && ! empty( $about_tab['tab_content'] ) ? $about_tab['tab_content'] : false;
	
	                if ( $tab_content ) :
	                    
	            ?>
	                    <div class="tab-pane fade active" id="home" role="tabpanel" aria-labelledby="home-tab">
	                        <div class="inner-tab-content">
	                            <?php echo wp_kses_post( $tab_content ); ?>
	                        </div>
	                    </div>
	            <?php endif; 
	                endif;
	
	                if ( $dosage_protocol ) :
	                    $tab_content  = isset( $dosage_protocol['tab_content'] ) && ! empty( $dosage_protocol['tab_content'] ) ? $dosage_protocol['tab_content'] : false;
	    
	                    if ( $tab_content ) :
	                        
	                ?>
	                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
	                        <div class="inner-tab-content">
	                            <?php echo wp_kses_post( $tab_content ); ?>
	                        </div>
	                    </div>
	                <?php 
	                    endif;
	                endif;
	
	                if ( $safety_information ) :
	                    $tab_content  = isset( $safety_information['tab_content'] ) && ! empty( $safety_information['tab_content'] ) ? $safety_information['tab_content'] : false;
	    
	                    if ( $tab_content ) :
	                ?>
	                
	                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
	                            <div class="inner-tab-content">
	                                <?php echo wp_kses_post( $tab_content ); ?>
	                            </div>
	                        </div>
	                <?php 
	                    endif;
	                endif;
	
	                if ( $faq_tab ) :
	
	                    $faq_content = isset( $faq_tab['faq_content'] ) && ! empty( $faq_tab['faq_content'] ) ? $faq_tab['faq_content'] : array();
	
	                ?>
	                    <div class="tab-pane fade" id="faq" role="tabpanel" aria-labelledby="contact-tab">
	                        <div class="inner-tab-content">
	                            <div class="accordion" id="accordionExample">
	                                <?php 
	                                    $i = 1;
	                                    foreach( $faq_content as $key => $fad ) :
	
	                                        $faq_question = isset( $fad['faq_question'] ) && ! empty( $fad['faq_question'] ) ? $fad['faq_question'] : false;
	                                        $faq_content = isset( $fad['faq_content'] ) && ! empty( $fad['faq_content'] ) ? $fad['faq_content'] : false;
	
	                                        $collaspe = $i === 1 ? 'collaspe' : 'collasped'; 
	                                ?>
	                                        <div class="card">
	                                            <div class="card-header" id="headingOne">
	                                                <?php if ( $faq_question ) : ?>
	                                                    <button class="btn-acordian <?php echo $i !== 1 ? 'collasped' : ''; ?>" type="button" data-toggle="collapse" data-target="#collapseOne<?php echo esc_attr( $key ); ?>" aria-expanded="<?php echo $i === 1 ? 'true' : 'false'; ?>" aria-controls="collapseOne<?php echo esc_attr( $key ); ?>">
	                                                        <?php echo esc_html( $faq_question ); ?>
	                                                    </button>
	                                                <?php endif; ?>
	                                            </div>
	                                            <?php if ( $faq_content ) : ?>
	                                                <div id="collapseOne<?php echo esc_attr( $key ); ?>" class="collapse <?php echo $i === 1 ? 'in' : ''; ?>" aria-labelledby="headingOne" data-parent="#accordionExample">
	                                                    <div class="card-body">
	                                                        <?php echo wp_kses_post( $faq_content ); ?>
	                                                    </div>
	                                                </div>
	                                            <?php endif; ?>
	                                        </div>
	                                <?php $i++; 
	                                    endforeach; ?>
	                            </div>
	                        </div>
	                    </div>
	                <?php endif; 
	                if ( $reviews_tab ) :
	
	                    $video_title = isset( $reviews_tab['video_title'] ) && ! empty( $reviews_tab['video_title'] ) ? $reviews_tab['video_title'] : false;
	
	                    $videos = isset( $reviews_tab['videos'] ) && ! empty( $reviews_tab['videos'] ) ? $reviews_tab['videos'] : array();
	            ?>
	                    <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="contact-tab">
	                        <div class="inner-tab-content">
	                            <?php if( $video_title ) : ?>
	                                <h4><?php echo esc_html( $video_title ); ?></h4>
	                            <?php endif; ?>
	                            <?php if ( ! empty( $videos ) ) : ?>
	                                <div class="main-slider">
	                                    <div class="slider slider-for">
	                                        <?php 
	                                            $thumbs = array();
	                                            
	                                            foreach( $videos as $k => $iframe ) :
	
	                                                $iframe = $iframe['video'];
	                                                
	                                                // use preg_match to find iframe src
	                                                preg_match('/src="(.+?)"/', $iframe, $matches);
	                                                $src = $matches[1];
	
	                                                $parsedURL = parse_url($src);
	                                                $host = $parsedURL['host'];
	
	                                                // add extra params to iframe src
	                                                $params = array(
	                                                    'controls'    => 1,
	                                                    'hd'          => 1,
	                                                    'autohide'    => 1,
	                                                    'enablejsapi' => 1,
	                                                    'api'         => 1,
	                                                );
	
	                                                $new_src = add_query_arg($params, $src);
	
	                                                $iframe = str_replace($src, $new_src, $iframe);
	
	                                                // add extra attributes to iframe html
	                                                $attributes = 'frameborder="0" class="embed-responsive-item"';
	
	                                                $iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);
	                        
	                                                preg_match('/embed(.*?)?feature/', $src, $matches_id );
	
	                                                if (strpos($host, 'youtube') !== false) {
	
	                                                    $id = $matches_id[1];
	                                                    $id = str_replace( str_split( '?/' ), '', $id );
	                                                    
	                                                    $thumbs[$k] = 'http://img.youtube.com/vi/' . $id . '/mqdefault.jpg';
	
	                                                } elseif (strpos($host, 'vimeo') !== false) {
	
	                                                    // if is hosted on vimeo, extract ID from the path property
	                                                    $vidQuery = $parsedURL['path'];
	
	                                                    $vidID = explode( '/', $vidQuery );
	
	                                                    $vidID = $vidID[2];
	                                                    // all data about vimeo videos is stored in api, like so:
	                                                    $hash = simplexml_load_file("https://vimeo.com/api/v2/video/$vidID.xml");
	                                                    // grab url for large thumb
	                                                    $thumbs[$k] = $hash->video[0]->thumbnail_large;
	                                                }
	                                        ?>
	                                                <div class="item <?php echo strpos($host, 'youtube') !== false ? 'youtube' : '';  ?><?php echo strpos($host, 'vimeo') !== false ? 'vimeo' : ''; ?>">
	                                                    <div class="embed-responsive embed-responsive-16by9">
	                                                        <?php echo $iframe; ?>
	                                                    </div>
	                                                </div>
	                                        <?php endforeach; ?>
	                                    </div>
	                                    <div class="slider slider-nav">
	                                        <?php foreach( $thumbs as $k => $thumb ) : ?>
	                                            <div class="thumbnail-video">
	                                                <img  src="<?php echo esc_url( $thumb ); ?>">
	                                            </div>
	                                        <?php endforeach; ?>
	                                    </div>
	                                </div>
	                            <?php else : ?>
	                                <!-- TrustBox widget - List -->
	                                <script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
	                                <div class="trustpilot-widget" data-locale="en-US" data-template-id="539ad60defb9600b94d7df2c" data-businessunit-id="5cd8bffe0fb4a100010dd10c" data-style-height="500px" data-style-width="100%" data-theme="light" data-stars="4,5">
	                                <a href="https://www.trustpilot.com/review/hisclinic.com" target="_blank" rel="noopener">Trustpilot</a>
	                                </div>
	                                <!-- End TrustBox widget -->
	                        <?php endif; ?>
	                        </div>
	                    </div>
	            <?php endif; ?>
	        </div>
	    </div>
	</div>

<?php else : ?>

	<div class="single-product-wrapper">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<h1 class="h3">You need to login to access product information and order products.</h1>
					<p><a href="<?php echo home_url('my-account/') ?>" class="btn filled">
						<span class="text">Login</span>
					</a></p>
					<br>
					<h2 class="h4">Don't have an account yet?</h2>
					<p><a href="<?php echo home_url('medical-form/') ?>" class="btn filled">
						<span class="text">Check Eligibility</span>
					</a></p>
				</div>
			</div>
		</div>
    </div>

<?php endif; ?>

<?php
the_content();

endwhile;

get_footer();
