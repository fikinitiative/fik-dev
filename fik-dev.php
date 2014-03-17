<?php
   /*
   Plugin Name: Fik Dev
   Plugin URI: http://fikstores.com
   Description: Fik Stores Themes helper plugin
   Version: 1.0
   Author: Fik Stores
   Author URI: http://fikstores.com
   License: GPL2
   */

// Register Product Post Type
function product_post_type() {

    $labels = array(
        'name' => _x('Products', 'Product post type general name (plural)' , 'text_domain' ),
        'singular_name' => _x('Product', 'post type singular name' , 'text_domain' ),
        'add_new' => _x('Add New', 'Product' , 'text_domain' ),
        'add_new_item' => __('Add New Product' , 'text_domain'),
        'edit_item' => __('Edit Product' , 'text_domain'),
        'new_item' => __('New Product' , 'text_domain'),
        'all_items' => __('All Products' , 'text_domain'),
        'view_item' => __('View Product' , 'text_domain'),
        'search_items' => __('Search Products' , 'text_domain'),
        'not_found' => __('No products found' , 'text_domain'),
        'not_found_in_trash' => __('No products found in Trash' , 'text_domain'),
        'parent_item_colon' => __( 'Parent Product:', 'text_domain' ),
        'menu_name' => __('Products' , 'text_domain'),
        'update_item'         => __( 'Update Product', 'text_domain' ),
    );

	$args = array(
		'label'               => __( 'fik_product', 'text_domain' ),
		'description'         => __( 'Product post type used in Fik Stores for store products', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( ),
		'taxonomies'          => array( 'store_category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'fik_product', $args );

}

// Hook into the 'init' action
add_action( 'init', 'product_post_type', 0 );


function the_fik_price(){
	return '<span itemprop="price" class="price"><span class="amount">109,99</span> EUR</span>';

}


?>