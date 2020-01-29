<?php
/**
 * Link Widget
 *
 * Rich link widget
 */
class LinkWidget extends \Elementor\Widget_Base
{
	public function get_name() {
		return 'link';
	}

	public function get_title() {
		return 'Link';
	}

	public function get_icon() {
		return 'fa fa-link';
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
			'url',
			[
				'label' => 'Url',
				'type' => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
				'label_block' => true,
			]
		);

		$this->add_control(
			'text',
			[
				'label' => 'Text',
				'type' => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
				'label_block' => true,
			]
		);

		$this->add_control(
			'image',
			[
				'label' => 'Image',
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label_block' => true,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => 'Color',
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-custom-link' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-custom-link',
				'scheme' => \Elementor\Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label' => 'Alignment',
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => 'Left',
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => 'Center',
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => 'Right',
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'css_class',
			[
				'label' => 'CSS Classes',
				'type' => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
				'label_block' => true,
			]
		);

		$this->add_control(
			'new_tab',
			[
				'label' => 'Open in New Tab',
				'type' => \Elementor\Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'download',
			[
				'label' => 'Link to Download',
				'type' => \Elementor\Controls_Manager::SWITCHER,
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
		$url = $settings['url'];
		$text = $settings['text'];
		$image = $settings['image'];
		$css_class = $settings['css_class'];
		$download = (!empty($settings['download'])) ? $settings['download'] : '';
		$target = ($settings['new_tab'] == 'yes') ? '_blank' : '_self';
		
		echo "<a href='$url' class='$css_class elementor-custom-link' target='$target' $download>";
		
		if ($image) {
			echo "<img src='{$image['url']}'>";
		}

		if ($text) {
			echo "<span>$text</span>";
		}

		echo '</a>';
	}
}
