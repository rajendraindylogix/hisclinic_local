<?php
session_start();

/*
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
*/
define( 'HC_GOOGLE_MAPS_API_KEY', 'AIzaSyA5KUOXuBl_Q7haumG8rSmrtvWaNWEwx2k' );

require_once('inc/theme-setup.php');
require_once('inc/ajax-calls.php');
require_once('inc/class-medical-form.php');
require_once('inc/medical-form-admin.php');
require_once('inc/woocommerce-custom.php');

//Medical form
require_once('medical-form/core-medicalform.php');

//Shortcodes
require_once('inc/shortcodes.php');

/* Enqueues scripts and styles. */
function theme_scripts() {

	// Get the Theme Version
	$this_theme = wp_get_theme();
	$this_theme_version = '2.0.13';//$this_theme->get( 'Version' );

	// Theme stylesheet.
	wp_enqueue_style( 'bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), $this_theme_version );
	wp_enqueue_style( 'theme-style', get_template_directory_uri() . '/assets/css/custom.css', array(), $this_theme_version );

	wp_enqueue_style( 'hisclinic-product-css', get_template_directory_uri() . '/assets/css/products.css', array(), $this_theme_version );
	wp_enqueue_style( 'hisclinic-woocommerce account-css', get_template_directory_uri() . '/assets/css/waccount.css', array(), $this_theme_version );

	// Load Javascript
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_enqueue_script( 'bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery' ), $this_theme_version );
	wp_enqueue_script( 'theme-validate', 'https://cdn.jsdelivr.net/npm/jquery-validation@1.15.0/dist/jquery.validate.min.js', array( 'jquery' ), null );
	wp_enqueue_script( 'theme-additional-methods', 'https://cdn.jsdelivr.net/npm/jquery-validation@1.15.0/dist/additional-methods.min.js', array( 'jquery' ), null );
	wp_enqueue_script( 'theme-mask', get_template_directory_uri() . '/assets/js/jquery.mask.min.js', array( 'jquery' ), $this_theme_version );
	wp_enqueue_script( 'jquery-slick' );
	wp_enqueue_script( 'jquery-lightbox_me', get_template_directory_uri() . '/assets/js/jquery.lightbox_me.js', array( 'jquery' ), $this_theme_version );
	wp_enqueue_script( 'theme-script', get_template_directory_uri() . '/assets/js/scripts.js', array( 'jquery', 'wp-util' ), $this_theme_version );
	wp_localize_script(
		'theme-script',
		'wp_paths',
		[
			'admin' => admin_url('admin-ajax.php'),
			'theme' => theme(),
			'home_url' => home_url(),
			'mf_admision_date' => get_field('step_12_b_label'),
			'mf_admision_description' =>get_field('step_12_b_description'),
		]
	);
	wp_enqueue_script( 'hc-google-maps-script', 'https://maps.googleapis.com/maps/api/js?key=' . HC_GOOGLE_MAPS_API_KEY .'&libraries=places', array( 'jquery' ), '', true );
}
add_action( 'wp_enqueue_scripts', 'theme_scripts', 20 );

function irt_add_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'irt_add_woocommerce_support' );


/* Returns template path */
function theme() {
	return get_template_directory_uri();
}

/* Prints assets folder */
function assets() {
	echo theme() . '/assets';
}

/* Prints images folder */
function img() {
	echo theme() . '/assets/img';
}

