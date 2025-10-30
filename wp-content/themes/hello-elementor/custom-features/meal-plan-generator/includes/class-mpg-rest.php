<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MPG_REST {

  public static function register_routes() {
    register_rest_route('mpg/v1', '/plan', [
      [
        'methods'  => 'POST',
        'callback' => [__CLASS__, 'generate_plan'],
        'permission_callback' => function() {
          return current_user_can('read') || true; // 可依需求限制
        }
      ]
    ]);
  }

  // ====== 核心計算 ======
  private static function bmr($sex, $age, $heightCm, $weightKg) {
    if ($sex === 'male') {
      return 10*$weightKg + 6.25*$heightCm - 5*$age + 5;
    }
    return 10*$weightKg + 6.25*$heightCm - 5*$age - 161;
  }

  private static function activity_factor($activity) {
    $map = [
      'sedentary'=>1.2, 'light'=>1.375, 'moderate'=>1.55,
      'active'=>1.725, 'athlete'=>1.9
    ];
    return isset($map[$activity]) ? $map[$activity] : 1.55;
  }

  private static function macro_targets($sex, $age, $heightCm, $weightKg, $goal, $activity) {
    $bmr = self::bmr($sex,$age,$heightCm,$weightKg);
    $tdee = $bmr * self::activity_factor($activity);
    $kcal = $goal === 'cut' ? round($tdee * 0.8) : round($tdee * 1.1);
    $proteinPerKg = ($goal === 'cut') ? 2.1 : 1.8;
    $protein_g = round($weightKg * $proteinPerKg);
    $fat_g = round(($kcal * 0.30) / 9);
    $carb_g = max(0, round(($kcal - ($protein_g*4) - ($fat_g*9)) / 4));
    return compact('kcal','protein_g','fat_g','carb_g');
  }

  // ====== Demo 菜色池（可自行擴充/替換）======
  private static function pools() {
    return [
      'breakfast' => [
        [ 'name'=>'Oats + Greek Yogurt Bowl', 'kcal'=>430,'p'=>28,'f'=>12,'c'=>55, 'tags'=>['high_protein'] ],
        [ 'name'=>'Egg Scramble + Toast + Avocado','kcal'=>460,'p'=>24,'f'=>22,'c'=>40 ],
        [ 'name'=>'Protein Smoothie (Whey/Plant)','kcal'=>380,'p'=>30,'f'=>8,'c'=>50, 'tags'=>['dairy_free'] ],
      ],
      'main' => [
        [ 'name'=>'Grilled Chicken + Brown Rice + Broccoli','kcal'=>600,'p'=>45,'f'=>14,'c'=>70, 'tags'=>['high_protein'] ],
        [ 'name'=>'Baked Salmon + Sweet Potato + Green Beans','kcal'=>620,'p'=>40,'f'=>22,'c'=>60 ],
        [ 'name'=>'Tofu Stir-fry + Quinoa + Veggies','kcal'=>560,'p'=>32,'f'=>16,'c'=>75, 'tags'=>['vegetarian','vegan','dairy_free'] ],
        [ 'name'=>'Lean Beef/Beans Taco Bowl','kcal'=>630,'p'=>42,'f'=>18,'c'=>75, 'tags'=>['dairy_free'] ],
      ],
      'snack' => [
        [ 'name'=>'Greek Yogurt + Nuts','kcal'=>220,'p'=>15,'f'=>10,'c'=>16 ],
        [ 'name'=>'Protein Bar','kcal'=>200,'p'=>18,'f'=>7,'c'=>20 ],
        [ 'name'=>'Boiled Eggs + Fruit','kcal'=>230,'p'=>14,'f'=>10,'c'=>20 ],
      ],
    ];
  }

  private static function pick($arr, $i) {
    $len = count($arr);
    return $arr[$i % $len];
  }

  private static function scale_macros($base, $targetKcal) {
    $r = max(0.6, min(1.6, $targetKcal / max(1,$base['kcal'])));
    return [
      'kcal'=> round($base['kcal'] * $r),
      'p'   => round($base['p']    * $r),
      'f'   => round($base['f']    * $r),
      'c'   => round($base['c']    * $r),
      'ratio'=> $r
    ];
  }

  public static function generate_plan( WP_REST_Request $req ) {
    // Nonce 驗證（前端以 X-WP-Nonce 傳入）
    $nonce = $req->get_header('x_wp_nonce');
    if ( ! wp_verify_nonce($nonce, 'wp_rest') ) {
      return new WP_Error('forbidden', 'Nonce 驗證失敗', ['status'=>403]);
    }

    $p = $req->get_json_params();

    // 讀取與基礎驗證
    $sex = sanitize_text_field($p['sex'] ?? 'female');
    $age = intval($p['age'] ?? 28);
    $height = floatval($p['heightCm'] ?? 161);
    $weight = floatval($p['weightKg'] ?? 58);
    $goal = sanitize_text_field($p['goal'] ?? 'cut');
    $activity = sanitize_text_field($p['activity'] ?? 'moderate');
    $mealsPerDay = intval($p['mealsPerDay'] ?? 4);
    $preferences = $p['preferences'] ?? [];
    $allergies = sanitize_text_field($p['allergies'] ?? '');
    $cookTime = sanitize_text_field($p['cookTime'] ?? '');
    $budget = sanitize_text_field($p['budget'] ?? '');
    $useOpenAI = boolval($p['useOpenAI'] ?? true); // 默认使用OpenAI

    if ($age < 18 || $age > 80 || $height < 120 || $height > 220 || $weight < 30 || $weight > 200) {
      return new WP_Error('invalid_input', '輸入超出允許範圍', ['status'=>400]);
    }

    // 准备用户数据
    $user_data = [
      'sex' => $sex,
      'age' => $age,
      'heightCm' => $height,
      'weightKg' => $weight,
      'goal' => $goal,
      'activity' => $activity,
      'mealsPerDay' => $mealsPerDay,
      'preferences' => $preferences,
      'allergies' => $allergies,
      'cookTime' => $cookTime,
      'budget' => $budget
    ];

    // 尝试使用OpenAI生成餐计划
    if ($useOpenAI) {
      try {
        $openai_service = new OpenAI_Service();
        if ($openai_service->is_configured()) {
          $ai_meal_plan = $openai_service->generate_meal_plan($user_data);
          
          // 转换AI响应格式为API格式
          $converted_plan = self::convert_ai_response_to_api_format($ai_meal_plan);
          
          // 添加生成方式标识
          $converted_plan['generation_method'] = 'openai';
          $converted_plan['generation_info'] = [
            'method' => 'OpenAI GPT',
            'model' => get_option('mpg_openai_model', 'gpt-3.5-turbo'),
            'status' => 'success'
          ];
          
          return $converted_plan;
        } else {
          // OpenAI未配置，使用本地生成
          $local_plan = self::generate_local_meal_plan($user_data);
          $local_plan['generation_method'] = 'local';
          $local_plan['generation_info'] = [
            'method' => 'Local Algorithm',
            'reason' => 'OpenAI API key not configured',
            'status' => 'fallback'
          ];
          return $local_plan;
        }
      } catch (Exception $e) {
        // 记录错误但继续使用备用方案
        error_log('OpenAI meal plan generation failed: ' . $e->getMessage());
        
        $local_plan = self::generate_local_meal_plan($user_data);
        $local_plan['generation_method'] = 'local';
        $local_plan['generation_info'] = [
          'method' => 'Local Algorithm',
          'reason' => 'OpenAI API error: ' . $e->getMessage(),
          'status' => 'fallback'
        ];
        return $local_plan;
      }
    }

    // 直接使用本地计算
    $local_plan = self::generate_local_meal_plan($user_data);
    $local_plan['generation_method'] = 'local';
    $local_plan['generation_info'] = [
      'method' => 'Local Algorithm',
      'reason' => 'User selected local generation',
      'status' => 'direct'
    ];
    return $local_plan;
  }

  /**
   * 转换AI响应为API格式
   */
  private static function convert_ai_response_to_api_format($ai_response) {
    $days = [];
    $day_names = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    
    foreach ($ai_response['days'] as $index => $day) {
      $meals = [];
      $total = ['kcal' => 0, 'p' => 0, 'f' => 0, 'c' => 0];
      
      foreach ($day['meals'] as $meal) {
        $meal_data = [
          'title' => ucfirst($meal['type']),
          'item' => $meal['name'],
          'macros' => [
            'kcal' => intval($meal['calories']),
            'p' => intval($meal['protein']),
            'f' => intval($meal['fat']),
            'c' => intval($meal['carbs'])
          ],
          'ingredients' => $meal['ingredients'] ?? [],
          'instructions' => $meal['instructions'] ?? '',
          'cookTime' => intval($meal['cookTime'] ?? 0)
        ];
        
        $meals[] = $meal_data;
        
        // 累计每日总量
        $total['kcal'] += $meal_data['macros']['kcal'];
        $total['p'] += $meal_data['macros']['p'];
        $total['f'] += $meal_data['macros']['f'];
        $total['c'] += $meal_data['macros']['c'];
      }
      
      $days[] = [
        'day' => $day_names[$index] ?? $day['day'],
        'meals' => $meals,
        'total' => $total
      ];
    }
    
    return [
      'goal' => 'cut', // 从用户数据获取
      'dailyTarget' => [
        'kcal' => intval($ai_response['dailyTarget']['calories']),
        'protein_g' => intval($ai_response['dailyTarget']['protein']),
        'fat_g' => intval($ai_response['dailyTarget']['fat']),
        'carb_g' => intval($ai_response['dailyTarget']['carbs'])
      ],
      'days' => $days,
      'source' => 'openai'
    ];
  }

  /**
   * 本地餐计划生成（备用方案）
   */
  private static function generate_local_meal_plan($user_data) {
    $target = self::macro_targets(
      $user_data['sex'],
      $user_data['age'],
      $user_data['heightCm'],
      $user_data['weightKg'],
      $user_data['goal'],
      $user_data['activity']
    );

    // 餐次切分（4 餐：30/35/25/10；3 餐：35/40/25）
    $splits = ($user_data['mealsPerDay'] === 3) ? [0.35,0.40,0.25] : [0.30,0.35,0.25,0.10];
    $pool = self::pools();

    $days = [];
    $names = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    for ($d=0; $d<7; $d++) {
      $meals = [];

      // breakfast
      $b = self::pick($pool['breakfast'], $d);
      $bScaled = self::scale_macros($b, round($target['kcal']*$splits[0]));
      $meals[] = ['title'=>'Breakfast','item'=>$b['name'],'macros'=>$bScaled];

      // lunch
      $l = self::pick($pool['main'], $d);
      $lScaled = self::scale_macros($l, round($target['kcal']*$splits[1]));
      $meals[] = ['title'=>'Lunch','item'=>$l['name'],'macros'=>$lScaled];

      // dinner
      $dn = self::pick($pool['main'], $d+1);
      $dnScaled = self::scale_macros($dn, round($target['kcal']*$splits[2]));
      $meals[] = ['title'=>'Dinner','item'=>$dn['name'],'macros'=>$dnScaled];

      if ($user_data['mealsPerDay'] === 4) {
        $s = self::pick($pool['snack'], $d);
        $sScaled = self::scale_macros($s, round($target['kcal']*$splits[3]));
        $meals[] = ['title'=>'Snack','item'=>$s['name'],'macros'=>$sScaled];
      }

      // 合計
      $sum = ['kcal'=>0,'p'=>0,'f'=>0,'c'=>0];
      foreach ($meals as $m) {
        $sum['kcal'] += $m['macros']['kcal'];
        $sum['p']    += $m['macros']['p'];
        $sum['f']    += $m['macros']['f'];
        $sum['c']    += $m['macros']['c'];
      }

      $days[] = ['day'=>$names[$d],'meals'=>$meals,'total'=>$sum];
    }

    return [
      'goal' => $user_data['goal'],
      'dailyTarget' => $target,
      'days' => $days,
      'source' => 'local'
    ];
  }
}
