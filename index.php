<?php
/**
 * Plugin Name: Rest API
 * Description: Hello
 * Version: 1.0
 * Author: Rupom
 * Text Domain: rest
 */

//  request 
function rest_callback(){
    register_rest_route( 'my_plugin/v1', '/cars/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'rest_get_car'
    ) );
}
add_action( 'rest_api_init', 'rest_callback' );

function rest_get_car($request){
    // echo '<pre>';
    // print_r($request);
    // echo '</pre>';
    $car = rest_get_the_car( $request['id'] );
    return rest_ensure_response( $car );
}

function rest_get_the_car( $id ) {
    $cars = array(
        'Toyota',
        'Honda',
        'Tesla',
        'Ford',
    );

    $car = '';
    if ( isset( $cars[ $id ] ) ) {
        $car = $cars[ $id ];
    } else {
        return new WP_Error( 'rest_not_found', esc_html__( 'The car does not exist', 'rest' ), array( 'status' => 404 ) );
    }

    return $car;
}

function rest_get_endpoint_phrase() {
    return rest_ensure_response( 'Hello World, this is the WordPress REST API' );
}

function rest_register_example_callback() {
    register_rest_route( 'hello-world/v1', '/test', array(
        'methods'  => 'GET',
        'callback' => 'rest_get_endpoint_phrase',
    ) );
}

add_action( 'rest_api_init', 'rest_register_example_callback' );

// error handaling
function rest_register_broken_route() {
    register_rest_route( 'err/v1', '/broken', array(
        'methods' => 'GET',
        'callback' => 'rest_get_an_error',
    ) );
}

add_action( 'rest_api_init', 'rest_register_broken_route' );

function rest_get_an_error( $request ) {
    return new WP_Error( 'wrong', esc_html__( 'This endpoint is broken', 'rest' ), array( 'status' => 400 ) );
}

function rest_get_products( $request ) {
    $products = array(
        '1' => 'I am product 1',
        '2' => 'I am product 2',
        '3' => 'I am product 3',
    );

    return rest_ensure_response( $products );
}

function rest_get_product( $request ) {
    $products = array(
        '1' => 'I am product 1',
        '2' => 'I am product 2',
        '3' => 'I am product 3',
    );

    $id =  $request['id'];

    if ( isset( $products[ $id ] ) ) {
        $product = $products[ $id ];

        return rest_ensure_response( $product );
    } else {
        return new WP_Error( 'rest_product_invalid', esc_html__( 'The product does not exist.', 'rest' ), array( 'status' => 404 ) );
    }

    return new WP_Error( 'wrong', esc_html__( 'Something went wrong.', 'rest' ), array( 'status' => 500 ) );
}


function register_product_routes() {
    register_rest_route( 'my-shop/v1', '/products', array(
        'methods'  => 'GET',
        'callback' => 'rest_get_products',
    ) );
    register_rest_route( 'my-shop/v1', '/products/(?P<id>[\d]+)', array(
        'methods'  => 'GET',
        'callback' => 'rest_get_product',
    ) );
}

add_action( 'rest_api_init', 'register_product_routes' );

/// get page data

add_action('rest_api_init', function () {
    register_rest_route('p/v1', '/post/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_page_by_id',
    ));
});

function get_page_by_id($request) {
    $page_id = $request['id'];
    // $page_id = $request->get_param('id');
    $page = get_post($page_id);

    if (!$page) {
        return new WP_Error('page_not_found', 'Page not found', array('status' => 404));
    }

    $response = array(
        'id' => $page->ID,
        'title' => $page->post_title,
        'content' => $page->post_content,
        'post_status' => $page->post_status,
    );

    return rest_ensure_response($response);
}
