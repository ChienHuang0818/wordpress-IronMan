/**
 * Professional Meal Plan Generator - Form Logic
 * Handles form submission, calculations, and meal plan generation
 */

class MealPlanGenerator {
  constructor() {
    this.form = document.getElementById("mpg-form");
    this.loading = document.getElementById("mpg-loading");
    this.result = document.getElementById("mpg-result");
    this.init();
  }

  init() {
    if (this.form) {
      this.form.addEventListener("submit", (e) => this.handleSubmit(e));
    }
  }

  handleSubmit(e) {
    e.preventDefault();

    // Get form data
    const formData = this.getFormData();

    // Validate form
    if (!this.validateForm(formData)) {
      return;
    }

    // Show loading animation
    this.showLoading();

    // Call WordPress REST API
    this.callAPI(formData);
  }

  getFormData() {
    const formData = new FormData(this.form);
    return {
      sex: formData.get("sex"),
      age: parseInt(formData.get("age")),
      heightCm: parseInt(formData.get("heightCm")),
      weightKg: parseInt(formData.get("weightKg")),
      goal: formData.get("goal"),
      activity: formData.get("activity"),
      preferences: formData.getAll("preferences[]"),
      allergies: formData.get("allergies"),
      mealsPerDay: parseInt(formData.get("mealsPerDay")),
      cookTime: formData.get("cookTime"),
      budget: formData.get("budget"),
      agree: formData.get("agree"),
    };
  }

  validateForm(data) {
    // Basic validation
    if (
      !data.sex ||
      !data.age ||
      !data.heightCm ||
      !data.weightKg ||
      !data.goal ||
      !data.activity
    ) {
      this.showError("Please fill in all required fields.");
      return false;
    }

    if (data.age < 18 || data.age > 80) {
      this.showError("Age must be between 18 and 80.");
      return false;
    }

    if (data.heightCm < 120 || data.heightCm > 220) {
      this.showError("Height must be between 120cm and 220cm.");
      return false;
    }

    if (data.weightKg < 30 || data.weightKg > 200) {
      this.showError("Weight must be between 30kg and 200kg.");
      return false;
    }

    if (!data.agree) {
      this.showError("Please agree to the terms and conditions.");
      return false;
    }

    return true;
  }

  calculateMacros(data) {
    // Calculate BMR using Mifflin-St Jeor Equation
    let bmr;
    if (data.sex === "male") {
      bmr = 10 * data.weightKg + 6.25 * data.heightCm - 5 * data.age + 5;
    } else {
      bmr = 10 * data.weightKg + 6.25 * data.heightCm - 5 * data.age - 161;
    }

    // Activity multipliers
    const activityMultipliers = {
      sedentary: 1.2,
      light: 1.375,
      moderate: 1.55,
      active: 1.725,
      athlete: 1.9,
    };

    // Calculate TDEE (Total Daily Energy Expenditure)
    const tdee = bmr * activityMultipliers[data.activity];

    // Adjust for goal
    let targetCalories;
    if (data.goal === "cut") {
      targetCalories = Math.round(tdee * 0.8); // 20% deficit for cutting
    } else {
      targetCalories = Math.round(tdee * 1.1); // 10% surplus for bulking
    }

    // Calculate macronutrients
    const proteinPerKg = data.goal === "cut" ? 2.2 : 2.0; // Higher protein for cutting
    const protein = Math.round(data.weightKg * proteinPerKg);
    const proteinCalories = protein * 4;

    const fatPercentage = data.goal === "cut" ? 0.25 : 0.3; // Higher fat for bulking
    const fatCalories = Math.round(targetCalories * fatPercentage);
    const fat = Math.round(fatCalories / 9);

    const carbCalories = targetCalories - proteinCalories - fatCalories;
    const carbs = Math.round(carbCalories / 4);

    return {
      calories: targetCalories,
      protein: protein,
      fat: fat,
      carbs: carbs,
      bmr: Math.round(bmr),
      tdee: Math.round(tdee),
    };
  }

  generateMealPlan(data, macros) {
    const mealPlan = [];
    const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

    // Sample meal templates based on preferences
    const mealTemplates = this.getMealTemplates(data.preferences, data.budget, data.cookTime);

    for (let i = 0; i < 7; i++) {
      const day = {
        name: days[i],
        meals: [],
      };

      // Generate meals for the day
      for (let j = 0; j < data.mealsPerDay; j++) {
        const mealType = this.getMealType(j, data.mealsPerDay);
        const meal = this.generateMeal(mealType, macros, data, mealTemplates);
        day.meals.push(meal);
      }

      mealPlan.push(day);
    }

    return mealPlan;
  }

