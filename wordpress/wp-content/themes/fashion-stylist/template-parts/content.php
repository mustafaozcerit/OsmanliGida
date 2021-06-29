<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Fashion_Stylist
 */

$home_layout    = get_theme_mod( 'blog_layout_option', 'home-two' ); 
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="https://schema.org/Blog">
    <?php 
        if( $home_layout == 'home-two' ) :
            echo '<div class="post-content">';
            blossom_fashion_post_thumbnail(); //Post Thumbnail
            echo '<div class="text-holder">';
        endif;
        
        blossom_fashion_entry_header(); //Entry Header
        if( $home_layout == 'home-one' ) blossom_fashion_post_thumbnail(); //Post Thumbnail
        
        blossom_fashion_entry_content(); //Entry Content
        blossom_fashion_entry_footer(); //Entry Footer
        
        if( $home_layout == 'home-two' ) echo '</div></div>';        
    ?>
</article><!-- #post-<?php the_ID(); ?> -->