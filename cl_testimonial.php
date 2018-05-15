<?php
/*
  Plugin Name: CL Testimonial
  Plugin URI: http://creepeslab.com
  description: A Client Feedback Display or client Comments with Rating Plugin. That means testimonial with rating plugin.
  Version: 1.0
  Author: K.H. Hirok
  Author URI: http://creepeslab.com
  License: GPL2
 */


/* -----------------------------Team Section---------------------------------------------------- */
//wp_register_style( 'style', '/assets/css/style.css' );

wp_register_style( 'style_css', plugins_url('assets/css/style.css', __FILE__) );
wp_enqueue_style( 'style_css' );

function cl_testimonial() {
    $args = array(
        'labels' => array(
            'name' => __('Testimonials'),
            'singular_name' => __('Testimonials'),
            'all_items' => __('All Testimonials'),
            'add_new_item' => __('Add New Testimonial'),
            'edit_item' => __('Edit Testimonial'),
            'view_item' => __('View Testimonial')
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'cl_testimonials'),
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'capability_type' => 'page',
        'supports' => array('title', 'editor', 'thumbnail'),
        'exclude_from_search' => true,
        'menu_position' => 80,
        'has_archive' => true,
        'menu_icon' => 'dashicons-format-status'
    );
    register_post_type('cl_testimonial', $args);
}

add_action('init', 'cl_testimonial');

function cl_add_meta_box() {
    add_meta_box('testimonial-details', 'Testimonial Details', 'cl_meta_box_cb', 'cl_testimonial', 'normal', 'default');
}

function cl_meta_box_cb($post) {
    $values = get_post_custom($post->ID);
    $client_reviews = isset($values['client_reviews']) ? esc_attr($values['client_reviews'][0]) : "";
    $company = isset($values['company']) ? esc_attr($values['company'][0]) : "";
    $company_url = isset($values['company_url']) ? esc_attr($values['company_url'][0]) : "";
    wp_nonce_field('testimonial_details_nonce_action', 'testimonial_details_nonce');
    $html = '';
    $html .= '<label>Client Review:</label>';
    $html .= '<input type="number" step="any" name="client_reviews" id="client_reviews" style="margin-top:15px; margin-left:9px; margin-bottom:10px;" value="' . $client_reviews . '" /><br/>';
    $html .= '<label>Company:</label>';
    $html .= '<input type="text" name="company" id="company" style="margin-top:15px; margin-left:9px; margin-bottom:10px;" value="' . $company . '" /><br/>';
    $html .= '<label>Company Url:</label>';
    $html .= '<input type="url" name="company_url" id="company_url" style="margin-top:15px; margin-left:9px; margin-bottom:10px;" value="' . $company_url . '" />';
    echo $html;
}

function cl_save_meta_box($post_id) {
    // Bail if we're doing an auto save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    // if our nonce isn't there, or we can't verify it, bail
    if (!isset($_POST['testimonial_details_nonce']) || !wp_verify_nonce($_POST['testimonial_details_nonce'], 'testimonial_details_nonce_action'))
        return;

    // if our current user can't edit this post, bail
    if (!current_user_can('edit_post'))
        return;

    if (isset($_POST['client_reviews']))
        update_post_meta($post_id, 'client_reviews', $_POST['client_reviews']);

    if (isset($_POST['company']))
        update_post_meta($post_id, 'company', $_POST['company']);
    
    if (isset($_POST['company_url']))
        update_post_meta($post_id, 'company_url', $_POST['company_url']);
}

add_action('add_meta_boxes', 'cl_add_meta_box');
add_action('save_post', 'cl_save_meta_box');