  getMealTemplates(preferences, budget, cookTime) {
    // Use the comprehensive meal database
    let templates = { ...MEAL_DATABASE };

    // Apply dietary preference filters
    preferences.forEach((preference) => {
      if (DIETARY_FILTERS[preference]) {
        Object.keys(templates).forEach((mealType) => {
          templates[mealType] = templates[mealType].filter(DIETARY_FILTERS[preference]);
        });
      }
    });

    // Apply cooking time filter
    if (cookTime && COOKING_TIME_FILTERS[cookTime]) {
      Object.keys(templates).forEach((mealType) => {
        templates[mealType] = templates[mealType].filter(COOKING_TIME_FILTERS[cookTime]);
      });
    }

    // Apply budget filter
    if (budget && BUDGET_LEVELS[budget]) {
      const budgetConfig = BUDGET_LEVELS[budget];
      Object.keys(templates).forEach((mealType) => {
        templates[mealType] = templates[mealType].filter((meal) => {
          // Filter out expensive ingredients for lower budgets
          if (budgetConfig.expensiveIngredients.length > 0) {
            const hasExpensiveIngredient = meal.ingredients.some((ingredient) =>
              budgetConfig.expensiveIngredients.some((expensive) =>
                ingredient.toLowerCase().includes(expensive.toLowerCase())
              )
            );
            if (hasExpensiveIngredient) return false;
          }

          // Limit number of ingredients for lower budgets
          return meal.ingredients.length <= budgetConfig.maxIngredients;
        });
      });
    }

    // Ensure we have at least one option for each meal type
    Object.keys(templates).forEach((mealType) => {
      if (templates[mealType].length === 0) {
        templates[mealType] = MEAL_DATABASE[mealType].slice(0, 2); // Fallback to original options
      }
    });

    return templates;
  }

  getMealType(mealIndex, totalMeals) {
    if (totalMeals === 3) {
      return ["breakfast", "lunch", "dinner"][mealIndex];
    } else if (totalMeals === 4) {
      return ["breakfast", "lunch", "dinner", "snack"][mealIndex];
    } else if (totalMeals === 5) {
      return ["breakfast", "snack", "lunch", "dinner", "snack"][mealIndex];
    } else {
      return ["breakfast", "snack", "lunch", "snack", "dinner", "snack"][mealIndex];
    }
  }

  generateMeal(mealType, macros, data, templates) {
    const availableMeals = templates[mealType] || templates.snack;
    const randomMeal = availableMeals[Math.floor(Math.random() * availableMeals.length)];

    return {
      type: mealType,
      name: randomMeal.name,
      calories: randomMeal.calories,
      protein: randomMeal.protein,
      fat: randomMeal.fat,
      carbs: randomMeal.carbs,
      ingredients: randomMeal.ingredients,
      cookTime: randomMeal.time,
    };
  }

  showLoading() {
    this.loading.classList.add("show");
    this.form.style.display = "none";
    this.result.hidden = true;
  }

  displayResults(macros, mealPlan, generationInfo = null) {
    // Hide loading
    this.loading.classList.remove("show");

    // Update macro targets
    document.getElementById("mpg-kcal").textContent = macros.calories.toLocaleString();
    document.getElementById("mpg-pro").textContent = macros.protein;
    document.getElementById("mpg-fat").textContent = macros.fat;
    document.getElementById("mpg-carb").textContent = macros.carbs;

    // Generate meal plan HTML
    const mealPlanHTML = this.generateMealPlanHTML(mealPlan);
    document.getElementById("mpg-days").innerHTML = mealPlanHTML;

    // Add generation method info if available
    if (generationInfo) {
      const generationHTML = this.generateGenerationInfoHTML(generationInfo);
      const daysContainer = document.getElementById("mpg-days");
      daysContainer.insertAdjacentHTML("beforebegin", generationHTML);
    }

    // Show results
    this.result.hidden = false;

    // Scroll to results
    this.result.scrollIntoView({ behavior: "smooth" });
  }

