<?php
/*This file is part of astra-child, astra child theme.

All functions of this file will be loaded before of parent theme functions.
Learn more at https://codex.wordpress.org/Child_Themes.

Note: this function loads the parent stylesheet before, then child theme stylesheet
(leave it in place unless you know what you are doing.)
*/

// Enable Users for Editor.
function isa_editor_manage_users() {
 
    if ( get_option( 'isa_add_cap_editor_once' ) != 'done' ) {
     
        // let editor manage users
 
        $edit_editor = get_role('editor'); // Get the user role
        $edit_editor->add_cap('edit_users');
        $edit_editor->add_cap('list_users');
        $edit_editor->add_cap('promote_users');
        $edit_editor->add_cap('create_users');
        $edit_editor->add_cap('add_users');
        $edit_editor->add_cap('delete_users');
 
        update_option( 'isa_add_cap_editor_once', 'done' );
    }
 
}
add_action( 'init', 'isa_editor_manage_users' );

// Hide Admin users for Editor.
function isa_pre_user_query($user_search) {
 
    $user = wp_get_current_user();
     
    if ( ! current_user_can( 'manage_options' ) ) {
   
        global $wpdb;
     
        $user_search->query_where = 
            str_replace('WHERE 1=1', 
            "WHERE 1=1 AND {$wpdb->users}.ID IN (
                 SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta 
                    WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
                    AND {$wpdb->usermeta}.meta_value NOT LIKE '%administrator%')", 
            $user_search->query_where
        );
 
    }
}
add_action('pre_user_query','isa_pre_user_query');

// Remove Administrator role from roles list
function hide_adminstrator_editable_roles( $roles ){
    if ( isset( $roles['administrator'] ) && !current_user_can('level_10') ){
        unset( $roles['administrator'] );
    }
    return $roles;
}
add_action( 'editable_roles' , 'hide_adminstrator_editable_roles' );

// Enqueue Font Awesome 5 in WordPress 
function tme_load_font_awesome() {
    // You can find the current URL for the latest version here: https://fontawesome.com/start
    wp_enqueue_style( 'font-awesome-free', '//use.fontawesome.com/releases/v5.6.3/css/all.css' );
}
add_action( 'wp_enqueue_scripts', 'tme_load_font_awesome' );

// Add custom user role
function add_user_role(){
    add_role(
        'guest_author',
        __( 'Guest', 'testdomain' ),
        array(
            'read'         => true,  // true allows this capability
            'edit_posts'   => true,
            'delete_posts' => false, // Use false to explicitly deny
        )
    );
}
add_action( 'init', 'add_user_role' );

if ( ! function_exists( 'suffice_child_enqueue_child_styles' ) ) {
	function astra_child_enqueue_child_styles() {
	    // loading parent style
	    wp_register_style(
	      'parente2-style',
	      get_template_directory_uri() . '/style.css'
	    );

	    wp_enqueue_style( 'parente2-style' );
	    // loading child style
	    wp_register_style(
	      'childe2-style',
	      get_stylesheet_directory_uri() . '/style.css'
	    );
	    wp_enqueue_style( 'childe2-style');
	    wp_enqueue_style( 'privacy-policy', get_stylesheet_directory_uri() . '/css/privacy-policy.css', [] );
	    wp_enqueue_style( 'articles', get_stylesheet_directory_uri() . '/css/articles.css', [] );
	    wp_enqueue_style( 'responsive', get_stylesheet_directory_uri() . '/css/responsive.css', [] );
	    wp_enqueue_style( 'general', get_stylesheet_directory_uri() . '/css/general.css', [] );
	    wp_enqueue_style( 'dev', get_stylesheet_directory_uri() . '/css/dev.css', [] );
        wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array(), '1.0.0', true );
        wp_enqueue_script('jquery');
	 }
}
add_action( 'wp_enqueue_scripts', 'astra_child_enqueue_child_styles' );


function custom_admincss() {
    wp_enqueue_style('admin_styles' , get_stylesheet_directory_uri().'/css/admin_style.css', []);
}

add_action('admin_enqueue_scripts', 'custom_admincss');

/*Write here your own functions */
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

add_action( 'init', 'register_custom_post_types' );

