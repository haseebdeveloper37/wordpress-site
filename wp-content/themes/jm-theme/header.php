<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<?php 

$theme_font_color = get_option('theme_font_color', '#000000');

?>

<style>
    body {
        color: <?php echo esc_attr($theme_font_color); ?>;
    }
	h1,h2,h3,h4,h5,h6,p{
		color: <?php echo esc_attr($theme_font_color); ?>;
	}
	a{
		color: <?php echo esc_attr($theme_font_color); ?>;
	}
</style>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'jm-theme' ); ?></a>

	<header id="masthead" class="site-header">
    <div class="header-container">
        <div class="site-branding">
            <?php
            $theme_logo = get_option('theme_logo', ''); // Get the custom theme logo option
            $theme_font_color = get_option('theme_font_color', '#000000'); // Get the font color option
            
            if ( has_custom_logo() ) {
                // Display the custom logo if set
                the_custom_logo();
            } elseif ( $theme_logo ) {
                // Display the theme logo from settings if custom logo is not set
                echo '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
                echo '<img src="' . esc_url( $theme_logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="theme-logo">';
                echo '</a>';
            } else {
                // Default site title as fallback
                ?>
                <h1 class="site-title">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
                </h1>
                <?php
            }
            ?>
        </div><!-- .site-branding -->

        <nav id="site-navigation" class="main-navigation">
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                <?php esc_html_e( 'Primary Menu', 'jm-theme' ); ?>
            </button>
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'menu-1',
                    'menu_id'        => 'primary-menu',
                )
            );
            ?>
        </nav><!-- #site-navigation -->
    </div>
</header><!-- #masthead -->