/* Prints template part with arguments */
function get_template_part_with_args($slug, $args = array(), $echo = true) {
	global $part_args;

	$part_args = $args;

	if (!$echo) {
		ob_start();
	}

	get_template_part($slug);
	$part_args = null;

	if (!$echo) {
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}

/* Google Maps API for ACF */
function acf_google_map_api($api) {
	$api['key'] = get_field('google_maps_api', 'option');
	return $api;
}
add_filter('acf/fields/google_map/api', 'acf_google_map_api');

/* Prints image with different resolutions */
function res_img($img, $defaultSize = null) {
	if (is_array($img) && !empty($img['sizes'])) {
		$src = ($defaultSize) ? $img['sizes'][$defaultSize] : $img['url'];
		$srcset = "{$src} 991w, {$img['sizes']['medium_large']} 767w";
		$sizes = "(max-width: 767px) 767px";
		$alt = $img['alt'];
		$w = $img['width'];
		$h = $img['height'];

		echo "<img src='$src' srcset='$srcset' sizes='$sizes' width='$w' height='$h' alt='$alt'>";
	}
}

/* Sets class to body */
function custom_body_classes($classes) {
	$classes[] = 'override';

    return $classes;
}
add_filter('body_class', 'custom_body_classes');

/*
	Searches the content folder for a specific folder and returns
	the files that contain Widget Title to display on the widget settings
*/
function get_widgets_children($folder_name) {
	$path = get_template_directory() . '/template-parts/content/' . $folder_name;
	$result = [];

	if (file_exists($path)) {
		$files = array_diff(scandir($path), ['..', '.']);

		if (is_array($files)) {
			foreach ($files as $file) {
				$file_path = "$path/$file";
				if (!preg_match('|Widget Title:(.*)$|mi', file_get_contents($file_path), $title)) {
					continue;
				}

				if (!empty($title[1])) {
					$key = str_replace('.php', '', $file);
					$result[$key] = $title[1];
				}
			}
		}
	}

	return $result;
}

/* Returns post excerpt, wordpress' is too buggy */
function get_post_excerpt($p, $limit = 25) {
	if ($p->post_excerpt) {
		$excerpt = $p->post_excerpt;
	} else {
		$excerpt = strip_tags($p->post_content);
	}

	$excerpt = wp_trim_words($excerpt, $limit);

	return $excerpt;
}

/* Finds related posts based on categories and topics */
function get_related_posts($args = []) {
	$id = (!empty($args['id'])) ? $args['id'] : get_the_ID();
	$limit = (!empty($args['limit'])) ? $args['limit'] : 10;

	if (get_field('related_posts', $id)) {

		$posts = get_field('related_posts', $id);

	} else {

		$current_post = get_post($id);
		$categories = get_the_category($id);

		$categories_ids = [];

		foreach ($categories as $c) {
			$categories_ids[] = $c->term_id;
		}

		$related_args = [
			'post__not_in' => [$id],
			'post_status' => 'publish',
			'post_type' => 'post',
			'posts_per_page' => $limit,
			'tax_query' => array(
				array(
					'taxonomy' => 'category',
					'field' => 'id',
					'terms' => $categories_ids,
				),
			),
		];

		$posts = new WP_Query($related_args);
		if ($posts->post_count) {
			$posts = $posts->posts;
		} else {
			$posts = [];
		}
	}

	return $posts;
}

// Custom styling for posts comments
function custom_comments_fields($fields) {
	$comment_field = $fields['comment'];
	$commenter = wp_get_current_commenter();

	unset($fields['comment']);
	unset($fields['url']);

	$fields['author'] = '<div class="col-sm-6"><div class="field input-text">
			<input id="author" name="author" type="text" value="' . $commenter['comment_author'] . '" placeholder="Name" required />
		</div></div>';
	$fields['email'] = '<div class="col-sm-6"><div class="field input-text">
		<input name="email" type="email" placeholder="Email*" value="' . $commenter['comment_author_email'] . '" required/>
	</div></div>';
	$fields['comment'] = '<div class="col-sm-12"><div class="field textarea">
		<textarea id="comment" name="comment" placeholder="Your Comment*" required></textarea>
	</div></div>';

	return $fields;
}

add_filter( 'comment_form_fields', 'custom_comments_fields' );

// Returns number of items in cart
function get_cart_count() {
	return count(WC()->cart->get_cart());
}

// Removing annoying admin bar spacing
function remove_admin_login_header() {
	remove_action('wp_head', '_admin_bar_bump_cb');
}
add_action('get_header', 'remove_admin_login_header');

function get_attribute_note($attribute_name) {
	$notes = get_field('attributes_notes', 'option');
	$found = null;

	foreach ($notes as $note) {
		$composed_attr = 'pa_' . $note['attribute'];

		if ($composed_attr == $attribute_name) {
			$found = $note['note'];
		}
	}

	return $found;
}

function get_email() {
	return get_field('email', 'option');
}

function get_phone($formatted = true) {
	if ($formatted) {
		return get_field('phone_formatted', 'option');
	} else {
		return get_field('phone_unformatted', 'option');
	}
}

function custom_user_fields_view($user) {
?>
    <table class="form-table">
		<tr>
			<th>Approved Customer</th>
			<td>
				<label>
					<input type="hidden" name="approved" value="0"> <!-- To send something in case checkbox is not checked -->
					<input type="checkbox" name="approved" value="1" <?php if (is_approved($user->ID)) echo 'checked' ?>>
					Approved
				</label>
			</td>
		</tr>
    </table>
<?php
}
add_action( 'show_user_profile', 'custom_user_fields_view');
add_action( 'edit_user_profile', 'custom_user_fields_view');

function custom_user_fields_add($user_id) {
	if (!current_user_can( 'edit_user', $user_id)) {
		return false;
	}

	if (isset($_POST['approved'])) {
		update_user_meta($user_id, 'approved', intval($_POST['approved']));
	}
}
add_action( 'personal_options_update', 'custom_user_fields_add' );
add_action( 'edit_user_profile_update', 'custom_user_fields_add' );

function is_approved($user_id = null) {
	if (!$user_id) {
		$user_id = get_current_user_id();
	}

	$result = get_user_meta($user_id, 'approved', true);

/*
	if (current_user_can('administrator')) {
		$result = true;
	}
*/

	return $result;
}

function email_admin($args = []) {
	$subject = (!empty($args['subject'])) ? $args['subject'] : null;
	$heading = (!empty($args['heading'])) ? $args['heading'] : null;
	$content = (!empty($args['content'])) ? $args['content'] : null;
	$email = (!empty($args['email'])) ? $args['email'] : get_option('admin_email');

	$mailer = WC()->mailer();

	$html = wc_get_template_html('emails/admin-email.php', [
		'email_heading' => $heading,
		'sent_to_admin' => true,
		'plain_text' => false,
		'email' => $mailer,
		'content' => $content,
	]);

	$recipient = $email;
	$headers = "Content-Type: text/html\r\n";

	$mailer->send($recipient, $subject, $html, $headers);
}

function email_customer($args = []) {
	$name = (!empty($args['name'])) ? $args['name'] : '';
	$email = (!empty($args['email'])) ? $args['email'] : null;

	if ( $email ) {
		$mailer = WC()->mailer();

		$html = wc_get_template_html('emails/customer-email.php', [
			'name' => $name,
			'sent_to_admin' => false,
			'plain_text' => false,
			'email' => $mailer,
		]);

		$recipient = $email;
		$headers = "Content-Type: text/html\r\n";

		$mailer->send($recipient, '[His Clinic] Welcome', $html, $headers);
	}
}

function replace_text( $translated_text, $text, $domain ) {
	$replaces = array(
		'Recurring Total' => 'Total',
		'Proceed to checkout' => 'Checkout',
	);

	foreach ($replaces as $key => $value) {
		if ($key == $text) {
			$translated_text = $value;
			break;
		}
	}

	return $translated_text;
}
add_filter( 'gettext', 'replace_text', 20, 3 );

// Comments Walker
function his_comment($comment, $args, $depth) {
    if ( 'div' === $args['style'] ) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }?>
    <<?php echo $tag; ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID() ?>"><?php
    if ( 'div' != $args['style'] ) { ?>
        <div id="div-comment-<?php comment_ID() ?>" class="comment-body"><?php
    } ?>
        <div class="comment-author vcard"><?php
            if ( $args['avatar_size'] != 0 ) {
                $avatar = get_user_avatar($comment->user_id);
				echo '<img src="' . get_user_avatar_url($avatar) . '" alt="' . $comment->comment_author . '">';
            }
            printf( __( '<cite class="fn">%s</cite> <span class="says">says:</span>' ), get_comment_author_link() ); ?>
        </div><?php
        if ( $comment->comment_approved == '0' ) { ?>
            <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></em><br/><?php
        } ?>
        <div class="comment-meta commentmetadata">
            <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>"><?php
                /* translators: 1: date, 2: time */
                printf(
                    __('%1$s at %2$s'),
                    get_comment_date(),
                    get_comment_time()
                ); ?>
            </a><?php
            edit_comment_link( __( '(Edit)' ), '  ', '' ); ?>
        </div>

        <?php comment_text(); ?>

        <div class="reply"><?php
                comment_reply_link(
                    array_merge(
                        $args,
                        array(
                            'add_below' => $add_below,
                            'depth'     => $depth,
                            'max_depth' => $args['max_depth']
                        )
                    )
                ); ?>
        </div><?php
    if ( 'div' != $args['style'] ) : ?>
        </div><?php
    endif;
}