function register_custom_post_types() {
    
    // Articless Post Type
    $article_args = array(
        'labels' => array(
            'name' => 'Articles',
            'menu_name' => 'Articles',
            'singular_name' => 'Article'
        ),

        'public' => true,
        'has_archive' => true,
        'rewrite'  => array( 'slug' => 'article' ),
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', 'excerpt' , 'page-attributes' , 'comments')
    );

    register_post_type( 'article', $article_args );   
    
}

add_action( 'init', 'register_custom_taxonomies', 0 );

function register_custom_taxonomies() {
    
    // Add new taxonomy, make it hierarchical (like categories)
    $case_labels = array(
        'name'              => _x( 'Categories', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Category', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Search Category', 'textdomain' ),
        'all_items'         => __( 'All Category', 'textdomain' ),
        'parent_item'       => __( 'Parent Category', 'textdomain' ),
        'parent_item_colon' => __( 'Parent Category:', 'textdomain' ),
        'edit_item'         => __( 'Edit Category', 'textdomain' ),
        'update_item'       => __( 'Update Category', 'textdomain' ),
        'add_new_item'      => __( 'Add New Category', 'textdomain' ),
        'new_item_name'     => __( 'New Category Name', 'textdomain' ),
        'menu_name'         => __( 'Categories', 'textdomain' ),
    );
 
     $article_categories = array(
        'hierarchical'      => true,
        'labels'            => $article_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'articles-categories' ),
    );
 
    register_taxonomy( 'article_categories', array( 'article' ), $article_categories );
    
    
    register_taxonomy( 
    'custom-tag', //taxonomy 
    'article', //post-type
        array( 
            'hierarchical'  => false, 
            'label'         => __( 'Tags','taxonomy general name'), 
            'singular_name' => __( 'Tag', 'taxonomy general name' ), 
            'rewrite'       => true, 
            'query_var'     => true 
        )    
    );
    
}

/**
 * Shortcode
 */
