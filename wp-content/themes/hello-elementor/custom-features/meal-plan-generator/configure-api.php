<?php
/**
 * Quick API Configuration Script
 * Run this once to configure your OpenAI API key
 */

// 确保在WordPress环境中运行
if (!defined('ABSPATH')) {
    // 如果不在WordPress环境中，手动加载WordPress
    require_once('../../../wp-config.php');
}

// 您的API密钥
$api_key = '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';

// 配置设置
$settings = [
    'mpg_openai_api_key' => $api_key,
    'mpg_openai_model' => 'gpt-3.5-turbo',
    'mpg_use_openai' => 1,
    'mpg_max_tokens' => 4000,
    'mpg_temperature' => 0.7
];

echo "<h1>🔧 OpenAI API Configuration</h1>\n";
echo "<p>Configuring your OpenAI API settings...</p>\n";

// 保存设置
foreach ($settings as $key => $value) {
    update_option($key, $value);
    echo "<p>✅ <strong>$key</strong>: " . (is_bool($value) ? ($value ? 'Enabled' : 'Disabled') : $value) . "</p>\n";
}

echo "<h2>🧪 Testing API Connection</h2>\n";

// 测试API连接
try {
    // 加载OpenAI服务类
    require_once('includes/class-openai-service.php');
    
    $openai_service = new OpenAI_Service();
    
    if ($openai_service->is_configured()) {
        echo "<p>✅ API key is configured</p>\n";
        
        // 进行简单的API测试
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
        
        echo "<p>🔄 Testing API call...</p>\n";
        
        $result = $openai_service->generate_meal_plan($test_data);
        
        if ($result && isset($result['dailyTarget'])) {
            echo "<p>✅ <strong>API Test Successful!</strong></p>\n";
            echo "<p>Daily Target: " . $result['dailyTarget']['calories'] . " calories</p>\n";
            echo "<p>Generated " . count($result['days']) . " days of meal plans</p>\n";
        } else {
            echo "<p>❌ API returned invalid response</p>\n";
        }
        
    } else {
        echo "<p>❌ API key not configured properly</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Error:</strong> " . $e->getMessage() . "</p>\n";
}

echo "<h2>📋 Next Steps</h2>\n";
echo "<ul>\n";
echo "<li>✅ API key configured</li>\n";
echo "<li>✅ Settings saved</li>\n";
echo "<li>🔗 <a href='test-page.html'>Test the meal plan generator</a></li>\n";
echo "<li>🔗 <a href='openai-test.html'>Test OpenAI integration</a></li>\n";
echo "<li>🔗 <a href='../../../wp-admin/options-general.php?page=meal-plan-generator'>WordPress Settings Page</a></li>\n";
echo "</ul>\n";

echo "<h2>🎯 Usage Instructions</h2>\n";
echo "<ol>\n";
echo "<li>Create a new page or post in WordPress</li>\n";
echo "<li>Add the shortcode: <code>[meal_plan_form]</code></li>\n";
echo "<li>Publish the page</li>\n";
echo "<li>Users can now generate AI-powered meal plans!</li>\n";
echo "</ol>\n";

echo "<p><strong>🎉 Configuration complete! Your meal plan generator is ready to use.</strong></p>\n";
?>
