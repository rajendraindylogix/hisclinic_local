<?php
	if ( !function_exists( 'theme_setup' ) ) :
		function theme_setup() {
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'menu' );
			add_theme_support( 'widgets' );
			add_theme_support( 'post-formats', ['video'] );
	
			register_sidebar(['id' => 'default']);
	
			// Custom image sizes
			add_image_size('post_featured', 450, 295, ['center', 'center']); // Post list
			add_image_size('post_carousel', 335, 415, ['center', 'center']); // Post carousel
			add_image_size( 'product-featured', 596, 445, [ 'center', 'center' ] );
		}
	endif;
	add_action( 'after_setup_theme', 'theme_setup' );

	/**
	 * Add theme options page
	 */
	function add_site_settings_page() {
		if ( current_user_can( 'edit_pages' ) && function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page( array(
				'page_title'    => 'Theme Options',
				'menu_title'    => 'Theme Options',
				'menu_slug'     => 'theme-options',
				'capability'    => 'edit_pages',
				'icon_url'      => 'dashicons-art',
				'update_button' => 'Save Options',
				'redirect'      => false
			) );
		}
	}
	add_action( 'init', 'add_site_settings_page' );

	/* Loading Elementor custom widgets */
	function elementor_custom_widgets($widgets_manager) {
		require('elementor/class-link-widget.php');
		require('elementor/class-rotating-text-widget.php');
		require('elementor/class-medical-form-widget.php');
		require('elementor/class-code-block-widget.php');
		require('elementor/class-card-carousel-widget.php');
		require('elementor/class-image-text-carousel-widget.php');
		require('elementor/class-start-order-product-button.php');

		$widgets_manager->register_widget_type( new LinkWidget() );
		$widgets_manager->register_widget_type( new RotatingTextWidget() );
		$widgets_manager->register_widget_type( new MedicalFormWidget() );
		$widgets_manager->register_widget_type( new CodeBlockWidget() );
		$widgets_manager->register_widget_type( new CardCarouselWidget() );
		$widgets_manager->register_widget_type( new ImageTextCarouselWidget() );
		$widgets_manager->register_widget_type( new StartOrderProdButton() );
	}
	add_action( 'elementor/widgets/widgets_registered', 'elementor_custom_widgets' );
