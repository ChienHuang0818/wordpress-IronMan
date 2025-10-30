<?php
/**
 * Single Program Template Content
 * 训练项目单页模板内容
 * 
 * @package HelloElementor
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// 加载样式和脚本
wp_enqueue_style( 'single-program-style', get_template_directory_uri() . '/custom-templates/single-program/style.css', array(), '1.0.1' );
wp_enqueue_script( 'single-program-script', get_template_directory_uri() . '/custom-templates/single-program/script.js', array( 'jquery' ), '1.0.1', true );

?>

<main id="content" class="single-program-page">
    <?php
    while ( have_posts() ) :
        the_post();
        
        // 获取自定义字段
        $price = get_post_meta( get_the_ID(), '_program_price', true );
        $duration = get_post_meta( get_the_ID(), '_program_duration', true );
        $difficulty = get_post_meta( get_the_ID(), '_program_difficulty', true );
        $max_students = get_post_meta( get_the_ID(), '_program_max_students', true );
        $custom_image_id = get_post_meta( get_the_ID(), '_program_custom_image', true );
        
        // 获取分类
        $categories = get_the_terms( get_the_ID(), 'program_category' );
        
        // 难度标签
        $difficulty_labels = array(
            'beginner' => '初级',
            'intermediate' => '中级',
            'advanced' => '高级',
        );
        $difficulty_label = isset( $difficulty_labels[ $difficulty ] ) ? $difficulty_labels[ $difficulty ] : '';
        
        // 获取图片
        $image_url = '';
        if ( $custom_image_id ) {
            $image_url = wp_get_attachment_image_url( $custom_image_id, 'full' );
        } elseif ( has_post_thumbnail() ) {
            $image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
        }
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'program-single' ); ?>>
            
            <!-- 返回按钮 -->
            <div class="program-back-button">
                <a href="javascript:history.back()" class="back-link">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    返回列表
                </a>
            </div>
            
            <!-- 头部横幅 -->
            <div class="program-hero" <?php if ( $image_url ) echo 'style="background-image: url(' . esc_url( $image_url ) . ');"'; ?>>
                <div class="program-hero-overlay">
                    <div class="program-hero-content">
                        
                        <!-- 分类和难度 -->
                        <div class="program-meta-badges">
                            <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
                                <?php foreach ( $categories as $category ) : ?>
                                    <span class="program-category-badge">
                                        <?php echo esc_html( $category->name ); ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <?php if ( $difficulty ) : ?>
                                <span class="program-difficulty-badge difficulty-<?php echo esc_attr( $difficulty ); ?>">
                                    <?php echo esc_html( $difficulty_label ); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- 标题 -->
                        <h1 class="program-title"><?php the_title(); ?></h1>
                        
                        <!-- 摘要 -->
                        <?php if ( has_excerpt() ) : ?>
                            <div class="program-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
            
            <!-- 主要内容区 -->
            <div class="program-container">
                
                <!-- 信息卡片 -->
                <div class="program-info-cards">
                    
                    <?php if ( $price ) : ?>
                        <div class="info-card info-price">
                            <div class="info-icon">💰</div>
                            <div class="info-content">
                                <div class="info-label">价格</div>
                                <div class="info-value">NT$ <?php echo number_format( $price ); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $duration ) : ?>
                        <div class="info-card info-duration">
                            <div class="info-icon">⏱️</div>
                            <div class="info-content">
                                <div class="info-label">时长</div>
                                <div class="info-value"><?php echo esc_html( $duration ); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $max_students ) : ?>
                        <div class="info-card info-students">
                            <div class="info-icon">👥</div>
                            <div class="info-content">
                                <div class="info-label">人数限制</div>
                                <div class="info-value">最多 <?php echo esc_html( $max_students ); ?> 人</div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-card info-date">
                        <div class="info-icon">📅</div>
                        <div class="info-content">
                            <div class="info-label">发布日期</div>
                            <div class="info-value"><?php echo get_the_date(); ?></div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- 内容区 -->
                <div class="program-content-wrapper">
                    
                    <!-- 左侧主要内容 -->
                    <div class="program-main-content">
                        <div class="program-content">
                            <?php the_content(); ?>
                        </div>
                        
                        <?php
                        // 分页链接（如果内容使用了 <!--nextpage--> 分页）
                        wp_link_pages( array(
                            'before' => '<div class="page-links"><span class="page-links-title">页面：</span>',
                            'after'  => '</div>',
                            'link_before' => '<span>',
                            'link_after'  => '</span>',
                        ) );
                        ?>
                    </div>
                    
                    <!-- 右侧边栏 -->
                    <aside class="program-sidebar">
                        
                        <!-- CTA 卡片 -->
                        <div class="sidebar-card cta-card">
                            <h3>立即报名</h3>
                            <p>准备好开始你的健身之旅了吗？</p>
                            
                            <?php if ( $price ) : ?>
                                <div class="cta-price">
                                    <span class="price-label">仅需</span>
                                    <span class="price-value">NT$ <?php echo number_format( $price ); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php
                            // 获取关联的 WooCommerce 产品 ID
                            $product_id = get_post_meta( get_the_ID(), '_program_product_id', true );
                            
                            if ( $product_id && function_exists( 'wc_get_checkout_url' ) ) :
                                // 生成添加到购物车并跳转到结账的链接
                                $checkout_url = add_query_arg( array(
                                    'add-to-cart' => $product_id,
                                    'quantity' => 1
                                ), wc_get_checkout_url() );
                                ?>
                                <a href="<?php echo esc_url( $checkout_url ); ?>" class="cta-button">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M10 5V15M5 10H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    立即报名
                                </a>
                            <?php else : ?>
                                <button class="cta-button" onclick="alert('此项目暂未开放报名，请联系我们获取更多信息。')">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M10 5V15M5 10H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    立即报名
                                </button>
                            <?php endif; ?>
                            
                            <div class="cta-features">
                                <div class="feature-item">✓ 专业教练指导</div>
                                <div class="feature-item">✓ 个性化训练计划</div>
                                <div class="feature-item">✓ 全程跟踪支持</div>
                            </div>
                        </div>
                        
                        <!-- 分享卡片 -->
                        <div class="sidebar-card share-card">
                            <h3>分享项目</h3>
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>" target="_blank" class="share-btn share-facebook">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 0C4.477 0 0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.879V12.89h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.989C16.343 19.129 20 14.99 20 10c0-5.523-4.477-10-10-10z"/>
                                    </svg>
                                    Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>" target="_blank" class="share-btn share-twitter">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"/>
                                    </svg>
                                    Twitter
                                </a>
                                <button class="share-btn share-link" onclick="copyToClipboard('<?php echo get_permalink(); ?>')">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                        <path d="M8 12a4 4 0 005.657 0l4-4A4 4 0 1012 2.343l-1.415 1.414" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M12 8a4 4 0 00-5.657 0l-4 4A4 4 0 007.999 17.657l1.414-1.414" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    复制链接
                                </button>
                            </div>
                        </div>
                        
                    </aside>
                    
                </div>
                
            </div>
            
        </article>
        
        <!-- 相关项目 -->
        <?php
        // 第1步：准备查询条件
        $related_args = array(
            'post_type' => 'program',                    // 只查询训练项目
            'posts_per_page' => 3,                       // 显示3个推荐
            'post__not_in' => array( get_the_ID() ),    // 排除当前项目
            'orderby' => 'rand',                         // 随机排序
        );
        
        // 第2步：如果当前项目有分类，优先推荐同类项目
        if ( $categories && ! is_wp_error( $categories ) ) {
            $category_ids = wp_list_pluck( $categories, 'term_id' );
            $related_args['tax_query'] = array(
                array(
                    'taxonomy' => 'program_category',
                    'field' => 'term_id',
                    'terms' => $category_ids,
                ),
            );
        }
        
        // 第3步：执行数据库查询
        $related_query = new WP_Query( $related_args );
        
        // 第4步：如果找到相关项目，显示推荐区块
        if ( $related_query->have_posts() ) : 
            ?>
            
            <section class="related-programs">
                <div class="program-container">
                    <h2 class="section-title">相关训练项目</h2>
                    
                    <div class="related-programs-grid">
                        
                        <?php 
                        // 循环显示每个推荐项目
                        while ( $related_query->have_posts() ) : $related_query->the_post(); 
                            ?>
                            
                            <?php
                            // 获取项目的自定义数据
                            $rel_price = get_post_meta( get_the_ID(), '_program_price', true );
                            $rel_difficulty = get_post_meta( get_the_ID(), '_program_difficulty', true );
                            $rel_custom_image_id = get_post_meta( get_the_ID(), '_program_custom_image', true );
                            
                            // 获取项目图片（优先自定义图片，其次特色图片）
                            $rel_image_url = '';
                            if ( $rel_custom_image_id ) {
                                $rel_image_url = wp_get_attachment_image_url( $rel_custom_image_id, 'medium' );
                            } elseif ( has_post_thumbnail() ) {
                                $rel_image_url = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                            }
                            ?>
                            
                            <!-- 单个推荐项目卡片 -->
                            <article class="related-program-card">
                                
                                <?php if ( $rel_image_url ) : ?>
                                    <!-- 项目图片区 -->
                                    <div class="related-program-image" style="background-image: url(<?php echo esc_url( $rel_image_url ); ?>);">
                                        <a href="<?php the_permalink(); ?>"></a>
                                        
                                        <?php if ( $rel_difficulty ) : ?>
                                            <!-- 难度徽章 -->
                                            <span class="difficulty-badge difficulty-<?php echo esc_attr( $rel_difficulty ); ?>">
                                                <?php echo isset( $difficulty_labels[ $rel_difficulty ] ) ? $difficulty_labels[ $rel_difficulty ] : ''; ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                    </div>
                                <?php endif; ?>
                                
                                <!-- 项目信息区 -->
                                <div class="related-program-content">
                                    <h3>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    
                                    <?php if ( $rel_price ) : ?>
                                        <div class="related-program-price">
                                            NT$ <?php echo number_format( $rel_price ); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                            </article>
                            
                        <?php endwhile; ?>
                        
                    </div>
                </div>
            </section>
            
        <?php 
        endif; 
        
        // 第5步：重置文章数据，避免影响其他部分
        wp_reset_postdata(); 
        ?>
        
    <?php endwhile; ?>
</main>

<?php
get_footer();

