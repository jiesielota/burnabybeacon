<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<?php if ( astra_page_layout() == 'left-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

	<div id="primary" <?php astra_primary_class(); ?>>

		<?php astra_primary_content_top(); ?>

        <div class="featured-wrap">
            <?php
                $term = get_queried_object();

                $author_id = $term->ID;

                //echo do_shortcode("[featured-article author='".$author_id."']");
            ?>
        </div>
        <?php
        $fname = get_the_author_meta("user_firstname", $author_id);
        $lname = get_the_author_meta("user_lastname", $author_id);
        $description = get_the_author_meta("description", $author_id);
        $twitter = get_the_author_meta("twitter", $author_id);
        $email = get_the_author_meta("email", $author_id);
        $get_author_gravatar = get_field( 'user_profile_image', 'user_'.$author_id );
        ?>
        <section class="ast-author-box ast-archive-description">
            <div class="ast-author-avatar" style="margin: 0 auto !important; align-self: center;"><img src="<?=$get_author_gravatar;?>"/></div>
            <div class="ast-author-bio">
                <h1 class="page-title ast-archive-title" style="text-align: center; padding-top:1em;"><?=ucfirst($fname)." ".ucfirst($lname)?></h1>
                <p class="excerpt" style="padding-top: 2em;"><?=$description;?></p>
                <?php
                if(!empty($twitter)){
                ?>
                    <p class="social" style="padding-top: 2em; text-align:center;"><a href="https://twitter.com/<?=$twitter;?>">@<?=$twitter;?></a> | <a href="mailto:<?=$email;?>"><?=$email;?></a></p>
                <?php
                }else{
                ?>
                    <p class="social" style="padding-top: 2em;text-align:center;"><a href="mailto:<?=$email;?>"><?=$email;?></a></p>
                <?php
                }
                ?>
            </div>
        </section>

		<?php
            $post_id = do_shortcode("[getpost-id author='".$author_id."']");

            $loop = new WP_Query( array(
                'post_type' => 'article',
                'posts_per_page' => '10',
                'author' => $author_id,
                'post__not_in' => array($post_id),
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1
            )
            );

            $output .= '<div class="articles">';
                $output .= '<div class="list">';

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
                                $output .= '<div class="thumb-img img" data-url="'.$permalink.'" class="article-card-image"><div class="img" style="background-image: url('.$image.');">';
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

                    $output .=  paginate_links( array(
                        'base' => get_pagenum_link(1) . '%_%',
                        'format' => 'page/%#%', //for replacing the page number
                        'type' => 'list', //format of the returned value
                        'total' => $loop->max_num_pages,
                        'current' => max( 1, get_query_var('paged') ),
                        'prev_text'    => __('< Previous'),
                        'next_text'    => __('Next >'),
                    ) );

                    wp_reset_postdata();

                $output .= '</div>';
            $output .= '</div>';
            
            echo $output;
        ?>

		<?php astra_primary_content_bottom(); ?>

	</div><!-- #primary -->

<?php if ( astra_page_layout() == 'right-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

<?php get_footer(); ?>