function redirect_not_logged_in() {
    if (
        ! is_user_logged_in()
        && (is_woocommerce() || is_cart() || is_checkout())
    ) {
        // feel free to customize the following line to suit your needs
        wp_redirect('/hisclinic/my-account');
        exit;
    }
}
// add_action('template_redirect', 'redirect_not_logged_in');

// Customise Yoast SEO breadcrumbs
function hisclinic_seo_breadcrumbs( $links ){
	global $wp_query;

	if ( 'post' == get_post_type() ) :
		$post_link = array_pop($links);

		$links[] = array(
			'text' => 'Men\'s Health',
			'url' => esc_url(home_url('mens-health-blog-erectile-dysfunction/')),
			'allow_html' => 1,
		);

		$links[] = $post_link;
	endif;

	return $links;
}

add_filter('wpseo_breadcrumb_links', 'hisclinic_seo_breadcrumbs', 1);

// Abandoned cart emails
function hisclinic_ac_item_name($item_name) {
	if ( strpos( $item_name, 'Sildenafil' ) !== false ) {
		$item_name .= ' 100mg';
	} elseif ( strpos( $item_name, 'Cialis' ) !== false ) {
		$item_name .= ' 20mg';
	}

	return $item_name;
}
add_filter( 'wcap_product_name', 'hisclinic_ac_item_name', 1, 99 );

