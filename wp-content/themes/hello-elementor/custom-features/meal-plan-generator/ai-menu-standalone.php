<?php
/**
 * AI Menu Standalone Page
 * 獨立的 AI Menu 頁面，包含完整的 Header
 */

// 載入 WordPress
require_once( dirname(__FILE__) . '/../../../../wp-load.php' );

// 確保已登入（可選）
// if ( ! is_user_logged_in() ) {
//     wp_redirect( home_url() );
//     exit;
// }

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Menu - <?php bloginfo( 'name' ); ?></title>
    <?php wp_head(); ?>
    
    <!-- 手動載入 Meal Plan Generator 樣式 -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/custom-features/meal-plan-generator/assets/style.css?ver=<?php echo time(); ?>">
    <script src="<?php echo get_template_directory_uri(); ?>/custom-features/meal-plan-generator/assets/form.js?ver=<?php echo time(); ?>" defer></script>
    <script>
        // 設置全局配置
        var MPG_CFG = {
            root: '<?php echo esc_url_raw( rest_url('mpg/v1/') ); ?>',
            nonce: '<?php echo wp_create_nonce('wp_rest'); ?>'
        };
    </script>
</head>
<body <?php body_class(); ?>>
<?php 
// 這會自動載入你的 custom header
wp_body_open(); 
?>

<!-- 主內容區域 -->
<div id="page" class="site">
    <main id="content" class="site-main" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
        <?php
        // 載入 Meal Plan Generator 表單
        if ( class_exists( 'MealPlanGenerator' ) ) {
            $mpg = new MealPlanGenerator();
            echo $mpg->render_form();
        } else {
            echo '<p>Meal Plan Generator is not loaded. Please check your functions.php</p>';
        }
        ?>
    </main>
</div>

<?php wp_footer(); ?>
</body>
</html>