function all_articles($atts, $content) {
    
    $args = shortcode_atts( array(
        'post' => '10',
        'filter' => 0
    ), $atts );
    
    $output = "";
    
    if($args["filter"] == 1){
        $filtered_category = $_GET["filter_c"];
        $filtered_author = (isset($_GET["filter_a"])) ? $_GET["filter_a"] : "";
        
        $display_admins = false;
        $order_by = 'nicename'; // 'nicename', 'email', 'url', 'registered', 'display_name', or 'post_count'
        $order = 'DESC';
        $role = 'author, editor'; // 'subscriber', 'contributor', 'editor', 'author' - leave blank for 'all'
        $avatar_size = 161;
        $hide_empty = false; // hides authors with zero posts
        $content = '';
        
        if(!empty($display_admins)) {
            //$blogusers = get_users('orderby='.$order_by.'&role='.$role);
            $blogusers = get_users( [ 'role__in' => [ 'editor', 'author' ], 'orderby' => $order ] );
        } else {
            $admins = get_users('role=administrator');
            $exclude = array();
            
            foreach($admins as $ad) {
              $exclude[] = $ad->ID;
            }
            $exclude = implode(',', $exclude);
            //$blogusers = get_users('exclude='.$exclude.'&orderby='.$order_by.'&order='.$order.'&role='.$role.'&meta_query[key]=order');
            $blogusers = get_users( [ 'role__in' => [ 'editor', 'author' ], 'orderby' => $order ] );
        }
        
        $authors = array();
        foreach ($blogusers as $bloguser) {
        $user = get_userdata($bloguser->ID);
        
        if(!empty($hide_empty)) {
          $numposts = count_user_posts($user->ID);
          if($numposts < 1) continue;
          }
          $authors[] = (array) $user;
        }
        
        $categories = get_categories( array(
            'orderby' => 'name',
            'taxonomy' => 'article_categories',
            'order'   => 'ASC'
        ) );
        
        $output .= '<div class="articles-filter">';
            $output .= '<div class="filter-by">Filter by: </div>';
            $output .= '<select id="filter_category">';
                $output .= '<option data-url="' . get_the_permalink() . '">Category</option>';
                foreach( $categories as $category ) {
                    $selected = ($filtered_category == $category->slug) ? "selected" : "";
                    $output .= '<option '. $selected .' data-url="' . get_the_permalink() . '?filter_c='. $category->slug .'">' . $category->name . '</option>';   
                }
            $output .= '</select>';
            $output .= '<select id="filter_authors">';
                $output .= '<option data-url="' . get_the_permalink() . '">Authors</option>';
                foreach($authors as $author) {
                  $first_name = get_the_author_meta( 'first_name', $author['ID'] );
                  $last_name = get_the_author_meta( 'last_name', $author['ID'] );
                  //$selected_author = ($filtered_category == $category->slug) ? "selected" : "";
                  $output .= '<option '. $selected .' data-url="' . get_the_permalink() . '?filter_a='. $first_name .' '. $last_name . '">'. ucfirst($first_name) .' '. ucfirst($last_name) . '</option>';
                }
            $output .= '</select>';
        $output .= '</div>';
    }
  
    $output .= '<div class="articles">';
      $output .= '<div class="list">';
      if(isset($filtered_category) && $filtered_category != 'all'){
          $loop = new WP_Query( array(
              'post_type' => 'article',
              'posts_per_page' => '-1',
              'author_name' => $filtered_author,
              'tax_query' => array(
                    array(
                        'taxonomy' => 'article_categories',
                        'field'    => 'slug',
                        'terms' => $filtered_category
                    )
                )
            )
          );
      }else{
          $loop = new WP_Query( array(
              'post_type' => 'article',
              'posts_per_page' => $args["post"],
              'paged' => get_query_var('paged') ? get_query_var('paged') : 1
            )
          );
      }
          
          $count = 1;
          while ( $loop->have_posts() ) : $loop->the_post(); 
              $image = get_the_post_thumbnail_url(get_the_ID(),'full'); 
              $permalink = get_permalink( $loop->ID );
              $title = get_the_title( $loop->ID );
              //$date = get_the_date('F d Y'); 
			  $date = get_the_date('F j, Y');  
              $excerpt = get_the_excerpt();  
              $counter = $count++;
              $get_author = get_the_author( $loop->ID );
              
              $terms = get_the_terms( $loop->ID , 'article_categories' );
              
              foreach ( $terms as $term ) {
                  if ($term->slug != 'featured-article') {
					  $category_link = get_category_link( $term->term_id );
                    $class = '<a href="'.esc_url( $category_link ).'"><span class="category__badge">'.$term->name.'</span></a>';
                  }
              }
              
              $output .= '<div class="article">';
                  $output .= '<div class="wrap">';
                      $output .= '<div class="article-card-image thumb-img" data-url="'.$permalink.'"><div class="img" style="background-image: url('.$image.');">';
                      $output .= '<p class="category"><span>'.$class.'</span></p>';
                      $output .= '</div></div>';
                      $output .= '<div class="infos">';
                          $output .= '<div class="infos-wrap">';
                              $output .= '<p class="date">'.$date.'</p>';
                              $output .= '<h2 class="title"><a href="'.$permalink.'">'.$title.'</a></h2>';
                              $output .= '<p class="excerpt">'.$excerpt.'</p>';
                          $output .= '</div>';
                      $output .= '</div>';
                  $output .= '</div>';
              $output .= '</div>';
          
      endwhile; 
        if(!isset($filtered_category)){
            $output .=  paginate_links( array(
                'base' => get_pagenum_link(1) . '%_%',
                'format' => 'page/%#%', //for replacing the page number
                'type' => 'list', //format of the returned value
                'total' => $loop->max_num_pages,
                'current' => max( 1, get_query_var('paged') ),
                'prev_text'    => __('< Previous'),
                'next_text'    => __('Next >'),
            ) );
        }
      wp_reset_postdata();
      $output .= '</div>';
  $output .= '</div>';
  
  return $output;
}

add_shortcode( 'all-articles' , 'all_articles' );

