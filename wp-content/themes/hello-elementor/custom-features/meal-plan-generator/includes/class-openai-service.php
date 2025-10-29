<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * OpenAI Service Class
 * Handles all OpenAI API interactions for meal plan generation
 */
class OpenAI_Service {
    
    private $api_key;
    private $api_url = 'https://api.openai.com/v1/chat/completions';
    private $model = 'gpt-3.5-turbo'; // 可以改为 gpt-4 如果需要更好的结果
    
    public function __construct() {
        // 从WordPress选项或环境变量获取API密钥
        $this->api_key = get_option('mpg_openai_api_key', '');
        
        // 如果没有设置，尝试从环境变量获取
        if (empty($this->api_key)) {
            $this->api_key = getenv('OPENAI_API_KEY');
        }
        
        // 从设置获取模型
        $this->model = get_option('mpg_openai_model', 'gpt-3.5-turbo');
    }
    
    /**
     * 设置API密钥
     */
    public function set_api_key($key) {
        $this->api_key = $key;
        update_option('mpg_openai_api_key', $key);
    }
    
    /**
     * 检查API密钥是否已设置
     */
    public function is_configured() {
        return !empty($this->api_key);
    }
    
    /**
     * 生成个性化餐计划
     */
    public function generate_meal_plan($user_data) {
        if (!$this->is_configured()) {
            throw new Exception('OpenAI API key not configured');
        }
        
        $prompt = $this->build_meal_plan_prompt($user_data);
        
        $response = $this->make_api_call($prompt);
        
        return $this->parse_meal_plan_response($response);
    }
    
    /**
     * 构建餐计划生成提示
     */
    private function build_meal_plan_prompt($user_data) {
        $goal_text = $user_data['goal'] === 'cut' ? '减脂' : '增肌';
        $activity_text = $this->get_activity_text($user_data['activity']);
        $preferences_text = $this->get_preferences_text($user_data['preferences']);
        
        $prompt = "你是一位专业的营养师和健身教练。请为以下用户生成一个7天的个性化餐计划：

用户信息：
- 性别：{$user_data['sex']}
- 年龄：{$user_data['age']}岁
- 身高：{$user_data['heightCm']}cm
- 体重：{$user_data['weightKg']}kg
- 目标：{$goal_text}
- 活动水平：{$activity_text}
- 每日餐数：{$user_data['mealsPerDay']}餐
- 饮食偏好：{$preferences_text}
- 过敏/避免食物：{$user_data['allergies']}
- 烹饪时间限制：{$user_data['cookTime']}分钟
- 预算水平：{$user_data['budget']}

请严格按照以下JSON格式返回7天的餐计划，不要添加任何其他文字或解释：

```json
{
  \"dailyTarget\": {
    \"calories\": 目标卡路里,
    \"protein\": 蛋白质克数,
    \"fat\": 脂肪克数,
    \"carbs\": 碳水化合物克数
  },
  \"days\": [
    {
      \"day\": \"Monday\",
      \"meals\": [
        {
          \"type\": \"breakfast\",
          \"name\": \"餐食名称\",
          \"calories\": 卡路里,
          \"protein\": 蛋白质,
          \"fat\": 脂肪,
          \"carbs\": 碳水化合物,
          \"ingredients\": [\"配料1\", \"配料2\", \"配料3\"],
          \"instructions\": \"简单制作说明\",
          \"cookTime\": 烹饪时间
        }
      ]
    }
  ]
}
```

重要要求：
1. 必须返回有效的JSON格式
2. 不要添加任何markdown代码块标记
3. 不要添加任何解释文字
4. 确保JSON语法正确
5. 每日总卡路里必须符合目标
6. 蛋白质、脂肪、碳水化合物比例合理
7. 考虑用户的饮食偏好和限制
8. 提供多样化的餐食选择
9. 配料要容易购买和制作

请直接返回JSON，不要其他内容：";

        return $prompt;
    }
    
    /**
     * 获取活动水平中文描述
     */
    private function get_activity_text($activity) {
        $activities = [
            'sedentary' => '久坐（很少运动）',
            'light' => '轻度活动（每周1-3次轻度运动）',
            'moderate' => '中度活动（每周3-5次中等强度运动）',
            'active' => '高度活动（每周6-7次高强度运动）',
            'athlete' => '运动员级别（每天高强度训练）'
        ];
        
        return $activities[$activity] ?? '中度活动';
    }
    
    /**
     * 获取饮食偏好中文描述
     */
    private function get_preferences_text($preferences) {
        if (empty($preferences)) {
            return '无特殊偏好';
        }
        
        $preference_map = [
            'high_protein' => '高蛋白',
            'low_carb' => '低碳水',
            'vegetarian' => '素食',
            'vegan' => '纯素',
            'dairy_free' => '无乳制品',
            'gluten_free' => '无麸质',
            'keto' => '生酮',
            'paleo' => '原始饮食'
        ];
        
        $chinese_preferences = array_map(function($pref) use ($preference_map) {
            return $preference_map[$pref] ?? $pref;
        }, $preferences);
        
        return implode('、', $chinese_preferences);
    }
    
