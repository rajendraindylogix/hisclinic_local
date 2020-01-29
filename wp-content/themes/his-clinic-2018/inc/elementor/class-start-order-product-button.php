<?php
/**
 * Start Order Button
 *
 * Widget for displaying order button single page
 */
class StartOrderProdButton extends \Elementor\Widget_Base
{
	public function get_name() {
		return 'start-order-button';
	}

	public function get_title() {
		return 'Start Your Order Button';
	}

	public function get_icon() {
		return 'eicon-favorite';
	}

	public function get_categories() {
		return ['basic'];
	}

	/**
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => 'Content',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
			'button_label',
			[
				'label' => __( 'Button Label', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Start Your Order', 'plugin-domain' ),
				'placeholder' => __( 'Type your title here', 'plugin-domain' ),
			]
        );
        
        $this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
        $settings = $this->get_settings_for_display();
        $label    = isset( $settings['button_label'] ) && ! empty( $settings['button_label'] ) ? $settings['button_label'] : __( 'Start Your Order', 'woocommerce' );
		?>
            <div class="order-btn">
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php 
						$permalink = home_url( '/order-details' );
						$permalink = add_query_arg( 'prod_id', get_the_ID(), $permalink );

						echo esc_url( $permalink );
					
					?>"><?php echo esc_html( $label ); ?> <img src="<?php echo get_template_directory_uri() ?>/assets/css/img/arrow-white.png"></a>
				<?php else : ?>

					<a href="<?php echo home_url('medical-form') ?>" class="btn filled check-eligibility">
						<span class="text">Check Eligibility</span>
					</a>

				<?php endif; ?>
            </div>
        <?php
	}
}