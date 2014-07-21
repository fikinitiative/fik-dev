<?php

class FikCart
{
    private $format;

    public function __construct()
    {
        $this->format = $this->cart_format();
    }

    public function build_cart()
    {
        $order = fik_order_get();
        if($failedPrecondition = $this->hasFailedPreconditions($order)) {

            return $failedPrecondition;
        }

        return
            $this->build_cart_header() .
            $this->build_cart_body($order_cookie['items']) .
            $this->build_cart_footer() .
            $this->build_checkout_form();
    }

    private function cart_format()
    {
        $format = array(
            'before_cart'           => '<table class="table table-striped">',
            'after_cart'            => '</table>',
            'before_header_row'     => '<tr>',
            'after_header_row'      => '</tr>',
            'before_row'            => '<tr id="cart_item_%s" class="cart_item_row">',
            'after_row'             => '</tr>',
            'before_element'        => '<td>',
            'after_element'         => '</td>',
            'before_header'         => '<thead>',
            'after_header'          => '</thead>',
            'before_header_element' => '<th>',
            'after_header_element'  => '</th>',
            'before_body'           => '<tbody>',
            'after_body'            => '</tbody>',
            'before_total'          => '<tr class="fik-cart-subtotal-row">',
            'after_total'           => '</tr>',
            'before_total_element'  => '<td>',
            'after_total_element'   => '</td>',
            'cart_image_element'    => '<td class="cart_image"><a href="%s"><img src="%s" alt="%s"></a></td>',
            'product_name_element'  => '<td><a href="%s">%s</a><br/>%s</td>',
            'quantity_form'         => '<td><form action="" method="post"><input type="hidden" name="cart_item_%s" value="%s">
                                        <input type="number" name="cart_item_%s_quantity" min="0" max="10" step="1" value="%s"
                                        placeholder="%s" class="input-mini" required="">
                                        <button type="submit" class="cart_item_update btn btn-small" name="update_quantity">%s</button></form></td>',
            'product_price'         => '<td>%s</td>',
            'product_subtotal'      => '<td>%s</td>',
            'checkout_form'         => '<form action="" class="fik_to_checkout text-center" method="post" enctype="multipart/form-data">
                                        <button name="checkout" type="submit" class="button alt btn btn-large btn-primary">%s</button></form>',
        );
        $format = apply_filters('cart_format', $format);

        return $format;
    }

    private function hasFailedPreconditions($order_cookie)
    {
        if(!$this->store_active()){

            return $this->store_not_active_message();
        }

        if ($this->cart_empty()) {

            return $this->cart_empty_message();
        }

        if (($order_cookie === FALSE) || (empty($order_cookie['items']))){

            return $this->cart_empty_message();
        }

        return false;
    }

    private function build_cart_header()
    {
        return $this->format['before_cart'] . 
               $this->format['before_header'] .
               $this->format['before_header_row'] .
               $this->format['before_header_element'] .
                   __('Image', 'fik-stores') .
               $this->format['after_header_element'] .
               $this->format['before_header_element'] .
                   __('Item', 'fik-stores') .
               $this->format['after_header_element'] .
               $this->format['before_header_element'] .
                   __('Quantity', 'fik-stores') .
               $this->format['after_header_element'] .
               $this->format['before_header_element'] .
                   __('Unit Price', 'fik-stores') .
               $this->format['after_header_element'] .
               $this->format['before_header_element'] .
                   __('Subtotal', 'fik-stores') .
               $this->format['after_header_element'] .
               $this->format['after_header_row'] .
               $this->format['after_header'];
    }

    private function build_cart_footer()
    {
        return $this->format['after_cart'];
    }

    private function build_cart_body($items)
    {
        $items = $this->cart_items($items);

        return 
            $this->format['before_body'] .
            $items['items'] . $this->cart_total($items['total']) .
            $this->format['after_body'];
    }

    private function build_checkout_form()
    {
        return sprintf($this->format['checkout_form'], __("Proceed to checkout", 'fik-stores'));
    }

    private function store_active()
    {
        return _fik_store_status_sync() == STORE_ACTIVE;
    }

