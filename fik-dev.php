<?php
   /*
   Plugin Name: Fik Stores Dev
   Plugin URI: http://fikstores.com
   Description: Fik Stores Themes development helper plugin
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
		'label'               => __( 'Products', 'text_domain' ),
		'description'         => __( 'Product post type used in Fik Stores for store products', 'text_domain' ),
		'labels'              => $labels,
		'supports' => array('title', 'editor', 'excerpt', 'custom-fields', 'page-attributes', 'thumbnail'), // thumbnail, revision or comment support can be added here
		'taxonomies'          => array( 'store_category', 'post_tag' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 8,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'fik_product', $args );

}

// Hook into the 'init' action
add_action( 'init', 'product_post_type', 0 );



function fik_stores_customizer($wp_customize) {

    class fik_stores_Customize_Badge_Control extends WP_Customize_Control {

        public $type = 'select';

        public function render_content() {
        	$badge_colors = array('Default','Red', 'Green', 'Blue', 'Purple', 'Gray');
        	echo ('<label><span class="customize-control-title">' . esc_html($this->label) . '</span><select ' . $this->link() . '>');
        	foreach ($badge_colors as $color) {
        		echo ('<option value="' . ($color == $badge_colors[0] ? '': $color) . '"' . selected($this->value(), $color) . '>' .  $color . '</option>');
        	}
        	echo ('</select></label>');
        }

    }

    $wp_customize->add_section('fik_stores_fikstores_badge', array(
        'title' => __('Fik Stores Badge', 'fik-stores'),
        'priority' => 130,
    ));

    $wp_customize->add_setting('fik_stores_badge', array(
        'default' => '',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(
    	new fik_stores_Customize_Badge_Control($wp_customize, 'fik_stores_badge', array(
                'label' => __('Badge', 'fik-stores'),
                'section' => 'fik_stores_fikstores_badge',
                'settings' => 'fik_stores_badge',
            )));
}

add_action('customize_register', 'fik_stores_customizer');

function get_fikstores_badge(){
	$badge = get_theme_mod('fik_stores_badge', '');
    $src = plugins_url( 'img/poweredbyfikstores' . $badge . '.png' , __FILE__ ) ;

    return '<a href="http://fikstores.com/" title="Better ecommerce">
    	<img width="105" height="50" src="' . $src . '" class="replace-2x fikstores-badge" alt="Better ecommerce">
    	</a>';

}

function the_fikstores_badge() {
	echo get_fikstores_badge();
	return;
}


function the_fik_price(){
	echo('<span itemprop="price" class="price"><span class="amount">109,99</span> EUR</span>');
	return;

}

function the_fik_add_to_cart_button(){
	echo('<form action="" class="fik_add_cart" method="post" enctype="multipart/form-data"><input type="hidden" name="store_product_id" value="38">'
		. get_fik_product_select_variations() . get_fik_product_select_quantity() . get_add_to_cart_button() .
		'</form>');
	return;
}

function get_fik_product_select_variations(){
	return '';
}

function get_fik_product_select_quantity(){
	return '<div class="control-group product-quantity"><label class="control-label" for="quantity">Quantity</label><div class="controls"><input type="number" name="quantity" class="input-mini" min="1" max="10" step="1" value="1" required=""></div></div>';
}

function get_add_to_cart_button(){
	return '<button type="submit" class="button alt btn btn-primary">Add to cart</button>';
}


function the_product_gallery_thumbnails($thumnail_size = 'post-thumbnail', $image_size = 'medium', $zoom_image_size = 'large'){
    global $post;
    $post = get_post($post);

    /* image code */
    $thumblist = array();
    //$images = & get_children('post_type=attachment&post_mime_type=image&output=ARRAY_N&orderby=menu_order&order=ASC&post_parent=' . $post->ID);
    $args = array(
        'numberposts' => -1,
        'order' => 'ASC',
        'orderby' => 'menu_order',
        'post_mime_type' => 'image',
        'post_parent' => $post->ID,
        'post_type' => 'attachment',
        'meta_query' => array(
            array(
                'key' => '_fik_product_image',
                'value' => $post->ID,
            )
        ) // The attachment was downloaded via the Product Images metabox, so it should be shown in the image gallery
    );

    $images = & get_children($args);

    if ($images) {
        foreach ($images as $imageID => $imagePost) {
            unset($the_thumbnail);
            unset($the_image_url);
            $the_thumbnail = wp_get_attachment_image($imageID, $thumnail_size, false);
            $the_image = get_post($imageID);
            $the_image_url = wp_get_attachment_image_src($imageID, $image_size, false);
            $the_zoom_image_url = wp_get_attachment_image_src($imageID, $zoom_image_size, false);
            $thumblist[$imageID] = '<a target="_blank" href="' . $the_image_url[0] . '" title="' . $the_image->post_title . '" data-width="' . $the_image_url[1] . '" data-height="' . $the_image_url[2] . '" data-zoom-image="' . $the_zoom_image_url[0] . '" data-zoomimagewidth="' . $the_zoom_image_url[1] . '" data-zoomimageheight="' . $the_zoom_image_url[2] . '">' . $the_thumbnail . '</a>';
        }
    }

    if ($thumblist == array())
        return false;

    if ($hide_if_one) {
        $thumbnail_count = 0;
    }

    $output = '<ul class="product-image-thumbnails thumbnails">';

    foreach ($thumblist as $thumbnail) {
        $output .= '<li class="thumbnail">' . $thumbnail . '</li>';
        if ($hide_if_one) {
            $thumbnail_count++;
        }
    }

    $output .= '</ul>';

    if ($hide_if_one) {
        if ($thumbnail_count <= 1) {
            return;
        }
    }

    echo $output;
    return;
}



function the_store_logo($size = "full", $args = array('class' => 'logo')){
    $logo_id = get_option('_fik_store_logo');
    echo wp_get_attachment_image($logo_id, $size, false, $args);
}

/**
 * Returns HTML for status and/or error messages, grouped by type.
 *
 * An invisible heading identifies the messages for assistive technology.
 * Sighted users see a colored box. See http://www.w3.org/TR/WCAG-TECHS/H69.html
 * for info.
 *
 * @param string $display
 *   - display: (optional) Set to 'status', 'warning', 'help' or 'error' to display only messages
 *     of that type.
 */

function fik_messages($display = FALSE, $message = array ('status' => 'This is a test message')) {
  $output = '';
  $status_heading = array(
    'success' => __('Status message', 'fik-stores'), 
    'error' => __('Error message', 'fik-stores'), 
    'warning' => __('Warning message', 'fik-stores'),
    'info' => __('Info message', 'fik-stores'), 
  );
  foreach ($message as $type => $messages) {
    if ($type) $alert_class = "alert-" . $type;
    $output .= "<div class=\"alert $alert_class\">\n";
    if (!empty($status_heading[$type])) {
      $output .= '<h4 class="hide">' . $status_heading[$type] . "</h4>\n";
    }
    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= '<p>' . $messages[0] . '</p>';
    }
    $output .= "</div>\n";
  }
  $output = apply_filters( 'fik_messages', $output);
  return $output;
}

?>