/**
 * Returns product price based on sales.
 *
 * @return string
 */

add_filter('woocommerce_variable_price_html', 'custom_variation_price', 10, 2);
function custom_variation_price( $price, $product ) {
    $available_variations = $product->get_available_variations();
    $selectedPrice = '';
    $dump = '';

    foreach ( $available_variations as $variation )
    {
        // $dump = $dump . '<pre>' . var_export($variation['attributes'], true) . '</pre>';

        $isDefVariation=false;
        foreach($product->get_default_attributes() as $key=>$val){
            // $dump = $dump . '<pre>' . var_export($key, true) . '</pre>';
            // $dump = $dump . '<pre>' . var_export($val, true) . '</pre>';
            if($variation['attributes']['attribute_'.$key]==$val){
                $isDefVariation=true;
            }
        }
        if($isDefVariation){
            $price = $variation['display_price'];
        }
    }
    $selectedPrice = wc_price($price);

//  $dump = $dump . '<pre>' . var_export($available_variations, true) . '</pre>';

    return $selectedPrice . $dump;
}
function woocommerce_after_shop_loop_item_title_short_description() {
	global $product;
	if ( ! $product->post->post_excerpt ) return;
	?>
	<div class="display-on-hover" itemprop="description">
		<p><strong><?php echo get_the_title(); ?> - From <?php echo $product->get_price_html(); ?>

		</strong></p>
		<hr noshade>
		<?php echo apply_filters( 'woocommerce_short_description', $product->post->post_excerpt ) ?>
		<span class="arrow">&nbsp;</span>
	</div>
	<?php
}
add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_after_shop_loop_item_title_short_description', 5);

add_filter('manage_edit-shop_order_columns', 'add_medical_form_column');
function add_medical_form_column($columns) {
	$new_columns = [];	

	$i = 0;
	foreach ($columns as $column_name => $column_data) {
		if ($i == 2) {
			$new_columns['legal_name'] = 'Legal Name';
		} 
		
		if ($column_name === 'order_total') {
            $new_columns['medical_form'] = 'Medical data';
        } else {
			$new_columns[$column_name] = $column_data;		
		}

		$i++;
	}
	
    return $new_columns;
};