function show_recent(){
    
    $content = '';
    $content .='<div class="recent-post-container">';
        $content .='<h2>Recent Posts</h2>';
        $content .='<div class="recent-post-list">';
            $loop = new WP_Query( array(
                    'post_type' => 'article',
                    'posts_per_page' => 3
                  )
                );
             while ( $loop->have_posts() ) : $loop->the_post(); 
                $image = get_the_post_thumbnail_url(get_the_ID(),'full'); 
                $link = get_permalink( $loop->ID );
                $title = get_the_title( $loop->ID );
                $excerpt = get_the_excerpt( $loop->ID );
                $date = get_the_date('F j, Y');
                    $content .='<div class="recent-post-item">';
                        $content .='<div class="img-wrap"><a href="' . $link . '"><img src="'. $image .'"></a></div>';
                        $content .='<div class="recent-post-info">';
                            $content .='<h4><a href="' . $link . '" title="Look '. $title .'" >' .   $title .'</a></h4>';
                            $content .='<h6 class="sidebar-date">'. $date .'</h6>';
                            $content .='<p class="excerpt">'. $excerpt .'</p>';
                        $content .='</div>';
                    $content .='</div>';
            endwhile; wp_reset_query();
        $content .='</div>';
    $content .='</div>';
    return $content;
}
add_shortcode( 'show-recent' , 'show_recent' );

function related_posts() {
    
    $content = '';
    
    //Get array of terms
    $terms = get_the_terms( $post->ID , 'article_categories', 'string');
    //Pluck out the IDs to get an array of IDS
    $term_ids = wp_list_pluck($terms,'term_id');
    
    //Query posts with tax_query. Choose in 'IN' if want to query posts with any of the terms
    //Chose 'AND' if you want to query for posts with all terms
    $loop = new WP_Query( array(
      'post_type' => 'article',
      'tax_query' => array(
                    array(
                        'taxonomy' => 'article_categories',
                        'field' => 'id',
                        'terms' => $term_ids,
                        'operator'=> 'IN' //Or 'AND' or 'NOT IN'
                     )),
      'posts_per_page' => 2,
      'ignore_sticky_posts' => 1,
      'orderby' => 'rand',
      'post__not_in'=>array(get_the_ID())
    ) );
    
    if ( $loop->have_posts() ) {
        $content .='<div class="related-posts">';
            $content .='<div class="related-posts-wrap">';
                $content .='<h2 class="main-title">Related Articles</h2>';
                $content .='<div class="list">';
                     while ( $loop->have_posts() ) : $loop->the_post(); 
                        $image = get_the_post_thumbnail_url(get_the_ID(),'full'); 
                        $link = get_permalink( $loop->ID );
                        $title = get_the_title( $loop->ID );
                        $excerpt = get_the_excerpt( $loop->ID );
                        $date = get_the_date('F j, Y');
                            $content .='<div class="post-item">';
                                $content .='<div class="recent-post-info">';
                                    $content .='<h4 class="title"><a href="' . $link . '" title="Look '. $title .'" >' .   $title .'</a></h4>';
                                    $content .='<p class="excerpt">'. $excerpt .'</p>';
                                $content .='</div>';
                            $content .='</div>';
                    endwhile; wp_reset_query();
                $content .='</div>';
            $content .='</div>';
        $content .='</div>';
    }
    
    return $content;
}
add_shortcode( 'related-posts' , 'related_posts' );

function showPost_id($atts, $content){
    $args_atts = shortcode_atts( array(
        'category' => '',
        'author' => ''
    ), $atts );

    if(!empty($args_atts["category"])){
        $args = array(
            'post_type' => 'article',
            'post_per_page' => 1,
            'orderby' => 'post_date',
            'order' => 'DESC',
            'tax_query' => array(
                array (
                    'taxonomy' => 'article_categories',
                    'field' => 'slug',
                    'terms' => $args_atts['category'],
                )
            ),
        );
    }elseif(!empty($args_atts["author"])){
        $args = array(
            'post_type' => 'article',
            'post_per_page' => 1,
            'orderby' => 'post_date',
            'author' => $args_atts["author"],
            'order' => 'DESC',
            'tax_query' => array(
                array (
                    'taxonomy' => 'article_categories',
                    'field' => 'slug',
                    'terms' => 'featured-article',
                )
            ),
        );
    }

    $query = new WP_Query( $args );
        $count = 1;
        while ($query -> have_posts()) : $query -> the_post();
        
        if($count == 1){
            $id = get_the_ID();
        }
        $count++;
        
        endwhile;
        wp_reset_postdata();
    return $id;
}

add_shortcode( 'getpost-id' , 'showPost_id' );

