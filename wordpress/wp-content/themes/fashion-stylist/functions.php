<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * After setup theme hook
 */
function fashion_stylist_theme_setup(){
    /*
     * Make chile theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    load_child_theme_textdomain( 'fashion-stylist', get_stylesheet_directory() . '/languages' );

    add_image_size( 'fashion-stylist-slider', 430, 600, true );
    add_image_size( 'fashion-stylist-blog-two', 410, 570, true );

}
add_action( 'after_setup_theme', 'fashion_stylist_theme_setup' );

if ( !function_exists( 'fashion_stylist_styles' ) ):
    function fashion_stylist_styles() {
    	$my_theme = wp_get_theme();
    	$version = $my_theme['Version'];
        
        if( blossom_fashion_is_woocommerce_activated() ){
            $dependencies = array( 'blossom-fashion-woocommerce', 'owl-carousel', 'animate', 'blossom-fashion-google-fonts' );  
        }else{
            $dependencies = array( 'owl-carousel', 'animate', 'blossom-fashion-google-fonts' );
        }

        wp_enqueue_style( 'fashion-stylist-parent-style', get_template_directory_uri() . '/style.css', $dependencies );
        
        wp_enqueue_script( 'fashion-stylist', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), $version, true );
        
        $array = array( 
            'rtl'       => is_rtl(),
        ); 
        wp_localize_script( 'fashion-stylist', 'fashion_stylist_data', $array );
    }
endif;
add_action( 'wp_enqueue_scripts', 'fashion_stylist_styles', 10 );

//Remove a function from the parent theme
function remove_parent_filters(){ //Have to do it after theme setup, because child theme functions are loaded first
    remove_action( 'customize_register', 'blossom_fashion_customizer_theme_info' );
    remove_action( 'customize_register', 'blossom_fashion_customize_register' );
    remove_action( 'customize_register', 'blossom_fashion_customize_register_appearances' );

}
add_action( 'init', 'remove_parent_filters' );

function blossom_fashion_body_classes( $classes ) {

    $blog_layout_option = get_theme_mod( 'blog_layout_option', 'home-two' );
    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }
    
    // Adds a class of custom-background-image to sites with a custom background image.
    if ( get_background_image() ) {
        $classes[] = 'custom-background-image custom-background';
    }
    
    // Adds a class of custom-background-color to sites with a custom background color.
    if ( get_background_color() != 'ffffff' ) {
        $classes[] = 'custom-background-color custom-background';
    }
    
    $classes[] = blossom_fashion_sidebar_layout();
    
    if( is_home() && $blog_layout_option == 'home-two' ){
        $classes[] = 'homepage-layout-two';
    }

    return $classes;
}

function blossom_fashion_post_classes( $classes ){
    global $wp_query;
    $blog_layout_option = get_theme_mod( 'blog_layout_option', 'home-two' );
    if( is_front_page() && is_home() && $wp_query->current_post == 0 && $blog_layout_option == 'home-one' ){
        $classes[] = 'first-post';
    }

    if( is_search() ){
        $classes[] = 'search-post';
    }
    
    return $classes;
}

function blossom_fashion_exclude_cat( $query ){
    $ed_banner      = get_theme_mod( 'ed_banner_section', 'slider_banner' );
    $slider_type    = get_theme_mod( 'slider_type', 'latest_posts' );
    $slider_cat     = get_theme_mod( 'slider_cat' );
    $posts_per_page = get_theme_mod( 'no_of_slides', 5 );
    
    if( ! is_admin() && $query->is_main_query() && $query->is_home() && $ed_banner == 'slider_banner' ){
        if( $slider_type === 'cat' && $slider_cat  ){            
            $query->set( 'category__not_in', array( $slider_cat ) );            
        }elseif( $slider_type == 'latest_posts' ){
            $args = array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => $posts_per_page,
                'ignore_sticky_posts' => true
            );
            $latest = get_posts( $args );
            $excludes = array();
            foreach( $latest as $l ){
                array_push( $excludes, $l->ID );
            }
            $query->set( 'post__not_in', $excludes );
        }  
    }      
}

function fashion_stylist_customizer_register( $wp_customize ) {

    $wp_customize->get_control( 'slider_animation' )->active_callback   = 'fashion_stylist_slider_active_cb';
    
    $wp_customize->add_section( 'theme_info', array(
        'title'       => __( 'Demo & Documentation' , 'fashion-stylist' ),
        'priority'    => 6,
    ) );
    
    /** Important Links */
    $wp_customize->add_setting( 'theme_info_theme',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    
    $theme_info = '<p>';
    $theme_info .= sprintf( __( 'Demo Link: %1$sClick here.%2$s', 'fashion-stylist' ),  '<a href="' . esc_url( 'https://blossomthemes.com/theme-demo/?theme=fashion-stylist' ) . '" target="_blank">', '</a>' );
    $theme_info .= '</p><p>';
    $theme_info .= sprintf( __( 'Documentation Link: %1$sClick here.%2$s', 'fashion-stylist' ),  '<a href="' . esc_url( 'https://docs.blossomthemes.com/docs/fashion-stylist/' ) . '" target="_blank">', '</a>' );
    $theme_info .= '</p>';

    $wp_customize->add_control( new Blossom_Fashion_Note_Control( $wp_customize,
        'theme_info_theme', 
            array(
                'section'     => 'theme_info',
                'description' => $theme_info
            )
        )
    );

    /** Add postMessage support for site title and description */
    $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
    $wp_customize->get_setting( 'background_color' )->transport = 'refresh';
    $wp_customize->get_setting( 'background_image' )->transport = 'refresh';
    
    if ( isset( $wp_customize->selective_refresh ) ) {
        $wp_customize->selective_refresh->add_partial( 'blogname', array(
            'selector'        => '.site-title a',
            'render_callback' => 'blossom_fashion_customize_partial_blogname',
        ) );
        $wp_customize->selective_refresh->add_partial( 'blogdescription', array(
            'selector'        => '.site-description',
            'render_callback' => 'blossom_fashion_customize_partial_blogdescription',
        ) );
    }
    
    /** Site Title Font */
    $wp_customize->add_setting( 
        'site_title_font', 
        array(
            'default' => array(                                         
                'font-family' => 'Abril Fatface',
                'variant'     => 'regular',
            ),
            'sanitize_callback' => array( 'Blossom_Fashion_Fonts', 'sanitize_typography' )
        ) 
    );

    $wp_customize->add_control( 
        new Blossom_Fashion_Typography_Control( 
            $wp_customize, 
            'site_title_font', 
            array(
                'label'       => __( 'Site Title Font', 'fashion-stylist' ),
                'description' => __( 'Site title and tagline font.', 'fashion-stylist' ),
                'section'     => 'title_tagline',
                'priority'    => 60, 
            ) 
        ) 
    );
    
    /** Site Title Font Size*/
    $wp_customize->add_setting( 
        'site_title_font_size', 
        array(
            'default'           => 120,
            'sanitize_callback' => 'blossom_fashion_sanitize_number_absint'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Fashion_Slider_Control( 
            $wp_customize,
            'site_title_font_size',
            array(
                'section'     => 'title_tagline',
                'label'       => __( 'Site Title Font Size', 'fashion-stylist' ),
                'description' => __( 'Change the font size of your site title.', 'fashion-stylist' ),
                'priority'    => 65,
                'choices'     => array(
                    'min'   => 10,
                    'max'   => 200,
                    'step'  => 1,
                )                 
            )
        )
    );
    
    /** Primary Color*/
    $wp_customize->add_setting( 
        'primary_color', array(
            'default'           => '#ea4e59',
            'sanitize_callback' => 'sanitize_hex_color'
        ) 
    );

    $wp_customize->add_control( 
        new WP_Customize_Color_Control( 
            $wp_customize, 
            'primary_color', 
            array(
                'label'       => __( 'Primary Color', 'fashion-stylist' ),
                'description' => __( 'Primary color of the theme.', 'fashion-stylist' ),
                'section'     => 'colors',
                'priority'    => 5,                
            )
        )
    );

    /** Layout Settings */
    $wp_customize->add_panel(
        'layout_settings',
        array(
            'title'    => __( 'Layout Settings', 'fashion-stylist' ),
            'priority' => 55,
        )
    );

    /** Blog Layout */
    $wp_customize->add_section(
        'header_layout',
        array(
            'title'    => __( 'Header Layout', 'fashion-stylist' ),
            'panel'    => 'layout_settings',
            'priority' => 10,
        )
    );
    
    /** Blog Page layout */
    $wp_customize->add_setting( 
        'header_layout_option', 
        array(
            'default'           => 'header-sec',
            'sanitize_callback' => 'esc_attr'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Fashion_Radio_Image_Control(
            $wp_customize,
            'header_layout_option',
            array(
                'section'     => 'header_layout',
                'label'       => __( 'Header Layout', 'fashion-stylist' ),
                'description' => __( 'This is the layout for header.', 'fashion-stylist' ),
                'choices'     => array(                 
                    'header-one'   => get_stylesheet_directory_uri() . '/images/header/header-one.png',
                    'header-sec'   => get_stylesheet_directory_uri() . '/images/header/header-two.png',
                )
            )
        )
    );
    
    /** Blog Layout */
    $wp_customize->add_section(
        'blog_layout',
        array(
            'title'    => __( 'Home Page Layout', 'fashion-stylist' ),
            'panel'    => 'layout_settings',
            'priority' => 10,
        )
    );
    
    /** Blog Page layout */
    $wp_customize->add_setting( 
        'blog_layout_option', 
        array(
            'default'           => 'home-two',
            'sanitize_callback' => 'esc_attr'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Fashion_Radio_Image_Control(
            $wp_customize,
            'blog_layout_option',
            array(
                'section'     => 'blog_layout',
                'label'       => __( 'Home Page Layout', 'fashion-stylist' ),
                'description' => __( 'This is the layout for blog index page.', 'fashion-stylist' ),
                'choices'     => array(                 
                    'home-one'   => get_stylesheet_directory_uri() . '/images/home/one-right.jpg',
                    'home-two'   => get_stylesheet_directory_uri() . '/images/home/two-right.jpg',
                )
            )
        )
    );

    /** No. of slides */
    $wp_customize->add_setting(
        'no_of_slides',
        array(
            'default'           => 5,
            'sanitize_callback' => 'blossom_fashion_sanitize_number_absint'
        )
    );
    
    $wp_customize->add_control(
        new Blossom_Fashion_Slider_Control( 
            $wp_customize,
            'no_of_slides',
            array(
                'section'     => 'header_image',
                'label'       => __( 'Number of Slides', 'fashion-stylist' ),
                'description' => __( 'Choose the number of slides you want to display', 'fashion-stylist' ),
                'choices'     => array(
                    'min'   => 1,
                    'max'   => 20,
                    'step'  => 1,
                ),
                'active_callback' => 'blossom_fashion_banner_ac'                 
            )
        )
    );

    /** Slider Layout Settings */
    $wp_customize->add_section(
        'slider_layout_settings',
        array(
            'title'    => __( 'Slider Layout', 'fashion-stylist' ),
            'priority' => 20,
            'panel'    => 'layout_settings',
        )
    );
    
    /** Page Sidebar layout */
    $wp_customize->add_setting( 
        'slider_layout', 
        array(
            'default'           => 'two',
            'sanitize_callback' => 'esc_attr'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Fashion_Radio_Image_Control(
            $wp_customize,
            'slider_layout',
            array(
                'section'     => 'slider_layout_settings',
                'label'       => __( 'Slider Layout', 'fashion-stylist' ),
                'description' => __( 'Choose the layout of the slider for your site.', 'fashion-stylist' ),
                'choices'     => array(
                    'one'   => get_stylesheet_directory_uri() . '/images/slider/one.jpg',
                    'two'   => get_stylesheet_directory_uri() . '/images/slider/two.jpg',
                )
            )
        )
    );
    
}
add_action( 'customize_register', 'fashion_stylist_customizer_register', 40 );

function blossom_fashion_header(){ 
    $ed_cart = get_theme_mod( 'ed_shopping_cart', true );
    $header_layout = get_theme_mod( 'header_layout_option', 'header-sec' ); ?>
    <header class="site-header <?php echo esc_attr( $header_layout ); ?>" itemscope itemtype="http://schema.org/WPHeader" itemscope itemtype="http://schema.org/WPHeader">
        <div class="header-holder">
            <div class="header-t">
                <div class="container">
                    <?php if( $header_layout == 'header-one' ) : ?>
                    <div class="row">
                        <div class="col">
                            <?php get_search_form(); ?>
                        </div>
                        <div class="col">
                    <?php endif; ?>
                            <div class="text-logo" itemscope itemtype="http://schema.org/Organization">
                            <?php
                                if( function_exists( 'has_custom_logo' ) && has_custom_logo() ){
                                    the_custom_logo();
                                }
                                
                                if( is_front_page() ){ ?>
                                    <h1 class="site-title" itemprop="name"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="url"><?php bloginfo( 'name' ); ?></a></h1>
                                    <?php 
                                }else{ ?>
                                    <p class="site-title" itemprop="name"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="url"><?php bloginfo( 'name' ); ?></a></p>
                                <?php 
                                } 
                             
                                $description = get_bloginfo( 'description', 'display' );
                                if ( $description || is_customize_preview() ){ ?>
                                    <p class="site-description"><?php echo esc_html( $description ); ?></p>
                                <?php
            
                                }
                            ?>
                            </div>
                        <?php if( $header_layout == 'header-one' ) : ?>
                            </div>
                            <div class="col">
                                <div class="tools">
                                    <?php 
                                    if( blossom_fashion_social_links( false ) || ( blossom_fashion_is_woocommerce_activated() && $ed_cart ) ){
                                        if( blossom_fashion_is_woocommerce_activated() && $ed_cart ) blossom_fashion_wc_cart_count();
                                        if( blossom_fashion_is_woocommerce_activated() && $ed_cart && blossom_fashion_social_links( false ) ) echo '<span class="separator"></span>';
                                        blossom_fashion_social_links();
                                    }                                    
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if( $header_layout == 'header-one' ) :
            $add_class = 'nav';
        else:
            echo '<div class="sticky-holder"></div>';
            $add_class = 'navigation';
        endif; ?>
        <div class="<?php echo esc_attr( $add_class ); ?>-holder">
            <div class="container">
                <div class="overlay"></div>
                <button aria-label="primary menu toggle" id="toggle-button" data-toggle-target=".main-menu-modal" data-toggle-body-class="showing-main-menu-modal" aria-expanded="false" data-set-focus=".close-main-nav-toggle">
                    <span></span><?php esc_html_e( 'Menu', 'fashion-stylist' ); ?>
                </button>
                <nav id="site-navigation" class="main-navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
                    <div class="primary-menu-list main-menu-modal cover-modal" data-modal-target-string=".main-menu-modal">
                        <button class="btn-close-menu close-main-nav-toggle" data-toggle-target=".main-menu-modal" data-toggle-body-class="showing-main-menu-modal" aria-expanded="false" data-set-focus=".main-menu-modal"><span></span></button>
                        <div class="mobile-menu" aria-label="<?php esc_attr_e( 'Mobile', 'fashion-stylist' ); ?>">
                			<?php
                				wp_nav_menu( array(
                					'theme_location' => 'primary',
                					'menu_id'        => 'primary-menu',
                                    'menu_class'     => 'main-menu-modal',
                                    'fallback_cb'    => 'blossom_fashion_primary_menu_fallback',
                				) );
                			?>
                        </div>
                    </div>
        		</nav><!-- #site-navigation -->
                <div class="tools">
                    <div class="form-section">
                        <button aria-label="search form toggle" id="btn-search" data-toggle-target=".search-modal" data-toggle-body-class="showing-search-modal" data-set-focus=".search-modal .search-field" aria-expanded="false"><i class="fa fa-search"></i></button>
                        <div class="form-holder search-modal cover-modal" data-modal-target-string=".search-modal">
                            <div class="header-search-inner-wrap">
                                <?php get_search_form(); ?>
                                <button class="btn-close-form" data-toggle-target=".search-modal" data-toggle-body-class="showing-search-modal" data-set-focus=".search-modal .search-field" aria-expanded="false">
                                    <span></span>
                                </button><!-- .search-toggle -->
                            </div>
                        </div>                    
                    </div>
                    <?php 
                    if( blossom_fashion_social_links( false ) || ( blossom_fashion_is_woocommerce_activated() && $ed_cart ) ){
                        if( blossom_fashion_is_woocommerce_activated() && $ed_cart ) blossom_fashion_wc_cart_count();
                        if( blossom_fashion_is_woocommerce_activated() && $ed_cart && blossom_fashion_social_links( false ) ) echo '<span class="separator"></span>';
                        blossom_fashion_social_links();
                    }
                    ?>                  
                </div>
            </div>
        </div>
    </header>
    <?php 
}

function blossom_fashion_banner(){
    $ed_banner      = get_theme_mod( 'ed_banner_section', 'slider_banner' );
    $slider_type    = get_theme_mod( 'slider_type', 'latest_posts' ); 
    $slider_cat     = get_theme_mod( 'slider_cat' );
    $posts_per_page = get_theme_mod( 'no_of_slides', 5 );
    $slider_layout  = get_theme_mod( 'slider_layout', 'two' );
    $image_size     = ( $slider_layout == 'two' ) ? 'fashion-stylist-slider' : 'blossom-fashion-slider';    
    
    if( is_front_page() || is_home() ){ 
        
        if( $ed_banner == 'static_banner' && has_custom_header() ){ ?>
            <div class="banner<?php if( has_header_video() ) echo esc_attr( ' video-banner' ); ?>">
                <?php the_custom_header_markup(); ?>
            </div>
            <?php
        }elseif( $ed_banner == 'slider_banner' ){
            $args = array(
                'post_type'           => 'post',
                'post_status'         => 'publish',            
                'ignore_sticky_posts' => true
            );
            
            if( $slider_type === 'cat' && $slider_cat ){
                $args['cat']            = $slider_cat; 
                $args['posts_per_page'] = -1;  
            }else{
                $args['posts_per_page'] = $posts_per_page;
            }
                
            $qry = new WP_Query( $args );
            
            if( $qry->have_posts() ){ ?>
            <div class="banner<?php echo ( $slider_layout == 'two' ) ? ' banner-layout-two' : ''; ?>">
                <div id="banner-slider<?php echo ( $slider_layout == 'two' ) ? '-two' : ''; ?>" class="owl-carousel">
                    <?php while( $qry->have_posts() ){ $qry->the_post(); ?>
                    <div class="item">
                        <?php 
                        if( has_post_thumbnail() ){
                            the_post_thumbnail( $image_size );    
                        }else{ 
                            blossom_fashion_get_fallback_svg( $image_size ); 
                        }
                        ?>                        
                        <div class="banner-text">
                            <div class="container">
                                <div class="text-holder">
                                    <?php
                                        blossom_fashion_category();
                                        the_title( '<h2 class="title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    
                </div>
            </div>
            <?php
            wp_reset_postdata();
            }
        } 
    }    
}

/**
 * Active Callback for banner section
*/
function fashion_stylist_slider_active_cb( $control ){
    
    $slider_layout  = get_theme_mod( 'slider_layout', 'two' );

    if( $slider_layout == 'one' ) return true;
    return false;
}

function blossom_fashion_post_thumbnail() {
    global $wp_query;
    $image_size     = 'thumbnail';
    $ed_featured    = get_theme_mod( 'ed_featured_image', true );
    $sidebar_layout = blossom_fashion_sidebar_layout();
    $home_layout    = get_theme_mod( 'blog_layout_option', 'home-two' );
    
    if( is_front_page() && is_home() ){
        echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
        if( has_post_thumbnail() ){
            if( $wp_query->current_post == 0 && $home_layout != 'home-two' ){                
                $image_size = ( $sidebar_layout == 'full-width' ) ? 'blossom-fashion-fullwidth' : 'blossom-fashion-with-sidebar';
            }elseif( $home_layout == 'home-two' ){
                $image_size = 'fashion-stylist-blog-two';
            }else{
                $image_size = 'blossom-fashion-blog-home';    
            }            
            the_post_thumbnail( $image_size );    
        }else{
            $image_size = ( $wp_query->current_post == 0 ) ? 'blossom-fashion-fullwidth' : 'blossom-fashion-blog-home';
            blossom_fashion_get_fallback_svg( $image_size );
        }        
        echo '</a>';
    }elseif( is_home() ){        
        echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
        if( has_post_thumbnail() ){                        
            the_post_thumbnail( 'blossom-fashion-blog-home' );    
        }else{ 
            blossom_fashion_get_fallback_svg( 'blossom-fashion-blog-home' );
        }        
        echo '</a>';
    }elseif( is_archive() || is_search() ){
        echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
        if( has_post_thumbnail() ){
            the_post_thumbnail( 'blossom-fashion-blog-archive' );    
        }else{ 
            blossom_fashion_get_fallback_svg( 'blossom-fashion-blog-archive' );
        }
        echo '</a>';
    }elseif( is_singular() ){
        echo '<div class="post-thumbnail">';
        $image_size = ( $sidebar_layout == 'full-width' ) ? 'blossom-fashion-fullwidth' : 'blossom-fashion-with-sidebar';
        if( is_single() ){
            if( $ed_featured ) the_post_thumbnail( $image_size );
        }else{
            the_post_thumbnail( $image_size );
        }
        echo '</div>';
    }
}

function blossom_fashion_category() {
    $ed_cat_single = get_theme_mod( 'ed_category', false );
    // Hide category and tag text for pages.
    if ( 'post' === get_post_type() && !$ed_cat_single ) {
        /* translators: used between list items, there is a space after the comma */
        $categories_list = get_the_category_list( ' ' );
        if ( $categories_list ) {
            echo '<span class="cat-links" itemprop="about">' . $categories_list . '</span>';
        }
    }       
}

function blossom_fashion_footer_bottom(){ ?>
    <div class="footer-b">
        <div class="container">
            <div class="site-info">            
            <?php
                blossom_fashion_get_footer_copyright();
                esc_html_e( ' Fashion Stylist | Developed By ', 'fashion-stylist' );                                
                echo '<a href="' . esc_url( 'https://blossomthemes.com/' ) .'" rel="nofollow" target="_blank">' . esc_html__( 'Blossom Themes', 'fashion-stylist' ) . '</a>.';                                
                printf( esc_html__( ' Powered by %s', 'fashion-stylist' ), '<a href="'. esc_url( __( 'https://wordpress.org/', 'fashion-stylist' ) ) .'" target="_blank">WordPress</a>.' );
                if ( function_exists( 'the_privacy_policy_link' ) ) {
                    the_privacy_policy_link();
                }
            ?>               
            </div>
        </div>
    </div>
    <?php
}

function blossom_fashion_dynamic_css(){
    
    $primary_font    = get_theme_mod( 'primary_font', 'Montserrat' );
    $primary_fonts   = blossom_fashion_get_fonts( $primary_font, 'regular' );
    $secondary_font  = get_theme_mod( 'secondary_font', 'Cormorant Garamond' );
    $secondary_fonts = blossom_fashion_get_fonts( $secondary_font, 'regular' );
    $font_size       = get_theme_mod( 'font_size', 16 );
    
    $site_title_font      = get_theme_mod( 'site_title_font', array( 'font-family'=>'Abril Fatface', 'variant'=>'regular' ) );
    $site_title_fonts     = blossom_fashion_get_fonts( $site_title_font['font-family'], $site_title_font['variant'] );
    $site_title_font_size = get_theme_mod( 'site_title_font_size', 120 );
    
    $primary_color = get_theme_mod( 'primary_color', '#ea4e59' );
    
    $rgb = blossom_fashion_hex2rgb( blossom_fashion_sanitize_hex_color( $primary_color ) );
     
    $custom_css = '';
    $custom_css .= '
     
    .content-newsletter .blossomthemes-email-newsletter-wrapper.bg-img:after,
    .widget_blossomthemes_email_newsletter_widget .blossomthemes-email-newsletter-wrapper:after{
        ' . 'background: rgba(' . $rgb[0] . ', ' . $rgb[1] . ', ' . $rgb[2] . ', 0.8);' . '
    }
    
    /*Typography*/

    body,
    button,
    input,
    select,
    optgroup,
    textarea{
        font-family : ' . wp_kses_post( $primary_fonts['font'] ) . ';
        font-size   : ' . absint( $font_size ) . 'px;        
    }
    
    .site-title{
        font-size   : ' . absint( $site_title_font_size ) . 'px;
        font-family : ' . wp_kses_post( $site_title_fonts['font'] ) . ';
        font-weight : ' . esc_html( $site_title_fonts['weight'] ) . ';
        font-style  : ' . esc_html( $site_title_fonts['style'] ) . ';
    }
    
    .main-navigation ul {
        font-family : ' . wp_kses_post( $primary_fonts['font'] ) . ';
    }
    
    /*Color Scheme*/
    a,
    .site-header .social-networks li a:hover,
    .site-title a:hover,
    .shop-section .shop-slider .item h3 a:hover,
    #primary .post .entry-header .entry-meta a:hover,
    #primary .post .entry-footer .social-networks li a:hover,
    .widget ul li a:hover,
    .widget_bttk_author_bio .author-bio-socicons ul li a:hover,
    .widget_bttk_popular_post ul li .entry-header .entry-title a:hover,
    .widget_bttk_pro_recent_post ul li .entry-header .entry-title a:hover,
    .widget_bttk_popular_post ul li .entry-header .entry-meta a:hover,
    .widget_bttk_pro_recent_post ul li .entry-header .entry-meta a:hover,
    .bottom-shop-section .bottom-shop-slider .item .product-category a:hover,
    .bottom-shop-section .bottom-shop-slider .item h3 a:hover,
    .instagram-section .header .title a:hover,
    .site-footer .widget ul li a:hover,
    .site-footer .widget_bttk_popular_post ul li .entry-header .entry-title a:hover,
    .site-footer .widget_bttk_pro_recent_post ul li .entry-header .entry-title a:hover,
    .single .single-header .site-title:hover,
    .single .single-header .right .social-share .social-networks li a:hover,
    .comments-area .comment-body .fn a:hover,
    .comments-area .comment-body .comment-metadata a:hover,
    .page-template-contact .contact-details .contact-info-holder .col .icon-holder,
    .page-template-contact .contact-details .contact-info-holder .col .text-holder h3 a:hover,
    .page-template-contact .contact-details .contact-info-holder .col .social-networks li a:hover,
    #secondary .widget_bttk_description_widget .social-profile li a:hover,
    #secondary .widget_bttk_contact_social_links .social-networks li a:hover,
    .site-footer .widget_bttk_contact_social_links .social-networks li a:hover,
    .site-footer .widget_bttk_description_widget .social-profile li a:hover,
    .portfolio-sorting .button:hover,
    .portfolio-sorting .button.is-checked,
    .entry-header .portfolio-cat a:hover,
    .single-blossom-portfolio .post-navigation .nav-previous a:hover,
    .single-blossom-portfolio .post-navigation .nav-next a:hover,
    #primary .post .entry-header .entry-title a:hover,
    .widget_bttk_posts_category_slider_widget .carousel-title .title a:hover{
        color: ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ';
    }

    .site-header .tools .cart .number,
    .shop-section .header .title:after,
    .header-two .header-t,
    .header-six .header-t,
    .header-eight .header-t,
    .shop-section .shop-slider .item .product-image .btn-add-to-cart:hover,
    .widget .widget-title:before,
    .widget .widget-title:after,
    .widget_calendar caption,
    .widget_bttk_popular_post .style-two li:after,
    .widget_bttk_popular_post .style-three li:after,
    .widget_bttk_pro_recent_post .style-two li:after,
    .widget_bttk_pro_recent_post .style-three li:after,
    .instagram-section .header .title:before,
    .instagram-section .header .title:after,
    #primary .post .entry-content .pull-left:after,
    #primary .page .entry-content .pull-left:after,
    #primary .post .entry-content .pull-right:after,
    #primary .page .entry-content .pull-right:after,
    .page-template-contact .contact-details .contact-info-holder h2:after,
    .widget_bttk_image_text_widget ul li .btn-readmore:hover,
    #secondary .widget_bttk_icon_text_widget .text-holder .btn-readmore:hover,
    #secondary .widget_blossomtheme_companion_cta_widget .btn-cta:hover,
    #secondary .widget_blossomtheme_featured_page_widget .text-holder .btn-readmore:hover,
    .widget_bttk_author_bio .text-holder .readmore:hover,
    .banner .text-holder .cat-links a:hover,
    #primary .post .entry-header .cat-links a:hover,
    .banner .text-holder .cat-links a:hover, #primary .post .entry-header .cat-links a:hover,
    .widget_bttk_popular_post .style-two li .entry-header .cat-links a:hover,
    .widget_bttk_pro_recent_post .style-two li .entry-header .cat-links a:hover,
    .widget_bttk_popular_post .style-three li .entry-header .cat-links a:hover,
    .widget_bttk_pro_recent_post .style-three li .entry-header .cat-links a:hover,
    .widget_bttk_posts_category_slider_widget .carousel-title .cat-links a:hover,
    .portfolio-item .portfolio-cat a:hover, .entry-header .portfolio-cat a:hover,
    .widget_bttk_posts_category_slider_widget .owl-theme .owl-nav [class*="owl-"]:hover,
    .widget_tag_cloud .tagcloud a:hover,
    .site-footer .widget_bttk_author_bio .text-holder .readmore:hover,
    .site-footer .widget_blossomtheme_companion_cta_widget .btn-cta:hover{
        background: ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ';
    }
    
    .banner .text-holder .cat-links a,
    #primary .post .entry-header .cat-links a,
    .widget_bttk_popular_post .style-two li .entry-header .cat-links a,
    .widget_bttk_pro_recent_post .style-two li .entry-header .cat-links a,
    .widget_bttk_popular_post .style-three li .entry-header .cat-links a,
    .widget_bttk_pro_recent_post .style-three li .entry-header .cat-links a,
    .page-header span,
    .page-template-contact .top-section .section-header span,
    .portfolio-item .portfolio-cat a,
    .entry-header .portfolio-cat a{
        border-bottom-color: ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ';
    }

    .banner .text-holder .title a,
    .header-four .main-navigation ul li a,
    .header-four .main-navigation ul ul li a,
    #primary .post .entry-header .entry-title a,
    .portfolio-item .portfolio-img-title a{
        background-image: linear-gradient(180deg, transparent 96%, ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ' 0);
    }

    .widget_bttk_social_links ul li a:hover{
        border-color: ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ';
    }

    button:hover,
    input[type="button"]:hover,
    input[type="reset"]:hover,
    input[type="submit"]:hover,
    .site-footer .widget_bttk_icon_text_widget .text-holder .btn-readmore:hover,
    .site-footer .widget_blossomtheme_featured_page_widget .text-holder .btn-readmore:hover{
        background: ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ';
        border-color: ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ';
    }

    #primary .post .btn-readmore:hover{
        background: ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ';
    }

    .banner .text-holder .cat-links a,
    #primary .post .entry-header .cat-links a,
    .widget_bttk_popular_post .style-two li .entry-header .cat-links a,
    .widget_bttk_pro_recent_post .style-two li .entry-header .cat-links a,
    .widget_bttk_popular_post .style-three li .entry-header .cat-links a,
    .widget_bttk_pro_recent_post .style-three li .entry-header .cat-links a,
    .page-header span,
    .page-template-contact .top-section .section-header span,
    .widget_bttk_posts_category_slider_widget .carousel-title .cat-links a,
    .portfolio-item .portfolio-cat a,
    .entry-header .portfolio-cat a, 
    .widget:not(.widget_bttk_author_bio) .widget-title:after, 
    .widget.widget_bttk_author_bio .widget-title::before,
    .widget.widget_bttk_author_bio .widget-title:after {
        ' . 'background-color: rgba(' . $rgb[0] . ', ' . $rgb[1] . ', ' . $rgb[2] . ', 0.3);' . '
    }

    .single-post-layout-two .post-header-holder .entry-header .cat-links a,
    .single #primary .post .entry-footer .tags a, #primary .page .entry-footer .tags a {
        ' . 'background: rgba(' . $rgb[0] . ', ' . $rgb[1] . ', ' . $rgb[2] . ', 0.3);' . '
    }

    @media only screen and (min-width: 1025px){
        .main-navigation ul li:after, 
        .header-sec .main-navigation ul li a:hover, 
        .header-sec .main-navigation ul li:hover > a, 
        .header-sec .main-navigation ul .current-menu-item > a, 
        .header-sec .main-navigation ul .current-menu-ancestor > a, 
        .header-sec .main-navigation ul .current_page_item > a, 
        .header-sec .main-navigation ul .current_page_ancestor > a{
            background: ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ';
        }
    }

    @media only screen and (max-width: 1024px){
        .main-navigation ul li a{
            background-image: linear-gradient(180deg, transparent 93%, ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ' 0);
        }
    }
    
    /*Typography*/
    .banner .text-holder .title,
    .top-section .newsletter .blossomthemes-email-newsletter-wrapper .text-holder h3,
    .shop-section .header .title,
    #primary .post .entry-header .entry-title,
    #primary .post .post-shope-holder .header .title,
    .widget_bttk_author_bio .title-holder,
    .widget_bttk_popular_post ul li .entry-header .entry-title,
    .widget_bttk_pro_recent_post ul li .entry-header .entry-title,
    .widget-area .widget_blossomthemes_email_newsletter_widget .text-holder h3,
    .bottom-shop-section .bottom-shop-slider .item h3,
    .page-title,
    #primary .post .entry-content blockquote,
    #primary .page .entry-content blockquote,
    #primary .post .entry-content .dropcap,
    #primary .page .entry-content .dropcap,
    #primary .post .entry-content .pull-left,
    #primary .page .entry-content .pull-left,
    #primary .post .entry-content .pull-right,
    #primary .page .entry-content .pull-right,
    .author-section .text-holder .title,
    .single .newsletter .blossomthemes-email-newsletter-wrapper .text-holder h3,
    .related-posts .title, .popular-posts .title,
    .comments-area .comments-title,
    .comments-area .comment-reply-title,
    .single .single-header .title-holder .post-title,
    .portfolio-text-holder .portfolio-img-title,
    .portfolio-holder .entry-header .entry-title,
    .related-portfolio-title{
        font-family: ' . wp_kses_post( $secondary_fonts['font'] ) . ';
    }';
    if( blossom_fashion_is_woocommerce_activated() ) {
        $custom_css .= '
        .woocommerce #secondary .widget_price_filter .ui-slider .ui-slider-range{
            background: ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ';
        }
        
        .woocommerce #secondary .widget .product_list_widget li .product-title:hover,
        .woocommerce #secondary .widget .product_list_widget li .product-title:focus,
        .woocommerce div.product .entry-summary .product_meta .posted_in a:hover,
        .woocommerce div.product .entry-summary .product_meta .posted_in a:focus,
        .woocommerce div.product .entry-summary .product_meta .tagged_as a:hover,
        .woocommerce div.product .entry-summary .product_meta .tagged_as a:focus{
            color: ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ';
        }
        
        .woocommerce-checkout .woocommerce .woocommerce-info,
        .woocommerce ul.products li.product .add_to_cart_button:hover,
        .woocommerce ul.products li.product .add_to_cart_button:focus,
        .woocommerce ul.products li.product .product_type_external:hover,
        .woocommerce ul.products li.product .product_type_external:focus,
        .woocommerce ul.products li.product .ajax_add_to_cart:hover,
        .woocommerce ul.products li.product .ajax_add_to_cart:focus,
        .woocommerce ul.products li.product .added_to_cart:hover,
        .woocommerce ul.products li.product .added_to_cart:focus,
        .woocommerce div.product form.cart .single_add_to_cart_button:hover,
        .woocommerce div.product form.cart .single_add_to_cart_button:focus,
        .woocommerce div.product .cart .single_add_to_cart_button.alt:hover,
        .woocommerce div.product .cart .single_add_to_cart_button.alt:focus,
        .woocommerce #secondary .widget_shopping_cart .buttons .button:hover,
        .woocommerce #secondary .widget_shopping_cart .buttons .button:focus,
        .woocommerce #secondary .widget_price_filter .price_slider_amount .button:hover,
        .woocommerce #secondary .widget_price_filter .price_slider_amount .button:focus,
        .woocommerce-cart #primary .page .entry-content table.shop_table td.actions .coupon input[type="submit"]:hover,
        .woocommerce-cart #primary .page .entry-content table.shop_table td.actions .coupon input[type="submit"]:focus,
        .woocommerce-cart #primary .page .entry-content .cart_totals .checkout-button:hover,
        .woocommerce-cart #primary .page .entry-content .cart_totals .checkout-button:focus{
            background: ' . blossom_fashion_sanitize_hex_color( $primary_color ) . ';
        }

        .woocommerce div.product .product_title,
        .woocommerce div.product .woocommerce-tabs .panel h2{
            font-family: ' . wp_kses_post( $secondary_fonts['font'] ) . ';
        }';    
    }
           
    wp_add_inline_style( 'blossom-fashion-style', $custom_css );
}