  generateGenerationInfoHTML(generationInfo) {
    const statusClass =
      generationInfo.status === "success"
        ? "success"
        : generationInfo.status === "fallback"
        ? "warning"
        : "info";

    const icon = generationInfo.method.includes("OpenAI") ? "🤖" : "💻";

    return `
      <div class="mpg-generation-info ${statusClass}">
        <div class="mpg-generation-header">
          <span class="mpg-generation-icon">${icon}</span>
          <span class="mpg-generation-method">${generationInfo.method}</span>
          <span class="mpg-generation-status">${generationInfo.status}</span>
        </div>
        ${
          generationInfo.model
            ? `<div class="mpg-generation-model">Model: ${generationInfo.model}</div>`
            : ""
        }
        ${
          generationInfo.reason
            ? `<div class="mpg-generation-reason">${generationInfo.reason}</div>`
            : ""
        }
      </div>
    `;
  }

  generateMealPlanHTML(mealPlan) {
    let html = "";

    mealPlan.forEach((day) => {
      html += `
                <div class="mpg-day">
                    <h4>${day.name}</h4>
                    <div class="mpg-meals">
            `;

      day.meals.forEach((meal) => {
        html += `
                    <div class="mpg-meal">
                        <div class="mpg-meal-header">
                            <h5>${meal.name}</h5>
                            <span class="mpg-meal-type">${
                              meal.type.charAt(0).toUpperCase() + meal.type.slice(1)
                            }</span>
                        </div>
                        <div class="mpg-meal-details">
                            <div class="mpg-macros">
                                <span>${meal.calories} cal</span>
                                <span>${meal.protein}g protein</span>
                                <span>${meal.fat}g fat</span>
                                <span>${meal.carbs}g carbs</span>
                            </div>
                            <div class="mpg-ingredients">
                                <strong>Ingredients:</strong> ${meal.ingredients.join(", ")}
                            </div>
                            <div class="mpg-cook-time">
                                <strong>Cook Time:</strong> ${meal.cookTime} minutes
                            </div>
                        </div>
                    </div>
                `;
      });

      html += `
                    </div>
                </div>
            `;
    });

    return html;
  }

  async callAPI(formData) {
    try {
      // Check if MPG_CFG is available (WordPress environment)
      if (typeof MPG_CFG === "undefined") {
        // Fallback to local calculation if not in WordPress
        console.log("Not in WordPress environment, using local calculation");
        const macros = this.calculateMacros(formData);
        const mealPlan = this.generateMealPlan(formData, macros);

        // Create local generation info
        const localInfo = {
          method: "Local Algorithm",
          reason: "Not in WordPress environment",
          status: "direct",
        };

        this.displayResults(macros, mealPlan, localInfo);
        return;
      }

      // Prepare API request data
      const requestData = {
        sex: formData.sex,
        age: formData.age,
        heightCm: formData.heightCm,
        weightKg: formData.weightKg,
        goal: formData.goal,
        activity: formData.activity,
        mealsPerDay: formData.mealsPerDay,
        preferences: formData.preferences,
        allergies: formData.allergies,
        cookTime: formData.cookTime,
        budget: formData.budget,
      };

      // Make API call to WordPress REST API
      const response = await fetch(MPG_CFG.root + "plan", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": MPG_CFG.nonce,
        },
        body: JSON.stringify(requestData),
      });

      if (!response.ok) {
        throw new Error(`API Error: ${response.status} ${response.statusText}`);
      }

      const apiResult = await response.json();

      // Convert API result to our format
      const macros = {
        calories: apiResult.dailyTarget.kcal,
        protein: apiResult.dailyTarget.protein_g,
        fat: apiResult.dailyTarget.fat_g,
        carbs: apiResult.dailyTarget.carb_g,
      };

      const mealPlan = this.convertAPIResultToMealPlan(apiResult);