    /**
     * 调用OpenAI API
     */
    private function make_api_call($prompt) {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ];
        
        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => '你是一位专业的营养师和健身教练，擅长制定个性化的餐计划。请始终以JSON格式回复，确保数据准确且实用。'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => intval(get_option('mpg_max_tokens', 4000)),
            'temperature' => floatval(get_option('mpg_temperature', 0.7))
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }
        
        if ($http_code !== 200) {
            $error_data = json_decode($response, true);
            $error_message = $error_data['error']['message'] ?? 'Unknown API error';
            throw new Exception('OpenAI API Error: ' . $error_message);
        }
        
        return json_decode($response, true);
    }
    
    /**
     * 解析OpenAI响应
     */
    private function parse_meal_plan_response($response) {
        $content = $response['choices'][0]['message']['content'];
        
        // 记录原始响应用于调试
        error_log('OpenAI Raw Response: ' . $content);
        
        // 尝试多种JSON提取方法
        $meal_plan = $this->extract_json_from_content($content);
        
        if ($meal_plan === null) {
            // 如果JSON解析失败，尝试从文本中提取信息
            $fallback_plan = $this->extract_meal_plan_from_text($content);
            
            if ($fallback_plan !== null) {
                error_log('Using fallback text extraction for meal plan');
                return $fallback_plan;
            }
            
            // 如果文本提取也失败，记录详细错误信息
            $error_details = [
                'content_length' => strlen($content),
                'content_preview' => substr($content, 0, 500),
                'json_error' => json_last_error_msg(),
                'content_contains_json' => (strpos($content, '{') !== false && strpos($content, '}') !== false)
            ];
            
            error_log('JSON Parse Error Details: ' . json_encode($error_details));
            
            throw new Exception('Failed to parse OpenAI response as valid JSON. Content preview: ' . substr($content, 0, 200));
        }
        
        return $meal_plan;
    }
    
    /**
     * 从内容中提取JSON
     */
    private function extract_json_from_content($content) {
        // 方法1: 尝试直接解析整个内容
        $meal_plan = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($meal_plan)) {
            return $meal_plan;
        }
        
        // 方法2: 查找第一个完整的JSON对象
        $json_start = strpos($content, '{');
        if ($json_start !== false) {
            $brace_count = 0;
            $json_end = $json_start;
            
            for ($i = $json_start; $i < strlen($content); $i++) {
                if ($content[$i] === '{') {
                    $brace_count++;
                } elseif ($content[$i] === '}') {
                    $brace_count--;
                    if ($brace_count === 0) {
                        $json_end = $i + 1;
                        break;
                    }
                }
            }
            
            if ($brace_count === 0) {
                $json_content = substr($content, $json_start, $json_end - $json_start);
                $meal_plan = json_decode($json_content, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($meal_plan)) {
                    return $meal_plan;
                }
            }
        }
        
        // 方法3: 查找JSON代码块
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $content, $matches)) {
            $meal_plan = json_decode($matches[1], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($meal_plan)) {
                return $meal_plan;
            }
        }
        
        // 方法4: 查找任何看起来像JSON的内容
        if (preg_match('/\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s', $content, $matches)) {
            $meal_plan = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($meal_plan)) {
                return $meal_plan;
            }
        }
        
        return null;
    }
    
    /**
     * 生成餐食建议（用于备用方案）
     */
    public function get_meal_suggestions($user_data, $meal_type) {
        if (!$this->is_configured()) {
            return null;
        }
        
        $prompt = "请为{$meal_type}推荐3个适合以下用户的餐食：
用户信息：{$user_data['sex']}，{$user_data['age']}岁，目标{$user_data['goal']}，偏好" . implode('、', $user_data['preferences']) . "

请以JSON数组格式返回：
[
  {
    \"name\": \"餐食名称\",
    \"calories\": 卡路里,
    \"protein\": 蛋白质,
    \"fat\": 脂肪,
    \"carbs\": 碳水化合物,
    \"ingredients\": [\"配料1\", \"配料2\"],
    \"cookTime\": 烹饪时间
  }
]";
        
        try {
            $response = $this->make_api_call($prompt);
            $content = $response['choices'][0]['message']['content'];
            
            $json_start = strpos($content, '[');
            $json_end = strrpos($content, ']') + 1;
            
            if ($json_start !== false && $json_end !== false) {
                $json_content = substr($content, $json_start, $json_end - $json_start);
                return json_decode($json_content, true);
            }
        } catch (Exception $e) {
            error_log('OpenAI meal suggestion error: ' . $e->getMessage());
        }
        
        return null;
    }
}