function featured_article($atts, $content) {

    $args_atts = shortcode_atts( array(
        'category' => '',
        'author' => ''
    ), $atts );

    $content = '';

    if(!empty($args_atts["category"])){
        $args = array(
            'post_type' => 'article',
            'post_per_page' => 1,
            'orderby' => 'post_date',
            'order' => 'DESC',
            'tax_query' => array(
                array (
                    'taxonomy' => 'article_categories',
                    'field' => 'slug',
                    'terms' => $args_atts['category'],
                )
            ),
        );
    }elseif(!empty($args_atts["author"])){
        $args = array(
            'post_type' => 'article',
            'post_per_page' => 1,
            'orderby' => 'post_date',
            'author' => $args_atts["author"],
            'order' => 'DESC',
            'tax_query' => array(
                array (
                    'taxonomy' => 'article_categories',
                    'field' => 'slug',
                    'terms' => 'featured-article',
                )
            ),
        );
    }else{
        $args = array(
            'post_type' => 'article',
            'post_per_page' => 1,
            'orderby' => 'post_date',
            'order' => 'DESC',
            'tax_query' => array(
                array (
                    'taxonomy' => 'article_categories',
                    'field' => 'slug',
                    'terms' => 'featured-article',
                )
            ),
        );
    }

    $query = new WP_Query( $args );
        $count = 1;
        while ($query -> have_posts()) : $query -> the_post();
        
        if($count == 1){
            $image = get_the_post_thumbnail_url(get_the_ID(),'full'); 
            $link = get_permalink( $query->ID );
            $title = get_the_title( $query->ID );
            $excerpt = get_the_excerpt( $query->ID );
            $date = get_the_date('F j, Y');
            $author = get_the_author( $query->ID );
            $terms = get_the_terms( $query->ID , 'article_categories' );
                $class = [];
                foreach ( $terms as $term ) {
                    $category_slug[] = $term->slug;
                    if ($term->slug != 'featured-article') {
                            $category_link = get_category_link( $term->term_id );
                            $class[] = '<a href="'.esc_url( $category_link ).'"><span class="category__badge">'.$term->name.'</span></a>';
                    }
                }
                
                if (in_array("featured-article", $category_slug) && !empty($args_atts["category"])){
                    $content .='<div class="featured-article thumb-img" data-url="'.$link.'" style="background-image: url('. $image .');">';
                        $content .='<div class="wrap">';
                            $content .= '<p class="category"><span>'.implode(' ',$class).'</span></p>';
                            $content .= '<h2 class="title"><a href="'.$link.'">'.$title.'</a></h2>';
                            $content .= '<p class="excerpt">'.$excerpt.'</p>';
    //                         $content .= '<div class="meta"><span class="date">'.$date.'</span><span class="author">'.$author.'</span></div>';
                            $content .= '<div class="meta"><span class="date">'.$date.'</span></div>';
                        $content .='</div>';
                    $content .='</div>';
                }else{
                    $content .='<div class="featured-article thumb-img" data-url="'.$link.'" style="background-image: url('. $image .');">';
                        $content .='<div class="wrap">';
                            $content .= '<p class="category"><span>'.implode(' ',$class).'</span></p>';
                            $content .= '<h2 class="title"><a href="'.$link.'">'.$title.'</a></h2>';
                            $content .= '<p class="excerpt">'.$excerpt.'</p>';
    //                         $content .= '<div class="meta"><span class="date">'.$date.'</span><span class="author">'.$author.'</span></div>';
                            $content .= '<div class="meta"><span class="date">'.$date.'</span></div>';
                        $content .='</div>';
                    $content .='</div>';
                }
        }
        $count++;
        
        endwhile;
        wp_reset_postdata();
    return $content;
}
add_shortcode( 'featured-article' , 'featured_article' );