add_action('manage_shop_order_posts_custom_column', 'populate_medical_form_column');
function populate_medical_form_column($column) {
	global $post;
	
    if ($column === 'medical_form') {
		$user_id = get_post_meta($post->ID, '_customer_user', true);

		$form = get_user_meta($user_id, 'medical-form', true);
/*
		$form = get_user_meta($user_id, 'medical-form-new', true);
		$form_id = 'medical-forms-new';
		if (empty($form)) {
			$form = get_user_meta($user_id, 'medical-form', true);
			$form_id = 'medical-forms-new';
		}
*/
		$form_id = 'medical-forms-new';
		$form = json_decode($form, true);

        $flagged = '>View questionnaire</a>';
        foreach ($form as $key => $value) {
			//error_log($key);
			//error_log($value);
            if ($key === 'symptoms-of-ed' && $value === 'No') {
                $flagged = ' style="background-color: red; color: white;">Please check questionnaire</a>';
                break;
            }
        }
		echo '<a href="'.get_admin_url().'users.php?page='.$form_id.'&user_id='.$user_id.'" target="_blank"' . $flagged;
		$updated_questionnaire = get_user_meta($user_id, 'updated_questionnaire', true);
/*
		if ($form_id === 'medical-forms') {
			echo '<br /><span style="background-color: orange; color: white;">OLD questionnaire</span>';
		}
*/
		if ($updated_questionnaire) {
			echo '<br /><span style="background-color: green; color: white;">UPDATED </span>&#x2705;';
		}

		$order = new WC_Order($post->ID);
		if ($order && ($order->get_status() === 'scripted' || $order->get_status() === 'completed')) {
			echo '<br /><a href="' . home_url('script-pdf') . '?download_order_id=' . $post->ID . '" target="_blank">Download script</a>';
		}
		echo '<br /><a href="' . home_url('q') . '?order_id[]=' . $post->ID . '&download=1" target="_blank">Download questionnaire</a>';
	}
	
    if ($column == 'legal_name') {
        $order = new WC_Order($post->ID);
        
        if ($order) {
            $user_id = $order->get_customer_id();
            echo get_full_name($user_id);
        }
    }
};

add_action('admin_init', 'doctor_limited_admin_menu', 99);
function doctor_limited_admin_menu() {
    if (in_array('doctor', wp_get_current_user()->roles)) {
        remove_menu_page('index.php');
        remove_menu_page('edit.php');
        remove_menu_page('upload.php');
        remove_menu_page('edit.php?post_type=page');
        remove_menu_page('edit-comments.php');
        remove_menu_page('profile.php');
        remove_menu_page('themes.php');
        remove_menu_page('plugins.php');
        remove_menu_page('tools.php');
        remove_menu_page('settings.php');
        remove_menu_page('options-general.php');
        remove_menu_page('wpcf7');
        remove_menu_page('edit.php?post_type=wcec_email');
        remove_menu_page('edit.php?post_type=acf-field-group');
        remove_menu_page('edit.php?post_type=product');
        remove_menu_page('duplicator');
        remove_menu_page('elementor');
        remove_menu_page('cfdb7-list.php');
        remove_menu_page('users.php');
        remove_menu_page('woocommerce');
        add_menu_page('Orders', 'Orders', 'prescribe_capability', 'edit.php?post_type=shop_order', '', 'dashicons-clipboard');
    }
};

add_filter('login_redirect', 'doctor_login_redirect');
function doctor_login_redirect($url) {
    if (in_array('doctor', wp_get_current_user()->roles)) {
        $url = 'edit.php?post_type=shop_order';
    }
    return $url;
};

add_action('admin_head', 'remove_upgrade_nag');
function remove_upgrade_nag() {
    echo '<style type="text/css">.update-nag {display: none}</style>';
};


