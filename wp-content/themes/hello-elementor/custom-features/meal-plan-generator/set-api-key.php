<?php
/**
 * Simple API Key Configuration
 * This script directly sets the API key in WordPress options
 */

// 您的API密钥
$api_key = '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';

// 数据库连接信息（请根据您的MAMP设置调整）
$host = 'localhost';
$port = '8889'; // MAMP默认MySQL端口
$dbname = 'wordpress';
$username = 'root';
$password = 'root';

try {
    // 连接数据库
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🔧 OpenAI API Configuration</h1>\n";
    echo "<p>Connecting to database...</p>\n";
    
    // 设置API密钥
    $settings = [
        'mpg_openai_api_key' => $api_key,
        'mpg_openai_model' => 'gpt-3.5-turbo',
        'mpg_use_openai' => '1',
        'mpg_max_tokens' => '4000',
        'mpg_temperature' => '0.7'
    ];
    
    foreach ($settings as $key => $value) {
        // 检查选项是否已存在
        $stmt = $pdo->prepare("SELECT option_id FROM wp_options WHERE option_name = ?");
        $stmt->execute([$key]);
        
        if ($stmt->rowCount() > 0) {
            // 更新现有选项
            $stmt = $pdo->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = ?");
            $stmt->execute([$value, $key]);
            echo "<p>✅ Updated: <strong>$key</strong> = $value</p>\n";
        } else {
            // 插入新选项
            $stmt = $pdo->prepare("INSERT INTO wp_options (option_name, option_value, autoload) VALUES (?, ?, 'yes')");
            $stmt->execute([$key, $value]);
            echo "<p>✅ Added: <strong>$key</strong> = $value</p>\n";
        }
    }
    
    echo "<h2>🎉 Configuration Complete!</h2>\n";
    echo "<p>Your OpenAI API key has been configured successfully.</p>\n";
    
    echo "<h2>📋 Next Steps:</h2>\n";
    echo "<ol>\n";
    echo "<li>✅ API key configured in WordPress database</li>\n";
    echo "<li>🔗 <a href='test-page.html'>Test the meal plan generator</a></li>\n";
    echo "<li>🔗 <a href='openai-test.html'>Test OpenAI integration</a></li>\n";
    echo "<li>📝 Create a WordPress page with shortcode: <code>[meal_plan_form]</code></li>\n";
    echo "</ol>\n";
    
    echo "<h2>🧪 Quick Test</h2>\n";
    echo "<p>You can now test the API by:</p>\n";
    echo "<ul>\n";
    echo "<li>Opening <a href='test-page.html'>test-page.html</a> in your browser</li>\n";
    echo "<li>Filling out the form and clicking 'Generate My Meal Plan'</li>\n";
    echo "<li>The system will automatically use OpenAI to generate personalized meal plans</li>\n";
    echo "</ul>\n";
    
} catch (PDOException $e) {
    echo "<h1>❌ Database Connection Error</h1>\n";
    echo "<p>Error: " . $e->getMessage() . "</p>\n";
    echo "<h2>🔧 Manual Configuration</h2>\n";
    echo "<p>Please configure your API key manually:</p>\n";
    echo "<ol>\n";
    echo "<li>Login to WordPress admin</li>\n";
    echo "<li>Go to <strong>Settings > Meal Plan Generator</strong></li>\n";
    echo "<li>Enter your API key: <code>$api_key</code></li>\n";
    echo "<li>Save settings</li>\n";
    echo "</ol>\n";
}
?>
