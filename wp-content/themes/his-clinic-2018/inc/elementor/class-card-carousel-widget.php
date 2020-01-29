<?php
/**
 * Card Carousel Widget
 *
 * Widget for displaying cards
 */
class CardCarouselWidget extends \Elementor\Widget_Base
{
	public function get_name() {
		return 'card-carousel';
	}

	public function get_title() {
		return 'Card Carousel';
	}

	public function get_icon() {
		return 'eicon-slideshow';
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

        $repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => 'Image',
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
        );

		$repeater->add_control(
			'title',
			[
				'label' => 'Title',
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
        );

		$repeater->add_control(
			'content',
			[
				'label' => 'Content',
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
        );
        
		$this->add_control(
			'cards',
			[
				'label' => 'Cards',
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
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
		get_template_part_with_args('template-parts/sections/card-carousel', ['settings' => $settings]);
	}
}