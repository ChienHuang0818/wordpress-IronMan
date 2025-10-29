<?php
/**
 * Program List Feature
 * 自定义课程/训练项目列表功能
 * 
 * Shortcode: [program_list]
 * 
 * @package HelloElementor
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// 定义常量
define( 'PROGRAM_LIST_VER', '1.0.0' );
define( 'PROGRAM_LIST_URL', get_template_directory_uri() . '/custom-features/program-list/' );
define( 'PROGRAM_LIST_PATH', get_template_directory() . '/custom-features/program-list/' );

class Program_List_Feature {
    
    /**
     * 构造函数
     */
    public function __construct() {
        // 注册自定义文章类型
        add_action( 'init', [ $this, 'register_program_post_type' ] );
        
        // 注册 shortcode
        add_action( 'init', [ $this, 'register_shortcode' ] );
        
        // 载入资源
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        
        // 添加自定义栏位
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post', [ $this, 'save_meta_boxes' ] );
    }

    /**
     * 注册自定义文章类型
     */
    public function register_program_post_type() {
        $labels = array(
            'name'                  => '训练项目',
            'singular_name'         => '项目',
            'menu_name'             => '训练项目',
            'add_new'               => '新增项目',
            'add_new_item'          => '新增训练项目',
            'edit_item'             => '编辑项目',
            'new_item'              => '新项目',
            'view_item'             => '查看项目',
            'view_items'            => '查看项目',
            'search_items'          => '搜索项目',
            'all_items'             => '所有项目',
            'archives'              => '项目归档',
            'attributes'            => '项目属性',
            'insert_into_item'      => '插入到项目',
            'uploaded_to_this_item' => '上传到此项目',
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
            'show_in_rest'        => true, // 支持 Gutenberg 编辑器
        );

        register_post_type( 'program', $args );

        // 注册分类法
        register_taxonomy(
            'program_category',
            'program',
            array(
                'labels' => array(
                    'name'          => '项目分类',
                    'singular_name' => '分类',
                    'search_items'  => '搜索分类',
                    'all_items'     => '所有分类',
                    'edit_item'     => '编辑分类',
                    'add_new_item'  => '新增分类',
                ),
                'hierarchical'      => true,
                'show_in_rest'      => true,
                'show_admin_column' => true,
                'rewrite'           => array( 'slug' => 'program-category' ),
            )
        );
    }

    /**
     * 注册 Shortcode
     */
    public function register_shortcode() {
        add_shortcode( 'program_list', [ $this, 'render_program_list' ] );
    }

    /**
     * 载入 CSS 和 JavaScript
     */
    public function enqueue_assets() {
        // 只在包含 shortcode 的页面载入
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

            // 传递数据给 JavaScript
            wp_localize_script( 'program-list-script', 'ProgramListConfig', array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'program_list_nonce' ),
            ) );
        }
    }

    /**
     * 添加自定义栏位
     */
    public function add_meta_boxes() {
        add_meta_box(
            'program_details',
            '项目详细信息',
            [ $this, 'render_meta_box' ],
            'program',
            'normal',
            'high'
        );
        
        // 加载媒体上传器
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
    }
    
    /**
     * 加载后台脚本
     */
    public function enqueue_admin_scripts( $hook ) {
        global $post_type;
        
        // 只在编辑 program 类型的文章时加载
        if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'program' === $post_type ) {
            wp_enqueue_media();
        }
    }

    /**
     * 渲染自定义栏位
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
            <label>📸 项目图片</label>
            <div class="program-image-upload">
                <input type="hidden" id="program_custom_image" name="program_custom_image" value="<?php echo esc_attr( $custom_image ); ?>" />
                
                <div class="program-image-preview" id="program-image-preview">
                    <?php if ( $custom_image ) : ?>
                        <img src="<?php echo esc_url( wp_get_attachment_url( $custom_image ) ); ?>" alt="项目图片" />
                    <?php else : ?>
                        <p style="color: #999;">还没有上传图片</p>
                    <?php endif; ?>
                </div>
                
                <button type="button" class="upload-btn" id="upload-program-image">
                    <?php echo $custom_image ? '更换图片' : '上传图片'; ?>
                </button>
                
                <?php if ( $custom_image ) : ?>
                    <button type="button" class="remove-btn" id="remove-program-image">移除图片</button>
                <?php endif; ?>
            </div>
        </div>

        <div class="program-meta-field">
            <label for="program_price">价格 (NT$)</label>
            <input type="number" id="program_price" name="program_price" value="<?php echo esc_attr( $price ); ?>" placeholder="3000" />
        </div>

        <div class="program-meta-field">
            <label for="program_duration">时长</label>
            <input type="text" id="program_duration" name="program_duration" value="<?php echo esc_attr( $duration ); ?>" placeholder="例如：8周、3个月" />
        </div>

        <div class="program-meta-field">
            <label for="program_difficulty">难度等级</label>
            <select id="program_difficulty" name="program_difficulty">
                <option value="">选择难度</option>
                <option value="beginner" <?php selected( $difficulty, 'beginner' ); ?>>初级</option>
                <option value="intermediate" <?php selected( $difficulty, 'intermediate' ); ?>>中级</option>
                <option value="advanced" <?php selected( $difficulty, 'advanced' ); ?>>高级</option>
            </select>
        </div>

        <div class="program-meta-field">
            <label for="program_max_students">最大学员数</label>
            <input type="number" id="program_max_students" name="program_max_students" value="<?php echo esc_attr( $max_students ); ?>" placeholder="20" />
        </div>

        <div class="program-meta-field">
            <label for="program_product_id">🛒 关联 WooCommerce 产品</label>
            <select id="program_product_id" name="program_product_id">
                <option value="">选择产品（用于结账）</option>
                <?php
                // 检查 WooCommerce 是否已激活
                if ( function_exists( 'wc_get_products' ) ) {
                    // 获取所有 WooCommerce 产品
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
                    echo '<option value="">请先安装并激活 WooCommerce</option>';
                }
                ?>
            </select>
            <p style="color: #666; font-size: 13px; margin-top: 5px;">
                💡 选择此训练项目对应的 WooCommerce 产品，用户点击"立即报名"将跳转到结账页面
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
                    title: '选择项目图片',
                    button: { text: '使用这张图片' },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#program_custom_image').val(attachment.id);
                    $('#program-image-preview').html('<img src="' + attachment.url + '" alt="项目图片" />');
                    $('#upload-program-image').text('更换图片');
                    if ($('#remove-program-image').length === 0) {
                        $('#upload-program-image').after('<button type="button" class="remove-btn" id="remove-program-image">移除图片</button>');
                    }
                });
                
                mediaUploader.open();
            });
            
            $(document).on('click', '#remove-program-image', function(e) {
                e.preventDefault();
                $('#program_custom_image').val('');
                $('#program-image-preview').html('<p style="color: #999;">还没有上传图片</p>');
                $('#upload-program-image').text('上传图片');
                $(this).remove();
            });
        });
        </script>

        <?php
    }

    /**
     * 保存自定义栏位
     */
    public function save_meta_boxes( $post_id ) {
        // 检查 nonce
        if ( ! isset( $_POST['program_nonce'] ) || ! wp_verify_nonce( $_POST['program_nonce'], 'program_meta_nonce' ) ) {
            return;
        }

        // 检查自动保存
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // 检查权限
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // 保存字段
        $fields = array( 'program_price', 'program_duration', 'program_difficulty', 'program_max_students', 'program_custom_image', 'program_product_id' );
        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
            }
        }
    }

    /**
     * 渲染课程列表
     */
    public function render_program_list( $atts ) {
        // 处理 shortcode 参数
        $atts = shortcode_atts( array(
            'category'   => '',      // 特定分类
            'limit'      => 12,      // 显示数量
            'layout'     => 'grid',  // 布局类型: grid, list
            'difficulty' => '',      // 难度筛选
        ), $atts );

        // 查询参数
        $args = array(
            'post_type'      => 'program',
            'posts_per_page' => intval( $atts['limit'] ),
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        // 分类筛选
        if ( ! empty( $atts['category'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'program_category',
                    'field'    => 'slug',
                    'terms'    => $atts['category'],
                ),
            );
        }

        // 难度筛选
        if ( ! empty( $atts['difficulty'] ) ) {
            $args['meta_query'] = array(
                array(
                    'key'   => '_program_difficulty',
                    'value' => $atts['difficulty'],
                ),
            );
        }

        $query = new WP_Query( $args );

        // 开始输出缓冲
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
                        
                        // 优先使用自定义图片，其次使用特色图片
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
                                <!-- 没有图片时显示占位符 -->
                                <div class="program-thumbnail program-no-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <div class="placeholder-image">
                                            <span class="placeholder-icon">📋</span>
                                            <span class="placeholder-text">训练项目</span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ( $difficulty ) : ?>
                                <span class="program-difficulty-badge difficulty-<?php echo esc_attr( $difficulty ); ?>">
                                    <?php 
                                    $diff_labels = array(
                                        'beginner' => '初级',
                                        'intermediate' => '中级',
                                        'advanced' => '高级',
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
                                            最多 <?php echo esc_html( $max_students ); ?> 人
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="program-button">
                                    查看详情 →
                                </a>
                            </div>

                        </article>

                    <?php endwhile; ?>
                </div>

            <?php else : ?>
                <p class="no-programs">目前没有可用的训练项目。</p>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        </div>

        <?php
        return ob_get_clean();
    }
}

// 初始化功能
new Program_List_Feature();

