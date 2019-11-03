<?php
class SP_Customizer{

	public $config = array();

	function __construct(){
		add_action( 'customize_register', array($this, 'sp_customizer'));
	}

	function sp_customizer($wp_customize){
		$panel = $this->config;

		$wp_customize->add_panel(
			$panel['name'],array(
			    'priority'      => $panel['priority'],
			    'capability'    =>  'edit_theme_options',
			    'description'   =>  $panel['description'],
			    'theme_supports'=>  '',
			    'title'         =>  $panel['title'],
		    )
		);

		if(isset($panel['section'])){

			$counter = 0;
			foreach ($panel['section'] as $section){
				$counter++;
				
				if(isset($section['description'])){
					$description = $section['description'];
				} else {
					$description = '';
				}

				$wp_customize->add_section($section['name'] , array(
					'title'    => $section['title'],
					'priority' => $counter,
					'panel' => $panel['name'],
					'description' => $description,
				));

				if(isset($section['fields'])){
					foreach ($section['fields'] as $fields){
							
						$sanitize_callback = '';	

						if($fields['type'] == 'input'){
							$sanitize_callback = 'sanitize_text_field';	
						}

						if($fields['type'] == 'textarea'){
							$sanitize_callback = array($this,'sp_sanitize_textarea');
						}

						if($fields['type'] == 'checkbox'){
							$sanitize_callback = array($this,'sp_sanitize_checkbox');
						}

						if($fields['type'] == 'image'){

							$wp_customize->add_setting($fields['name'], array(
								'capability'        => 'edit_theme_options',
								'sanitize_callback' => 'esc_url_raw',
								'default' => '',
							));

							$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, $fields['name'], array(
								'label'    => $fields['label'],
								'section'  => $section['name'],
								'settings' => $fields['name'],
							)));

						} else {
							
							if(isset($fields['sanitize']) && $fields['sanitize'] == 'y'){
								$wp_customize->add_setting($fields['name'], array(
									'capability' => 'edit_theme_options',
									'default' => '',
									'sanitize_callback' => $sanitize_callback,
								));
							} else {
								$wp_customize->add_setting($fields['name'], array(
									'capability' => 'edit_theme_options',
									'default' => '',
									'sanitize_callback' => '',
								));
							}	

							$wp_customize->add_control($fields['name'], array(
								'type' => $fields['type'],
								'section' => $section['name'],
								'label' => $fields['label'],
							));

						}	
					}
				}
			}
		}
	}

	public function sp_create_customizer($data){
		$this->config = $data;
	}

	//sanitize textarea
	function sp_sanitize_textarea($input){

		$allowed_html = array(
			'h1'     => array(),
			'h2'     => array(),
			'h3'     => array(),
			'p'     => array(),
			'a' => array(
				'href'  => true,
				'title' => true,
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'dl' => array(),
			'dt' => array(),
			'dd' => array(),
			'b' => array(),
			'i' => array()
		); 

		return wp_kses($input, $allowed_html);
	}

	function sp_sanitize_checkbox($input) {
		if ($input == 1) return 1;
		else return '';   
	}

}

?>
