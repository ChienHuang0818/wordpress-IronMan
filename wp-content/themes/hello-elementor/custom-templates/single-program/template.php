<?php
/**
 * Single Program Template Content
 * Single training program template content
 * 
 * @package HelloElementor
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// Enqueue styles and scripts
wp_enqueue_style( 'single-program-style', get_template_directory_uri() . '/custom-templates/single-program/style.css', array(), '1.0.1' );
wp_enqueue_script( 'single-program-script', get_template_directory_uri() . '/custom-templates/single-program/script.js', array( 'jquery' ), '1.0.1', true );

?>

<main id="content" class="single-program-page">
    <?php
    while ( have_posts() ) :
        the_post();
        
        // Get custom fields
        $price = get_post_meta( get_the_ID(), '_program_price', true );
        $duration = get_post_meta( get_the_ID(), '_program_duration', true );
        $difficulty = get_post_meta( get_the_ID(), '_program_difficulty', true );
        $max_students = get_post_meta( get_the_ID(), '_program_max_students', true );
        $custom_image_id = get_post_meta( get_the_ID(), '_program_custom_image', true );
        
        // Get categories
        $categories = get_the_terms( get_the_ID(), 'program_category' );
        
        // Difficulty labels
        $difficulty_labels = array(
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
        );
        $difficulty_label = isset( $difficulty_labels[ $difficulty ] ) ? $difficulty_labels[ $difficulty ] : '';
        
        // Get image
        $image_url = '';
        if ( $custom_image_id ) {
            $image_url = wp_get_attachment_image_url( $custom_image_id, 'full' );
        } elseif ( has_post_thumbnail() ) {
            $image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
        }
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'program-single' ); ?>>
            
            <!-- Back Button -->
            <div class="program-back-button">
                <a href="javascript:history.back()" class="back-link">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Back to List
                </a>
            </div>
            
            <!-- Hero Banner -->
            <div class="program-hero" <?php if ( $image_url ) echo 'style="background-image: url(' . esc_url( $image_url ) . ');"'; ?>>
                <div class="program-hero-overlay">
                    <div class="program-hero-content">
                        
                        <!-- Categories and Difficulty -->
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
                        
                        <!-- Title -->
                        <h1 class="program-title"><?php the_title(); ?></h1>
                        
                        <!-- Excerpt -->
                        <?php if ( has_excerpt() ) : ?>
                            <div class="program-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="program-container">
                
                <!-- Info Cards -->
                <div class="program-info-cards">
                    
                    <?php if ( $price ) : ?>
                        <div class="info-card info-price">
                            <div class="info-icon">💰</div>
                            <div class="info-content">
                                <div class="info-label">Price</div>
                                <div class="info-value">NT$ <?php echo number_format( $price ); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $duration ) : ?>
                        <div class="info-card info-duration">
                            <div class="info-icon">⏱️</div>
                            <div class="info-content">
                                <div class="info-label">Duration</div>
                                <div class="info-value"><?php echo esc_html( $duration ); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $max_students ) : ?>
                        <div class="info-card info-students">
                            <div class="info-icon">👥</div>
                            <div class="info-content">
                                <div class="info-label">Max Students</div>
                                <div class="info-value">Up to <?php echo esc_html( $max_students ); ?> students</div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-card info-date">
                        <div class="info-icon">📅</div>
                        <div class="info-content">
                            <div class="info-label">Published</div>
                            <div class="info-value"><?php echo get_the_date(); ?></div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Content Area -->
                <div class="program-content-wrapper">
                    
                    <!-- Left Side Main Content -->
                    <div class="program-main-content">
                        <div class="program-content">
                            <?php the_content(); ?>
                        </div>
                        
                        <?php
                        // Pagination links (if content uses <!--nextpage--> pagination)
                        wp_link_pages( array(
                            'before' => '<div class="page-links"><span class="page-links-title">Pages:</span>',
                            'after'  => '</div>',
                            'link_before' => '<span>',
                            'link_after'  => '</span>',
                        ) );
                        ?>
                    </div>
                    
                    <!-- Right Sidebar -->
                    <aside class="program-sidebar">
                        
                        <!-- CTA Card -->
                        <div class="sidebar-card cta-card">
                            <h3>Enroll Now</h3>
                            <p>Ready to start your fitness journey?</p>
                            
                            <?php if ( $price ) : ?>
                                <div class="cta-price">
                                    <span class="price-label">Only</span>
                                    <span class="price-value">NT$ <?php echo number_format( $price ); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php
                            // Get linked WooCommerce product ID
                            $product_id = get_post_meta( get_the_ID(), '_program_product_id', true );
                            
                            if ( $product_id && function_exists( 'wc_get_checkout_url' ) ) :
                                // Generate add to cart and redirect to checkout link
                                $checkout_url = add_query_arg( array(
                                    'add-to-cart' => $product_id,
                                    'quantity' => 1
                                ), wc_get_checkout_url() );
                                ?>
                                <a href="<?php echo esc_url( $checkout_url ); ?>" class="cta-button">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M10 5V15M5 10H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    Enroll Now
                                </a>
                            <?php else : ?>
                                <button class="cta-button" onclick="alert('This program is not currently open for enrollment. Please contact us for more information.')">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M10 5V15M5 10H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    Enroll Now
                                </button>
                            <?php endif; ?>
                            
                            <div class="cta-features">
                                <div class="feature-item">✓ Professional Coaching</div>
                                <div class="feature-item">✓ Personalized Training Plan</div>
                                <div class="feature-item">✓ Full Progress Support</div>
                            </div>
                        </div>
                        
                        <!-- Share Card -->
                        <div class="sidebar-card share-card">
                            <h3>Share Program</h3>
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
                                    Copy Link
                                </button>
                            </div>
                        </div>
                        
                    </aside>
                    
                </div>
                
            </div>
            
        </article>
        
        <!-- Related Programs -->
        <?php
        // Step 1: Prepare query conditions
        $related_args = array(
            'post_type' => 'program',                    // Only query training programs
            'posts_per_page' => 3,                       // Show 3 recommendations
            'post__not_in' => array( get_the_ID() ),    // Exclude current program
            'orderby' => 'rand',                         // Random order
        );
        
        // Step 2: If current program has categories, prioritize same category programs
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
        
        // Step 3: Execute database query
        $related_query = new WP_Query( $related_args );
        
        // Step 4: If related programs found, display recommendations section
        if ( $related_query->have_posts() ) : 
            ?>
            
            <section class="related-programs">
                <div class="program-container">
                    <h2 class="section-title">Related Training Programs</h2>
                    
                    <div class="related-programs-grid">
                        
                        <?php 
                        // Loop through each recommended program
                        while ( $related_query->have_posts() ) : $related_query->the_post(); 
                            ?>
                            
                            <?php
                            // Get program custom data
                            $rel_price = get_post_meta( get_the_ID(), '_program_price', true );
                            $rel_difficulty = get_post_meta( get_the_ID(), '_program_difficulty', true );
                            $rel_custom_image_id = get_post_meta( get_the_ID(), '_program_custom_image', true );
                            
                            // Get program image (custom image first, then featured image)
                            $rel_image_url = '';
                            if ( $rel_custom_image_id ) {
                                $rel_image_url = wp_get_attachment_image_url( $rel_custom_image_id, 'medium' );
                            } elseif ( has_post_thumbnail() ) {
                                $rel_image_url = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                            }
                            ?>
                            
                            <!-- Single Recommended Program Card -->
                            <article class="related-program-card">
                                
                                <?php if ( $rel_image_url ) : ?>
                                    <!-- Program Image Area -->
                                    <div class="related-program-image" style="background-image: url(<?php echo esc_url( $rel_image_url ); ?>);">
                                        <a href="<?php the_permalink(); ?>"></a>
                                        
                                        <?php if ( $rel_difficulty ) : ?>
                                            <!-- Difficulty Badge -->
                                            <span class="difficulty-badge difficulty-<?php echo esc_attr( $rel_difficulty ); ?>">
                                                <?php echo isset( $difficulty_labels[ $rel_difficulty ] ) ? $difficulty_labels[ $rel_difficulty ] : ''; ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Program Info Area -->
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
        
        // Step 5: Reset post data to avoid affecting other sections
        wp_reset_postdata(); 
        ?>
        
    <?php endwhile; ?>
</main>

<?php
get_footer();

