<?php
/*
Plugin Name: Custom menu 2
Description: Custom menu with an option to specify page id's where the menu is displayed.
Author: - based on Custom Menu by WP, functionality added by Aleksey A. (eServices @ York University)
Version: 1
*/
 
class CustomMenuTwo extends WP_Widget
{

	/* Widget instance constructor - PHP5 (!!)*/
	function __construct() {
		$widget_ops = array(
			'description' => __('Custom menu with an option to specify page id\'s where the menu is displayed. Based on Custom Menu widget. Use Appearance->Menus to create the menus.') 
		);
		
		parent::__construct( 'nav_menu_two', __('Custom Menu Two'), $widget_ops );
	}

// ===================================================================================================
	/* Called from within the actual web page where the menu is displayed.
	     Outputs the menu HTML structure and content.
	*/
	function widget($args, $instance) {

		/* Comma separated list of page id's where the menu is displayed */
		$pages = $instance['pagelist'];
        $hide_def_menu = $instance['hide_def_menu'];
		$page_array = explode(",", $pages);
		
		/* check if current page exists in the list of pages that use this menu */
		$display_menu = false;
		foreach ($page_array as $page) {
			if ($page ==  is_page($page)) {
				$display_menu = true;
				break; //no need to continue looping
			}
		}
		
		/* if current page is listed (i.e. uses this menu) - display it */
		if ($display_menu) {
			$nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;

			if ( !$nav_menu )
				return;

			/* output generic html for the beginning of a widget */
			echo $args['before_widget'];
			
			/*  */
			wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu ) );
			
			/* output generic html for the end of a widget */
			echo $args['after_widget'];
			

            /* Hide default Custom Menu if using Custom Menu Two (so we don't have doubled menus)*/

            if ($hide_def_menu){
                echo "
                <style type = \"text/css\">
                    .widget_nav_menu {display: none !important}
                </style>
			    ";
            }

			
		} 
		/* page isn't listed - don't display Custom Menu Two and don't hide the standard "Custom Menu" widget */
		else echo NULL;
	}

// ===================================================================================================
	/* Called from within the Dashboard (Appearance->Widgets) when the plugin is added (dragged to the Primary Widget Area on the right,
	     and when the user clicks "Save" button.
	*/
	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['pagelist'] = strip_tags( stripslashes($new_instance['pagelist']) );
		$instance['hide_def_menu'] =$new_instance['hide_def_menu'];
		$instance['nav_menu'] = (int) $new_instance['nav_menu'];
		/*$instance['my_test'] = "my test set";*/
		return $instance;
	}

// ===================================================================================================
	/* Called from within the Dashboard (Appearance->Widgets) when the plugin is added (dragged to the Primary Widget Area on the right,
	     and displays the form with the widget's settings.
	*/
	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		
		$pagelist = isset( $instance['pagelist'] ) ? $instance['pagelist'] : '';
		
		$hide_def_menu = isset( $instance['hide_def_menu'] ) ? "checked=\"checked\"": "";
		
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';

		// Get menus
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );

		// If no menus exists, direct the user to go and create some.
		if ( !$menus ) {
			echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
			return;
		}
		?>

		<p>
			<label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu:'); ?></label>
			<select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>" style="width:225px">
		<?php
			foreach ( $menus as $menu ) {
				$selected = $nav_menu == $menu->term_id ? ' selected="selected"' : '';
				echo '<option'. $selected .' value="'. $menu->term_id .'">'. $menu->name .'</option>';
			}

		?>
			</select>
		</p>
		
		<p>
			Page ID's to display this menu on<br/>
			(comma separated. ex: 1,2,15,26):
			<input
				type="text"
				class="widefat"
				id="<?php echo $this->get_field_id('pagelist'); ?>"
				name="<?php echo $this->get_field_name('pagelist'); ?>"
				value="<?php echo $pagelist; ?>"
			/>
		</p>
		
		<p>
			<input
				type="checkbox" 
				id="<?php echo $this->get_field_id('hide_def_menu'); ?>"
				name="<?php echo $this->get_field_name('hide_def_menu'); ?>"
				 <?php echo $hide_def_menu; ?>"
			/> 
			Hide default Custom Menu when this menu is shown.
			<br/>
		</p>
		<?php
	}
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("CustomMenuTwo");') );
?>