<?php
/**
 * Single Trainer Template
 * 教练详情页模板
 * 
 * @package HelloElementor
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 加载样式和脚本
function single_trainer_enqueue_assets() {
    wp_enqueue_style( 
        'single-trainer-style', 
        get_template_directory_uri() . '/custom-templates/single-trainer/style.css',
        array(),
        '1.0.0'
    );
    
    wp_enqueue_script( 
        'single-trainer-script', 
        get_template_directory_uri() . '/custom-templates/single-trainer/script.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'single_trainer_enqueue_assets' );

get_header();
?>

<div class="single-trainer-page">
    <?php while ( have_posts() ) : the_post(); 
        // 获取教练的自定义字段
        $experience = get_post_meta( get_the_ID(), '_trainer_experience', true );
        $certification = get_post_meta( get_the_ID(), '_trainer_certification', true );
        $phone = get_post_meta( get_the_ID(), '_trainer_phone', true );
        $email = get_post_meta( get_the_ID(), '_trainer_email', true );
        $facebook = get_post_meta( get_the_ID(), '_trainer_facebook', true );
        $instagram = get_post_meta( get_the_ID(), '_trainer_instagram', true );
        $linkedin = get_post_meta( get_the_ID(), '_trainer_linkedin', true );
        $custom_image_id = get_post_meta( get_the_ID(), '_trainer_custom_image', true );
        
        // 获取教练分类
        $categories = get_the_terms( get_the_ID(), 'trainer_category' );
        $category_names = array();
        if ( $categories && ! is_wp_error( $categories ) ) {
            $category_names = wp_list_pluck( $categories, 'name' );
        }
    ?>
    
    <!-- Hero Section -->
    <section class="trainer-hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="container">
                <div class="hero-inner">
                    <div class="hero-text">
                        <?php if ( ! empty( $category_names ) ) : ?>
                            <div class="trainer-categories">
                                <?php foreach ( $category_names as $cat_name ) : ?>
                                    <span class="category-tag"><?php echo esc_html( $cat_name ); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <h1 class="trainer-title"><?php the_title(); ?></h1>
                        
                        <?php if ( $experience ) : ?>
                            <p class="trainer-subtitle">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M10 2L12.5 7.5L18 8L14 12L15 18L10 15L5 18L6 12L2 8L7.5 7.5L10 2Z" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                <?php echo esc_html( $experience ); ?> 年专业经验
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="hero-image">
                        <?php
                        if ( $custom_image_id ) {
                            $image_url = wp_get_attachment_image_url( $custom_image_id, 'large' );
                            if ( $image_url ) {
                                echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title() ) . '" />';
                            }
                        } elseif ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'large' );
                        } else {
                            ?>
                            <div class="trainer-placeholder-image">
                                <span class="placeholder-icon">👤</span>
                                <span class="placeholder-text">教练照片</span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 向下滚动提示 -->
        <div class="scroll-indicator">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M12 5V19M12 19L5 12M12 19L19 12" stroke="currentColor" stroke-width="2"/>
            </svg>
        </div>
    </section>

    <!-- 主要内容区域 -->
    <div class="trainer-main-content">
        <div class="container">
            <div class="content-wrapper">
                <!-- 左侧内容 -->
                <div class="content-area">
                    <!-- 快速信息卡片 -->
                    <div class="info-cards">
                        <?php if ( $experience ) : ?>
                            <div class="info-card">
                                <div class="card-icon">⏱️</div>
                                <div class="card-content">
                                    <div class="card-label">执教经验</div>
                                    <div class="card-value"><?php echo esc_html( $experience ); ?> 年</div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( $certification ) : ?>
                            <div class="info-card">
                                <div class="card-icon">🏆</div>
                                <div class="card-content">
                                    <div class="card-label">专业认证</div>
                                    <div class="card-value"><?php echo esc_html( $certification ); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( $phone ) : ?>
                            <div class="info-card">
                                <div class="card-icon">📞</div>
                                <div class="card-content">
                                    <div class="card-label">联系电话</div>
                                    <div class="card-value">
                                        <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( $email ) : ?>
                            <div class="info-card">
                                <div class="card-icon">✉️</div>
                                <div class="card-content">
                                    <div class="card-label">电子邮箱</div>
                                    <div class="card-value">
                                        <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 主要描述内容 -->
                    <article class="trainer-description">
                        <h2 class="section-title">
                            <span class="title-icon">📋</span>
                            关于教练
                        </h2>
                        <div class="description-content">
                            <?php 
                            $content = get_the_content();
                            if ( ! empty( $content ) ) {
                                the_content();
                                
                                wp_link_pages( array(
                                    'before' => '<div class="page-links">' . __( 'Pages:', 'hello-elementor' ),
                                    'after'  => '</div>',
                                ) );
                            } else {
                                ?>
                                <div class="no-content-message">
                                    <h3>👋 教练简介</h3>
                                    <p>这位教练拥有丰富的执教经验，致力于帮助学员达到健身目标。</p>
                                    
                                    <h4>教学特色：</h4>
                                    <ul>
                                        <li>✅ 个性化训练计划定制</li>
                                        <li>✅ 专业的动作指导和纠正</li>
                                        <li>✅ 科学的训练方法和体系</li>
                                        <li>✅ 耐心细致的教学态度</li>
                                        <li>✅ 持续的进度跟踪和反馈</li>
                                    </ul>
                                    
                                    <?php if ( $experience ) : ?>
                                        <h4>专业背景：</h4>
                                        <p>拥有 <?php echo esc_html( $experience ); ?> 年的专业执教经验，帮助数百位学员成功达成健身目标。</p>
                                    <?php endif; ?>
                                    
                                    <?php if ( $certification ) : ?>
                                        <h4>资质认证：</h4>
                                        <p><?php echo esc_html( $certification ); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="content-tip">
                                        <strong>💡 提示：</strong> 管理员可以在 WordPress 后台编辑此教练信息，在编辑器中添加详细的教练介绍、教学理念、成功案例等内容。
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </article>

                    <!-- 相关教练推荐 -->
                    <?php
                    $related_args = array(
                        'post_type' => 'trainer',
                        'posts_per_page' => 3,
                        'post__not_in' => array( get_the_ID() ),
                        'orderby' => 'rand',
                    );
                    
                    if ( $categories && ! is_wp_error( $categories ) ) {
                        $category_ids = wp_list_pluck( $categories, 'term_id' );
                        $related_args['tax_query'] = array(
                            array(
                                'taxonomy' => 'trainer_category',
                                'field' => 'term_id',
                                'terms' => $category_ids,
                            ),
                        );
                    }
                    
                    $related_trainers = new WP_Query( $related_args );
                    
                    if ( $related_trainers->have_posts() ) :
                    ?>
                        <section class="related-trainers">
                            <h2 class="section-title">
                                <span class="title-icon">👥</span>
                                其他教练
                            </h2>
                            <div class="related-trainers-grid">
                                <?php while ( $related_trainers->have_posts() ) : $related_trainers->the_post(); 
                                    $related_image_id = get_post_meta( get_the_ID(), '_trainer_custom_image', true );
                                    $related_experience = get_post_meta( get_the_ID(), '_trainer_experience', true );
                                    $related_certification = get_post_meta( get_the_ID(), '_trainer_certification', true );
                                ?>
                                    <article class="related-trainer-card">
                                        <a href="<?php the_permalink(); ?>" class="trainer-card-link">
                                            <div class="trainer-card-image">
                                                <?php
                                                if ( $related_image_id ) {
                                                    $image_url = wp_get_attachment_image_url( $related_image_id, 'medium' );
                                                    if ( $image_url ) {
                                                        echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title() ) . '" />';
                                                    }
                                                } elseif ( has_post_thumbnail() ) {
                                                    the_post_thumbnail( 'medium' );
                                                } else {
                                                    ?>
                                                    <div class="trainer-card-placeholder">
                                                        <span class="placeholder-icon">👤</span>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="trainer-card-content">
                                                <h3 class="trainer-card-title"><?php the_title(); ?></h3>
                                                <?php if ( $related_experience ) : ?>
                                                    <p class="trainer-card-experience">
                                                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                                                            <path d="M10 2L12.5 7.5L18 8L14 12L15 18L10 15L5 18L6 12L2 8L7.5 7.5L10 2Z" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <?php echo esc_html( $related_experience ); ?> 年经验
                                                    </p>
                                                <?php endif; ?>
                                                <?php if ( $related_certification ) : ?>
                                                    <p class="trainer-card-cert"><?php echo esc_html( $related_certification ); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    </article>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div>
                        </section>
                    <?php endif; ?>
                </div>

                <!-- 右侧边栏 -->
                <aside class="trainer-sidebar">
                    <!-- 社交媒体 -->
                    <?php if ( $facebook || $instagram || $linkedin ) : ?>
                        <div class="sidebar-widget social-widget">
                            <h3 class="widget-title">关注教练</h3>
                            <div class="social-links">
                                <?php if ( $facebook ) : ?>
                                    <a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener" class="social-link facebook" data-platform="facebook">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                        <span>Facebook</span>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ( $instagram ) : ?>
                                    <a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener" class="social-link instagram" data-platform="instagram">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                        </svg>
                                        <span>Instagram</span>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ( $linkedin ) : ?>
                                    <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener" class="social-link linkedin" data-platform="linkedin">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                        </svg>
                                        <span>LinkedIn</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 联系教练CTA -->
                    <div class="sidebar-widget cta-widget">
                        <h3 class="widget-title">联系教练</h3>
                        <p class="cta-description">想要开始训练？立即联系我们的专业教练！</p>
                        
                        <?php if ( $phone ) : ?>
                            <a href="tel:<?php echo esc_attr( $phone ); ?>" class="cta-button cta-phone">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M2 3C2 2.44772 2.44772 2 3 2H5.15287C5.64171 2 6.0589 2.35341 6.13927 2.8356L6.87858 7.27147C6.95075 7.70451 6.73206 8.13397 6.3394 8.3303L4.79126 9.10437C5.90756 11.8783 8.12168 14.0924 10.8956 15.2087L11.6697 13.6606C11.866 13.2679 12.2955 13.0492 12.7285 13.1214L17.1644 13.8607C17.6466 13.9411 18 14.3583 18 14.8471V17C18 17.5523 17.5523 18 17 18H15C7.8203 18 2 12.1797 2 5V3Z" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                立即致电
                            </a>
                        <?php endif; ?>
                        
                        <?php if ( $email ) : ?>
                            <a href="mailto:<?php echo esc_attr( $email ); ?>" class="cta-button cta-email">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M3 4L10 11L17 4M3 4H17M3 4V16H17V4" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                发送邮件
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- 分享小部件 -->
                    <div class="sidebar-widget share-widget">
                        <h3 class="widget-title">分享教练</h3>
                        <div class="share-buttons">
                            <button class="share-btn share-facebook" data-platform="facebook">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </button>
                            <button class="share-btn share-twitter" data-platform="twitter">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                                </svg>
                            </button>
                            <button class="share-btn share-copy" data-url="<?php echo esc_url( get_permalink() ); ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2" stroke-width="2"/>
                                    <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" stroke-width="2"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <!-- 阅读进度条 -->
    <div class="reading-progress">
        <div class="reading-progress-bar"></div>
    </div>

    <?php endwhile; ?>
</div>

<?php get_footer(); ?>