/* Pull apart OEmbed video link to get thumbnails out*/
function his_clinic_get_video_thumbnail_uri( $video_uri ) {

	$thumbnail_uri = '';

	// determine the type of video and the video id
	$video = his_clinic_parse_video_uri( $video_uri );

	// get youtube thumbnail
	if ( $video['type'] == 'youtube' )
		$thumbnail_uri = 'http://img.youtube.com/vi/' . $video['id'] . '/hqdefault.jpg';

	// get vimeo thumbnail
	if( $video['type'] == 'vimeo' )
		$thumbnail_uri = his_clinic_get_vimeo_thumbnail_uri( $video['id'] );
	// get wistia thumbnail
	if( $video['type'] == 'wistia' )
		$thumbnail_uri = his_clinic_get_wistia_thumbnail_uri( $video_uri );
	// get default/placeholder thumbnail
	if( empty( $thumbnail_uri ) || is_wp_error( $thumbnail_uri ) )
		$thumbnail_uri = '';

	//return thumbnail uri
	return $thumbnail_uri;

}


/* Parse the video uri/url to determine the video type/source and the video id */
function his_clinic_parse_video_uri( $url ) {

	// Parse the url
	$parse = parse_url( $url );

	// Set blank variables
	$video_type = '';
	$video_id = '';

	// Url is http://youtu.be/xxxx
	if ( $parse['host'] == 'youtu.be' ) {

		$video_type = 'youtube';

		$video_id = ltrim( $parse['path'],'/' );

	}

	// Url is http://www.youtube.com/watch?v=xxxx
	// or http://www.youtube.com/watch?feature=player_embedded&v=xxx
	// or http://www.youtube.com/embed/xxxx
	if ( ( $parse['host'] == 'youtube.com' ) || ( $parse['host'] == 'www.youtube.com' ) ) {

		$video_type = 'youtube';

		parse_str( $parse['query'] );

		$video_id = $v;

		if ( !empty( $feature ) )
			$video_id = end( explode( 'v=', $parse['query'] ) );

		if ( strpos( $parse['path'], 'embed' ) == 1 )
			$video_id = end( explode( '/', $parse['path'] ) );

	}

	// Url is http://www.vimeo.com
	if ( ( $parse['host'] == 'vimeo.com' ) || ( $parse['host'] == 'www.vimeo.com' ) ) {

		$video_type = 'vimeo';

		$video_id = ltrim( $parse['path'],'/' );

	}
	$host_names = explode(".", $parse['host'] );
	$rebuild = ( ! empty( $host_names[1] ) ? $host_names[1] : '') . '.' . ( ! empty($host_names[2] ) ? $host_names[2] : '');
	// Url is an oembed url wistia.com
	if ( ( $rebuild == 'wistia.com' ) || ( $rebuild == 'wi.st.com' ) ) {

		$video_type = 'wistia';

		if ( strpos( $parse['path'], 'medias' ) == 1 )
				$video_id = end( explode( '/', $parse['path'] ) );

	}

	// If recognised type return video array
	if ( !empty( $video_type ) ) {

		$video_array = array(
			'type' => $video_type,
			'id' => $video_id
		);

		return $video_array;

	} else {

		return false;

	}

}


/* Takes a Vimeo video/clip ID and calls the Vimeo API v2 to get the large thumbnail URL.*/
function his_clinic_get_vimeo_thumbnail_uri( $clip_id ) {
	$vimeo_api_uri = 'http://vimeo.com/api/v2/video/' . $clip_id . '.php';
	$vimeo_response = wp_remote_get( $vimeo_api_uri );
	if( is_wp_error( $vimeo_response ) ) {
		return $vimeo_response;
	} else {
		$vimeo_response = unserialize( $vimeo_response['body'] );
		return $vimeo_response[0]['thumbnail_large'];
	}

}

/* Takes a wistia oembed url and gets the video thumbnail url. */
function his_clinic_get_wistia_thumbnail_uri( $video_uri ) {
	if ( empty($video_uri) )
		return false;
	$wistia_api_uri = 'http://fast.wistia.com/oembed?url=' . $video_uri;
	$wistia_response = wp_remote_get( $wistia_api_uri );
	if( is_wp_error( $wistia_response ) ) {
		return $wistia_response;
	} else {
		$wistia_response = json_decode( $wistia_response['body'], true );
		return $wistia_response['thumbnail_url'];
	}
}

