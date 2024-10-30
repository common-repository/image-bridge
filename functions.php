<?php 

/**
 * Summary of ImageBridge_plugin_add_custom_sidebar_meta_box
 * @return void
 */
function ImageBridge_plugin_add_custom_sidebar_meta_box() {

    $post_types = ['product', 'page', 'post'];

    foreach( $post_types  as $post_type ){
        add_meta_box( 'ImageBridge_plugin_sidebar_meta_box', __( 'Image Bridge', 'textdomain' ), 'ImageBridge_plugin_render_custom_sidebar_meta_box', $post_type , 'side', 'default' );  
    }

}
add_action( 'add_meta_boxes', 'ImageBridge_plugin_add_custom_sidebar_meta_box' );



/**
 * Summary of ImageBridge_plugin_render_custom_sidebar_meta_box
 * @param mixed $post
 * @return void
 */
function ImageBridge_plugin_render_custom_sidebar_meta_box( $post ) {
    $custom_meta_data  = get_post_meta( $post->ID, 'image-bridge-url', true );
    $custom_meta_alt   = get_post_meta( $post->ID, 'image-bridge-alt', true );
    $custom_meta_title = get_post_meta( $post->ID, 'image-bridge-title', true );

    if ( ! empty( $custom_meta_data ) ) {
        ?>
        <div class ="ImageBridge-plugin-image">
                <img src="<?php echo esc_url( $custom_meta_data ); ?>" alt="<?php echo esc_attr( $custom_meta_alt ); ?>" title="<?php echo esc_attr( $custom_meta_title ); ?>" style="width:250px;">
        </div>
        <?php
    }
    
    // Add input fields to the meta box
    ?>
    <label for  ="image-bridge-url"><?php _e('Image URL', 'textdomain'); ?></label>
    <br />
    <input type ="text" id ="image-bridge-url" name ="image-bridge-url" value ="<?php echo esc_url( $custom_meta_data ); ?>" style="width: 100%;" />
    <br />
    <label for  ="image-bridge-alt"><?php _e('Alt text (optional)', 'textdomain'); ?></label>
    <br />
    <input type ="text" id ="image-bridge-alt" name ="image-bridge-alt" value ="<?php echo ( $custom_meta_alt ); ?>" style="width: 100%;" />
    <br />
    <label for  ="image-bridge-title"><?php _e('Title (optional)', 'textdomain'); ?></label>
    <br />
    <input type ="text" id= "image-bridge-title" name ="image-bridge-title" value ="<?php echo ( $custom_meta_title ); ?>" style="width: 100%;" />
    <?php  
}
   
       

/**
 * Summary of ImageBridge_plugin_save_custom_meta_data
 * @param mixed $post_id
 * @return void
 */
function ImageBridge_plugin_save_custom_meta_data( $post_id ) {

    // Check if the current user has permission to edit the post
    if ( ! current_user_can('edit_post', $post_id ) ) {
        return;
    }

    // Check if the image URL has been submitted and update the post meta data
    if ( isset ($_POST['image-bridge-url'] ) ) {
        $meta_value1 = sanitize_text_field( $_POST['image-bridge-url'] );
        $result1 = update_post_meta( $post_id, 'image-bridge-url', $meta_value1 );

        if ( ! $result1 ) {

            error_log('Failed to update meta data for post ' . $post_id );

        }
    } else {

        error_log('Input field value not submitted for post ' . $post_id );

    }

    // Update the alt if it has been submitted
    if ( isset ( $_POST['image-bridge-alt'] ) ) {

        $meta_value2 = sanitize_text_field( $_POST['image-bridge-alt'] );
        $result2     = update_post_meta( $post_id, 'image-bridge-alt', $meta_value2 );

        if ( ! $result2 ) {
            error_log('Failed to update meta data for post ' . $post_id );
        }

    }

    // Update the title if it has been submitted
    if ( isset( $_POST['image-bridge-title'] ) ) {

        $meta_value3 = sanitize_text_field( $_POST['image-bridge-title'] );
        $result3 = update_post_meta( $post_id, 'image-bridge-title', $meta_value3 );

        if ( ! $result3 ) {
            error_log('Failed to update meta data for post ' . $post_id );
        }

    }

}
add_action('save_post', 'ImageBridge_plugin_save_custom_meta_data');



