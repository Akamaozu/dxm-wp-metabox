<?php

class metabox_handler {

	function create($boxesToMake) {
	
	/*  
	=================================================
	HOW 'metabox_handler->create($boxesToMake)' WORKS
	=================================================
	
	$boxesToMake = array containing arrays of metaboxes to build and their properties
	
		ex. $boxesToMake = array (
			array (
				'id' => 'example1',
				'title' => 'Example of a Metabox 1',
				'callback' => 'ui_for_example1'
			),
			
			array (
				'id' => 'example2',
				'title' => 'Example of a Metabox 2',
				'callback' => 'ui_for_example2'
			)
		);
		
		foreach ($boxesToMake as $metabox) 
		$metabox[args] = array( id, title, callback, posttype, context, priority, callback args);
			
			id = HTML id [string]
			title = Metabox Title [string]
			callback = Function responsible for markup inside the metabox [string]
			posttype = Edit screen to display the metabox in [string, array]-> {post, page, link, custom_post_type}
			context = Position in the Post Page [string]-> {normal, advanced, side}
			priority = Vertical Position in assigned context [string]-> {high, core, default, low}
			callbackargs = parameters to pass to callback function [string, array]
			

	*/
		
		foreach ($boxesToMake as $metabox_name => $metabox) {
		
			// Metabox shows up on multiple types of page edit screens
			if (is_array($metabox['posttypes'])) {
				
				foreach ( $metabox['posttypes'] as $posttype) { 
					add_meta_box( 
					$metabox['id'], 
					$metabox['title'], 
					$metabox['callback'], 
					$posttype, 
					$metabox['context'], 
					$metabox['priority'], 
					$metabox['callbackargs']
					);
				}
			
			} 
			
			// Metabox shows up on a single type of page edit screen
			else {
				add_meta_box( 
				$metabox['id'], 
				$metabox['title'], 
				$metabox['callback'], 
				$metabox['posttypes'], 
				$metabox['context'], 
				$metabox['priority'],
				$metabox['callbackargs']
				);
			
			}
			
		}

	}
	
}

?>