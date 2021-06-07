// disable google fonts
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

// disable elementor title
function ele_disable_page_title( $return ) {
   return false;
}
add_filter( 'hello_elementor_page_title', 'ele_disable_page_title' );

// disable eicons
add_action( 'elementor/frontend/after_enqueue_styles', 'js_dequeue_eicons' );
function js_dequeue_eicons() {
  
  // Don't remove it in the backend
  if ( is_admin() || current_user_can( 'manage_options' ) ) {
        return;
  }
  wp_dequeue_style( 'elementor-icons' );
}

/*rmove query string */
function _remove_script_version( $src ){
	$parts = explode( '?ver', $src );
        return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

/* disable feeds and remove them from head */
function itsme_disable_feed() {
    wp_die(__('No feed available, please visit the <a href="' . esc_url(home_url('/')) . '">homepage</a>!'));
}

add_action('do_feed', 'itsme_disable_feed', 1);
add_action('do_feed_rdf', 'itsme_disable_feed', 1);
add_action('do_feed_rss', 'itsme_disable_feed', 1);
add_action('do_feed_rss2', 'itsme_disable_feed', 1);
add_action('do_feed_atom', 'itsme_disable_feed', 1);
add_action('do_feed_rss2_comments', 'itsme_disable_feed', 1);
add_action('do_feed_atom_comments', 'itsme_disable_feed', 1);
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );

/*RSD is a discovery service that helps discover Pingbacks and XML-RPC on WordPress blogs*/
remove_action( 'wp_head', 'rsd_link' );

/* remove version from head */
remove_action('wp_head', 'wp_generator');

// remove version from rss
add_filter('the_generator', '__return_empty_string');

// remove version from scripts and styles
function remove_version_scripts_styles ($src) {
  if (strpos($src, 'ver=')) {
    $src = remove_query_arg('ver', $src);
  }
  return $src;
}
add_filter('style_loader_src', 'remove_version_scripts_styles', 9999);
add_filter('script_loader_src', 'remove_version_scripts_styles', 9999);


if ( ! is_user_logged_in() ){ // otherwise your WP admin will not work correctly

	/* defer all js with some excludes*/
	add_filter( 'script_loader_tag', function ( $tag, $handle ) {

    	// deferring this may be dangerous because other inlined scripts my depend on
    	if ( strpos( $tag, 'jquery.js' ) ) return $tag; 

    	if ( strpos ( $tag, 'make speciic script sync') )
      		return str_replace( ' src', ' async="async" src', $tag );
   
   		// Exxlude oto include specific scripts by their handle
   		/* if ( 'excluded handle' !== $handle )
        	return $tag;*/
    
    	return str_replace( ' src', ' defer="defer" src', $tag );
	}, 10, 2 );

}

// Windows Live Writer
remove_action('wp_head', 'wlwmanifest_link');

// Remove short link
add_filter('after_setup_theme', 'remove_redundant_shortlink');
function remove_redundant_shortlink() {
  // remove HTML meta tag
  // <link rel='shortlink' rel="noopener" href='http://example.com/?p=25' />
  remove_action('wp_head', 'wp_shortlink_wp_head', 10);

  // remove HTTP header
  // Link: <https://example.com/?p=25>; rel=shortlink
  remove_action( 'template_redirect', 'wp_shortlink_header', 11);
}

// Disable self pingback
function no_self_ping( &$links ) {

$home = get_option( 'home' );

foreach ( $links as $l => $link )

  if ( 0 === strpos( $link, $home ) )
  
    unset($links[$l]);
}

add_action( 'pre_ping', 'no_self_ping' );

// remove rest API
add_action('after_setup_theme', function(){
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
});

// remove dashicons
function wpdocs_dequeue_dashicon() {

if (current_user_can( 'update_core' )) {
  return;
}

wp_deregister_style('dashicons');
}
add_action( 'wp_enqueue_scripts', 'wpdocs_dequeue_dashicon' );

// Adding DNS Prefetching 
// Already added with Swift
function stb_dns_prefetch() { 
    echo '<meta https-equiv="x-dns-prefetch-control" content="on">
    <link rel="dns-prefetch" href="//fonts.googleapis.com" /> 
    <link rel="dns-prefetch" href="//fonts.gstatic.com" /> 
    <link rel="dns-prefetch" href="//0.gravatar.com/" /> 
    <link rel="dns-prefetch" href="//2.gravatar.com/" /> 
    <link rel="dns-prefetch" href="//1.gravatar.com/" />
    <link rel="dns-prefetch" href="//maps.googleapis.com/" /> 
    <link rel="dns-prefetch" href="//maps.google.com/" /> 
    <link rel="dns-prefetch" href="//maps.gstatic.com/" />
    <link rel="dns-prefetch" href="//disqus.com/" />
    <link rel="dns-prefetch" href="//go.disqus.com/" /> 
    <link rel="dns-prefetch" href="//yourdomain.disqus.com/" />'; 
  }
  
  add_action('wp_head', 'stb_dns_prefetch', 0);