function shortcodeBlogAuthors()
{
    $display_admins = false;
    $order_by = 'meta_value_num'; // 'nicename', 'email', 'url', 'registered', 'display_name', or 'post_count'
    $order = 'DESC';
    $role = 'author, editor'; // 'subscriber', 'contributor', 'editor', 'author' - leave blank for 'all'
    $avatar_size = 161;
    $hide_empty = false; // hides authors with zero posts
    $content = '';
    
    if(!empty($display_admins)) {
        //$blogusers = get_users('orderby='.$order_by.'&role='.$role);
        $blogusers = get_users( [ 'role__in' => [ 'editor', 'author' ], 'orderby' => 'meta_value', 'meta_query' => 'order' ] );
    } else {
        $admins = get_users('role=administrator');
        $exclude = array();
        
        foreach($admins as $ad) {
          $exclude[] = $ad->ID;
        }
        $exclude = implode(',', $exclude);
        //$blogusers = get_users('exclude='.$exclude.'&orderby='.$order_by.'&order='.$order.'&role='.$role.'&meta_query[key]=order');
        $blogusers = get_users( [ 'role__in' => [ 'editor', 'author' ], 'orderby' => 'meta_value', 'exclude' => $exclude, 'meta_key' => 'order' ] );
    }
    
    $authors = array();
    foreach ($blogusers as $bloguser) {
    $user = get_userdata($bloguser->ID);
    
    if(!empty($hide_empty)) {
      $numposts = count_user_posts($user->ID);
      if($numposts < 1) continue;
      }
      $authors[] = (array) $user;
    }
    
    $content .= '<div class="authors-list">';
    foreach($authors as $author) {
      $first_name = get_the_author_meta( 'first_name', $author['ID'] );
      $last_name = get_the_author_meta( 'last_name', $author['ID'] );
      $avatar = get_field( 'user_profile_image', 'user_'.$author['ID'] );
      $author_profile_url = get_author_posts_url($author['ID']);
      $twitter = get_the_author_meta( 'twitter', $author['ID'] );
      $bio = get_the_author_meta( 'description', $author['ID'] );
      $author_role = get_field( 'title', 'user_'.$author['ID'] );
     
      $content .= '<div class="single-item-wrap">';
          $content .= '<div class="single-item-wrap_details">';
              $content .= '<div class="author-gravatar"><a href="javascript:void(0);" class="contributor-link toggle-info" data-id="'.$author['ID'].'"><img style="width: 100%; height: auto;" src="' . $avatar . '"></a></div>';
              $content .= '<div class="item-details_wrap">';
                  $content .= '<div class="author-name"><a href="javascript:void(0);" class="contributor-link toggle-info" data-id="'.$author['ID'].'"><span class="fname">' . $first_name . '</span> <span class="lname">' . $last_name . '</span></a><div class="author-role">'.$author_role.'</div></div>';
                  $content .= '<div class="author-twitter"><a href="https://twitter.com/'. $twitter .'" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg></a></div>';
              $content .= '</div>';
          $content .= '</div>';
      $content .= '</div>';
      $content .= '<div class="backdrop backdrop-'.$author['ID'].'"></div>
                    <div class="box box-'.$author['ID'].'">
                      <div class="close">x</div>
                      <p>'.$bio.'</p>
                    </div>';
      }
    $content .= '</div>';
    
    return $content;
}

add_shortcode( 'show-authors' , 'shortcodeBlogAuthors' );


function single_article_form($atts) {
    
    $campaign_form = shortcode_atts( array(
        'activecampaignform' => ''
    ), $atts );
    
    $content = '';    
        $content .='<div id="main-subscribe-form" class="form-container">';
            $content .='<div class="form-wrap">';
                $content .='<div class="inner-wrap">';
                    $content .='<h2 class="heading">Get Burnaby Beacon in your inbox.</h2>';
                    $content .='<h4 class="subheading">An in-depth understanding of the stories that affect Burnaby and beyond, every weekday.</h4>';
                    $content .='<div class="the-form">'. do_shortcode("[activecampaign form={$campaign_form['activecampaignform']} css=1]") .'</div>';
                $content .='</div>';
            $content .='</div>';
        $content .='</div>';
    return $content;
}
add_shortcode('single-article-form' , 'single_article_form');

function article_form() {
    $content = '';    
        $content .='<div class="article-form-container">';
            $content .='<div class="form-wrap">';
                $content .='<div class="inner-wrap">';
                    $content .='<h2 class="heading">Get Burnaby Beacon in your inbox.</h2>';
                    $content .='<h4 class="subheading">An in-depth understanding of the stories that affect Burnaby and beyond, every weekday.</h4>';
                    $content .='<div class="the-form">'. do_shortcode('[wpforms id="732"]') .'</div>';
                $content .='</div>';
            $content .='</div>';
        $content .='</div>';
    return $content;
}
add_shortcode('article-form' , 'article_form');