    private function cart_empty()
    {
        return fik_order_cookie_status() === FALSE;
    }

    private function store_not_active_message()
    {
        $message = "<h3>" . __('The store is under maintenance', 'fik-stores') . "</h3>";
        $message .= "<br><p>" . sprintf(__('<a href="%1$s" title="%2$s">The store</a> is under maintenance, we can not display your cart at the moment. Please check back soon.', 'fik-stores'), home_url('/products/'), get_bloginfo('name')) . "</p>";

        $message = apply_filters('store_not_active_message', $message);

        return $message;
    }

    private function cart_empty_message()
    {
        $message = "<h3 class='fik-cart-empty'>" . __('Your Shopping Cart is empty', 'fik-stores') . "</h3>";
        $message .= "<br><p class='fik-cart-empty'>" . sprintf(__('Take a look at <a href="%1$s" title="%2$s">the store</a> and add some products to your cart.', 'fik-stores'), home_url('/products/'), get_bloginfo('name')) . "</p>";

        $message = apply_filters('cart_empty_message', $message);

        return $message;
    }

    private function cart_items($items)
    {
        foreach ($items as $number => $checkoutItem) {
            $checkoutTable .= $this->cart_row($this->row_calculate($number, $checkoutItem));
            $cartTotal += get_the_fik_price($checkoutItem['product_id'], $checkoutItem['variations'], $checkoutItem['quantity']);
        }

        return array('items' => $checkoutTable, 'total' => $cartTotal);
    }

    private function row_calculate($number, $checkoutItem)
    {
        $productQuantity = $checkoutItem['quantity'];
        $productID = $checkoutItem['product_id'];
        $productTitle = fik_product_title($productID);

        if (isset($checkoutItem['variations'])) {
            $productDesc = fik_product_pretify_variations($checkoutItem['variations']);
        } else {
            $productDesc = "";
            $checkoutItem['variations'] = array();
        }

        $productPrice = fik_format_price(get_the_fik_price($productID, $checkoutItem['variations']));
        $productUrl = fik_product_url($productID);
        $productIMG = fik_product_thumbnail($productID, 200);

        $productSubotal = fik_format_price(get_the_fik_price($productID, $checkoutItem['variations'], $productQuantity) );

        return array(
            'number' => $number,
            'productUrl' => $productUrl,
            'productIMG' => $productIMG,
            'productTitle' => $productTitle,
            'productDesc' => $productDesc,
            'checkoutItem' => urlencode(json_encode($checkoutItem)),
            'productQuantity' => $productQuantity,
            'productPrice' => $productPrice,
            'productSubotal' => $productSubotal,
        );
    }

    private function cart_row($args)
    {
        return sprintf($this->format['before_row'], $args['number']) .
               sprintf($this->format['cart_image_element'], $args['productUrl'], $args['productIMG'],$args['productTitle']) .
               sprintf($this->format['product_name_element'], $args['productUrl'], $args['productTitle'], $args['productDesc']) .
               sprintf($this->format['quantity_form'], $args['number'], $args['checkoutItem'], $args['number'], $args['productQuantity'], $args['productQuantity'], __('Update', 'fik-stores')) .
               sprintf($this->format['product_price'], $args['productPrice']) .
               sprintf($this->format['product_subtotal'], $args['productSubotal']) .
               $this->format['after_row'];
    }

    private function cart_total($cartTotal)
    {
        return $this->format['before_total'] .
               $this->format['before_total_element'] .
                   '&nbsp' .
               $this->format['after_total_element'] .
               $this->format['before_total_element'] .
                   '&nbsp' .
               $this->format['after_total_element'] .
               $this->format['before_total_element'] .
                   '&nbsp' .
               $this->format['after_total_element'] .
               $this->format['before_total_element'] .
                   '<strong>' . __('Subtotal', 'fik-stores') . '</strong>' .
               $this->format['after_total_element'] .
               $this->format['before_total_element'] .
                   '<strong>' . fik_format_price($cartTotal) . '</strong>' .
               $this->format['after_total_element'] .
               $this->format['after_total'];
    }
}
