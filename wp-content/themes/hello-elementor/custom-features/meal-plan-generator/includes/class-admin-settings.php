<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin Settings Class
 * Handles OpenAI API configuration and admin settings
 */
class MPG_Admin_Settings {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    /**
     * 添加管理菜单
     */
    public function add_admin_menu() {
        add_options_page(
            'Meal Plan Generator Settings',
            'Meal Plan Generator',
            'manage_options',
            'meal-plan-generator',
            [$this, 'admin_page']
        );
    }
    
    /**
     * 注册设置
     */
    public function register_settings() {
		register_setting('mpg_settings', 'mpg_openai_api_key', [
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default' => ''
		]);
		register_setting('mpg_settings', 'mpg_openai_model', [
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default' => 'gpt-3.5-turbo'
		]);
		register_setting('mpg_settings', 'mpg_use_openai', [
			'type' => 'boolean',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'default' => true
		]);
		register_setting('mpg_settings', 'mpg_max_tokens', [
			'type' => 'integer',
			'sanitize_callback' => 'absint',
			'default' => 4000
		]);
		register_setting('mpg_settings', 'mpg_temperature', [
			'type' => 'number',
			'sanitize_callback' => 'floatval',
			'default' => 0.7
		]);
	}
    
    /**
     * 管理页面
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>🍽️ Meal Plan Generator Settings</h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('mpg_settings');
                do_settings_sections('mpg_settings');
                ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">OpenAI API Key</th>
                        <td>
                            <input type="password" 
                                   name="mpg_openai_api_key" 
                                   value="<?php echo esc_attr(get_option('mpg_openai_api_key')); ?>" 
                                   class="regular-text" 
                                   placeholder="sk-..." />
                            <p class="description">
                                Get your API key from <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Enable OpenAI</th>
                        <td>
                            <label>
                                <input type="checkbox" 
                                       name="mpg_use_openai" 
                                       value="1" 
                                       <?php checked(get_option('mpg_use_openai', 1)); ?> />
                                Use OpenAI for meal plan generation
                            </label>
                            <p class="description">
                                When disabled, the system will use local meal database
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">OpenAI Model</th>
                        <td>
                            <select name="mpg_openai_model">
                                <option value="gpt-3.5-turbo" <?php selected(get_option('mpg_openai_model', 'gpt-3.5-turbo'), 'gpt-3.5-turbo'); ?>>
                                    GPT-3.5 Turbo (Faster, Cheaper)
                                </option>
                                <option value="gpt-4" <?php selected(get_option('mpg_openai_model', 'gpt-3.5-turbo'), 'gpt-4'); ?>>
                                    GPT-4 (Better Quality, More Expensive)
                                </option>
                            </select>
                            <p class="description">
                                GPT-4 provides better results but costs more
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Max Tokens</th>
                        <td>
                            <input type="number" 
                                   name="mpg_max_tokens" 
                                   value="<?php echo esc_attr(intval(get_option('mpg_max_tokens', 4000))); ?>" 
                                   min="100" 
                                   max="8000" 
                                   step="100" />
                            <p class="description">
                                Maximum tokens for OpenAI response (1000-8000)
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Temperature</th>
                        <td>
                            <input type="number" 
                                   name="mpg_temperature" 
                                   value="<?php echo esc_attr(floatval(get_option('mpg_temperature', 0.7))); ?>" 
                                   min="0" 
                                   max="2" 
                                   step="0.1" 
                                   class="small-text" />
                            <p class="description">
                                Controls randomness (0 = deterministic, 2 = very random)
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button('Save Settings'); ?>
            </form>
            
            <div class="card" style="max-width: 600px; margin-top: 30px;">
                <h2>🔧 API Test</h2>
                <p>Test your OpenAI API configuration:</p>
                <button type="button" class="button button-secondary" onclick="testOpenAI()">
                    Test OpenAI Connection
                </button>
                <div id="test-result" style="margin-top: 15px;"></div>
            </div>
            
            <div class="card" style="max-width: 600px; margin-top: 20px;">
                <h2>📊 Usage Statistics</h2>
                <p>OpenAI API usage tracking:</p>
                <ul>
                    <li><strong>Total Requests:</strong> <?php echo intval(get_option('mpg_openai_requests', 0)); ?></li>
                    <li><strong>Successful Requests:</strong> <?php echo intval(get_option('mpg_openai_success', 0)); ?></li>
                    <li><strong>Failed Requests:</strong> <?php echo intval(get_option('mpg_openai_failures', 0)); ?></li>
                    <li><strong>Last Request:</strong> <?php echo get_option('mpg_openai_last_request', 'Never'); ?></li>
                </ul>
            </div>
        </div>
        
        <script>
        function testOpenAI() {
            const resultDiv = document.getElementById('test-result');
            resultDiv.innerHTML = '<p>Testing OpenAI connection...</p>';
            
            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=test_openai_connection&nonce=' + '<?php echo wp_create_nonce('test_openai'); ?>'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<div style="color: green;"><strong>✅ Success!</strong> OpenAI API is working correctly.</div>';
                } else {
                    resultDiv.innerHTML = '<div style="color: red;"><strong>❌ Error:</strong> ' + data.data + '</div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div style="color: red;"><strong>❌ Error:</strong> ' + error.message + '</div>';
            });
        }
        </script>
        <?php
    }
}

// 添加AJAX处理
add_action('wp_ajax_test_openai_connection', 'handle_test_openai_connection');

function handle_test_openai_connection() {
    if (!wp_verify_nonce($_POST['nonce'], 'test_openai')) {
        wp_die('Security check failed');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }
    
    try {
        $openai_service = new OpenAI_Service();
        
        if (!$openai_service->is_configured()) {
            wp_send_json_error('OpenAI API key not configured');
        }
        
        // 测试简单的API调用
        $test_data = [
            'sex' => 'female',
            'age' => 28,
            'heightCm' => 165,
            'weightKg' => 60,
            'goal' => 'cut',
            'activity' => 'moderate',
            'mealsPerDay' => 3,
            'preferences' => ['high_protein'],
            'allergies' => '',
            'cookTime' => '30',
            'budget' => '$$'
        ];
        
        $result = $openai_service->generate_meal_plan($test_data);
        
        if ($result && isset($result['dailyTarget'])) {
            wp_send_json_success('OpenAI API is working correctly');
        } else {
            wp_send_json_error('Invalid response from OpenAI API');
        }
        
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}

// 初始化管理设置
new MPG_Admin_Settings();
