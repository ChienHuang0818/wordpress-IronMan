<?php
/**
 * Trainer List Feature
 * Custom trainer list functionality
 * 
 * Shortcode: [trainer_list]
 * 
 * @package HelloElementor
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Define constants
define( 'TRAINER_LIST_VER', '1.0.0' );
define( 'TRAINER_LIST_URL', get_template_directory_uri() . '/custom-features/trainer-list/' );
define( 'TRAINER_LIST_PATH', get_template_directory() . '/custom-features/trainer-list/' );

class Trainer_List_Feature {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Register custom post type
        add_action( 'init', [ $this, 'register_trainer_post_type' ] );
        
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
    public function register_trainer_post_type() {
        $labels = array(
            'name'                  => 'Trainers',
            'singular_name'         => 'Trainer',
            'menu_name'             => 'Trainers',
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New Trainer',
            'edit_item'             => 'Edit Trainer',
            'new_item'              => 'New Trainer',
            'view_item'             => 'View Trainer',
            'view_items'            => 'View Trainers',
            'search_items'          => 'Search Trainers',
            'all_items'             => 'All Trainers',
            'archives'              => 'Trainer Archives',
            'attributes'            => 'Trainer Attributes',
            'insert_into_item'      => 'Insert into trainer',
            'uploaded_to_this_item' => 'Uploaded to this trainer',
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'trainer' ),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 6,
            'menu_icon'           => 'dashicons-groups',
            'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'show_in_rest'        => true,
        );

        register_post_type( 'trainer', $args );

        // Register taxonomy
        register_taxonomy(
            'trainer_specialty',
            'trainer',
            array(
                'labels' => array(
                    'name'          => 'Specialties',
                    'singular_name' => 'Specialty',
                    'search_items'  => 'Search Specialties',
                    'all_items'     => 'All Specialties',
                    'edit_item'     => 'Edit Specialty',
                    'add_new_item'  => 'Add New Specialty',
                ),
                'hierarchical'      => true,
                'show_in_rest'      => true,
                'show_admin_column' => true,
                'rewrite'           => array( 'slug' => 'trainer-specialty' ),
            )
        );
    }

    /**
     * Register shortcode
     */
    public function register_shortcode() {
        add_shortcode( 'trainer_list', [ $this, 'render_trainer_list' ] );
    }

    /**
     * Enqueue CSS and JavaScript
     */
    public function enqueue_assets() {
        // Only load on pages with shortcode
        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'trainer_list' ) ) {
            wp_enqueue_style(
                'trainer-list-style',
                TRAINER_LIST_URL . 'assets/style.css',
                array(),
                TRAINER_LIST_VER
            );

            wp_enqueue_script(
                'trainer-list-script',
                TRAINER_LIST_URL . 'assets/script.js',
                array( 'jquery' ),
                TRAINER_LIST_VER,
                true
            );

            // Pass data to JavaScript
            wp_localize_script( 'trainer-list-script', 'TrainerListConfig', array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'trainer_list_nonce' ),
            ) );
        }
    }

    /**
     * Add custom meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'trainer_details',
            'Trainer Details',
            [ $this, 'render_meta_box' ],
            'trainer',
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
        
        // Only load when editing trainer post type
        if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'trainer' === $post_type ) {
            wp_enqueue_media();
        }
    }

    /**
     * Render meta box
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( 'trainer_meta_nonce', 'trainer_nonce' );

        $experience = get_post_meta( $post->ID, '_trainer_experience', true );
        $certification = get_post_meta( $post->ID, '_trainer_certification', true );
        $phone = get_post_meta( $post->ID, '_trainer_phone', true );
        $email = get_post_meta( $post->ID, '_trainer_email', true );
        $facebook = get_post_meta( $post->ID, '_trainer_facebook', true );
        $instagram = get_post_meta( $post->ID, '_trainer_instagram', true );
        $linkedin = get_post_meta( $post->ID, '_trainer_linkedin', true );
        $custom_image = get_post_meta( $post->ID, '_trainer_custom_image', true );
        ?>

        <style>
            .trainer-meta-field { margin-bottom: 20px; }
            .trainer-meta-field label { display: block; font-weight: 600; margin-bottom: 5px; }
            .trainer-meta-field input, .trainer-meta-field textarea { width: 100%; max-width: 600px; padding: 8px; }
            .trainer-meta-field textarea { min-height: 80px; }
            .trainer-image-upload { border: 2px dashed #ddd; padding: 20px; text-align: center; border-radius: 8px; background: #f9f9f9; }
            .trainer-image-preview { margin-top: 10px; }
            .trainer-image-preview img { max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            .upload-btn, .remove-btn { margin-top: 10px; padding: 10px 20px; cursor: pointer; border: none; border-radius: 5px; font-weight: 600; }
            .upload-btn { background: #667eea; color: white; }
            .remove-btn { background: #e63946; color: white; }
        </style>

        <div class="trainer-meta-field">
            <label>📸 Trainer Photo</label>
            <div class="trainer-image-upload">
                <input type="hidden" id="trainer_custom_image" name="trainer_custom_image" value="<?php echo esc_attr( $custom_image ); ?>" />
                
                <div class="trainer-image-preview" id="trainer-image-preview">
                    <?php if ( $custom_image ) : ?>
                        <img src="<?php echo esc_url( wp_get_attachment_url( $custom_image ) ); ?>" alt="Trainer photo" />
                    <?php else : ?>
                        <p style="color: #999;">No photo uploaded yet</p>
                    <?php endif; ?>
                </div>
                
                <button type="button" class="upload-btn" id="upload-trainer-image">
                    <?php echo $custom_image ? 'Change Photo' : 'Upload Photo'; ?>
                </button>
                
                <?php if ( $custom_image ) : ?>
                    <button type="button" class="remove-btn" id="remove-trainer-image">Remove Photo</button>
                <?php endif; ?>
            </div>
        </div>

        <div class="trainer-meta-field">
            <label for="trainer_experience">Years of Experience</label>
            <input type="number" id="trainer_experience" name="trainer_experience" value="<?php echo esc_attr( $experience ); ?>" placeholder="5" />
        </div>

        <div class="trainer-meta-field">
            <label for="trainer_certification">Certifications</label>
            <textarea id="trainer_certification" name="trainer_certification" placeholder="e.g., NASM-CPT, Sports Nutrition Certificate"><?php echo esc_textarea( $certification ); ?></textarea>
        </div>

        <div class="trainer-meta-field">
            <label for="trainer_phone">Phone Number</label>
            <input type="text" id="trainer_phone" name="trainer_phone" value="<?php echo esc_attr( $phone ); ?>" placeholder="+886 912 345 678" />
        </div>

        <div class="trainer-meta-field">
            <label for="trainer_email">Email Address</label>
            <input type="email" id="trainer_email" name="trainer_email" value="<?php echo esc_attr( $email ); ?>" placeholder="trainer@example.com" />
        </div>

        <h3 style="margin-top: 30px; margin-bottom: 15px;">Social Media</h3>

        <div class="trainer-meta-field">
            <label for="trainer_facebook">Facebook</label>
            <input type="url" id="trainer_facebook" name="trainer_facebook" value="<?php echo esc_attr( $facebook ); ?>" placeholder="https://facebook.com/username" />
        </div>

        <div class="trainer-meta-field">
            <label for="trainer_instagram">Instagram</label>
            <input type="url" id="trainer_instagram" name="trainer_instagram" value="<?php echo esc_attr( $instagram ); ?>" placeholder="https://instagram.com/username" />
        </div>

        <div class="trainer-meta-field">
            <label for="trainer_linkedin">LinkedIn</label>
            <input type="url" id="trainer_linkedin" name="trainer_linkedin" value="<?php echo esc_attr( $linkedin ); ?>" placeholder="https://linkedin.com/in/username" />
        </div>

        <script>
        jQuery(document).ready(function($) {
            var mediaUploader;
            
            $('#upload-trainer-image').on('click', function(e) {
                e.preventDefault();
                
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media({
                    title: 'Select Trainer Photo',
                    button: { text: 'Use This Photo' },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#trainer_custom_image').val(attachment.id);
                    $('#trainer-image-preview').html('<img src="' + attachment.url + '" alt="Trainer photo" />');
                    $('#upload-trainer-image').text('Change Photo');
                    if ($('#remove-trainer-image').length === 0) {
                        $('#upload-trainer-image').after('<button type="button" class="remove-btn" id="remove-trainer-image">Remove Photo</button>');
                    }
                });
                
                mediaUploader.open();
            });
            
            $(document).on('click', '#remove-trainer-image', function(e) {
                e.preventDefault();
                $('#trainer_custom_image').val('');
                $('#trainer-image-preview').html('<p style="color: #999;">No photo uploaded yet</p>');
                $('#upload-trainer-image').text('Upload Photo');
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
        if ( ! isset( $_POST['trainer_nonce'] ) || ! wp_verify_nonce( $_POST['trainer_nonce'], 'trainer_meta_nonce' ) ) {
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
        $fields = array( 
            'trainer_experience', 
            'trainer_certification', 
            'trainer_phone', 
            'trainer_email',
            'trainer_facebook',
            'trainer_instagram',
            'trainer_linkedin',
            'trainer_custom_image'
        );
        
        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
            }
        }
    }

    /**
     * Render trainer list
     */
    public function render_trainer_list( $atts ) {
        // Process shortcode attributes
        $atts = shortcode_atts( array(
            'specialty'  => '',      // Specific specialty
            'limit'      => 12,      // Number to display
            'layout'     => 'grid',  // Layout type: grid, list
            'orderby'    => 'date',  // Order by
        ), $atts );

        // Query parameters
        $args = array(
            'post_type'      => 'trainer',
            'posts_per_page' => intval( $atts['limit'] ),
            'post_status'    => 'publish',
            'orderby'        => $atts['orderby'],
            'order'          => 'DESC',
        );

        // Specialty filter
        if ( ! empty( $atts['specialty'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'trainer_specialty',
                    'field'    => 'slug',
                    'terms'    => $atts['specialty'],
                ),
            );
        }

        $query = new WP_Query( $args );

        // Start output buffering
        ob_start();
        ?>

        <div class="trainer-list-container layout-<?php echo esc_attr( $atts['layout'] ); ?>">

            <?php if ( $query->have_posts() ) : ?>

                <div class="trainer-list-grid">
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>

                        <?php
                        $experience = get_post_meta( get_the_ID(), '_trainer_experience', true );
                        $certification = get_post_meta( get_the_ID(), '_trainer_certification', true );
                        $phone = get_post_meta( get_the_ID(), '_trainer_phone', true );
                        $email = get_post_meta( get_the_ID(), '_trainer_email', true );
                        $facebook = get_post_meta( get_the_ID(), '_trainer_facebook', true );
                        $instagram = get_post_meta( get_the_ID(), '_trainer_instagram', true );
                        $linkedin = get_post_meta( get_the_ID(), '_trainer_linkedin', true );
                        $custom_image_id = get_post_meta( get_the_ID(), '_trainer_custom_image', true );
                        $specialties = get_the_terms( get_the_ID(), 'trainer_specialty' );
                        
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

                        <article class="trainer-item" data-trainer-id="<?php the_ID(); ?>">

                            <?php if ( $has_image ) : ?>
                                <div class="trainer-photo">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo $image_html; ?>
                                    </a>
                                </div>
                            <?php else : ?>
                                <!-- Show placeholder when no image available -->
                                <div class="trainer-photo trainer-no-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <div class="placeholder-image">
                                            <span class="placeholder-icon">👤</span>
                                            <span class="placeholder-text">Trainer</span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="trainer-content">
                                <h3 class="trainer-name">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <?php if ( $specialties && ! is_wp_error( $specialties ) ) : ?>
                                    <div class="trainer-specialties">
                                        <?php foreach ( $specialties as $specialty ) : ?>
                                            <span class="specialty-badge"><?php echo esc_html( $specialty->name ); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( has_excerpt() ) : ?>
                                    <div class="trainer-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="trainer-meta">
                                    <?php if ( $experience ) : ?>
                                        <div class="trainer-meta-item trainer-experience">
                                            <span class="meta-icon">💪</span>
                                            <span class="meta-label">Experience:</span>
                                            <span class="meta-value"><?php echo esc_html( $experience ); ?> years</span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( $certification ) : ?>
                                        <div class="trainer-meta-item trainer-certification">
                                            <span class="meta-icon">🏆</span>
                                            <span class="meta-label">Certifications:</span>
                                            <span class="meta-value"><?php echo esc_html( wp_trim_words( $certification, 10 ) ); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ( $facebook || $instagram || $linkedin ) : ?>
                                    <div class="trainer-social">
                                        <?php if ( $facebook ) : ?>
                                            <a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener" class="social-link facebook" aria-label="Facebook">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ( $instagram ) : ?>
                                            <a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener" class="social-link instagram" aria-label="Instagram">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ( $linkedin ) : ?>
                                            <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener" class="social-link linkedin" aria-label="LinkedIn">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <a href="<?php the_permalink(); ?>" class="trainer-button">
                                    View Profile →
                                </a>
                            </div>

                        </article>

                    <?php endwhile; ?>
                </div>

            <?php else : ?>
                <p class="no-trainers">No trainers available at this time.</p>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        </div>

        <?php
        return ob_get_clean();
    }
}

// Initialize feature
new Trainer_List_Feature();