/**
 * Summary of ImageBridge_plugin_custom_posts_columns
 * @param mixed $columns
 * @return mixed
 */
function ImageBridge_plugin_custom_posts_columns( $columns ) {
  
    $columns['ImageBridge_plugin_gallery_images'] = __( 'Image Bridge', 'textdomain' );
      return $columns;
} 
add_filter( 'manage_posts_columns'        , 'ImageBridge_plugin_custom_posts_columns' );
add_filter( 'manage_pages_columns'        , 'ImageBridge_plugin_custom_posts_columns' );
  


/**
 * Summary of ImageBridge_plugin_display_custom_meta
 * @param mixed $column
 * @param mixed $post_id
 * @return void
 */
function ImageBridge_plugin_display_custom_meta( $column, $post_id ) {

    if ( 'ImageBridge_plugin_gallery_images' === $column ) {

        $custom_meta_data = get_post_meta($post_id, 'image-bridge-url', true );       
            echo '<img src="' . esc_attr( $custom_meta_data ) . '" style="max-width: 40px;">';  

    }

}
add_action( 'manage_posts_custom_column'        , 'ImageBridge_plugin_display_custom_meta', 10, 2 );
add_action( 'manage_pages_custom_column'        , 'ImageBridge_plugin_display_custom_meta', 10, 2 );


/**
 * Summary of ImageBridge_replace_woocommerce_placeholder_img
 * @param mixed $image_url
 * @return string
 */
function ImageBridge_replace_woocommerce_placeholder_img( $image_url ) {

    global $product; 
    $custom_meta_data = get_post_meta( $product->get_id(), 'image-bridge-url', true );
    
    return '<img src="' . $custom_meta_data . '" alt="' . $product->get_name() . '" width="150" height="150">';
}
add_filter('woocommerce_placeholder_img', 'ImageBridge_replace_woocommerce_placeholder_img');



/**
 * Summary of ImageBridge_change_featured_image
 * @param mixed $html
 * @return mixed
 */
function ImageBridge_change_featured_image( $html ) {
    global $product;
    $custom_meta_data   = get_post_meta( $product->get_id(), 'image-bridge-url', true );
    $custom_meta_alt    = get_post_meta( $product->get_id(), 'image-bridge-alt', true );
    $custom_meta_title  = get_post_meta( $product->get_id(), 'image-bridge-title', true );
    $gallery_images     = get_post_meta( $product->get_id(), 'product_gallery', true);


    $output = '';
  
    //Check if custom images are set
    if ($custom_meta_data) {
        
        // Add featured image
        $output .= '
            <div data-thumb="' . $custom_meta_data . '" class="woocommerce-product-gallery__image ">
                <a href="' . $custom_meta_data . '" >
                    <img    style="width:100%;" 
                            src="' . $custom_meta_data . '" 
                            class="wp-post-image" 
                            alt="' . ( $custom_meta_alt ? esc_attr( $custom_meta_alt ) : esc_attr( $product->get_name() ) ) . '" 
                            title="' . ($custom_meta_title ? esc_attr( $custom_meta_title ) : esc_attr( $product->get_name() ) ) . '" 
                            decoding="async" 
                            loading="lazy" 
                            data-caption 
                            data-src="' . $custom_meta_data. '"
                            data-large_image="' . $custom_meta_data . '" 
                            data-large_image_width="1200" 
                            data-large_image_height="1800" 
                    />       
                </a>
            </div>
        ';

    
    }

    // Add gallery images
    if ( $gallery_images ) {
        foreach ( $gallery_images as $gallery_image ) {
            $output .= '
                <div data-thumb="' . $gallery_image . '" 
                    data-thumb-alt="" 
                    class="woocommerce-product-gallery__image" >
                    <a href="' . $gallery_image . '">
                        <img width="600" height="900" src="' . $gallery_image . '" 
                        class="" 
                        alt="" decoding="async" loading="lazy" title="" 
                        data-caption="" 
                        data-src="' . $gallery_image . '" 
                        data-large_image="' . $gallery_image . '" 
                        data-large_image_width="1200" data-large_image_height="1800" draggable="false">
                    </a>
                </div>
            ';
        }
    }
  
    // If neither custom images nor gallery images are set, display original product images
    if ( !$custom_meta_data && !$gallery_images ) {
        $output = $html;
    }
  
    return $output;
}

