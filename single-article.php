<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); 

$get_author_id = get_the_author_meta('ID');
$user_meta = get_userdata($get_author_id);
$user_roles = $user_meta->roles[0];
?>
<div class="single-pagetitle" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>)">
    <div class="wrap">
	   <div class="category-wrap">
	        <?php $terms = get_the_terms( $post->ID , 'article_categories' ); 
            $total = count($terms);
            $i=0;
            foreach ( $terms as $term ) {
                if($term->slug != "featured-article"){
                    $i++;
                    $term_link = get_term_link( $term, 'article_categories' );
                    if( is_wp_error( $term_link ) )
                    continue;
                    //echo '<p class="category"><span><a class="" href="' . $term_link . '">' . $term->name . '</a></span></p>';
                    echo '<p class="category"><span><a class="" href="' . $term_link . '">' . $term->name . '</a></span></p>';
                    if ($i != $total) echo ' ';
                }
                
            } 
            ?>
	    </div>
    </div>
</div>

<h6 class="image-info">   
    <span class="wrap">
        <?php 
            //variables
            $illustrator = get_field('photo_illustration'); 
            $photoby = get_field('photoby'); 
        ?>
            <?php if (!empty($illustrator)) { ?>
                <span class="illustrator"><?= $illustrator; ?></span>
            <?php } ?>
            <?php if (!empty($photoby)) { ?>
                <span class="photoby">Photo: <?= $photoby; ?></span>
            <?php } ?>    
    </span>
</h6>

<div id="content-wrap">
<?php if ( astra_page_layout() == 'left-sidebar' ) : ?>

	<?php get_sidebar(); ?>
    
    <?php endif ?>
    
    	<div id="primary" <?php astra_primary_class(); ?>>
    		
    		<div class="content-header">
    		    <?php
                    global $wp;
    		        $date = get_the_date('F j, Y');
    		        $time = get_the_time('g:i a');
                    $author = get_the_author( $post->ID );
    		    ?>
    		    
    		    <h1 class="title"><?= get_the_title($post->ID); ?></h1>
    		    <p class="excerpt"><?= get_the_excerpt($post->ID); ?></p>
    		    <p class="custom-meta">By <span class="author"> 
                <?php
                if($user_roles != 'guest_author'){
                ?>
                    <a href="<?=esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );?>"><?= $author ?></a> 
                <?php 
                }else{
                ?>
                <?= $author ?>
                <?php
                }
                 ?>
                </span>|<span class="date"> <?= $date ?> </span>|<span class="time"><?= $time ?></span></p>
    		    <div class="buttons-wrap">
    		        <a class="default-btn green-btn" target="_blank" href="http://twitter.com/intent/tweet?text=Currently reading <?php the_title(); ?>&url=<?php the_permalink(); ?>"><i class="fab fa-twitter"></i>TWEET</a>
    		        <a class="default-btn blue-btn" target="_blank" href="https://www.facebook.com/sharer?u=<?php the_permalink();?>&<?php the_title(); ?>"><i class="fab fa-facebook-f"></i>SHARE</a>
					<a class="default-btn green-btn" target="_blank" href="https://reddit.com/submit?url=<?php the_permalink();?>&<?php the_title(); ?>"><i class="fab fa-reddit-alien"></i>POST</a>
    		        <input type="hidden" id="copy_input" value="<?=home_url( $wp->request );?>">
    		        <a class="default-btn grey-btn" id="copy_link" href="<?= get_the_permalink($post->ID); ?>"><i class="fas fa-link"></i>COPY</a>
    		    </div>
    		</div>
    		<div class="the-content">
    		    <?= the_content($post->ID); ?>
				<?= the_field("copyright_text"); ?>
    		</div>
    
    	</div><!-- #primary -->
    
    <?php if ( astra_page_layout() == 'right-sidebar' ) : ?>
    
    	<?php get_sidebar(); ?>
    <?php else : ?>
        <style>
        	.single-article .site-content #content-wrap #primary {
                padding-top: 80px;
            }
        	</style>
    <?php endif ?>
    	
</div>

<div class="author-info-container">
    <?php
        $get_author_gravatar = get_field( 'user_profile_image', 'user_'.$get_author_id );
        $bio_excerpt = get_field('biographical_info_excerpt', 'user_'.$get_author_id);
        $author = get_the_author( $post->ID );
        $twitter = get_the_author_meta( 'twitter', $get_author_id );
        $email = get_the_author_meta( 'email', $get_author_id );
    ?>
    <div class="wrap">
        <div class="author-info">
            <div class="author-img" style="background: url('<?=$get_author_gravatar;?>')"></div>
            <div class="author-bio">
                <?php
                if($user_roles != 'guest_author'){
                ?>
                    <h4 class="the-author"><a href="<?=esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );?>"><?= $author ?></a></h4>
                <?php
                }else{
                ?>
                    <h4 class="the-author"><?= $author ?></h4>
                <?php
                }
                ?>
                <p><?=$bio_excerpt;?></p>
            </div>
        </div>
        <div class="social-links">
            <?php
            if(!empty($twitter) && $user_roles != 'guest_author'){
            ?>
                <a class="link" href="https://twitter.com/<?=$twitter;?>" target="_blank"><img src="/wp-content/uploads/2021/07/twitter.png"></a>
            <?php
            }
            
            if(!empty($email) && $user_roles != 'guest_author'){
            ?>
			    <a class="link" href="mailto:<?=$email;?>"><img src="/wp-content/uploads/2021/07/email.png"></a>
            <?php
            }
            ?>
        </div>
    </div>
</div>
    
<!-- <?= do_shortcode('[single-article-form activecampaignform=31]'); ?> -->


<?php $tags = get_the_terms( $post->ID , 'custom-tag' );  if( $tags ){ ?>
    <div class="tags-container">
        <div class="wrap">
        <h2 class="heading">Tags in this Article</h2>
            <div class="tags-wrap">
    	        <?php  
                $total = count($tags);
                $i=0;
                foreach ( $tags as $tag ) {
                    $i++;
                    $tag_link = get_term_link( $tag, 'custom' );
                    if( is_wp_error( $tag_link ) )
                    continue;
                    echo '<a class="tag" href="JavaScript:void(0);" style="cursor: unset;">' . $tag->name . '</a>';
                    if ($i != $total) echo' ';
                } 
                ?>
    	    </div>
        </div>
    </div>
<?php } ?>


<div class="related-posts-container">
    <?php echo do_shortcode('[related-posts]'); ?>
</div>

<div class="top-stories-container">
    <div class="wrap">
        <?php echo do_shortcode('[top-stories]'); ?>
    </div>
</div>

<?php get_footer(); ?>
