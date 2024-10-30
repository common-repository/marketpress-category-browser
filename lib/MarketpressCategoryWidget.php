<?php

/*
 * Implements our Marketpress Category widget
 * @author Samer Bechara <sam@thoughtengineer.com>
 */
class MarketpressCategoryWidget extends WP_Widget {

    // Holds the widget instance array
    private $instance = false;
    
    // Hide empty categories flag
    private $hide_empty = false;

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {

	// Call parent constructior
	parent::__construct(
		'marketpress_category_widget', // Base ID
		__('Marketpress Category Widget', 'marketpress-category-browser'), // Name
		array( 'description' => __( 'Displays the list of category children and siblings of the current Marketpress product or category', 'text_domain' ), ) // Args
	);
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {		     	        	    

	// Initialize instance object
	$this->instance = $instance;

	// Set hide empty categories flag
	$this->hide_empty = is_null($instance['hide_empty']) ? 0 : 1;
;	
	// Display image on top of listing
	$this->display_image();

	// Check if this is a product page
	if(is_singular('product')){		

	    $this->product_view();

	}

	// If this is a product category page
	elseif(is_tax('product_category')){

	    $this->category_view();

	}

	// For every other page, list all categories
	else{

	    $this->generic_view();	
	}


    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {

	// If widget height is set, display it
	if ( isset( $instance[ 'height' ] ) ) {
	    $height = $instance[ 'height' ];
	}
	// Use default height
	else {
	    $height = '150';
	}

	// If widget width is set, display it
	if ( isset( $instance[ 'width' ] ) ) {
	    $width = $instance[ 'width' ];
	}
	// Use default width
	else {
	    $width = '150';
	}
	
	?>
	<p>
	    <!-- Image height field -->
	    <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Widget Image Height' ); ?></label> 
	    <input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>">
	</p>
	<p>
	    <!-- Image width field -->
	    <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Widget Image Width' ); ?></label> 
	    <input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>">		
	</p>
	<p>
	    <!-- Hide Empty Categories -->
	    <label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>"><?php _e( 'Hide Empty Categories? ' ); ?></label> 
	    <input class="widefat" id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" type="checkbox" value="" <?php checked(isset($instance['hide_empty']) ? 1: 0); ?> />		
	</p>	
	<?php 

    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    public function update( $new_instance, $old_instance ) {

	// Initialize instance
	$instance = array();

	// Set featured image width based on user entered options
	$instance['width'] = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';

	// Set featured image height based on user entered options
	$instance['height'] = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '';	
	
	$instance['hide_empty'] = $new_instance['hide_empty'];

	// Return values to be saved
	return $instance;	    
    }

    private function list_categories($arguments){

	// Set default arguments
	$default_arguments = array('taxonomy' => 'product_category',
				'hide_empty' => $this->hide_empty ,
				'show_option_all ' => "All Products",
				'show_count' => true,
				'depth' => 2,
				'show_option_none' => ''
					);

	// Merge user and default arguments
	$arguments = array_merge($default_arguments, $arguments);

	// Display categories
	wp_list_categories($arguments);
    }

    /*
     * Displays widget on a product page
     */
    private function product_view(){

	global $wp_query;

	// We want to display siblings unless we are told otherwise
	$show_siblings = true;

	// Get product categories
	$categories = get_the_terms(get_the_ID(), 'product_category' );

	// Only show category children if a category was actually found
	if($categories !== FALSE){
	 
	    // Get category IDS
	    $cat_ids = array_keys($categories);

	    // Display tree for each of the categories the product belongs to 
	    foreach($categories as $cat){		

		// Get the current category's children and ancestors
		$children = get_term_children( $cat->term_id, 'product_category' );
		$ancestors = get_ancestors($cat->term_id, 'product_category');

		// Get the common ids between ancestors and current IDs
		$array_intersect = array_intersect($ancestors, $cat_ids);

		// Only display if the current category has children and none of the categories being displayed are an ancestor of the current category
		if(!empty($children) && empty($array_intersect)){									    			
		    // List children categories of current ID
		    $this->list_categories(array('child_of' => $cat->term_id, 'title_li' => '<a href="'.get_term_link($cat->term_id, 'product_category').'">'.$cat->name.'</a>'));

		    // Hide siblings since a tree is being displayed
		    $show_siblings = false;			
		}

	    }	    
	}


	// If none of the product categories have children, display our siblings
	if($show_siblings){

	    $this->list_categories(array('child_of' => $wp_query->queried_object->post_parent,'depth' => 1, 'title_li' => 'Category Siblings'));	

	}    

    }

    /*
     * Outputs widget on a product category page
     */
    private function category_view(){

	global $wp_query;

	// Get the current category's children and ancestors
	$children = get_term_children( $wp_query->queried_object_id, 'product_category' );

	// List children if they exist
	if(!empty($children)){

	    // List category children
	    $this->list_categories(array( 'child_of' => $wp_query->queried_object_id,
		'title_li' => '<a href="'.get_term_link($wp_query->queried_object_id, 'product_category').'">'.$wp_query->queried_object->name.'</a>'));

	}

	// Otherwise list siblings
	else {
	    $this->list_categories(array('child_of' => $wp_query->queried_object->post_parent,'depth' => 1, 'title_li' => 'Category Siblings'));		    
	}	    
    }
	
    /*
     * Outputs widget on non-product related pages
     */
    private function generic_view(){

	wp_list_categories(array('taxonomy' => 'product_category',
			    'hide_empty' => $this->hide_empty,
			    'show_count' => true
			));		    
    }

    /*
     * Displays the image at top of the menu
     */
    private function display_image(){

	// Check if this is a product page
	if(is_singular('product')){		

	    $image = $this->product_image();

	}

	// If this is a product category page
	elseif(is_tax('product_category')){

	    $image = $this->category_image();

	}

	// For every other page, list all categories
	else{

	    $image = $this->generic_image();	
	}

	echo $image;

    }
	
    /*
     * Returns the widget image for a product page
     */

    private function product_image(){

	// Get list of categories this product belongs to
	$categories = wp_get_post_terms(get_the_ID(), 'product_category');

	// Loop through each of the categories
	foreach($categories as $cat){

	    // Get image associated with category
	    $image = $this->get_cat_image($cat->slug);
	    
	    // Image was found return it
	    if($image !== FALSE){
		return $image;
	    }
	}


	// No image found
	return false;

    }

    /*
     * Returns widget image for a generic page
     */
    private function generic_image(){

	// Get the top level product categories (Level 0)
	$categories = get_terms('product_category');
	
	// Loop through them to get a proper image
	foreach($categories as $cat){
	    
	    // Get category image
	    $image = $this->get_cat_image($cat->slug);
	    
	    // Image was found
	    if($image !== FALSE){
		return $image;
	    }
	}
	
	// No image was found, return false
	return FALSE;
	
    }
	
    /*
     * Returns widget image for a product category page
     */
    private function category_image(){

	global $wp_query;

	// Get current category
	$cat = $wp_query->query_vars['product_category'];

	// Get category image
	$image = $this->get_cat_image($cat);
	
	// Image found
	if( $image !== FALSE){
	    return $image;
	}
	
	// not found
	return false;

    }	
    
    /*
     * Returns an image associated with a category
     * @param $cat_slug The category's slug
     * @return string Image HTML code
     * @return false  When image does not exist 
     */
    private function get_cat_image($cat_slug){
	
	// Get the first product in that category which has a thumbnail
	$product = get_posts(array( 'post_type' => 'product',
				     'product_category' => $cat_slug,
				     'meta_query' => array(array('key' => '_thumbnail_id')),
				     'posts_per_page' => 1
				    ));	

	// If product with thumbnail has been found
	if(!empty($product)){

	    // Get and return image
	    $image = get_the_post_thumbnail($product[0]->ID, array($this->instance['width'],$this->instance['height']));

	    return $image;
	}

	// No image found
	return false;
	
    }
}