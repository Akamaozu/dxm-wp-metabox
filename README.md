## Metabox Maker ##

Making it **ridiculously easy** for you to create metaboxes for Wordpress Edit Screens.

- Create a single box or multiple boxes at once with a few lines of code.

- Specify the conditions to be met before creating individual boxes, set conditions for a group of boxes or mix-and-match as you please.

- Provides easy-to-use hooks to save and autosave events. What you choose to do then (and how you want to filter it) is up to you. Metabox Maker supplies the hooks for you to use as you please.

- Don't need the default content editor? Remove it easily. Remove it only under specific conditions? Not a problem!


----------


### HOW IT WORKS ###


----------


1. Create a new Metabox Maker instance to work with.

    `$mm = new MetaboxMaker();`

2. Create the metabox you need.

		$mm->create( 
			array(
				"id" => "mm_testbox", 
				"post_type" => "post", 
				"markup" => "mm_testbox_markup" 
			) 
		);

3. Wrap it in a function and set it to run on `admin_init` hook.


		function create_test_metabox(){
			
			// include Metabox Maker class source
			require_once("/path/to/metabox.php");
			
			// create a new instance of Metabox Maker
			$mm-> new MetaboxMaker();
			
			// specify required metabox parameters
			$mm->create( 
				array( 
					"id" => "mm_testbox", 
					"post_type" => "post", 
					"markup" => "mm_testbox_markup" 
				) 
			);
		}

   		function mm_testbox_markup(){
			
			// html markup for the metabox			
			echo "<h1>Hello World!</h1>";
		}

		add_action('admin_init', 'create_test_metabox');
![alt text](http://img.photobucket.com/albums/v600/metalzoav2/mm_test_metabox_zps7ab9f43c.jpg "Tada!")

4. Rinse and repeat as needed.


----------

### DOING MORE THAN THE BASICS ###

----------

- Create Metabox for multiple Post Types

		$mm->create( 
			array(
				"id" => "mm_testbox", 
				"post_type" => array("post", "page", "custom_post_type"), 
				"markup" => "mm_testbox_markup" 
			) 
		);

- Create Multiple Metaboxes at once

	    $mm->create(
			array(
				
				array(					
					"id" => "first_box",
					"post_type => "post",
					"markup" => "first_box_markup" 
				),

				array(										
					"id" => "second_box",
					"post_type => array("post", "page"),
					"markup" => "second_box_markup" 
				)
			)
		);

- Create a Metabox only when specified conditions are true

		// create metabox only if the page slug is "about-us"

		$mm->create(
 
			array(
				"id" => "about_us_metabox", 
				"post_type" => "page", 
				"markup" => "about_us_metabox_markup"
				"conditions" => function(){
					
					global $post;
										
					if ($post->post_name == 'about-us'){			
						return true;
					}
					
					return false;
				}
			) 
		);

- Set Conditions for all Metaboxes created by an Instance of Metabox Maker

		// $mm will create Metaboxes only on draft post edit screens
		
		$mm = new MetaboxMaker( 
			
			array(
				
				"conditions" => function(){
		
					global $post;
					
					if( $post->post_type == "post" && ($post->post_status == "draft" || $post->post_status == "auto-draft") ) {
						return true;
					}
		
					return false;
				}
			)
		);
 


----------

### PASSING PARAMETERS

----------


When Instantiating Metabox Maker 
-
	
	// NOTE: $params is optional
	// You can instantiate Metabox Maker without it

	$mm = new MetaboxMaker( $params );

### About $params ###

- is an array
	
### What's Available ###

1. **$params[** 'conditions' **]**

	*Function that returns bool true/false. If false, $mm will not create any metaboxes, regardless of individual metabox creation conditions.*


When Creating a Metabox
-
	

	
	// NOTE: $args is required
	$mm->create( $args );	
	

### About $args ###
- is required
- is an array
- can be an array of a single metabox's properties
	
		// single metabox
		$args = array(
			
			"id" => "about_us_metabox", 
			"post_type" => "page", 
			"markup" => "about_us_metabox_markup"
		)

- can be an array containing multiple "single metabox properties"

		// multiple metaboxes
		$args = array (
			
			array(
				
				"id" => "about_us_metabox", 
				"post_type" => "page", 
				"markup" => "about_us_metabox_markup"
			),
		
			array(
			
				"id" => "contact_us_metabox", 
				"post_type" => "page", 
				"markup" => "contact_us_metabox_markup"
			)
		)

### What's Available (for Single Metabox properties)###
1. **$args[** 'id' **]** *(required)*
	
	*String containing a unique ID for your metabox. Will be used in the backend and also accessible via CSS.*

2. **$args[** 'markup' **]** *(required)*

	*String name of the function responsible for creating the HTML inside a metabox.*

3. **$args[** 'post_type' **]** *(required)*

	*String or Array containing post types to create the metabox on.* 

		// single post type	
		$args[ 'post_type' ] => "post"
 

		// multiple post types		
		$args[ 'post_type' ] => array( "post",  "page" )

4. **$args[** 'title' **]**
	
	*String containing the title used in the metabox header and screen options menu.*

	*Default is blank.*

5. **$args[** 'context' **]** 

	*String indicating the section of the page to place the metabox*

	*Options: Normal, Advanced, Side*
 
	*Default: Advanced*
	
6. **$args[** 'Priority' **]** 

	*String indicating the order in which metaboxes are placed in their section*

	*Options: High, Core, Default, Low*

	*Default: Default*

7. **$args[** 'conditions' **]**

	*Function for determining if metabox should be rendered or not*

	*Must return boolean true if condition met or false if not met*

8. **$args[** 'pass_params' **]**

	*Variable to pass to metabox on creation, during save or autosave*

	*Variable can be any type of your choice*

9. **$args[** 'on_autosave' **]**

	*Function to execute when autosave is triggered*

	*Will not trigger if it's a user or function-triggered save. Only works on autosave*

10. **$args[** 'on_save' **]**

	*Function to execute when save is triggered (by user or by programmatically)*

	*Will not trigger if Wordpress is doing an autosave*


Added Functionality
-

- Remove Default Editor 
	
	`$mm->remove_editor( $conditions );`

	*Need to get rid of the default WYSIWYG Editor? Use the* `remove_editor` *function*

	*Set conditions to determine  when the function will trigger or not*

	*Function* `remove_editor` *is subject to Metabox Maker's instantiation conditions*

	### About $conditions ###

	1. is a function
	2. returns boolean true if conditions are met; otherwise returns false

			$mm->remove_editor(
				
				function(){
					
					global $post;
		
					if ($post->ID > 9000){						
						return true;
					}
		
					return false;
				}
			);





----------

#That's all, Folks!#

----------

Got tips to improve this? **Awesome**! 

Send an email to  `uzo@designbymobius.ca` or we can do the Github thing. Whichever is more convenient.
