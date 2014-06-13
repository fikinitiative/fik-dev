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
    'rewrite' => array(
      'slug' => 'products',
        'with_front' => false ,
        'feeds' => true,
        'pages' => true
      ),
    'query_var' => 'product',
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'fik_product', $args );

}

// Hook into the 'init' action
add_action( 'init', 'product_post_type', 0 );


// Add the Store Section taxonomy for products

function store_sections_init() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name' => _x('Store Sections', 'taxonomy general name' , 'fik-stores' ),
        'singular_name' => _x('Store Section', 'taxonomy singular name' , 'fik-stores' ),
        'search_items' => __('Search Store Sections' , 'fik-stores'),
        'popular_items' => null, // null so popular categories will not be displayed in edit-tags.php admin page for this taxonomy
        'all_items' => __('All Store Sections' , 'fik-stores'),
        'parent_item' => __('Parent Store Section' , 'fik-stores'),
        'parent_item_colon' => __('Parent Store Section:' , 'fik-stores'),
        'edit_item' => __('Edit Store Section' , 'fik-stores'),
        'update_item' => __('Update Store Section' , 'fik-stores'),
        'add_new_item' => __('Add New Store Section' , 'fik-stores'),
        'new_item_name' => __('New Store Section Name' , 'fik-stores'),
        'menu_name' => __('Store Sections' , 'fik-stores'),
    );

    register_taxonomy('store-section', array('fik_product'), array(
        'hierarchical' => true,
        'label' => _x('Store Sections', 'taxonomy general name' , 'fik-stores' ),
        'labels' => $labels,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'section',
            'with_front' => true,
            'hierarchical' => true
        ),
    ));
}

add_action('init', 'store_sections_init', 0);

// Add Custom Product Variations

function _fik_product_vars_init() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name' => _x('Product Variations', 'taxonomy general name' , 'fik-stores' ),
        'singular_name' => _x('Product Variation', 'taxonomy singular name' , 'fik-stores' ),
        'search_items' => __('Search Product Variations' , 'fik-stores'),
        'popular_items' => null,
        'all_items' => __('All Product Variations' , 'fik-stores'),
        'parent_item' => __('Parent Product Variation' , 'fik-stores'),
        'parent_item_colon' => __('Parent Product Variation:' , 'fik-stores'),
        'edit_item' => __('Edit Product Variation' , 'fik-stores'),
        'update_item' => __('Update Product Variation' , 'fik-stores'),
        'add_new_item' => __('Add New Product Variation' , 'fik-stores'),
        'new_item_name' => __('New Product Variation Name' , 'fik-stores'),
        'menu_name' => __('Product Variations' , 'fik-stores'),
    );

    register_taxonomy('product-variation', array('fik_product'), array(
        'hierarchical' => true,
        'label' => _x('Product Variations', 'taxonomy general name' , 'fik-stores' ),
        'labels' => $labels,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'product-variation',
            'with_front' => true,
            'hierarchical' => true
        ),
    ));
}

add_action('init', '_fik_product_vars_init', 0);


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

function fik_product_sku(){
    return '140702/JINGLE';
}

function the_fik_add_to_cart_button(){
	echo('<form action="" class="fik_add_cart" method="post" enctype="multipart/form-data"><input type="hidden" name="store_product_id" value="38">'
		. get_fik_product_select_variations() . get_fik_product_select_quantity() . get_add_to_cart_button() .
		'</form>');
	return;
}

function get_fik_product_select_variations(){
  //The following example shows womens shoe sizes from 36 to 41
	return '<div class="control-group product-variations"><label class="control-label" for="variation-16">Talla mujer</label><div class="controls"><select name="variation-16" id="vv-talla-mujer"><option value="">Selecciona una opción …</option><option value="17">36</option><option value="18">37</option><option value="19">38</option><option value="20">39</option><option value="21">40</option><option value="22">41</option></select></div></div>';
}

function get_fik_product_select_quantity(){
	return '<div class="control-group product-quantity"><label class="control-label" for="quantity">Quantity</label><div class="controls"><input type="number" name="quantity" class="input-mini" min="1" max="10" step="1" value="1" required=""></div></div>';
}

function get_add_to_cart_button($prodID = null, $buttonClasses = "button alt btn btn-primary"){
	return '<button type="submit" class="' . $buttonClasses . '">Add to cart</button>';
}


function the_product_gallery_thumbnails($thumnail_size = 'post-thumbnail', $image_size = 'medium', $zoom_image_size = 'large'){
    $product_image = get_post_custom_values('product_image');
    if ($product_image){
      foreach ( $product_image as $key => $image_id ) {
          $the_thumbnail = wp_get_attachment_image($image_id, $thumnail_size, false);
          $the_image = get_post($image_id);
          $the_image_url = wp_get_attachment_image_src($image_id, $image_size, false);
          $the_zoom_image_url = wp_get_attachment_image_src($image_id, $zoom_image_size, false);
          $thumblist[$image_id] = '<a target="_blank" href="' . $the_image_url[0] . '" title="' . $the_image->post_title . '" data-width="' . $the_image_url[1] . '" data-height="' . $the_image_url[2] . '" data-zoom-image="' . $the_zoom_image_url[0] . '" data-zoomimagewidth="' . $the_zoom_image_url[1] . '" data-zoomimageheight="' . $the_zoom_image_url[2] . '">' . $the_thumbnail . '</a>';
      }

      if ($thumblist == array())
          return false;

      $output = '<ul class="product-image-thumbnails thumbnails">';

      foreach ($thumblist as $thumbnail) {
          $output .= '<li class="thumbnail">' . $thumbnail . '</li>';
      }

      $output .= '</ul>';

      echo $output;
    }
    return;
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

function fik_messages($display = FALSE, $message = array ('error' => "This is a test message")) {
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
        if(is_array($messages)){
            $output .= '<p>' . $messages[0] . '</p>';
        }else{
            $output .= '<p><ul><li>' . $messages . '</li></ul></p>';
        }
    }
    $output .= "</div>\n";
  }
  $output = apply_filters( 'fik_messages', $output);
  return $output;
}