function cl_testimonial_fun() {
    ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <div id="testimonials" class="cl-container">
            <?php
            $args = array(
                'post_type' => 'cl_testimonial',
                #'posts_per_page' => 1,
                'orderby' => 'DESC'
            );
            $cl_testimonial = new WP_Query($args);
            // The Loop
            if ($cl_testimonial->have_posts()) {
                while ($cl_testimonial->have_posts()) {
                    ?>  
                    <div class="cl-grid">
                        <?php $cl_testimonial->the_post(); ?>
                        <?php the_post_thumbnail('full', array('class' => 'client-photo')); ?>
                        <h3 class="sltitle text-center"><?php echo get_the_title(); ?></h3>
                        <?php
                        $client_reviews = get_post_meta(get_the_ID(), 'client_reviews', true);
                        if (!empty($client_reviews)):
                            ?>
                            <div class="cl-rating">
                            <progress id="clrating" class="clrating progress-bar-warning" value="<?php echo $client_reviews; ?>" min="0" max="5">
                                
                    </progress>
      
                    <div class="star">
                        
                        <img class="rating-img" src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/img/rating-final.png'; ?>" alt=""/>
                    </div>
                </div>
                        <?php endif; ?>
                        <?php
                        $company = get_post_meta(get_the_ID(), 'company', true);
                        $company_url = get_post_meta(get_the_ID(), 'company_url', true);
                        if (!empty($company)):
                            ?>
                            <h4 class="company text-center">
                                Company: <a href="<?php echo $company_url; ?>" target="_blank"><?php echo $company; ?></a>
                            </h4>
                        <?php endif; ?>

                      <?php the_content(); ?>
                    </div>
                    <?php
                }
            } else {
                ?>
                <h3>No testimonials found</h3>
            <?php } ?>
            <?php wp_reset_postdata(); ?>

        <!-- If we need pagination -->
       

    </div>


    <?php
}

add_shortcode('cl_testimonial', 'cl_testimonial_fun');

function slider_testimonial_fun() {
    ?>
    
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <div id="clslider" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">

            <?php
            $args = array(
                'post_type' => 'cl_testimonial',
                #'posts_per_page' => 1,
                'orderby' => 'DESC'
            );
            $cl_testimonial = new WP_Query($args);
            $i = 1;
            if ($cl_testimonial->have_posts()) {
                while ($cl_testimonial->have_posts()) {
                    ?> 
                    <div class="item <?php if ($i == 1) echo 'active'; ?>">                        
                        <?php $cl_testimonial->the_post(); ?>
                        <?php the_post_thumbnail('thumbnail', array('class' => 'client-photo')); ?>
                        <h3 class="sltitle text-center"><?php echo get_the_title(); ?></h3>
                        <?php
                        $client_reviews = get_post_meta(get_the_ID(), 'client_reviews', true);
                        if (!empty($client_reviews)):
                            ?>
                        
                        <div class="cl-rating">
                            <progress id="clrating" class="clrating progress-bar-warning" value="<?php echo $client_reviews; ?>" min="0" max="5">
                                
                    </progress>
      
                    <div class="star">
                        
                        <img class="rating-img" src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/img/rating-final.png'; ?>" alt=""/>
                    </div>
                </div>

                        <?php endif; ?>
                        <?php
                        $company = get_post_meta(get_the_ID(), 'company', true);
                        $company_url = get_post_meta(get_the_ID(), 'company_url', true);
                        if (!empty($company)):
                            ?>
                            <h4 class="company text-center">
                                Company: <a href="<?php echo $company_url; ?>" target="_blank"><?php echo $company; ?></a>
                            </h4>
                        <?php endif; ?>

                        <div class="slcon  text-center"><?php the_content(); ?></div>
                        </div>
                  
                    
                    <?php
                    $i++;
                }
            } else {
                ?>
                <h3>No testimonials found</h3>
            <?php } ?>
            <?php wp_reset_postdata(); ?>


                    
        </div> 

        <a class="left carousel-control" href="#clslider" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#clslider" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>


    <?php
}

add_shortcode('cl_slider_testimonial', 'slider_testimonial_fun');
?>