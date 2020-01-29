<?php
/**
 * Medical Form
 */
class MedicalFormWidget extends \Elementor\Widget_Base
{
	public function get_name() {
		return 'medical-form';
	}

	public function get_title() {
		return 'Medical Form';
	}

	public function get_icon() {
		return 'eicon-accordion';
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
			'title',
			[
				'label' => 'Title',
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'label_block' => true,
			]
		);

		$this->add_control(
			'intro',
			[
				'label' => 'Introductory Text',
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'label_block' => true,
			]
		);

		$this->add_control(
			'content',
			[
				'label' => 'Content',
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
		get_template_part_with_args('template-parts/sections/medical-form', ['settings' => $settings]);
	}
}
