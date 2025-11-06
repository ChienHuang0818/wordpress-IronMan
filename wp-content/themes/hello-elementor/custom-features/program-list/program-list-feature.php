<?php
/**
 * Program List Feature
 * Custom training program list functionality
 * 
 * Shortcode: [program_list]
 * 
 * @package HelloElementor
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Define constants
define( 'PROGRAM_LIST_VER', '1.0.0' );
define( 'PROGRAM_LIST_URL', get_template_directory_uri() . '/custom-features/program-list/' );
define( 'PROGRAM_LIST_PATH', get_template_directory() . '/custom-features/program-list/' );

class Program_List_Feature {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Register custom post type
        add_action( 'init', [ $this, 'register_program_post_type' ] );
        
        // Register shortcode
        add_action( 'init', [ $this, 'register_shortcode' ] );
        
        // Enqueue assets
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        
        // Add custom meta boxes
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post', [ $this, 'save_meta_boxes' ] );
    }

    /**
     * Register custom post type
     */
    public function register_program_post_type() {
        $labels = array(
            'name'                  => 'Training Programs',
            'singular_name'         => 'Program',
            'menu_name'             => 'Training Programs',
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New Program',
            'edit_item'             => 'Edit Program',
            'new_item'              => 'New Program',
            'view_item'             => 'View Program',
            'view_items'            => 'View Programs',
            'search_items'          => 'Search Programs',
            'all_items'             => 'All Programs',
            'archives'              => 'Program Archives',
            'attributes'            => 'Program Attributes',
            'insert_into_item'      => 'Insert into program',
            'uploaded_to_this_item' => 'Uploaded to this program',
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'program' ),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-heart',
            'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'show_in_rest'        => true, // Support Gutenberg editor
        );

        register_post_type( 'program', $args );

        // Register taxonomy
        register_taxonomy(
            'program_category',
            'program',
            array(
                'labels' => array(
                    'name'          => 'Program Categories',
                    'singular_name' => 'Category',
                    'search_items'  => 'Search Categories',
                    'all_items'     => 'All Categories',
                    'edit_item'     => 'Edit Category',
                    'add_new_item'  => 'Add New Category',
                ),
                'hierarchical'      => true,
                'show_in_rest'      => true,
                'show_admin_column' => true,
                'rewrite'           => array( 'slug' => 'program-category' ),
            )
        );
    }

    /**
     * Register shortcode
     */
    public function register_shortcode() {
        add_shortcode( 'program_list', [ $this, 'render_program_list' ] );
    }

    /**
     * Enqueue CSS and JavaScript
     */
    public function enqueue_assets() {
        // Only load on pages with shortcode
        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'program_list' ) ) {
            wp_enqueue_style(
                'program-list-style',
                PROGRAM_LIST_URL . 'assets/style.css',
                array(),
                PROGRAM_LIST_VER
            );

            wp_enqueue_script(
                'program-list-script',
                PROGRAM_LIST_URL . 'assets/script.js',
                array( 'jquery' ),
                PROGRAM_LIST_VER,
                true
            );

            // Pass data to JavaScript
            wp_localize_script( 'program-list-script', 'ProgramListConfig', array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'program_list_nonce' ),
            ) );
        }
    }

    /**
     * Add custom meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'program_details',
            'Program Details',
            [ $this, 'render_meta_box' ],
            'program',
            'normal',
            'high'
        );
        
        // Load media uploader
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts( $hook ) {
        global $post_type;
        
        // Only load when editing program post type
        if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'program' === $post_type ) {
            wp_enqueue_media();
        }
    }

    /**
     * Render meta box
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( 'program_meta_nonce', 'program_nonce' );

        $price = get_post_meta( $post->ID, '_program_price', true );
        $duration = get_post_meta( $post->ID, '_program_duration', true );
        $difficulty = get_post_meta( $post->ID, '_program_difficulty', true );
        $max_students = get_post_meta( $post->ID, '_program_max_students', true );
        $custom_image = get_post_meta( $post->ID, '_program_custom_image', true );
        $product_id = get_post_meta( $post->ID, '_program_product_id', true );
        ?>

        <style>
            .program-meta-field { margin-bottom: 20px; }
            .program-meta-field label { display: block; font-weight: 600; margin-bottom: 5px; }
            .program-meta-field input, .program-meta-field select { width: 100%; max-width: 400px; padding: 8px; }
            .program-image-upload { border: 2px dashed #ddd; padding: 20px; text-align: center; border-radius: 8px; background: #f9f9f9; }
            .program-image-preview { margin-top: 10px; }
            .program-image-preview img { max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            .upload-btn, .remove-btn { margin-top: 10px; padding: 10px 20px; cursor: pointer; border: none; border-radius: 5px; font-weight: 600; }
            .upload-btn { background: #667eea; color: white; }
            .remove-btn { background: #e63946; color: white; }
        </style>

        <div class="program-meta-field">
            <label>📸 Program Image</label>
            <div class="program-image-upload">
                <input type="hidden" id="program_custom_image" name="program_custom_image" value="<?php echo esc_attr( $custom_image ); ?>" />
                
                <div class="program-image-preview" id="program-image-preview">
                    <?php if ( $custom_image ) : ?>
                        <img src="<?php echo esc_url( wp_get_attachment_url( $custom_image ) ); ?>" alt="Program image" />
                    <?php else : ?>
                        <p style="color: #999;">No image uploaded yet</p>
                    <?php endif; ?>
                </div>
                
                <button type="button" class="upload-btn" id="upload-program-image">
                    <?php echo $custom_image ? 'Change Image' : 'Upload Image'; ?>
                </button>
                
                <?php if ( $custom_image ) : ?>
                    <button type="button" class="remove-btn" id="remove-program-image">Remove Image</button>
                <?php endif; ?>
            </div>
        </div>

        <div class="program-meta-field">
            <label for="program_price">Price (NT$)</label>
            <input type="number" id="program_price" name="program_price" value="<?php echo esc_attr( $price ); ?>" placeholder="3000" />
        </div>

        <div class="program-meta-field">
            <label for="program_duration">Duration</label>
            <input type="text" id="program_duration" name="program_duration" value="<?php echo esc_attr( $duration ); ?>" placeholder="e.g., 8 weeks, 3 months" />
        </div>

        <div class="program-meta-field">
            <label for="program_difficulty">Difficulty Level</label>
            <select id="program_difficulty" name="program_difficulty">
                <option value="">Select Difficulty</option>
                <option value="beginner" <?php selected( $difficulty, 'beginner' ); ?>>Beginner</option>
                <option value="intermediate" <?php selected( $difficulty, 'intermediate' ); ?>>Intermediate</option>
                <option value="advanced" <?php selected( $difficulty, 'advanced' ); ?>>Advanced</option>
            </select>
        </div>

        <div class="program-meta-field">
            <label for="program_max_students">Max Students</label>
            <input type="number" id="program_max_students" name="program_max_students" value="<?php echo esc_attr( $max_students ); ?>" placeholder="20" />
        </div>

        <div class="program-meta-field">
            <label for="program_product_id">🛒 Link WooCommerce Product</label>
            <select id="program_product_id" name="program_product_id">
                <option value="">Select Product (for checkout)</option>
                <?php
                // Check if WooCommerce is active
                if ( function_exists( 'wc_get_products' ) ) {
                    // Get all WooCommerce products
                    $products = wc_get_products( array(
                        'limit' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'status' => 'publish',
                    ) );
                    
                    foreach ( $products as $product ) {
                        printf(
                            '<option value="%d" %s>%s - NT$ %s</option>',
                            $product->get_id(),
                            selected( $product_id, $product->get_id(), false ),
                            esc_html( $product->get_name() ),
                            esc_html( $product->get_price() )
                        );
                    }
                } else {
                    echo '<option value="">Please install and activate WooCommerce first</option>';
                }
                ?>
            </select>
            <p style="color: #666; font-size: 13px; margin-top: 5px;">
                💡 Select the WooCommerce product for this training program. Users will be redirected to checkout when clicking "Enroll Now"
            </p>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var mediaUploader;
            
            $('#upload-program-image').on('click', function(e) {
                e.preventDefault();
                
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media({
                    title: 'Select Program Image',
                    button: { text: 'Use This Image' },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#program_custom_image').val(attachment.id);
                    $('#program-image-preview').html('<img src="' + attachment.url + '" alt="Program image" />');
                    $('#upload-program-image').text('Change Image');
                    if ($('#remove-program-image').length === 0) {
                        $('#upload-program-image').after('<button type="button" class="remove-btn" id="remove-program-image">Remove Image</button>');
                    }
                });
                
                mediaUploader.open();
            });
            
            $(document).on('click', '#remove-program-image', function(e) {
                e.preventDefault();
                $('#program_custom_image').val('');
                $('#program-image-preview').html('<p style="color: #999;">No image uploaded yet</p>');
                $('#upload-program-image').text('Upload Image');
                $(this).remove();
            });
        });
        </script>

        <?php
    }

    /**
     * Save custom meta boxes
     */
    public function save_meta_boxes( $post_id ) {
        // Check nonce
        if ( ! isset( $_POST['program_nonce'] ) || ! wp_verify_nonce( $_POST['program_nonce'], 'program_meta_nonce' ) ) {
            return;
        }

        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save fields
        $fields = array( 'program_price', 'program_duration', 'program_difficulty', 'program_max_students', 'program_custom_image', 'program_product_id' );
        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
            }
        }
    }

    /**
     * Render program list
     */
    public function render_program_list( $atts ) {
        // Process shortcode attributes
        $atts = shortcode_atts( array(
            'category'   => '',      // Specific category
            'limit'      => 12,      // Number to display
            'layout'     => 'grid',  // Layout type: grid, list
            'difficulty' => '',      // Difficulty filter
        ), $atts );

        // Query parameters
        $args = array(
            'post_type'      => 'program',
            'posts_per_page' => intval( $atts['limit'] ),
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        // Category filter
        if ( ! empty( $atts['category'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'program_category',
                    'field'    => 'slug',
                    'terms'    => $atts['category'],
                ),
            );
        }

        // Difficulty filter
        if ( ! empty( $atts['difficulty'] ) ) {
            $args['meta_query'] = array(
                array(
                    'key'   => '_program_difficulty',
                    'value' => $atts['difficulty'],
                ),
            );
        }

        $query = new WP_Query( $args );

        // Start output buffering
        ob_start();
        ?>

        <div class="program-list-container layout-<?php echo esc_attr( $atts['layout'] ); ?>">

            <?php if ( $query->have_posts() ) : ?>

                <div class="program-list-grid">
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>

                        <?php
                        $price = get_post_meta( get_the_ID(), '_program_price', true );
                        $duration = get_post_meta( get_the_ID(), '_program_duration', true );
                        $difficulty = get_post_meta( get_the_ID(), '_program_difficulty', true );
                        $max_students = get_post_meta( get_the_ID(), '_program_max_students', true );
                        $custom_image_id = get_post_meta( get_the_ID(), '_program_custom_image', true );
                        
                        // Use custom image first, then featured image
                        $has_image = false;
                        $image_html = '';
                        
                        if ( $custom_image_id ) {
                            $image_url = wp_get_attachment_image_url( $custom_image_id, 'large' );
                            if ( $image_url ) {
                                $image_html = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title() ) . '" />';
                                $has_image = true;
                            }
                        } elseif ( has_post_thumbnail() ) {
                            $image_html = get_the_post_thumbnail( get_the_ID(), 'large' );
                            $has_image = true;
                        }
                        ?>

                        <article class="program-item" data-program-id="<?php the_ID(); ?>" data-difficulty="<?php echo esc_attr( $difficulty ); ?>">

                            <?php if ( $has_image ) : ?>
                                <div class="program-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo $image_html; ?>
                                    </a>
                                </div>
                            <?php else : ?>
                                <!-- Show placeholder when no image available -->
                                <div class="program-thumbnail program-no-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <div class="placeholder-image">
                                            <span class="placeholder-icon">📋</span>
                                            <span class="placeholder-text">Training Program</span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ( $difficulty ) : ?>
                                <span class="program-difficulty-badge difficulty-<?php echo esc_attr( $difficulty ); ?>">
                                    <?php 
                                    $diff_labels = array(
                                        'beginner' => 'Beginner',
                                        'intermediate' => 'Intermediate',
                                        'advanced' => 'Advanced',
                                    );
                                    echo isset( $diff_labels[ $difficulty ] ) ? $diff_labels[ $difficulty ] : $difficulty;
                                    ?>
                                </span>
                            <?php endif; ?>

                            <div class="program-content">
                                <h3 class="program-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <?php if ( has_excerpt() ) : ?>
                                    <div class="program-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="program-meta">
                                    <?php if ( $price ) : ?>
                                        <span class="program-meta-item program-price">
                                            <span class="meta-icon">💰</span>
                                            NT$ <?php echo esc_html( number_format( $price ) ); ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ( $duration ) : ?>
                                        <span class="program-meta-item program-duration">
                                            <span class="meta-icon">⏱️</span>
                                            <?php echo esc_html( $duration ); ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ( $max_students ) : ?>
                                        <span class="program-meta-item program-students">
                                            <span class="meta-icon">👥</span>
                                            Max <?php echo esc_html( $max_students ); ?> students
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="program-button">
                                    View Details →
                                </a>
                            </div>

                        </article>

                    <?php endwhile; ?>
                </div>

            <?php else : ?>
                <p class="no-programs">No training programs available at the moment.</p>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        </div>

        <?php
        return ob_get_clean();
    }
}

// Initialize feature
new Program_List_Feature();

