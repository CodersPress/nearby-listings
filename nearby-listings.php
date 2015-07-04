<?php /* 
Plugin Name: Nearby Listings Widget
Plugin URI: http://coderspress.com/miscellaneous-plugins/nearby-listings/
Description: Displays nearby listings - Modified version of PremiumPress Listings Widget
Version: 2015.0704
Updated: 4th July 2015 
Author: sMarty, urban-media.ca & PremiumPress
Author URI: http://coderspress.com
WP_Requires: 3.8.1
WP_Compatible: 4.2.2
License: http://creativecommons.org/licenses/GPL/2.0
*/ 
add_action( 'init', 'nbl_plugin_updater' );
function nbl_plugin_updater() {
	if ( is_admin() ) { 
	include_once( dirname( __FILE__ ) . '/updater.php' );
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'nearby-listings',
			'api_url' => 'https://api.github.com/repos/CodersPress/nearby-listings',
			'raw_url' => 'https://raw.github.com/CodersPress/nearby-listings/master',
			'github_url' => 'https://github.com/CodersPress/nearby-listings',
			'zip_url' => 'https://github.com/CodersPress/nearby-listings/zipball/master',
			'sslverify' => true,
			'access_token' => 'eeda83b7cc390d565125ce990aaeb9ae43a1d936',
		);
		new WP_NBL_UPDATER( $config );
	}
}

