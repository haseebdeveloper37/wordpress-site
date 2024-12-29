<?php
/**
 * JM-theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package JM-theme
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function jm_theme_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on JM-theme, use a find and replace
		* to change 'jm-theme' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'jm-theme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'jm-theme' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'jm_theme_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'jm_theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function jm_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'jm_theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'jm_theme_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function jm_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'jm-theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'jm-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'jm_theme_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function jm_theme_scripts() {
	wp_enqueue_style( 'jm-theme-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'jm-theme-style', 'rtl', 'replace' );

	wp_enqueue_script( 'jm-theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'jm_theme_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


function enqueue_ajax_posts_script() {
    wp_enqueue_script(
        'ajax-posts-script',
        get_template_directory_uri() . '/js/ajax-posts.js',
        array('jquery'),
        null,
        true
    );

    wp_localize_script('ajax-posts-script', 'ajax_posts_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_posts'),
        'initial_page' => 1, // Starting page for initial load
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_posts_script');

function load_more_posts() {
    check_ajax_referer('load_more_posts', 'security');

    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;

    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'paged' => $paged,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div><?php the_excerpt(); ?></div>
            </article>
            <?php
        }
    } else {
        echo '<p>No more posts to load.</p>';
    }

    wp_die();
}
add_action('wp_ajax_load_more_posts', 'load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'load_more_posts');


// Add theme settings menu
function custom_theme_settings_menu() {
    add_menu_page(
        'Theme Settings',               // Page title
        'Theme Settings',               // Menu title
        'manage_options',               // Capability
        'theme-settings',               // Menu slug
        'custom_theme_settings_page',   // Callback function
        'dashicons-admin-generic',      // Icon
        90                              // Position
    );
}
add_action('admin_menu', 'custom_theme_settings_menu');

// Display the settings page
function custom_theme_settings_page() {
    // Save settings if the form is submitted
    if (isset($_POST['save_theme_settings'])) {
        if (isset($_FILES['logo_image']) && $_FILES['logo_image']['size'] > 0) {
            // Handle logo upload
            $uploaded_file = wp_handle_upload($_FILES['logo_image'], ['test_form' => false]);
            if (isset($uploaded_file['url'])) {
                update_option('theme_logo', esc_url($uploaded_file['url']));
            }
        }
        // Save font color
        if (isset($_POST['font_color'])) {
            update_option('theme_font_color', sanitize_hex_color($_POST['font_color']));
        }
        echo '<div class="updated"><p>Settings saved successfully!</p></div>';
    }

    // Get current settings
    $theme_logo = get_option('theme_logo', '');
    $theme_font_color = get_option('theme_font_color', '#000000');
    ?>
    <div class="wrap">
        <h1>Theme Settings</h1>
        <form method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Logo Image</th>
                    <td>
                        <input type="file" name="logo_image" />
                        <?php if ($theme_logo): ?>
                            <div>
                                <img src="<?php echo esc_url($theme_logo); ?>" alt="Logo" style="max-width: 200px; height: auto;">
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Font Color</th>
                    <td>
                        <input type="color" name="font_color" value="<?php echo esc_attr($theme_font_color); ?>">
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" name="save_theme_settings" class="button button-primary">Save Changes</button>
            </p>
        </form>
    </div>
    <?php
}

// Enqueue admin styles for better presentation
function custom_admin_styles() {
    wp_enqueue_style('wp-color-picker');
}
add_action('admin_enqueue_scripts', 'custom_admin_styles');

// Add a meta box for the rating field
function add_page_rating_meta_box() {
    add_meta_box(
        'page_rating_meta_box',          // Meta box ID
        'Page Rating',                   // Meta box title
        'render_page_rating_meta_box',   // Callback function
        'page',                          // Post type
    );
}
add_action('add_meta_boxes', 'add_page_rating_meta_box');

// Render the meta box
function render_page_rating_meta_box($post) {
    // Retrieve the current rating value
    $page_rating = get_post_meta($post->ID, '_page_rating', true);

    // Security nonce field
    wp_nonce_field('save_page_rating', 'page_rating_nonce');

    // Meta box content
    ?>
    <label for="page_rating">Assign a Rating (1 to 5):</label>
    <select name="page_rating" id="page_rating">
        <option value="">Select Rating</option>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?php echo $i; ?>" <?php selected($page_rating, $i); ?>>
                <?php echo $i; ?>
            </option>
        <?php endfor; ?>
    </select>
    <?php
}

// Save the rating when the page is saved
function save_page_rating_meta($post_id) {
    // Verify nonce
    if (!isset($_POST['page_rating_nonce']) || !wp_verify_nonce($_POST['page_rating_nonce'], 'save_page_rating')) {
        return;
    }

    // Check autosave and permissions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the rating
    if (isset($_POST['page_rating'])) {
        $page_rating = sanitize_text_field($_POST['page_rating']);
        update_post_meta($post_id, '_page_rating', $page_rating);
    } else {
        delete_post_meta($post_id, '_page_rating');
    }
}
add_action('save_post', 'save_page_rating_meta');

