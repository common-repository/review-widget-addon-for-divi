<?php

class TRNDX_Widget extends ET_Builder_Module {

	public $slug       = 'trndx_widget';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => 'https://www.trustindex.io/',
		'author'     => 'Trustindex.io <support@trustindex.io>',
		'author_uri' => 'https://www.trustindex.io/',
	);

	public function init() {
		$this->name = esc_html__( 'Trustindex widget', 'trndx-trustindex-divi' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
	}

	// admin widgets
	public function get_trndx_admin_widgets(){
		if (!trdnx_divi_check_ti_active())
		{
			return ['' => 'Please activate Trustindex core plugin!'];
		}

		$request = new \WP_REST_Request( 'GET', '/trustindex/v1/get-widgets' );
		$response = rest_do_request( $request );
		$data = rest_get_server()->response_to_data( $response, true );

		if (empty($data))
		{
			return ['' => 'Please connect your trustindex account!'];
		}

		$widgets = array_column($data, 'widgets');

		$result = ['' => 'Select widget'];
		foreach ($widgets as $widget)
		{
			$result = array_merge($result, array_combine(array_column($widget, "id"), array_column($widget, "name")));
		}

		return $result;
	}

	// free wp widgets
	public function get_trndx_wp_widgets(){
		$request = new \WP_REST_Request( 'GET', '/trustindex/v1/setup-complete' );
		$response = rest_do_request( $request );
		$data = rest_get_server()->response_to_data( $response, true );

		$result = [];

		if (isset($data['result']))
		{
			$result = ['' => "Select widget"];

			foreach ($data['result'] as $platform => $value)
			{
				if ($value)
				{
					$result[$platform] = ucfirst($platform);
				}
			}
		}

		if (empty($data['result']) || count($result) == 1)
		{
			$result = ['' => "Please set up your Trustindex widget!"];
		}

		return $result;
	}

	public function get_fields() {

		return array(
			'type_select'     => array(
				'label'           => esc_html__( 'Widget setup', 'trndx-trustindex-divi' ),
				'type'            => 'select',
				'options'		  => [
					'admin' => esc_html__( 'From connected Trustindex account', 'trndx-trustindex-divi'),
					'wp' => esc_html__( 'From Free Widget Configurator', 'trndx-trustindex-divi'),
					'custom_id' => esc_html__( 'By Trustindex widget ID', 'trndx-trustindex-divi'),
				],
				'default' => 'admin'
			),
			'admin_select'     => array(
				'label'           => esc_html__( 'Select widget', 'trndx-trustindex-divi' ),
				'type'            => 'select',
				'options'		  => $this->get_trndx_admin_widgets(),
				'show_if'         => array(
					'type_select' => 'admin',
				),
				'default' => ''
			),
			'wp_select'     => array(
				'label'           => esc_html__( 'Select widget', 'trndx-trustindex-divi' ),
				'type'            => 'select',
				'options'		  => $this->get_trndx_wp_widgets(),
				'show_if'         => array(
					'type_select' => 'wp',
				),
				'default' => ''
			),
			'custom_id'     => array(
				'label'           => esc_html__( 'Widget ID', 'trndx-trustindex-divi' ),
				'type'            => 'text',
				'show_if'         => array(
					'type_select' => 'custom_id',
				),
				'default' => ''
			),
		);
	}

	// backend rendering
	public function render( $attrs, $content = null, $render_slug )
	{
		 $sc = '';

		if (isset($attrs['admin_select']) && $attrs['admin_select'] !== '')
		{
			$sc = '[trustindex data-widget-id='.$attrs['admin_select'].']';
		}
		else if (isset($attrs['custom_id']) && $attrs['custom_id'] !== '')
		{
			$sc = '[trustindex data-widget-id='.$attrs['custom_id'].']';
		}
		else if (isset($attrs['wp_select']) && $attrs['wp_select'] !== '')
		{
			$sc = '[trustindex no-registration='.$attrs['wp_select'].']';
		}

		return do_shortcode($sc);
	}
}

new TRNDX_Widget;
