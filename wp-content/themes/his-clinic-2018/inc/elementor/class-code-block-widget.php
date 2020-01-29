<?php
/**
 * Code Block Widget
 *
 * Widget for displaying a chosen calculator
 */
class CodeBlockWidget extends \Elementor\Widget_Base
{
	public function get_name() {
		return 'code-block';
	}

	public function get_title() {
		return 'Code Block';
	}

	public function get_icon() {
		return 'fa fa-code';
	}

	public function get_categories() {
		return ['basic'];
	}

	/**
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function _register_controls() {
		$available_blocks = get_widgets_children('block');

		$this->start_controls_section(
			'content_section',
			[
				'label' => 'Content',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'block',
			[
				'label' => 'Block',
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => $available_blocks,
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
		get_template_part_with_args('template-parts/content/block/' . $settings['block'], ['settings' => $settings]);
	}
}