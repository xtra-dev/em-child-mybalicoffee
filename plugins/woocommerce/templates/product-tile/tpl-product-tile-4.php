<?php
defined( 'ABSPATH' ) || exit;

global $product;

if ( isset($em_post_id) ) {
	if( empty($em_post_id)) {
		$_product = $product;
	} else {
		$_product = wc_get_product($em_post_id);
	}
} else {
	$_product = wc_get_product( get_the_ID() );
}

if ( ! $_product->is_type('variation') ) {
	if ( $_product->is_type('variable') ) {
		$variation_id = $_product->get_available_variations()[0]['variation_id'];
		$_product = wc_get_product($variation_id);
	} else {
		return;
	}
}

wp_enqueue_script( 'wc-add-to-cart-variation' );

$em_post_id  = $_product->get_id();
$title 		 = $_product->get_title();
$price 		 = $_product->get_price_html();
$link 		 = $_product->get_permalink();
$images 	 = array();
$type 		 = $_product->get_type();
$product_tile_grid = apply_filters( 'product_tile_grid', 'col_sm_6 col_4' );


$region_field = get_field('gebiet_region');
$region_term_meta = get_term_meta( $region_field->term_id ); 
$region_background_color = $region_term_meta['background_color'][0]; 

$flavor_field = get_field('bohnenart');
$flavor_term_meta = get_term_meta( $flavor_field->term_id ); 
?>

<div class="em-product-tile-4 em-product-tile <?php echo $product_tile_grid; ?>" data-em_post_id="<?php echo $em_post_id; ?>"<?php if ( isset($region_background_color) ) { echo ' style="background-color: ' . $region_background_color . '"'; } ?>>

	<div class="tile-loader"></div>
	<div class="content">

		<?php 
		if (isset($_product->get_data()['attributes']['pa_farbe'])) {

			$attribute = $_product->get_data()['attributes']['pa_farbe'];
			$parent = wc_get_product($_product->get_parent_id());
			$link = add_query_arg( 'attribute_pa_farbe', $attribute, $parent->get_permalink() );
            foreach ($parent->get_gallery_image_ids() as $attachment_id) {
            	if ( !empty( get_field('img_color', $attachment_id, false) ) ) {
            		$img_color = get_term( get_field('img_color', $attachment_id, false), 'pa_farbe' )->slug;
                    if ( $attribute == $img_color ) {
                    	$images[$em_post_id] = $attachment_id;
                    	break;
                    }
            	}
            }
            $new_images = !empty($images) ? $images : array();
			echo em_display_images_lazyload( $link, $new_images );

		} else {

			$parent = wc_get_product($_product->get_parent_id());
			$link = add_query_arg( 'attribute_pa_farbe', $attribute, $parent->get_permalink() );

			if ( $product->get_image_id() ) {
				$new_images = array( $product->get_image_id() );
				array_push($new_images, $parent->get_gallery_image_ids());
			} else {
				$new_images = $parent->get_gallery_image_ids();
			}

			echo em_display_images_lazyload( $link, $new_images );
		}
		?>
		<div class="description">
			<div class="wrapper">
		        <span class="flavor"><?php echo $flavor_field->name; ?></span>
		        <span class="title"><?php echo $title; ?></span>
		        <span class="region"><?php echo $region_field->name; ?></span>
		    </div>
		    <div class="more-information">
		    	<a href="<?php echo $link; ?>" class="btn">Mehr erfahren</a>
		    </div>
	    </div>

    </div>
</div>