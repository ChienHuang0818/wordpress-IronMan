<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.4.4' );
define( 'EHP_THEME_SLUG', 'hello-elementor' );

define( 'HELLO_THEME_PATH', get_template_directory() );
define( 'HELLO_THEME_URL', get_template_directory_uri() );
define( 'HELLO_THEME_ASSETS_PATH', HELLO_THEME_PATH . '/assets/' );
define( 'HELLO_THEME_ASSETS_URL', HELLO_THEME_URL . '/assets/' );
define( 'HELLO_THEME_SCRIPTS_PATH', HELLO_THEME_ASSETS_PATH . 'js/' );
define( 'HELLO_THEME_SCRIPTS_URL', HELLO_THEME_ASSETS_URL . 'js/' );
define( 'HELLO_THEME_STYLE_PATH', HELLO_THEME_ASSETS_PATH . 'css/' );
define( 'HELLO_THEME_STYLE_URL', HELLO_THEME_ASSETS_URL . 'css/' );
define( 'HELLO_THEME_IMAGES_PATH', HELLO_THEME_ASSETS_PATH . 'images/' );
define( 'HELLO_THEME_IMAGES_URL', HELLO_THEME_ASSETS_URL . 'images/' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
					'navigation-widgets',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			/*
			 * Editor Styles
			 */
			add_theme_support( 'editor-styles' );
			add_editor_style( 'editor-styles.css' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				HELLO_THEME_STYLE_URL . 'reset.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				HELLO_THEME_STYLE_URL . 'theme.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				HELLO_THEME_STYLE_URL . 'header-footer.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

// 添加自定義 Header JavaScript (手機版導航切換功能)
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_script(
		'custom-header-js',
		get_template_directory_uri() . '/custom-templates/header/custom-header.js',
		[], // 不依賴其他腳本
		HELLO_ELEMENTOR_VERSION,
		true // 在頁面底部載入
	);
}, 25);

// Header Template
add_action( 'wp_body_open', function() {
	// 從自定義模板目錄載入
	$header_template = get_template_directory() . '/custom-templates/header/custom-header.php';
	if ( file_exists( $header_template ) ) {
		include $header_template;
	}
}, 5);

// 載入 Program List 功能
$program_list_feature = get_template_directory() . '/custom-features/program-list/program-list-feature.php';
if ( file_exists( $program_list_feature ) ) {
	require_once $program_list_feature;
}

// 載入 Trainer List 功能
$trainer_list_feature = get_template_directory() . '/custom-features/trainer-list/trainer-list-feature.php';
if ( file_exists( $trainer_list_feature ) ) {
	require_once $trainer_list_feature;
}

// 載入 Meal Plan Generator 功能
$meal_plan_generator = get_template_directory() . '/custom-features/meal-plan-generator/meal-plan-generator.php';
if ( file_exists( $meal_plan_generator ) ) {
	require_once $meal_plan_generator;
}

// 自動創建 AI Menu 頁面（如果不存在）
function create_ai_menu_page() {
	// 檢查頁面是否已存在
	$page = get_page_by_path( 'ai-menu' );
	
	if ( ! $page ) {
		// 創建新頁面
		$page_data = array(
			'post_title'    => 'AI Menu',
			'post_content'  => '[meal_plan_form]',
			'post_status'   => 'publish',
			'post_type'     => 'page',
			'post_name'     => 'ai-menu',
			'post_author'   => 1,
			'comment_status' => 'closed',
			'ping_status'   => 'closed',
		);
		
		wp_insert_post( $page_data );
	}
}
// 在主題啟用時執行一次
add_action( 'after_setup_theme', 'create_ai_menu_page' );

// 自動創建 Register 頁面（如果不存在）
function create_register_page() {
	// 檢查頁面是否已存在
	$page = get_page_by_path( 'register' );
	
	if ( ! $page ) {
		// 創建新頁面
		$page_data = array(
			'post_title'    => 'Register',
			'post_content'  => '<!-- wp:template-part {"slug":"register"} /-->',
			'post_status'   => 'publish',
			'post_type'     => 'page',
			'post_name'     => 'register',
			'post_author'   => 1,
			'page_template' => 'register-template.php',
			'comment_status' => 'closed',
			'ping_status'   => 'closed',
		);
		
		wp_insert_post( $page_data );
	}
}
add_action( 'after_setup_theme', 'create_register_page' );

// 註冊頁面模板
function register_custom_page_templates( $templates ) {
	$templates['register-template.php'] = 'Register Template';
	return $templates;
}
add_filter( 'theme_page_templates', 'register_custom_page_templates' );

// 加載註冊頁面模板
function load_register_template( $template ) {
	if ( is_page_template( 'register-template.php' ) || is_page( 'register' ) ) {
		$new_template = get_template_directory() . '/custom-templates/register/template.php';
		if ( file_exists( $new_template ) ) {
			return $new_template;
		}
	}
	return $template;
}
add_filter( 'template_include', 'load_register_template', 99 );

