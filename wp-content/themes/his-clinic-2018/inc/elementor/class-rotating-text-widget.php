<?php
/**
 * Rotating Text Widget
 */
class RotatingTextWidget extends \Elementor\Widget_Base
{
	public function get_name() {
		return 'rotating-text';
	}

	public function get_title() {
		return 'Rotating Text';
	}

	public function get_icon() {
		return 'eicon-type-tool';
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
			'text',
			[
				'label' => 'Text',
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'label_block' => true,
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
		get_template_part_with_args('template-parts/sections/rotating-text', ['settings' => $settings]);
	}
}
