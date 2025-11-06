<?php
/**
 * Custom Register Template
 * Registration page template for IronMan theme
 * 
 * @package HelloElementor
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Redirect logged-in users to home page
if ( is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

// Enqueue styles and scripts
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
    
    // Pass AJAX URL and nonce
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
        <!-- Left Side: Welcome Section -->
        <div class="register-welcome">
            <div class="welcome-content">
                <h1 class="welcome-title">Join Us 💪</h1>
                <p class="welcome-subtitle">Start Your Fitness Journey</p>
                
                <div class="welcome-features">
                    <div class="feature-item">
                        <span class="feature-icon">🎯</span>
                        <div class="feature-text">
                            <h3>Professional Training Plans</h3>
                            <p>Customized programs tailored to your goals</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <span class="feature-icon">👨‍🏫</span>
                        <div class="feature-text">
                            <h3>Expert Coaching</h3>
                            <p>Experienced trainers supporting you every step</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <span class="feature-icon">🍽️</span>
                        <div class="feature-text">
                            <h3>AI Nutrition Plans</h3>
                            <p>Smart meal plans aligned with your objectives</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <span class="feature-icon">📊</span>
                        <div class="feature-text">
                            <h3>Progress Tracking</h3>
                            <p>Real-time monitoring and analysis of your data</p>
                        </div>
                    </div>
                </div>
                
                <div class="welcome-stats">
                    <div class="stat-item">
                        <span class="stat-number">1000+</span>
                        <span class="stat-label">Active Members</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Expert Trainers</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">100+</span>
                        <span class="stat-label">Programs</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Registration Form -->
        <div class="register-form-section">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2 class="form-title">Create Account</h2>
                    <p class="form-subtitle">Fill in the information below to start your fitness journey</p>
                </div>
                
                <!-- Success Message -->
                <div id="register-success" class="register-message success-message" style="display: none;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>Registration successful! Redirecting...</span>
                </div>
                
                <!-- Error Message -->
                <div id="register-error" class="register-message error-message" style="display: none;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span id="error-text"></span>
                </div>
                
                <form id="custom-register-form" class="register-form" method="post">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3 class="section-title">Basic Information</h3>
                        
                        <div class="form-group">
                            <label for="username">
                                Username <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                required 
                                autocomplete="username"
                                placeholder="Enter username"
                            >
                            <small class="field-hint">Letters, numbers, and underscores only</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">
                                Email Address <span class="required">*</span>
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
                                    Password <span class="required">*</span>
                                </label>
                                <div class="password-input-wrapper">
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        required 
                                        autocomplete="new-password"
                                        placeholder="At least 8 characters"
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
                                    Confirm Password <span class="required">*</span>
                                </label>
                                <div class="password-input-wrapper">
                                    <input 
                                        type="password" 
                                        id="confirm_password" 
                                        name="confirm_password" 
                                        required 
                                        autocomplete="new-password"
                                        placeholder="Re-enter password"
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
                    
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3 class="section-title">Personal Information (Optional)</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input 
                                    type="text" 
                                    id="first_name" 
                                    name="first_name" 
                                    placeholder="Enter first name"
                                >
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input 
                                    type="text" 
                                    id="last_name" 
                                    name="last_name" 
                                    placeholder="Enter last name"
                                >
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender">
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="fitness_goal">Fitness Goal</label>
                                <select id="fitness_goal" name="fitness_goal">
                                    <option value="">Select</option>
                                    <option value="lose_weight">Lose Weight</option>
                                    <option value="build_muscle">Build Muscle</option>
                                    <option value="get_fit">Get Fit</option>
                                    <option value="improve_health">Improve Health</option>
                                    <option value="increase_strength">Increase Strength</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms and Conditions -->
                    <div class="form-group-checkbox">
                        <label class="checkbox-label">
                            <input type="checkbox" name="agree_terms" required>
                            <span class="checkbox-text">
                                I have read and agree to the
                                <a href="<?php echo esc_url( home_url( '/terms' ) ); ?>" target="_blank">Terms of Service</a>
                                and
                                <a href="<?php echo esc_url( home_url( '/privacy' ) ); ?>" target="_blank">Privacy Policy</a>
                            </span>
                        </label>
                    </div>
                    
                    <div class="form-group-checkbox">
                        <label class="checkbox-label">
                            <input type="checkbox" name="subscribe_newsletter">
                            <span class="checkbox-text">
                                I want to receive fitness tips, training plans, and special offers via email
                            </span>
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="register-submit-btn" id="register-submit-btn">
                        <span class="btn-text">Create Account</span>
                        <span class="btn-loader" style="display: none;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" stroke-dasharray="60" stroke-dashoffset="15" opacity="0.25"/>
                            </svg>
                        </span>
                    </button>
                    
                    <!-- Login Link -->
                    <div class="form-footer">
                        <p>Already have an account? <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Login Now</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>