/**
 * Slugify text string into URL slug
 *
 * @param [type] $text
 * @return void
 */
function his_clinic_tesxt_slugify($text)
{
  	// replace non letter or digits by -
	$text = preg_replace('~[^\pL\d]+~u', '-', $text);

	// transliterate
	$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

	// remove unwanted characters
	$text = preg_replace('~[^-\w]+~', '', $text);

	// trim
	$text = trim($text, '-');

	// remove duplicate -
	$text = preg_replace('~-+~', '-', $text);

	// lowercase
	$text = strtolower($text);

	if (empty($text)) {
	return 'n-a';
	}

	return $text;
}

/**
 * His clinic hide admin notices
 *
 * @return void
 */
function his_clinic_hide_update_msg_non_admins(){

	$screen = get_current_screen();

		if( 'users_page_medical-forms-new' === $screen->id ) {

			echo '<style> #message,  .updated,  .notice.elementor-message  { display: none; }</style>';
		}
	}

add_action( 'admin_head', 'his_clinic_hide_update_msg_non_admins');

// testing increase PHP limits
@ini_set( 'upload_max_size' , '256M' );
@ini_set( 'post_max_size', '256M');
@ini_set( 'max_execution_time', '300' );
@ini_set( 'max_input_time', '300' );

add_filter( 'gform_next_button', 'next_css_classes', 10, 2 );
add_filter( 'gform_previous_button', 'previous_css_classes', 10, 2 );
add_filter( 'gform_submit_button', 'next_css_classes', 10, 2 );
function next_css_classes( $button, $form ) {
    $dom = new DOMDocument();
    $dom->loadHTML( $button );
    $input = $dom->getElementsByTagName( 'input' )->item(0);
    $classes = $input->getAttribute( 'class' );
    $classes .= " btn filled mf-next";
    $input->setAttribute( 'class', $classes );
    return $dom->saveHtml( $input );
}
function previous_css_classes( $button, $form ) {
    $dom = new DOMDocument();
    $dom->loadHTML( $button );
    $input = $dom->getElementsByTagName( 'input' )->item(0);
    $classes = $input->getAttribute( 'class' );
    $classes .= " btn filled";
    $input->setAttribute( 'class', $classes );
    return $dom->saveHtml( $input );
}

//testing remove subscriptions thank you text
add_filter('woocommerce_subscriptions_thank_you_message', 'remove_subscription_thankyou_text');
function remove_subscription_thankyou_text() {
	return '';
}

add_filter('gform_confirmation_anchor', function() {
	return 20;
});

add_action('gform_after_submission_1', 'customer_updated_questionnaire', 10, 2);
function customer_updated_questionnaire($entry, $form) {
	$customer_id = get_current_user_id();
	$updated_time = current_time('mysql', 0);
	$updated_questionnaire = rgar($entry, 'id');
	update_user_meta($customer_id, 'updated_questionnaire', $updated_questionnaire);
	update_user_meta($customer_id, 'updated_time', $updated_time);
	error_log(print_r($entry, true));
}

// gravity forms needs newer jquery
add_action('wp_enqueue_scripts', 'update_jquery');
function update_jquery() {
	wp_deregister_script('jquery');
	wp_register_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js', array(), '3.4.1');
}

function his_clinic_object_to_array($data)
{
	if (is_array($data) || is_object($data))
	{
		$result = array();
		foreach ($data as $key => $value)
		{
			$result[$key] = his_clinic_object_to_array($value);
		}
		return $result;
	}
	return $data;
}

function get_uploads_path() {
	$uploads_info = wp_upload_dir();
	return $uploads_info['basedir'];
}

function get_first_name($user_id = null) {
	return ($user_id) ? get_user_meta($user_id, 'first_name', true) : null;
}

function get_last_name($user_id = null) {
	return ($user_id) ? get_user_meta($user_id, 'last_name', true) : null;
}

function get_full_name($user_id = null) {
	$result = null;

	if ($user_id) {
		$first_name = get_first_name($user_id);
		$last_name = get_last_name($user_id);
		$result = implode(' ', [$first_name, $last_name]);
	}

	return $result;
}

