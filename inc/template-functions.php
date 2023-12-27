<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package LBWD-2024
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function lbwd2024_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'lbwd2024_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function lbwd2024_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'lbwd2024_pingback_header' );

/**
 * Limit Post Revisions
 */
if (!defined('WP_POST_REVISIONS')) define('WP_POST_REVISIONS', 10);
if (!defined('WP_POST_REVISIONS')) define('WP_POST_REVISIONS', false);

/**
 * Gets Rid of Uncategorized Category
 */
 add_filter( 'get_terms', 'ts_get_subcategory_terms', 10, 3 );
 function ts_get_subcategory_terms( $terms, $taxonomies, $args ) {
 $new_terms = array();
 // if it is a product category and on the shop page
 if ( in_array( 'product_cat', $taxonomies ) && is_front_page()) {
 foreach( $terms as $key => $term ) {
 if ( !in_array( $term->slug, array('uncategorized') ) ) { //pass the slug name here
 $new_terms[] = $term;
 }}
 $terms = $new_terms;
 }
 return $terms;
 }

/**
* Add page slug to body class, love this - Credit: Starkers Wordpress Theme
 */

function add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}
add_filter('body_class', 'add_slug_to_body_class');

/**
 * Open all external links in new tab
 */

function lbwd2024_change_target($content){
	return preg_replace_callback('/<a[^>]+/', 'lbwd2024_target_callback', $content);
}

function lbwd2024_target_callback($matches){
	$link = $matches[0];
	$mu_url = get_bloginfo('url');

	if (strpos($link, 'target') === false){
    	$link = preg_replace("%(href=['|\"](?!(mailto:|$mu_url)).+['|\"])%i", 'target="_blank" $1', $link);
	}elseif (preg_match("%href=['|\"](?!(mailto:|$mu_url)).+['|\"]%i", $link)){
    	$link = preg_replace('/target=["|\'](?!_blank).+[\'|"]/i', 'target="_blank"', $link);
}
	return $link;
}
add_filter('the_content', 'lbwd2024_change_target');


/**
 * Add favicon to admin & login
 */

function add_site_favicon() {
	echo '<link rel="shortcut icon" href="' . get_stylesheet_directory_uri() . '/img/icons/favicon.ico" />';
}
  
add_action('login_head', 'add_site_favicon');
add_action('admin_head', 'add_site_favicon');

/**
 * Adds a Featured Image column to the admin post screens.
 */

// GET FEATURED IMAGE
function lbwd2024_get_admin_image($post_ID) {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
        return $post_thumbnail_img[0];
    }
}
// ADD NEW COLUMN
function lbwd2024_columns_head($defaults) {
    $defaults['featured_image'] = 'Featured Image';
    return $defaults;
}
 
// SHOW THE FEATURED IMAGE
function lbwd2024_columns_content($column_name, $post_ID) {
    if ($column_name == 'featured_image') {
        $post_featured_image = lbwd2024_get_admin_image($post_ID);
        if ($post_featured_image) {
            echo '<img style="width:50px;height:50px;" src="' . $post_featured_image . '" />';
        }
    }
}
add_filter('manage_posts_columns', 'lbwd2024_columns_head');
add_action('manage_posts_custom_column', 'lbwd2024_columns_content', 10, 2);

/**
 * Adds the option for columns back to the Screen Options.
 */

function lbwd2024_dashboard_columns() {
    add_screen_option(
        'layout_columns',
        array(
            'max'     => 4,
            'default' => 2
        )
    );
}
add_action( 'admin_head-index.php', 'lbwd2024_dashboard_columns' );

/**
 * Custom View Article link to Post
 */

function lbwd2024_view_article($more)
{
    global $post;
    return '... <a class="read-more" href="' . get_permalink($post->ID) . '">' . __('Read More', 'lbwd2024') . '</a>';
}
add_filter('excerpt_more', 'lbwd2024_view_article'); // Add 'View Article' button instead of [...] for Excerpts



/**
 * Change the Excerpt length
 */

add_filter(
    'excerpt_length',
    function ( $length ) {
        // Number of words to display in the excerpt.
        return 150;
    },
    500
);



/**
 * Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
 */

function lbwd2024_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}
add_action('init', 'lbwd2024_pagination'); // Add our default Pagination