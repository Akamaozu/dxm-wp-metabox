<?php

class MetaboxMaker {

	var $boxes_to_make = array(),
		$init_params;



	private function is_metabox_data($data){

		if(!is_array($data) || empty($data) || !$data['id'] || !$data['markup'] || !$data['post_type']){

			return false;
		}

		return true;
	}

	private function detect_input_type($input){

		// default return value
		$input_type = 'invalid';

		$is_metabox = $this->is_metabox_data($input);

		// filter out improper or empty $input
		if ( $is_metabox  == false ) {

			return $input_type;
		}

		// confirm proper $input
		if ( $is_metabox == true ) {

			$input_type = 'single';
			return $input_type;
		}

		// check if $input is an array of metabox data 
		foreach ($input as $nested_input){

			if ( $this->is_metabox_data($nested_input) == true ) {

				$input_type = 'multiple';
				return $input_type;
			}
		}

		// not single, not multiple, not empty, not usable -- invalid
		return $input_type;
	}

	private function set_defaults($metabox_data) {

		// conditionals with default values if unset 
		$metabox_data['title'] = ( isset ( $metabox_data['title'] ) ? $metabox_data['title'] : "" );
		$metabox_data['context'] = ( isset( $metabox_data['context'] ) ? $metabox_data['context'] : "advanced" );
		$metabox_data['priority'] = ( isset( $metabox_data['priority'] ) ? $metabox_data['priority'] : "default" );
		$metabox_data['conditions'] = ( isset( $metabox_data['conditions'] ) ? $metabox_data['conditions'] : NULL );
		$metabox_data['pass_params'] = ( isset( $metabox_data['pass_params'] ) ? $metabox_data['pass_params'] : NULL );
		$metabox_data['on_autosave'] = ( isset( $metabox_data['on_autosave'] ) ? $metabox_data['on_autosave'] : NULL );
		$metabox_data['on_save'] = ( isset( $metabox_data['on_save'] ) ? $metabox_data['on_save'] : NULL );

		return $metabox_data;
	}

	function create_save_hook($metabox_data){

		add_action( 'save_post', function($post_id, $metabox_data){

			// filter out unpermitted users
			if ( 'page' == $_POST['post_type'] ||  'post' == $_POST['post_type']) {
		        
		        if ( !current_user_can( 'edit_page', $post_id ) || !current_user_can( 'edit_post', $post_id )){
		          
		          return $post_id;
		        }
			}

			// do autosave if set
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE && $metabox_data['on_autosave'] != NULL ){

				$metabox_data['on_autosave']( $metabox_data['pass_params'] );
			}

			// do regular save if set
			else if ($metabox_data['on_save']) {

				$metabox_data['on_save']( $metabox_data['pass_params'] );
			}
		});
	}

	function create_for_post_type($metabox_data, $post_type){

		add_meta_box(

			$metabox_data['id'],
			$metabox_data['title'],
			$metabox_data['markup'],
			$post_type,
			$metabox_data['context'],
			$metabox_data['priority'],
			$metabox_data['pass_params']
		);
	}

	function meets_conditions( $metabox_data = array() ){

		if( 
			( isset($metabox_data['conditions']) && $metabox_data['conditions']() == false ) ||
			( isset($this->init_params['conditions']) && $this->init_params['conditions']() == false ) 
		) {

			return false;
		}

		return true;
	}

	function setup_construction(){

		// if action hook hasn't been declared, do it
		if ( !has_action( 'add_meta_boxes', array($this, 'init_construction')) ) {

			add_action( 'add_meta_boxes', array($this, 'init_construction') );
		}
	}

	function queue_for_construction($metabox_data){

		$this->boxes_to_make[] = $metabox_data;
	}

	function init_construction(){

		foreach ($this->boxes_to_make as $metabox_data){

			$this->create_box($metabox_data);
		}
	}

	function create_box($metabox_data){

		// set defaults for unset variables
		$metabox_data = $this->set_defaults($metabox_data);

		// create for one post type
		if ( is_string($metabox_data['post_type']) && $this->meets_conditions($metabox_data) == true ) {

			$this->create_for_post_type($metabox_data, $metabox_data['post_type']);
		}

		// create for multiple post types
		else if ( is_array($metabox_data['post_type']) ){

			foreach ($metabox_data['post_type'] as $post_type) {
				
				if ( $this->meets_conditions($metabox_data) == true ){
					
					$this->create_for_post_type($metabox_data, $post_type);
				}
			}
		}

		// if needed, create save hooks
		if ( $metabox_data['on_save'] != NULL || $metabox_data['on_autosave'] != NULL ){

			$this->create_save_hook($metabox_data);
		}
	}

	function create($data) {

		switch ( $this->detect_input_type($data) ) {

			case 'single':

				$metabox_data = $data;
				$this->queue_for_construction( $metabox_data );

				$this->setup_construction();
			break;

			case 'multiple':

				foreach ($data as $metabox_data) {

					$this->queue_for_construction( $metabox_data );
				}

				$this->setup_construction();
			break;

			case 'invalid':
			default:

				// no good data to work with ... bail!
			break;
		}
	}

	function remove_content_editor() {
		
		if ( $this->meets_conditions() == true ) {
		
			global $_wp_post_type_features;

			foreach ($_wp_post_type_features as $type => &$features) {
				if ( isset($features['editor']) && $features['editor'] ) {
					
					unset( $features['editor'] );
				}
			}
		}
	}

	function remove_editor(){
	
		add_action( 'add_meta_boxes', array( $this, 'remove_content_editor' ), 0 );
	}

	function __construct($init_params){

		$this->init_params = $init_params;
	}
} 
?> 