function the_store_logo($size = "full", $args = array('class' => 'logo')){
    $logo_id = get_option('fik_store_logo');
    echo wp_get_attachment_image($logo_id, $size, false, $args);
}

// Store sections
function _storecats_init() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name' => 'Store Sections',
        'singular_name' => _x('Store Section', 'taxonomy singular name' , 'fik-dev' ),
        'search_items' => __('Search Store Sections' , 'fik-dev'),
        'popular_items' => null, // null so popular categories will not be displayed in edit-tags.php admin page for this taxonomy
        'all_items' => __('All Store Sections' , 'fik-dev'),
        'parent_item' => __('Parent Store Section' , 'fik-dev'),
        'parent_item_colon' => __('Parent Store Section:' , 'fik-dev'),
        'edit_item' => __('Edit Store Section' , 'fik-dev'),
        'update_item' => __('Update Store Section' , 'fik-dev'),
        'add_new_item' => __('Add New Store Section' , 'fik-dev'),
        'new_item_name' => __('New Store Section Name' , 'fik-dev'),
        'menu_name' => __('Store Sections' , 'fik-dev'),
    );

    register_taxonomy('store-section', array('fik_product'), array(
        'hierarchical' => true,
        'label' => _x('Store Sections', 'taxonomy general name' , 'fik-dev' ),
        'labels' => $labels,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'section',
            'with_front' => true,
            'hierarchical' => true
        ),
    ));
}
add_action('init', '_storecats_init', 0);

class store_sections_widget extends WP_Widget {

    function store_sections_widget(){
        $widget_ops = array('classname' => 'store_sections_widget', 'description' => "Display the store sections belongs to a product" );
        $this->WP_Widget('store_sections_widget', "Store Sections Widget", $widget_ops);
    }

    function widget($args,$instance){
        echo '<div class="product-store-categories">' . get_the_term_list($post->ID, 'store-section', '', ', ', '' ) . '</div>';
    }

    function update($new_instance, $old_instance){
    }

    function form($instance){
    }
}

function store_sections_create_widget(){
    register_widget('store_sections_widget');
}
add_action('widgets_init','store_sections_create_widget');

function the_fik_checkout()
{
    ?>
    <table class="table table-striped">
      <thead>
        <tr>
          <th class="cart_image">Imagen</th>
          <th>Elemento</th>
          <th>Cantidad</th>
          <th class="hidden-phone">Precio unitario</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <tr class="cart_item_row" id="cart_item_0"><td class="cart_image"><a href="#"><img alt="Producto de ejemplo" src="http://placekitten.com/200"></a></td>
          <td><a href="#">My product</a><br>L</td>
          <td><form method="post" action="">
            <input type="hidden" value="" name="cart_item_0"><input type="number" required="" class="input-mini" placeholder="2" value="2" step="1" max="10" min="0" name="cart_item_0_quantity"><button name="update_quantity" class="cart_item_update btn btn-small" type="submit" style="display: none;">Actualizar</button></form></td>
          <td><span class="price" itemprop="price">€ <span class="amount">10,00</span></span></td>
          <td> <span class="price" itemprop="price">€ <span class="amount">20,00</span></span> </td>
        </tr>
        <tr class="cart_item_row" id="cart_item_1"><td class="cart_image"><a href="#"><img alt="Cabeza de toro" src="http://placekitten.com/200"></a></td>
          <td><a href="#">Best product</a><br></td>
          <td><form method="post" action="">
            <input type="hidden" value="" name="cart_item_1"><input type="number" required="" class="input-mini" placeholder="1" value="1" step="1" max="10" min="0" name="cart_item_1_quantity"><button name="update_quantity" class="cart_item_update btn btn-small" type="submit" style="display: none;">Actualizar</button></form></td>
          <td><span class="price" itemprop="price">€ <span class="amount">826,45</span></span></td>
          <td><span class="price" itemprop="price">€ <span class="amount">826,45</span></span></td>
        </tr>
        <tr class="cart_item_row" id="cart_item_2"><td class="cart_image"><a href="#"><img alt="Cabeza de toro" src="http://placekitten.com/200"></a></td>
          <td><a href="#">Our product</a><br></td>
          <td><form method="post" action="">
            <input type="hidden" value="" name="cart_item_1"><input type="number" required="" class="input-mini" placeholder="1" value="1" step="1" max="10" min="0" name="cart_item_1_quantity"><button name="update_quantity" class="cart_item_update btn btn-small" type="submit" style="display: none;">Actualizar</button></form></td>
          <td><span class="price" itemprop="price">€ <span class="amount">40</span></span></td>
          <td> <span class="price" itemprop="price">€ <span class="amount">40</span></span> </td>
        </tr>
        <tr class="fik-cart-subtotal-row">
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td><strong>Subtotal</strong></td>
          <td><strong><span class="price" itemprop="price">€ <span class="amount">886,45</span></span></strong></td>
        </tr>
      </tbody>
    </table>
    <?php
}