function top_stories() {
    $number_post = shortcode_atts( array(
        'post' => '2'
    ), $atts );
    
  $loop = new WP_Query( array(
      'post_type' => 'article',
      'post__not_in' => array( get_the_ID() ),
      'posts_per_page' => $number_post["post"]
    )
  );
    
  $output = "";
  
  if ( $loop->have_posts() ) {
      $output .= '<h2 class="heading">Latest Articles</h2>';
      $output .= '<div class="articles">';
          $output .= '<div class="list">';
              
              $count = 1;
              while ( $loop->have_posts() ) : $loop->the_post(); 
                  $image = get_the_post_thumbnail_url(get_the_ID(),'full'); 
                  $permalink = get_permalink( $loop->ID );
                  $title = get_the_title( $loop->ID );
                  $date = get_the_date('F j, Y');  
                  $excerpt = get_the_excerpt();  
                  $counter = $count++;
                  
                  $terms = get_the_terms( $loop->ID , 'article_categories' );
                  $class = [];
                  foreach ( $terms as $term ) {
                      if ($term->slug != 'featured-article') {
                        $category_link = get_category_link( $term->term_id );
                    $class[] = '<a href="'.esc_url( $category_link ).'"><span class="category__badge">'.$term->name.'</span></a>';
                      }
                  }
                  
                  $output .= '<div class="article">';
                      $output .= '<div class="wrap">';
                          $output .= '<div class="thumb-img" data-url="'.$permalink.'"><div class="img" style="background-image: url('.$image.');">';
                            $output .= '<p class="category"><span>'.implode(' ',$class).'</span></p>';
                          $output .= '</div></div>';
                          $output .= '<div class="infos">';
                              $output .= '<div class="infos-wrap">';
                                  $output .= '<p class="date">'.$date.'</p>';
                                  $output .= '<h2 class="title"><a href="'.$permalink.'">'.$title.'</a></h2>';
                                  $output .= '<p class="excerpt">'.$excerpt.'</p>';
                              $output .= '</div>';
                          $output .= '</div>';
                      $output .= '</div>';
                  $output .= '</div>';
              
          endwhile; wp_reset_postdata();
          $output .= '</div>';
      $output .= '</div>';
  }
  
  return $output;
}
add_shortcode( 'top-stories' , 'top_stories' );

function show_quotes() {
    $content = '';
        //variables
        $quotes = get_field('content');
        $caption = get_field('caption');
        $photoAuthor = get_field('photo_by');
        $img = get_field('quote_image');
        $imgurl = $img['url'];
        
        $content .='<div class="quotes-section">';
            $content .='<div class="wrap">';
                if( $img ) {
                    $content .='<div class="img-col" style="background-image: url('. $imgurl .');">';
                        $content .='<p class="text-col"><span class="quotes">'. $quotes .'</span></p>';
                    $content .='</div>';
                }
                if( $caption ) {
                    $content .='<p class="caption">'. $caption .'</p>';
                }
                if( $photoAuthor ) {
                    $content .='<p class="photoby">Photo: '. $photoAuthor .'</p>';
                }
            $content .='</div>';
        $content .='</div>';
    return $content;
} add_shortcode('show-quotes','show_quotes');

// Override Post count display in user
function users_events_column( $cols ) {
  unset($cols['posts']);
  $cols['user_events'] = 'Articles';
  return $cols;
}

/*
 * Print Event Column Value  
 */ 
function user_events_column_value( $value, $column_name, $id ) {
  if( $column_name == 'user_events' ) {
    global $wpdb;
    $count = (int) $wpdb->get_var( $wpdb->prepare(
      "SELECT COUNT(ID) FROM $wpdb->posts WHERE 
       post_type = 'article' AND post_status = 'publish' AND post_author = %d",
       $id
    ) );
    return $count;
  }
}

add_filter( 'manage_users_custom_column', 'user_events_column_value', 10, 3 );
add_filter( 'manage_users_columns', 'users_events_column' );

function my_acf_load_field( $field ) {
		
     $opinion ="<p><em>Any views or opinions represented in this article are those of the writer and do not represent those of Burnaby Beacon. If you're interested in submitting an opinion piece, reach out to us <a href='mailto:info@burnabybeacon.com'>here.</a></em></p>";
		
     $field['default_value'] = $opinion;
     $field['disabled'] = 1; 
	return $field;
}
add_filter('acf/load_field/name=copyright_text', 'my_acf_load_field');
//add_filter('acf/load_field/name=copyright_text_unauthorized', 'my_acf_load_field');




