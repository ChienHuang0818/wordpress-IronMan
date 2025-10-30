<?php
/**
 * Custom Register Template
 * 自定义注册页面模板
 * 
 * @package HelloElementor
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 如果用户已登录，重定向到首页
if ( is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

// 加载样式和脚本
function custom_register_enqueue_assets() {
    wp_enqueue_style( 
        'custom-register-style', 
        get_template_directory_uri() . '/custom-templates/register/style.css',
        array(),
        '1.0.0'
    );
    
    wp_enqueue_script( 
        'custom-register-script', 
        get_template_directory_uri() . '/custom-templates/register/script.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );
    
    // 传递 AJAX URL 和 nonce
    wp_localize_script( 'custom-register-script', 'registerAjax', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'custom_register_nonce' )
    ) );
}
add_action( 'wp_enqueue_scripts', 'custom_register_enqueue_assets' );

get_header();
?>

<div class="custom-register-page">
    <div class="register-container">
        <!-- 左侧：欢迎信息 -->
        <div class="register-welcome">
            <div class="welcome-content">
                <h1 class="welcome-title">加入我们 💪</h1>
                <p class="welcome-subtitle">开启你的健身之旅</p>
                
                <div class="welcome-features">
                    <div class="feature-item">
                        <span class="feature-icon">🎯</span>
                        <div class="feature-text">
                            <h3>专业训练计划</h3>
                            <p>根据你的目标定制个性化训练方案</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <span class="feature-icon">👨‍🏫</span>
                        <div class="feature-text">
                            <h3>专业教练指导</h3>
                            <p>经验丰富的教练团队全程陪伴</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <span class="feature-icon">🍽️</span>
                        <div class="feature-text">
                            <h3>AI 营养计划</h3>
                            <p>智能生成符合你目标的饮食方案</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <span class="feature-icon">📊</span>
                        <div class="feature-text">
                            <h3>进度追踪</h3>
                            <p>实时记录和分析你的训练数据</p>
                        </div>
                    </div>
                </div>
                
                <div class="welcome-stats">
                    <div class="stat-item">
                        <span class="stat-number">1000+</span>
                        <span class="stat-label">活跃会员</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">专业教练</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">100+</span>
                        <span class="stat-label">训练项目</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 右侧：注册表单 -->
        <div class="register-form-section">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2 class="form-title">创建账户</h2>
                    <p class="form-subtitle">填写以下信息开始你的健身之旅</p>
                </div>
                
                <!-- 注册成功消息 -->
                <div id="register-success" class="register-message success-message" style="display: none;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>注册成功！正在跳转...</span>
                </div>
                
                <!-- 错误消息 -->
                <div id="register-error" class="register-message error-message" style="display: none;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span id="error-text"></span>
                </div>
                
                <form id="custom-register-form" class="register-form" method="post">
                    <!-- 基本信息 -->
                    <div class="form-section">
                        <h3 class="section-title">基本信息</h3>
                        
                        <div class="form-group">
                            <label for="username">
                                用户名 <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                required 
                                autocomplete="username"
                                placeholder="请输入用户名"
                            >
                            <small class="field-hint">只能包含字母、数字和下划线</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">
                                电子邮箱 <span class="required">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required 
                                autocomplete="email"
                                placeholder="example@email.com"
                            >
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">
                                    密码 <span class="required">*</span>
                                </label>
                                <div class="password-input-wrapper">
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        required 
                                        autocomplete="new-password"
                                        placeholder="至少 8 位字符"
                                        minlength="8"
                                    >
                                    <button type="button" class="toggle-password" data-target="password">
                                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                            <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="2"/>
                                            <path d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="password-strength" id="password-strength"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">
                                    确认密码 <span class="required">*</span>
                                </label>
                                <div class="password-input-wrapper">
                                    <input 
                                        type="password" 
                                        id="confirm_password" 
                                        name="confirm_password" 
                                        required 
                                        autocomplete="new-password"
                                        placeholder="再次输入密码"
                                    >
                                    <button type="button" class="toggle-password" data-target="confirm_password">
                                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                            <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="2"/>
                                            <path d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 个人信息 -->
                    <div class="form-section">
                        <h3 class="section-title">个人信息（可选）</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">名字</label>
                                <input 
                                    type="text" 
                                    id="first_name" 
                                    name="first_name" 
                                    placeholder="请输入名字"
                                >
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name">姓氏</label>
                                <input 
                                    type="text" 
                                    id="last_name" 
                                    name="last_name" 
                                    placeholder="请输入姓氏"
                                >
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="gender">性别</label>
                                <select id="gender" name="gender">
                                    <option value="">请选择</option>
                                    <option value="male">男</option>
                                    <option value="female">女</option>
                                    <option value="other">其他</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="fitness_goal">健身目标</label>
                                <select id="fitness_goal" name="fitness_goal">
                                    <option value="">请选择</option>
                                    <option value="lose_weight">减脂</option>
                                    <option value="build_muscle">增肌</option>
                                    <option value="get_fit">塑形</option>
                                    <option value="improve_health">改善健康</option>
                                    <option value="increase_strength">增强力量</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 同意条款 -->
                    <div class="form-group-checkbox">
                        <label class="checkbox-label">
                            <input type="checkbox" name="agree_terms" required>
                            <span class="checkbox-text">
                                我已阅读并同意
                                <a href="<?php echo esc_url( home_url( '/terms' ) ); ?>" target="_blank">服务条款</a>
                                和
                                <a href="<?php echo esc_url( home_url( '/privacy' ) ); ?>" target="_blank">隐私政策</a>
                            </span>
                        </label>
                    </div>
                    
                    <div class="form-group-checkbox">
                        <label class="checkbox-label">
                            <input type="checkbox" name="subscribe_newsletter">
                            <span class="checkbox-text">
                                我希望接收健身技巧、训练计划和特别优惠的邮件
                            </span>
                        </label>
                    </div>
                    
                    <!-- 提交按钮 -->
                    <button type="submit" class="register-submit-btn" id="register-submit-btn">
                        <span class="btn-text">创建账户</span>
                        <span class="btn-loader" style="display: none;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" stroke-dasharray="60" stroke-dashoffset="15" opacity="0.25"/>
                            </svg>
                        </span>
                    </button>
                    
                    <!-- 登录链接 -->
                    <div class="form-footer">
                        <p>已有账户？ <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">立即登录</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>

