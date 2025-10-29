<?php
/**
 * Plugin Name: Professional Meal Plan Generator
 * Description: Professional gym-style meal plan generator (shortcode [meal_plan_form]). Generate cutting/bulking meal plans based on user input.
 * Version: 2.0.0
 * Author: Chien + ChatGPT
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'MPG_VER', '2.0.0' );
// 修正路徑：使用主題 URL 而不是插件 URL
define( 'MPG_URL', get_template_directory_uri() . '/custom-features/meal-plan-generator/' );
define( 'MPG_PATH', get_template_directory() . '/custom-features/meal-plan-generator/' );

require_once MPG_PATH . 'includes/class-mpg-rest.php';
require_once MPG_PATH . 'includes/class-openai-service.php';
require_once MPG_PATH . 'includes/class-admin-settings.php';

class MealPlanGenerator {
  public function __construct() {
    add_action('init', [$this, 'register_shortcode']);
    add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    add_action('rest_api_init', ['MPG_REST', 'register_routes']);
  }

  public function register_shortcode() {
    add_shortcode('meal_plan_form', [$this, 'render_form']);
  }

  public function enqueue_assets() {
    // 檢查是否在獨立頁面或包含 shortcode 的頁面
    $should_load = false;
    
    // 檢查是否是獨立頁面
    if ( isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'ai-menu-standalone.php') !== false ) {
      $should_load = true;
    }
    // 或者檢查是否包含 shortcode
    elseif ( is_singular() && has_shortcode( get_post_field('post_content', get_the_ID()), 'meal_plan_form' ) ) {
      $should_load = true;
    }
    
    if ( $should_load ) {
      wp_enqueue_style('mpg-style', MPG_URL.'assets/style.css', [], MPG_VER);
      wp_enqueue_script('mpg-form', MPG_URL.'assets/form.js', [], MPG_VER, true);
      // Provide REST API and nonce
      wp_localize_script('mpg-form', 'MPG_CFG', [
        'root'  => esc_url_raw( rest_url('mpg/v1/') ),
        'nonce' => wp_create_nonce('wp_rest')
      ]);
    }
  }

  public function render_form() {
    ob_start(); ?>
    <div class="mpg-card">
      <h2 class="mpg-title">Professional Meal Plan Generator</h2>
      
      <!-- Loading Animation -->
      <div id="mpg-loading" class="mpg-loading">
        <div class="mpg-spinner"></div>
        <div class="mpg-loading-text">Generating Your Custom Meal Plan...</div>
      </div>
      <form id="mpg-form" class="mpg-grid">
        <div class="mpg-row">
          <label>Gender</label>
          <div class="mpg-inline">
            <label><input type="radio" name="sex" value="male" required> Male</label>
            <label><input type="radio" name="sex" value="female" required checked> Female</label>
          </div>
        </div>

        <div class="mpg-row">
          <label>Age</label>
          <input type="number" name="age" min="18" max="80" placeholder="28" required>
        </div>

        <div class="mpg-row mpg-2col">
          <div>
            <label>Height (cm)</label>
            <input type="number" name="heightCm" min="120" max="220" placeholder="175" required>
          </div>
          <div>
            <label>Weight (kg)</label>
            <input type="number" name="weightKg" min="30" max="200" placeholder="70" required>
          </div>
        </div>

        <div class="mpg-row">
          <label>Fitness Goal</label>
          <div class="mpg-inline">
            <label><input type="radio" name="goal" value="cut" required checked> Cutting</label>
            <label><input type="radio" name="goal" value="bulk" required> Bulking</label>
          </div>
        </div>

        <div class="mpg-row">
          <label>Activity Level</label>
          <select name="activity" required>
            <option value="sedentary">Sedentary (1.2) - Little to no exercise</option>
            <option value="light">Light (1.375) - Light exercise 1-3 days/week</option>
            <option value="moderate" selected>Moderate (1.55) - Moderate exercise 3-5 days/week</option>
            <option value="active">Active (1.725) - Heavy exercise 6-7 days/week</option>
            <option value="athlete">Athlete (1.9) - Very heavy exercise, physical job</option>
          </select>
          <small class="mpg-hint">Choose based on your weekly workout frequency and intensity.</small>
        </div>

        <div class="mpg-row">
          <label>Dietary Preferences (Multiple Selection)</label>
          <div class="mpg-checkboxes">
            <?php
              $prefs = [
                'high_protein' => 'High Protein',
                'low_carb' => 'Low Carb',
                'vegetarian' => 'Vegetarian',
                'vegan' => 'Vegan',
                'dairy_free' => 'Dairy Free',
                'gluten_free' => 'Gluten Free',
                'keto' => 'Keto',
                'paleo' => 'Paleo'
              ];
              foreach ($prefs as $value => $label) {
                echo '<label><input type="checkbox" name="preferences[]" value="'.esc_attr($value).'"> '.esc_html($label).'</label>';
              }
            ?>
          </div>
        </div>

        <div class="mpg-row">
          <label>Food Allergies / Avoidances</label>
          <input type="text" name="allergies" placeholder="peanuts, shellfish, soy, etc.">
          <small class="mpg-hint">List any foods you need to avoid, separated by commas.</small>
        </div>

        <div class="mpg-row mpg-3col">
          <div>
            <label>Meals Per Day</label>
            <select name="mealsPerDay">
              <option value="3">3 Meals</option>
              <option value="4" selected>4 Meals</option>
              <option value="5">5 Meals</option>
              <option value="6">6 Meals</option>
            </select>
          </div>
          <div>
            <label>Max Cooking Time</label>
            <select name="cookTime">
              <option value="">No Limit</option>
              <option value="15">15 Minutes</option>
              <option value="20">20 Minutes</option>
              <option value="30">30 Minutes</option>
              <option value="45">45 Minutes</option>
              <option value="60">60 Minutes</option>
            </select>
          </div>
          <div>
            <label>Budget Level</label>
            <select name="budget">
              <option value="">No Preference</option>
              <option value="$">Budget ($)</option>
              <option value="$$" selected>Moderate ($$)</option>
              <option value="$$$">Premium ($$$)</option>
            </select>
          </div>
        </div>

        <div class="mpg-row mpg-consent">
          <label><input type="checkbox" name="agree" required> I understand this service provides general fitness nutrition advice only and is not intended for medical purposes.</label>
        </div>

        <button type="submit" class="mpg-btn">Generate My Meal Plan</button>
      </form>

      <div id="mpg-result" class="mpg-result" hidden>
        <h3>Your Daily Macros Target</h3>
        <div class="mpg-target">
          <div><strong>Calories</strong> <span id="mpg-kcal">-</span></div>
          <div><strong>Protein</strong> <span id="mpg-pro">-</span> g</div>
          <div><strong>Fat</strong> <span id="mpg-fat">-</span> g</div>
          <div><strong>Carbs</strong> <span id="mpg-carb">-</span> g</div>
        </div>
        <h3>Your 7-Day Meal Plan</h3>
        <div id="mpg-days" class="mpg-days"></div>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }
}

new MealPlanGenerator();
