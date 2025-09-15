<?php
/*
Plugin Name: Custom Article CPT & REST (Updated)
Description: Registers 'article' custom post type and exposes featured image, author and categories in REST API responses.
Version: 1.2
Author: Assessment Helper
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the 'article' custom post type.
 */
function ca_register_article_cpt() {
    $labels = array(
        'name' => __( 'Articles' ),
        'singular_name' => __( 'Article' ),
        'add_new_item' => __( 'Add New Article' ),
        'edit_item' => __( 'Edit Article' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_rest'       => true,
        'rest_base'          => 'articles',
        'has_archive'        => true,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'author', 'excerpt' ),
        'taxonomies'         => array( 'category' ),
        'capability_type'    => 'post',
        'rewrite'            => array( 'slug' => 'articles' ),
    );

    register_post_type( 'article', $args );
}
add_action( 'init', 'ca_register_article_cpt' );

/**
 * Register additional REST fields for posts and articles.
 */
function ca_register_rest_fields() {
    // Featured image URL
    register_rest_field(
        array( 'article', 'post' ),
        'featured_image_url',
        array(
            'get_callback' => 'ca_get_featured_image_url',
            'schema' => null,
        )
    );

    // Author details (id + name)
    register_rest_field(
        array( 'article', 'post' ),
        'author_details',
        array(
            'get_callback' => 'ca_get_author_details',
            'schema' => null,
        )
    );

    // Categories array (id + name + slug)
    register_rest_field(
        array( 'article', 'post' ),
        'category_terms',
        array(
            'get_callback' => 'ca_get_category_terms',
            'schema' => null,
        )
    );
}
add_action( 'rest_api_init', 'ca_register_rest_fields' );

/**
 * Callback for featured image URL.
 */
function ca_get_featured_image_url( $object, $field_name, $request ) {
    $post_id = $object['id'];
    $thumbnail_id = get_post_thumbnail_id( $post_id );
    if ( ! $thumbnail_id ) {
        return '';
    }
    $img = wp_get_attachment_image_src( $thumbnail_id, 'full' );
    if ( $img ) {
        return esc_url( $img[0] );
    }
    return '';
}

/**
 * Callback for author details.
 */
function ca_get_author_details( $object, $field_name, $request ) {
    $author_id = $object['author'];
    $user = get_userdata( $author_id );
    if ( ! $user ) {
        return null;
    }
    return array(
        'id' => $user->ID,
        'name' => $user->display_name,
        'url' => get_author_posts_url( $user->ID ),
    );
}

/**
 * Callback for category terms.
 */
function ca_get_category_terms( $object, $field_name, $request ) {
    $post_id = $object['id'];
    $terms = wp_get_post_terms( $post_id, 'category' );
    if ( is_wp_error( $terms ) ) {
        return array();
    }
    $cats = array();
    foreach ( $terms as $t ) {
        $cats[] = array(
            'id' => $t->term_id,
            'name' => $t->name,
            'slug' => $t->slug,
            'link' => get_term_link( $t ),
        );
    }
    return $cats;
}

/**
 * Activation hook - register CPT and flush rewrite rules.
 */
function ca_activate_plugin() {
    ca_register_article_cpt();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ca_activate_plugin' );

/**
 * Deactivation hook - flush rewrite rules.
 */
function ca_deactivate_plugin() {
    unregister_post_type( 'article' );
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ca_deactivate_plugin' );
