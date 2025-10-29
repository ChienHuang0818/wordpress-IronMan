/**
 * Meal Database for Professional Meal Plan Generator
 * Contains comprehensive meal options with nutritional information
 */

const MEAL_DATABASE = {
  breakfast: [
    {
      name: "Protein Pancakes",
      calories: 350,
      protein: 25,
      fat: 8,
      carbs: 35,
      ingredients: ["Oats", "Protein Powder", "Eggs", "Banana", "Almond Milk"],
      time: 15,
      tags: ["high_protein", "quick"],
    },
    {
      name: "Greek Yogurt Bowl",
      calories: 300,
      protein: 20,
      fat: 5,
      carbs: 30,
      ingredients: ["Greek Yogurt", "Berries", "Granola", "Honey", "Chia Seeds"],
      time: 5,
      tags: ["high_protein", "quick", "vegetarian"],
    },
    {
      name: "Avocado Toast",
      calories: 280,
      protein: 15,
      fat: 12,
      carbs: 25,
      ingredients: ["Whole Grain Bread", "Avocado", "Eggs", "Tomato", "Salt"],
      time: 10,
      tags: ["vegetarian", "quick"],
    },
    {
      name: "Overnight Oats",
      calories: 320,
      protein: 18,
      fat: 8,
      carbs: 40,
      ingredients: ["Rolled Oats", "Greek Yogurt", "Almond Milk", "Berries", "Nuts"],
      time: 5,
      tags: ["vegetarian", "quick", "make_ahead"],
    },
    {
      name: "Scrambled Eggs with Spinach",
      calories: 250,
      protein: 20,
      fat: 15,
      carbs: 8,
      ingredients: ["Eggs", "Spinach", "Cheese", "Olive Oil", "Salt"],
      time: 10,
      tags: ["high_protein", "low_carb", "quick"],
    },
    {
      name: "Smoothie Bowl",
      calories: 380,
      protein: 22,
      fat: 12,
      carbs: 45,
      ingredients: ["Protein Powder", "Frozen Berries", "Banana", "Almond Butter", "Granola"],
      time: 8,
      tags: ["high_protein", "quick", "vegetarian"],
    },
  ],

  lunch: [
    {
      name: "Grilled Chicken Salad",
      calories: 400,
      protein: 35,
      fat: 15,
      carbs: 20,
      ingredients: ["Chicken Breast", "Mixed Greens", "Olive Oil", "Nuts", "Cherry Tomatoes"],
      time: 20,
      tags: ["high_protein", "low_carb"],
    },
    {
      name: "Quinoa Buddha Bowl",
      calories: 450,
      protein: 20,
      fat: 18,
      carbs: 45,
      ingredients: ["Quinoa", "Chickpeas", "Roasted Vegetables", "Tahini", "Lemon"],
      time: 25,
      tags: ["vegetarian", "vegan"],
    },
    {
      name: "Turkey Wrap",
      calories: 380,
      protein: 28,
      fat: 12,
      carbs: 35,
      ingredients: ["Turkey Breast", "Whole Wheat Tortilla", "Avocado", "Lettuce", "Tomato"],
      time: 10,
      tags: ["quick", "portable"],
    },
    {
      name: "Salmon Rice Bowl",
      calories: 420,
      protein: 32,
      fat: 16,
      carbs: 38,
      ingredients: ["Salmon", "Brown Rice", "Edamame", "Cucumber", "Sesame Seeds"],
      time: 30,
      tags: ["high_protein", "omega3"],
    },
    {
      name: "Lentil Soup",
      calories: 350,
      protein: 22,
      fat: 8,
      carbs: 45,
      ingredients: ["Red Lentils", "Vegetables", "Vegetable Broth", "Spices", "Lemon"],
      time: 35,
      tags: ["vegetarian", "vegan", "high_protein"],
    },
    {
      name: "Tuna Salad",
      calories: 320,
      protein: 30,
      fat: 12,
      carbs: 15,
      ingredients: ["Tuna", "Greek Yogurt", "Celery", "Onion", "Lettuce"],
      time: 15,
      tags: ["high_protein", "low_carb", "quick"],
    },
  ],

  dinner: [
    {
      name: "Baked Salmon with Sweet Potato",
      calories: 500,
      protein: 40,
      fat: 20,
      carbs: 35,
      ingredients: ["Salmon Fillet", "Sweet Potato", "Broccoli", "Olive Oil", "Herbs"],
      time: 30,
      tags: ["high_protein", "omega3"],
    },
    {
      name: "Beef Stir Fry",
      calories: 480,
      protein: 35,
      fat: 18,
      carbs: 40,
      ingredients: ["Lean Beef", "Brown Rice", "Mixed Vegetables", "Soy Sauce", "Ginger"],
      time: 25,
      tags: ["high_protein"],
    },
    {
      name: "Baked Cod with Quinoa",
      calories: 420,
      protein: 38,
      fat: 12,
      carbs: 38,
      ingredients: ["Cod Fillet", "Quinoa", "Asparagus", "Lemon", "Herbs"],
      time: 35,
      tags: ["high_protein", "low_fat"],
    },
    {
      name: "Chicken Thighs with Roasted Vegetables",
      calories: 450,
      protein: 35,
      fat: 22,
      carbs: 25,
      ingredients: ["Chicken Thighs", "Bell Peppers", "Zucchini", "Onion", "Olive Oil"],
      time: 40,
      tags: ["high_protein"],
    },
    {
      name: "Vegetarian Chili",
      calories: 380,
      protein: 20,
      fat: 8,
      carbs: 55,
      ingredients: ["Black Beans", "Kidney Beans", "Tomatoes", "Bell Peppers", "Spices"],
      time: 45,
      tags: ["vegetarian", "vegan", "high_fiber"],
    },
    {
      name: "Pork Tenderloin with Mashed Cauliflower",
      calories: 420,
      protein: 38,
      fat: 15,
      carbs: 20,
      ingredients: ["Pork Tenderloin", "Cauliflower", "Butter", "Herbs", "Garlic"],
      time: 35,
      tags: ["high_protein", "low_carb"],
    },
  ],

  snack: [
    {
      name: "Protein Shake",
      calories: 200,
      protein: 25,
      fat: 3,
      carbs: 15,
      ingredients: ["Protein Powder", "Almond Milk", "Banana", "Ice"],
      time: 5,
      tags: ["high_protein", "quick", "post_workout"],
    },
    {
      name: "Greek Yogurt with Nuts",
      calories: 180,
      protein: 15,
      fat: 8,
      carbs: 12,
      ingredients: ["Greek Yogurt", "Almonds", "Berries", "Honey"],
      time: 2,
      tags: ["high_protein", "quick", "vegetarian"],
    },
    {
      name: "Hard Boiled Eggs",
      calories: 140,
      protein: 12,
      fat: 10,
      carbs: 1,
      ingredients: ["Eggs", "Salt", "Pepper"],
      time: 10,
      tags: ["high_protein", "low_carb", "quick"],
    },
    {
      name: "Apple with Almond Butter",
      calories: 220,
      protein: 8,
      fat: 12,
      carbs: 25,
      ingredients: ["Apple", "Almond Butter", "Cinnamon"],
      time: 3,
      tags: ["vegetarian", "quick", "portable"],
    },
    {
      name: "Cottage Cheese with Berries",
      calories: 160,
      protein: 18,
      fat: 2,
      carbs: 15,
      ingredients: ["Cottage Cheese", "Mixed Berries", "Honey"],
      time: 2,
      tags: ["high_protein", "low_fat", "quick", "vegetarian"],
    },
    {
      name: "Trail Mix",
      calories: 200,
      protein: 6,
      fat: 14,
      carbs: 18,
      ingredients: ["Nuts", "Dried Fruit", "Dark Chocolate Chips"],
      time: 1,
      tags: ["vegetarian", "quick", "portable"],
    },
  ],
};

