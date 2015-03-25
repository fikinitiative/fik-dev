jQuery(document).ready(function() {
    jQuery('.cart_item_update').hide();  
    jQuery('input[type=number]').focus(function() {
        jQuery('.cart_item_update').hide();
        jQuery(this).siblings('button').show();

    });

    jQuery('input[type=number]').change(function() {
    	if(jQuery('input[type=number]').val() == 0){
	    	jQuery(this).siblings('button').text(fik_cart_texts.deleteText);
    	}else{
    		jQuery(this).siblings('button').text(fik_cart_texts.updateText);
    	}

    });

    jQuery('input[type=number]').keyup(function() {
    	if(jQuery('input[type=number]').val() == 0){
    		jQuery(this).siblings('button').text(fik_cart_texts.deleteText);
    	}else{
    		jQuery(this).siblings('button').text(fik_cart_texts.updateText);
    	}

    });

    
});