add_filter('woocommerce_single_product_image_thumbnail_html', 'ImageBridge_change_featured_image', 10, 1);



/**
 * Summary of ImageBridge_change_post_featured_image
 * @param mixed $html
 * @param mixed $post_id
 * @param mixed $post_thumbnail_id
 * @return mixed
 */
function ImageBridge_change_post_featured_image( $html, $post_id, $post_thumbnail_id ) {
    $custom_meta_data = get_post_meta( $post_id, 'image-bridge-url', true );
    
    $output = '';
  
    //Check if custom images are set
    if ($custom_meta_data) {

      // Add featured image
      $output .= '
                <img src="' . $custom_meta_data . '" class="wp-post-image" alt="' . get_the_title($post_id) . '" />
                ';
    } else {

      // Display original featured image
      $output = $html;

    }
  
    return $output;

}
add_filter('post_thumbnail_html', 'ImageBridge_change_post_featured_image', 10, 3);



/**
 * Add meta box to product edit page sidebar
 */
function add_gallery_meta_box() {
    add_meta_box(
        'product_gallery_meta_box',
        'Image Bridge Gallery(Pro)',
        'product_gallery_meta_box_callback',
        'product',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_gallery_meta_box');

/**
 * Callback function for the gallery meta box
 */
function product_gallery_meta_box_callback($post) {
    // Get existing gallery images
    $gallery_images = get_post_meta($post->ID, 'product_gallery', true);
    if ( !is_array( $gallery_images ) ) {
        $gallery_images = array();
    }
    ?>

    <div>
        <div id="product_gallery_container" >
            <div class="gallery_image_container" >
                <?php foreach ( $gallery_images as $key => $gallery_image ) : ?>
                    
                        <img src="<?php echo esc_attr($gallery_image); ?>" alt="" style="max-width: 100px; max-height: 100px;">
                    
                <?php endforeach; ?>
            </div>
        </div>


        <div id ="product_gallery_container" >
            <?php foreach ( $gallery_images as $key => $gallery_image ) : ?>
                <div class="gallery_image_container">
                    <input type="text" name="product_gallery[<?php echo $key; ?>]" value="<?php echo esc_attr($gallery_image); ?>" class="gallery_image_input" />
                    <a href="#" class="gallery_image_remove"><i class="dashicons dashicons-trash"></i></a>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="#" class="button button-primary" id="product_gallery_add_image"><i class="dashicons dashicons-plus"></i> Add Image</a>
        
    </div>
    <script>
        jQuery(document).ready(function($) {
            // Handle add image button click
            $('#product_gallery_add_image').on('click', function(e) {
                e.preventDefault();
                var container = $('#product_gallery_container');
                var index     = container.children('.gallery_image_container').length;
                var newInput  = $('<div class="gallery_image_container"><input type="text" name="product_gallery[' + index + ']" value="" class="gallery_image_input" /><a href="#" class="gallery_image_remove"><i class="dashicons dashicons-trash"></i></a></div>');
                container.append(newInput);
            });

            // Handle remove image button click
            $(document).on('click', '.gallery_image_remove', function(e) {
                e.preventDefault();
                $(this).closest('.gallery_image_container').remove();
            });
        });
    </script>
    <?php
}

/**
 * Save gallery images when product is saved
 */
function save_product_gallery_meta( $post_id ) {
    if ( isset ( $_POST['product_gallery'] ) ) {

        $gallery_images = array();

        foreach ( $_POST['product_gallery'] as $gallery_image ) {
            $gallery_images[] = sanitize_text_field( $gallery_image );
        }

        update_post_meta( $post_id, 'product_gallery', $gallery_images );

    } else {

        delete_post_meta( $post_id, 'product_gallery' );

    }
}
add_action('save_post_product', 'save_product_gallery_meta');







