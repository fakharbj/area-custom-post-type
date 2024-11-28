<?php
/*
Plugin Name: Area Custom Post Type
Description: Area Pages Solution by fakhar
Version: 1.0
Author: Fakhar ul islam
Text Domain: area-custom-post-type
*/



// Prevent Direct Access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}



// Add Settings Link to Plugin List
function area_plugin_action_links( $links ) {
    $settings_link = '<a href="edit.php?post_type=area">Settings</a>';
    array_unshift( $links, $settings_link ); // Adds the settings link at the beginning
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'area_plugin_action_links' );

// Register Custom Post Type: Area
function register_area_post_type() {
    $labels = array(
        'name'                  => _x( 'Areas', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Area', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Areas', 'text_domain' ),
        'name_admin_bar'        => __( 'Area', 'text_domain' ),
        'archives'              => __( 'Area Archives', 'text_domain' ),
        'attributes'            => __( 'Area Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Area:', 'text_domain' ),
        'all_items'             => __( 'All Areas', 'text_domain' ),
        'add_new_item'          => __( 'Add New Area', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Area', 'text_domain' ),
        'edit_item'             => __( 'Edit Area', 'text_domain' ),
        'update_item'           => __( 'Update Area', 'text_domain' ),
        'view_item'             => __( 'View Area', 'text_domain' ),
        'view_items'            => __( 'View Areas', 'text_domain' ),
        'search_items'          => __( 'Search Area', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into area', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this area', 'text_domain' ),
        'items_list'            => __( 'Areas list', 'text_domain' ),
        'items_list_navigation' => __( 'Areas list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter areas list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Area', 'text_domain' ),
        'description'           => __( 'Custom Post Type for Areas', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        'taxonomies'            => array( 'category' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-location-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true, // Enables support for Gutenberg and Elementor
    );
    register_post_type( 'area', $args );
}
add_action( 'init', 'register_area_post_type', 0 );

// Add Meta Box for Custom Fields
function add_area_meta_box() {
    add_meta_box(
        'area_meta_box',           // Meta Box ID
        'Area Details',            // Title
        'render_area_meta_box',    // Callback
        'area',                    // Post Type
        'normal',                  // Context
        'high'                     // Priority
    );
}
add_action( 'add_meta_boxes', 'add_area_meta_box' );

// Render the Meta Box
function render_area_meta_box( $post ) {
    wp_nonce_field( 'save_area_meta_box_data', 'area_meta_box_nonce' );

    $text_editor_1 = get_post_meta( $post->ID, '_area_text_editor_1', true );
    $text_editor_2 = get_post_meta( $post->ID, '_area_text_editor_2', true );
    $enable_editor_2 = get_post_meta( $post->ID, '_enable_editor_2', true );

    ?>
    <p>
        <label for="area_text_editor_1">Text Editor 1 (Required):</label>
        <textarea id="area_text_editor_1" name="area_text_editor_1" class="widefat" rows="5"><?php echo esc_textarea( $text_editor_1 ); ?></textarea>
    </p>
    <p>
        <label>
            <input type="checkbox" id="enable_editor_2" name="enable_editor_2" value="1" <?php checked( $enable_editor_2, '1' ); ?> />
            Enable Text Editor 2
        </label>
    </p>
    <p>
        <textarea id="area_text_editor_2" name="area_text_editor_2" class="widefat" rows="5" <?php echo $enable_editor_2 ? '' : 'disabled'; ?>><?php echo esc_textarea( $text_editor_2 ); ?></textarea>
    </p>
    <script>
        document.getElementById('enable_editor_2').addEventListener('change', function() {
            document.getElementById('area_text_editor_2').disabled = !this.checked;
        });
    </script>
    <?php
}

// Save Meta Box Data
function save_area_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['area_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['area_meta_box_nonce'], 'save_area_meta_box_data' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['area_text_editor_1'] ) ) {
        update_post_meta( $post_id, '_area_text_editor_1', sanitize_textarea_field( $_POST['area_text_editor_1'] ) );
    }

    if ( isset( $_POST['enable_editor_2'] ) ) {
        update_post_meta( $post_id, '_enable_editor_2', '1' );
        if ( isset( $_POST['area_text_editor_2'] ) ) {
            update_post_meta( $post_id, '_area_text_editor_2', sanitize_textarea_field( $_POST['area_text_editor_2'] ) );
        }
    } else {
        delete_post_meta( $post_id, '_enable_editor_2' );
        delete_post_meta( $post_id, '_area_text_editor_2' );
    }
}
add_action( 'save_post', 'save_area_meta_box_data' );