// Creating the widget 
class wpb_widget extends WP_Widget {

function __construct() {
parent::__construct(
'wpb_widget', 

__('Nearby Listings Widget', 'wpb_widget_domain'), 

// Widget description
array( 'description' => __( 'Widget that displays nearby listings', 'wpb_widget_domain' ), ) 
);
}

public function widget( $args, $instance ) {

		global $CORE, $post, $wp_query, $wpdb; $STRING = ""; $image = ""; @extract($args);  
		if(!isset($GLOBALS['flag-single']) || ( isset($GLOBALS['flag-single']) && $post->post_type != THEME_TAXONOMY."_type" )){  return; }

$this_post = $post->ID;

		$user_lat = strip_tags(get_post_meta($post->ID,'map-lat',true));
		$user_long = strip_tags(get_post_meta($post->ID,'map-log',true));
		$lcount=$instance['pcount']+1;

		$the_posts = $wpdb->get_results("SELECT p.*, pm1.meta_value as lat, pm2.meta_value as lon, 
	ACOS(SIN(RADIANS($user_lat))*SIN(RADIANS(pm1.meta_value))+COS(RADIANS($user_lat))*COS(RADIANS(pm1.meta_value))*COS(RADIANS(pm2.meta_value)-RADIANS($user_long))) * 3959 AS distance 
		FROM $wpdb->posts p
		INNER JOIN $wpdb->postmeta pm1 ON p.id = pm1.post_id AND pm1.meta_key = 'map-lat'
		INNER JOIN $wpdb->postmeta pm2 ON p.id = pm2.post_id AND pm2.meta_key = 'map-log'
		WHERE post_type = 'listing_type' AND post_status = 'publish'
		ORDER BY distance ASC
		LIMIT ".$lcount.";");

 		// CHECK WE HAVE RESULTS
		if(count($the_posts) > 0 ){ //meta_value_num

			// 1. DISPLAY TITLE
			echo "<div class='core_widgets_listings'>".$before_widget.$before_title.$instance['title'];
			
			// 2. AFTER TITLE
			echo "</div>";
			
			// 3. LISTINGS
			echo "<ul class='list-group'>";
			
			$GLOBALS['IS_WIDGET'] = TRUE;			

			foreach($the_posts as $post){
			
			if( $post->ID == $this_post ) { continue; }
	
				global $post;	
					 		
				if(isset($instance['image']) && $instance['image'] == 1){
					$image = hook_image_display(get_the_post_thumbnail($wpost->ID, 'thumbnail', array('class'=> "wlt_thumbnail img-responsive")));			
					if($image == ""){$image = hook_fallback_image_display($CORE->FALLBACK_IMAGE($post->ID)); }							 
					echo "<li class='list-group-item'><div class='row clearfix'><div class='col-md-4 hidden-sm hidden-xs'><a href='".get_permalink($post->ID)."' class='frame'>".$image."</a></div>
					<div class='col-md-8 col-sm-12 col-xs-12'>".$CORE->ITEM_CONTENT($post, str_replace('[ID]',$post->ID,$instance['te']))."</div></div></li>";
				}else{
					echo "<li class='list-group-item'>".$CORE->ITEM_CONTENT($post, $instance['te'])."</li>";	
				}// end if

			}// end foreach			 
			unset($GLOBALS['IS_WIDGET']);
			
			// END LISTINGS
			echo "</ul>";
			echo $after_widget; 
		}
		wp_reset_postdata();		 

}
		
// Widget Backend 
public function form( $instance ) {
 		$defaults = array(
			'title'		=> 'Nearby Listings',	
			'pcount'	=> '1',
			'te'		=> "<b>[TITLE]</b> [EXCERPT size=100]",	
			'image'		=> false,		
		);		
		$instance = wp_parse_args( $instance, $defaults ); 
	?>     
 	<p><b>Title</b></p>
	<p><input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" /></p>
 	
    
    <p><b># of Listings to Show</b></p>
        <select id="<?php echo $this->get_field_id( 'pcount' ); ?>" name="<?php echo $this->get_field_name( 'pcount' ); ?>">
        <option value="1" <?php if(esc_attr($instance['pcount'])=='1') {echo 'selected';}?> >1</option>
        <option value="2" <?php if(esc_attr($instance['pcount'])=='2') {echo 'selected';}?>>2</option>
        <option value="3" <?php if(esc_attr($instance['pcount'])=='3') {echo 'selected';}?>>3</option>
        <option value="4" <?php if(esc_attr($instance['pcount'])=='4') {echo 'selected';}?>>4</option>
        <option value="5" <?php if(esc_attr($instance['pcount'])=='5') {echo 'selected';}?>>5</option>
        <option value="6" <?php if(esc_attr($instance['pcount'])=='6') {echo 'selected';}?>>6</option>
        <option value="7" <?php if(esc_attr($instance['pcount'])=='7') {echo 'selected';}?>>7</option>
        <option value="8" <?php if(esc_attr($instance['pcount'])=='8') {echo 'selected';}?>>8</option>
        </select>
    <p><br /><b>Custom Content</b> <textarea class="widefat" rows="16" cols="20" style="height:70px;" id="<?php echo $this->get_field_id( 'te' ); ?>" name="<?php echo $this->get_field_name( 'te' ); ?>"><?php echo esc_attr( $instance['te'] ); ?></textarea></p>
 <?php 
 	$out = '<p><input id="' . $this->get_field_id('image') . '" name="' . $this->get_field_name('image') . '" type="checkbox" ' . checked(isset($instance['image'])? $instance['image']: 0, true, false) . ' /> Show listing image </p><hr />';
	echo  $out; 	
	
 
}
	
// Updating widget replacing old instances with new
public function update( $new, $old ) {
		$clean = $old;		
		$clean['title'] = isset( $new['title'] ) ? strip_tags( esc_html( $new['title'] ) ) : '';
		$clean['pcount'] = isset( $new['pcount'] ) ?  esc_html( $new['pcount'] )  : '';	
  		$clean['image'] = isset( $new['image'] ) ? '1' : '0';
		if (current_user_can('unfiltered_html')) {
		  $clean['te'] = $new['te'];
		} else {
		  $clean['te'] = stripslashes(wp_filter_post_kses(addslashes($new['te'])));
		}
		return $clean;
}
} // Class wpb_widget ends here

// Register and load the widget
function wpb_load_widget() {
	register_widget( 'wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );

/* Stop Adding Functions Below this Line */
?>