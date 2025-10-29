<?php
/**
 * Custom Header Template
 * 
 */
defined( 'ABSPATH' ) || exit;
?>

<header id="custom-site-header" class="custom-header">
  <div class="custom-header-container">
    <!-- Logo 區域 -->
    <div class="custom-header-logo">
      <a href="<?php echo esc_url( home_url( 'index.php/my-account/' ) ); ?>" class="custom-logo-link">
        <?php 
        // 優先使用自定義 Logo
        if ( has_custom_logo() ) {
          $custom_logo_id = get_theme_mod( 'custom_logo' );
          $logo_url = wp_get_attachment_image_src( $custom_logo_id , 'full' );
          if ( $logo_url ) {
            echo '<img src="' . esc_url( $logo_url[0] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="custom-logo-img">';
          }
        } else {
          // 使用默認 Logo
          $default_logo = get_template_directory_uri() . '/assets/images/ironman-logo.png';
          if ( file_exists( get_template_directory() . '/assets/images/ironman-logo.png' ) ) {
            echo '<img src="' . esc_url( $default_logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="custom-logo-img">';
          }
        }
        ?>
        <span class="site-title"><?php bloginfo( 'name' ); ?></span>
      </a>
    </div>

    <!-- 導航菜單區域 -->
    <nav class="custom-header-nav">
      <?php
      wp_nav_menu( array(
        'theme_location' => 'menu-1',
        'menu_class'     => 'custom-nav-menu',
        'container'      => false,
        'fallback_cb'    => false,
      ) );
      ?>
    </nav>

    <!-- 右側按鈕區域 -->
    <div class="custom-header-actions">
      <?php if ( is_user_logged_in() ) : ?>
        <!-- 已登入用戶 - 顯示三個獨立按鈕 -->
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'program-list' ) ) ); ?>" class="header-btn header-btn-program">
          <span class="header-btn-text">Program</span>
        </a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'trainer-list' ) ) ); ?>" class="header-btn header-btn-trainer">
          <span class="header-btn-text">Trainer</span>
        </a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'ai-menu' ) ) ); ?>" class="header-btn header-btn-ai">
          <span class="header-btn-text">AI Menu</span>
        </a>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="header-btn header-btn-ai">
          <span class="header-btn-icon">My Account</span>
        </a>
        <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="header-btn header-btn-logout">
          <span class="header-btn-text">Log Out</span>
        </a>
      <?php else : ?>
        <!-- 未登入用戶 -->
        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="header-btn header-btn-login">
          <span class="header-btn-text">Login</span>
        </a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'register' ) ) ); ?>" class="header-btn header-btn-register">
          <span class="header-btn-text">Register</span>
        </a>
      <?php endif; ?>
    </div>

    <!-- 手機版菜單按鈕 -->
    <button class="custom-header-toggle" aria-label="開啟選單">
      <span class="toggle-bar"></span>
      <span class="toggle-bar"></span>
      <span class="toggle-bar"></span>
    </button>
  </div>

  <!-- 手機版菜單 -->
  <div class="custom-header-mobile-menu">
    <?php
    wp_nav_menu( array(
      'theme_location' => 'menu-1',
      'menu_class'     => 'custom-mobile-nav-menu',
      'container'      => false,
      'fallback_cb'    => false,
    ) );
    ?>
    <div class="custom-mobile-actions">
      <?php if ( is_user_logged_in() ) : ?>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'program-list' ) ) ); ?>" class="mobile-btn mobile-btn-program">Program</a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'trainer-list' ) ) ); ?>" class="mobile-btn mobile-btn-trainer">Trainer</a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'ai-menu' ) ) ); ?>" class="mobile-btn mobile-btn-ai">AI Menu</a>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="mobile-btn mobile-btn-account">My Account</a>
        <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="mobile-btn mobile-btn-logout">Log Out</a>
      <?php else : ?>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="mobile-btn mobile-btn-login">Login</a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'register' ) ) ); ?>" class="mobile-btn mobile-btn-register">Register</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<script>
// 手機版菜單切換功能
document.addEventListener('DOMContentLoaded', function() {
  const toggleButton = document.querySelector('.custom-header-toggle');
  const mobileMenu = document.querySelector('.custom-header-mobile-menu');
  
  if (toggleButton && mobileMenu) {
    toggleButton.addEventListener('click', function() {
      // 切換按鈕的 active 狀態
      this.classList.toggle('active');
      
      // 切換手機菜單的 active 狀態
      mobileMenu.classList.toggle('active');
      
      // 更新 aria-label
      const isActive = this.classList.contains('active');
      this.setAttribute('aria-label', isActive ? '關閉選單' : '開啟選單');
      
      // 防止背景滾動（可選）
      if (isActive) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    });
    
    // 點擊菜單項後關閉菜單（可選）
    const mobileMenuLinks = mobileMenu.querySelectorAll('a');
    mobileMenuLinks.forEach(link => {
      link.addEventListener('click', function() {
        toggleButton.classList.remove('active');
        mobileMenu.classList.remove('active');
        document.body.style.overflow = '';
      });
    });
  }
  
  // 滾動時添加陰影效果
  const header = document.querySelector('.custom-header');
  if (header) {
    window.addEventListener('scroll', function() {
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  }
});
</script>

