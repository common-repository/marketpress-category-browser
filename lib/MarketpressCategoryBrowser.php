<?php

class MarketpressCategoryBrowser {
    
    public function __construct() {

	// Register our widget
	add_action( 'widgets_init', function(){
	     register_widget( 'MarketpressCategoryWidget' );
	});	
	
	// Register our shortcode
	add_shortcode('marketpress_category_widget', array($this,'insert_shortcode') );
    }
    
    /*
     * Inserts a shortcode into our website
     * @param $atts The shortcode attributes
     * 
     */
    public function insert_shortcode($atts) {
	
	// Default attributes for shortcode
	$default = array('width' => 150, 'height' => 150);
	
	// Merge user attributes with our default attributes	
	$atts = shortcode_atts($default, $atts, 'marketpress_category_widget');
	
	// Send back our widget
	return the_widget('MarketpressCategoryWidget',$atts);
    }
    
}