// AJAX: 檢查用戶名是否可用
add_action( 'wp_ajax_check_username', 'check_username_availability' );
add_action( 'wp_ajax_nopriv_check_username', 'check_username_availability' );
function check_username_availability() {
	check_ajax_referer( 'custom_register_nonce', 'nonce' );
	
	$username = sanitize_user( $_POST['username'] );
	
	if ( username_exists( $username ) ) {
		wp_send_json_error( array(
			'message' => '此用户名已被使用'
		) );
	}
	
	wp_send_json_success( array(
		'message' => '用户名可用'
	) );
}

// AJAX: 檢查郵箱是否可用
add_action( 'wp_ajax_check_email', 'check_email_availability' );
add_action( 'wp_ajax_nopriv_check_email', 'check_email_availability' );
function check_email_availability() {
	check_ajax_referer( 'custom_register_nonce', 'nonce' );
	
	$email = sanitize_email( $_POST['email'] );
	
	if ( email_exists( $email ) ) {
		wp_send_json_error( array(
			'message' => '此邮箱已被注册'
		) );
	}
	
	wp_send_json_success( array(
		'message' => '邮箱可用'
	) );
}

// AJAX: 用戶註冊處理
add_action( 'wp_ajax_custom_register_user', 'handle_custom_registration' );
add_action( 'wp_ajax_nopriv_custom_register_user', 'handle_custom_registration' );
function handle_custom_registration() {
	check_ajax_referer( 'custom_register_nonce', 'nonce' );
	
	// 驗證並清理輸入數據
	$username  = sanitize_user( $_POST['username'] );
	$email     = sanitize_email( $_POST['email'] );
	$password  = $_POST['password']; // 不要清理密碼，保持原樣
	$first_name = sanitize_text_field( $_POST['first_name'] );
	$last_name  = sanitize_text_field( $_POST['last_name'] );
	$gender     = sanitize_text_field( $_POST['gender'] );
	$fitness_goal = sanitize_text_field( $_POST['fitness_goal'] );
	$subscribe  = intval( $_POST['subscribe_newsletter'] );
	
	// 驗證必填字段
	if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
		wp_send_json_error( array(
			'message' => '請填寫所有必填字段'
		) );
	}
	
	// 驗證用戶名格式
	if ( ! preg_match( '/^[a-zA-Z0-9_]+$/', $username ) ) {
		wp_send_json_error( array(
			'message' => '用戶名只能包含字母、數字和下劃線'
		) );
	}
	
	// 驗證用戶名長度
	if ( strlen( $username ) < 3 ) {
		wp_send_json_error( array(
			'message' => '用戶名至少需要 3 個字符'
		) );
	}
	
	// 驗證郵箱格式
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array(
			'message' => '請輸入有效的電子郵箱地址'
		) );
	}
	
	// 驗證密碼長度
	if ( strlen( $password ) < 8 ) {
		wp_send_json_error( array(
			'message' => '密碼至少需要 8 個字符'
		) );
	}
	
	// 檢查用戶名是否已存在
	if ( username_exists( $username ) ) {
		wp_send_json_error( array(
			'message' => '此用戶名已被使用'
		) );
	}
	
	// 檢查郵箱是否已被註冊
	if ( email_exists( $email ) ) {
		wp_send_json_error( array(
			'message' => '此郵箱已被註冊'
		) );
	}
	
	// 創建用戶
	$user_id = wp_create_user( $username, $password, $email );
	
	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array(
			'message' => '註冊失敗：' . $user_id->get_error_message()
		) );
	}
	
	// 更新用戶資料
	wp_update_user( array(
		'ID'         => $user_id,
		'first_name' => $first_name,
		'last_name'  => $last_name,
		'role'       => 'customer', // WooCommerce 客戶角色
	) );
	
	// 保存自定義字段
	if ( $gender ) {
		update_user_meta( $user_id, 'gender', $gender );
	}
	if ( $fitness_goal ) {
		update_user_meta( $user_id, 'fitness_goal', $fitness_goal );
	}
	if ( $subscribe ) {
		update_user_meta( $user_id, 'newsletter_subscription', 1 );
	}
	
	// 自動登入用戶
	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );
	
	// 發送歡迎郵件（可選）
	wp_new_user_notification( $user_id, null, 'user' );
	
	// 返回成功響應
	wp_send_json_success( array(
		'message' => '註冊成功！正在跳轉...',
		'redirect' => wc_get_page_permalink( 'myaccount' ) // 跳轉到我的帳戶頁面
	) );
}

// 添加您的 WooCommerce 自定义样式载入代码
add_action( 'wp_enqueue_scripts', function() {
	// 先確保主題樣式載入
	wp_enqueue_style( 'hello-style', get_stylesheet_uri(), [], null );
  
	// 再載入我們的 WooCommerce 客製樣式
	wp_enqueue_style(
	  'gym-woocommerce-style',
	  get_stylesheet_directory_uri() . '/css/woocommerce-custom.css/style.css',
	  [ 'hello-style' ],
	  wp_get_environment_type() === 'production' ? '1.0.0' : time()
	);
  }, 20);


require HELLO_THEME_PATH . '/theme.php';

HelloTheme\Theme::instance();