/*
	Checking a product is available to purchase, based on the suggested_product meta field... which is a permalink...
	https://hisclinic.com/product/cialis/
	We expect the item after 'product' is always the product slug
*/
function is_product_allowed($product) {
	$result = false;

	if (is_user_logged_in()) {
		$user = wp_get_current_user();
		$suggested_product_link = get_user_meta($user->ID, 'suggested_product', true);

		if ($suggested_product_link) {
			$parts = explode('/', $suggested_product_link);
			$suggested_product_slug = null;

			foreach ($parts as $key => $value) {
				if ($value == 'product' && !empty($parts[$key + 1])) {
					$suggested_product_slug = $parts[$key + 1];
					break;
				}
			}

			if ($suggested_product_slug) {
				if ($product->slug == $suggested_product_slug) {
					$result = true;
				}
			}
		}
	}

	return $result;
}

function save_medical_form_data($user_id, $data) {
	return update_user_meta($user_id, MF_KEY_NEW_2, $data);
}

// Some forms may come as an old json encoded version
// Updated forms are stored serialized
function get_medical_form_data($user_id) {
	$data = get_user_meta($user_id, MF_KEY_NEW_2, true);

	if (is_string($data) && json_decode($data)) {
		$data = json_decode($data);

		if ($data) {
			$data = hisclinic_looper_object_to_array($data);
		}
	}

	return $data;
}

function valid_date_of_birth($user_id) {
	$result = false;
	$medical_form = get_medical_form_data($user_id);
	
	if ($medical_form) {
		$dob_answer = (!empty($medical_form['medical_form_details']['personal_information']['date_of_birth']['answer'])) ? $medical_form['medical_form_details']['personal_information']['date_of_birth']['answer'] : null;
		$result = ($dob_answer && strlen($dob_answer) == 10);
	}

	return $result;
}

function requires_pre_purchase($product, $size) {
	$pre_required_purchase = get_pre_required_purchase($product, $size);
	$result = false;

	if ($pre_required_purchase > 0) {
		$user_id = get_current_user_id();
		$count = count_product_purchases_by_user($product, $user_id);

		if ($count < $pre_required_purchase) {
			$result = true;
		}
	}

	return $result;
}

function get_pre_required_purchase($product, $size) {
	$variations = $product->get_available_variations();
	$found = null;
	$result = null;

	foreach ($variations as $variation) {
		foreach ($variation['attributes'] as $key => $value) {
			$attribute = "attribute_pa_pack-size";
			
			if ($key == $attribute && $value == $size) {
				$found = $variation;
				break;
			}
		}
	}

	if ($found) {
		$result = get_post_meta($found['variation_id'], 'required_pre_purchases', true);
	}

	return $result;
}

function count_product_purchases_by_user($product, $user_id) {
	$result = 0;

	$customer_orders = get_posts([
		'numberposts' => -1,
		'meta_key'    => '_customer_user',
		'meta_value'  => $user_id,
		'post_type'   => wc_get_order_types(),
		'post_status' => array_keys(wc_get_order_statuses()),
	]);

	foreach ($customer_orders as $order) {
		$o = wc_get_order($order->ID);
		
		foreach ($o->get_items() as $p) {
			if ($p->get_product_id() == $product->get_id()) {
				$result++;
			}
		}
	}

	return $result;
}

function send_email_template($to, $subject, $data = []) {
	$content = get_template_part_with_args('template-parts/default-email', $data, false);
	
	$headers = "From: HisClinic <info@hisclinic.com>". "\r\n";
	$headers .= "Reply-To: info@hisclinic.com" . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";	

	return wp_mail($to, $subject, $content, $headers);
}

function sort_orders_ids_by_first_name($orders_ids) {
	$orders = [];
	$sorted_ids = [];

	foreach ($orders_ids as $order_id) {
		$orders[] = wc_get_order($order_id);
	}

	usort($orders, function($a, $b) {
		$a_name = get_first_name($a->get_customer_id());
		$b_name = get_first_name($b->get_customer_id());

		return strcmp($a_name, $b_name);
	});

	foreach ($orders as $order) {
		$sorted_ids[] = $order->get_id();
	}

	return $sorted_ids;
}