      // Display results with generation info
      this.displayResults(macros, mealPlan, apiResult.generation_info);
    } catch (error) {
      console.error("API Error:", error);

      // Fallback to local calculation
      console.log("Falling back to local calculation");
      try {
        const macros = this.calculateMacros(formData);
        const mealPlan = this.generateMealPlan(formData, macros);

        // Create fallback generation info
        const fallbackInfo = {
          method: "Local Algorithm",
          reason: `API Error: ${error.message}`,
          status: "fallback",
        };

        this.displayResults(macros, mealPlan, fallbackInfo);
      } catch (fallbackError) {
        console.error("Fallback calculation failed:", fallbackError);
        this.showError("Failed to generate meal plan. Please try again.");
      }
    }
  }

  convertAPIResultToMealPlan(apiResult) {
    const mealPlan = [];

    apiResult.days.forEach((day) => {
      const dayData = {
        name: day.day,
        meals: [],
      };

      day.meals.forEach((meal) => {
        dayData.meals.push({
          type: meal.title.toLowerCase(),
          name: meal.item,
          calories: meal.macros.kcal,
          protein: meal.macros.p,
          fat: meal.macros.f,
          carbs: meal.macros.c,
          ingredients: this.extractIngredientsFromMealName(meal.item),
          cookTime: this.estimateCookTime(meal.item),
        });
      });

      mealPlan.push(dayData);
    });

    return mealPlan;
  }

  extractIngredientsFromMealName(mealName) {
    // Simple ingredient extraction based on meal name
    // In a real implementation, you'd have a proper ingredient database
    const commonIngredients = {
      oats: ["Oats", "Greek Yogurt", "Berries", "Honey"],
      egg: ["Eggs", "Toast", "Avocado", "Salt"],
      smoothie: ["Protein Powder", "Almond Milk", "Banana", "Ice"],
      chicken: ["Chicken Breast", "Brown Rice", "Broccoli", "Olive Oil"],
      salmon: ["Salmon Fillet", "Sweet Potato", "Green Beans", "Herbs"],
      tofu: ["Tofu", "Quinoa", "Mixed Vegetables", "Soy Sauce"],
      beef: ["Lean Beef", "Beans", "Rice", "Vegetables"],
      yogurt: ["Greek Yogurt", "Nuts", "Berries"],
      "protein bar": ["Protein Bar"],
      eggs: ["Eggs", "Fruit", "Salt", "Pepper"],
    };

    const lowerName = mealName.toLowerCase();
    for (const [key, ingredients] of Object.entries(commonIngredients)) {
      if (lowerName.includes(key)) {
        return ingredients;
      }
    }

    // Default ingredients if no match found
    return ["Mixed Ingredients", "Seasonings", "Oil"];
  }

  estimateCookTime(mealName) {
    // Simple cook time estimation based on meal name
    const lowerName = mealName.toLowerCase();

    if (
      lowerName.includes("smoothie") ||
      lowerName.includes("yogurt") ||
      lowerName.includes("bar")
    ) {
      return 5;
    } else if (
      lowerName.includes("oats") ||
      lowerName.includes("egg") ||
      lowerName.includes("toast")
    ) {
      return 15;
    } else if (
      lowerName.includes("chicken") ||
      lowerName.includes("salmon") ||
      lowerName.includes("beef")
    ) {
      return 30;
    } else {
      return 25; // Default
    }
  }

  showError(message) {
    // Hide loading if showing
    this.loading.classList.remove("show");
    this.form.style.display = "block";

    // Show error message (you can implement a proper error display)
    alert(message);
  }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  new MealPlanGenerator();
});

// Add CSS for meal plan display
const style = document.createElement("style");
style.textContent = `
    .mpg-meals {
        display: grid;
        gap: 15px;
        margin-top: 15px;
    }
    
    .mpg-meal {
        background: var(--secondary-black);
        border: 1px solid var(--border-gray);
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s ease;
    }
    
    .mpg-meal:hover {
        border-color: var(--primary-red);
        box-shadow: 0 3px 15px var(--shadow-red);
    }
    
    .mpg-meal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .mpg-meal-header h5 {
        margin: 0;
        color: var(--text-white);
        font-size: 18px;
        font-weight: 700;
    }
    
    .mpg-meal-type {
        background: var(--primary-red);
        color: var(--text-white);
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .mpg-meal-details {
        display: grid;
        gap: 10px;
    }
    
    .mpg-macros {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .mpg-macros span {
        background: var(--accent-black);
        color: var(--text-white);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid var(--border-gray);
    }
    
    .mpg-ingredients,
    .mpg-cook-time {
        color: var(--text-gray);
        font-size: 14px;
        line-height: 1.5;
    }
    
    .mpg-ingredients strong,
    .mpg-cook-time strong {
        color: var(--text-white);
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .mpg-meal-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .mpg-macros {
            flex-direction: column;
            gap: 8px;
        }
        
        .mpg-macros span {
            text-align: center;
        }
    }
`;
document.head.appendChild(style);