// Dietary preference filters
const DIETARY_FILTERS = {
  high_protein: (meal) => meal.protein >= 20,
  low_carb: (meal) => meal.carbs <= 25,
  vegetarian: (meal) =>
    !meal.ingredients.some((ing) =>
      ["chicken", "beef", "pork", "turkey", "salmon", "cod", "tuna"].includes(ing.toLowerCase())
    ),
  vegan: (meal) =>
    !meal.ingredients.some((ing) =>
      [
        "chicken",
        "beef",
        "pork",
        "turkey",
        "salmon",
        "cod",
        "tuna",
        "eggs",
        "cheese",
        "yogurt",
        "milk",
      ].includes(ing.toLowerCase())
    ),
  dairy_free: (meal) =>
    !meal.ingredients.some((ing) =>
      ["cheese", "yogurt", "milk", "butter"].includes(ing.toLowerCase())
    ),
  gluten_free: (meal) =>
    !meal.ingredients.some((ing) =>
      ["bread", "tortilla", "oats", "wheat"].includes(ing.toLowerCase())
    ),
  keto: (meal) => meal.carbs <= 10,
  paleo: (meal) =>
    !meal.ingredients.some((ing) =>
      ["bread", "tortilla", "oats", "quinoa", "rice", "beans", "lentils"].includes(
        ing.toLowerCase()
      )
    ),
};

// Budget considerations
const BUDGET_LEVELS = {
  $: {
    maxIngredients: 4,
    expensiveIngredients: ["salmon", "cod", "beef", "pork tenderloin"],
  },
  $$: {
    maxIngredients: 6,
    expensiveIngredients: ["salmon", "cod"],
  },
  $$$: {
    maxIngredients: 8,
    expensiveIngredients: [],
  },
};

// Cooking time filters
const COOKING_TIME_FILTERS = {
  15: (meal) => meal.time <= 15,
  20: (meal) => meal.time <= 20,
  30: (meal) => meal.time <= 30,
  45: (meal) => meal.time <= 45,
  60: (meal) => meal.time <= 60,
};

// Export for use in main form.js
if (typeof module !== "undefined" && module.exports) {
  module.exports = {
    MEAL_DATABASE,
    DIETARY_FILTERS,
    BUDGET_LEVELS,
    COOKING_TIME_FILTERS,
  };
}
