<?php
/**
 * Template Name: Welcome Page
 * Description: 自訂的歡迎頁面模板 - Iron Man Fitness
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?> - Welcome</title>
    <?php wp_head(); ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
            overflow-x: hidden;
        }
        
        /* Hero Section */
        .welcome-hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
            opacity: 0.3;
            animation: wave 10s ease-in-out infinite;
        }
        
        @keyframes wave {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .welcome-content {
            position: relative;
            z-index: 1;
            max-width: 900px;
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .welcome-logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            animation: bounce 2s ease-in-out infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .welcome-hero h1 {
            font-size: clamp(2rem, 5vw, 4.5rem);
            margin-bottom: 1.5rem;
            font-weight: 800;
            line-height: 1.2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .welcome-hero p {
            font-size: clamp(1rem, 2vw, 1.5rem);
            margin-bottom: 3rem;
            opacity: 0.95;
            line-height: 1.6;
        }
        
        .btn-group {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 1.2rem 3rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .btn-primary {
            background: white;
            color: #667eea;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            background: #f8f9fa;
        }
        
        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-secondary:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
        }
        
        /* Features Section */
        .features-section {
            padding: 5rem 2rem;
            background: #f8f9fa;
        }
        
        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-title {
            text-align: center;
            font-size: clamp(2rem, 4vw, 3rem);
            margin-bottom: 3rem;
            color: #2d3748;
            font-weight: 800;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2.5rem;
        }
        
        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            display: block;
        }
        
        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #2d3748;
            font-weight: 700;
        }
        
        .feature-card p {
            color: #718096;
            line-height: 1.6;
            font-size: 1.05rem;
        }
        
        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5rem 2rem;
            text-align: center;
        }
        
        .stats-grid {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
        }
        
        .stat-item {
            animation: fadeIn 1s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .stat-number {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .stat-label {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 5rem 2rem;
            text-align: center;
            background: white;
        }
        
        .cta-section h2 {
            font-size: clamp(2rem, 4vw, 3rem);
            margin-bottom: 1.5rem;
            color: #2d3748;
            font-weight: 800;
        }
        
        .cta-section p {
            font-size: 1.3rem;
            color: #718096;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .welcome-hero {
                padding: 1.5rem;
            }
            
            .btn-group {
                flex-direction: column;
                width: 100%;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .features-section,
            .stats-section,
            .cta-section {
                padding: 3rem 1.5rem;
            }
        }
    </style>
</head>
<body <?php body_class('welcome-page'); ?>>
    
    <!-- Hero Section -->
    <div class="welcome-hero">
        <div class="welcome-content">
            <?php
            $logo_url = get_template_directory_uri() . '/assets/images/ironman-logo.png';
            if (file_exists(get_template_directory() . '/assets/images/ironman-logo.png')): ?>
                <img src="<?php echo esc_url($logo_url); ?>" alt="Iron Man Fitness Logo" class="welcome-logo">
            <?php else: ?>
                <div class="welcome-logo" style="font-size: 5rem; line-height: 1;">🏋️</div>
            <?php endif; ?>
            
            <h1>歡迎來到 Iron Man Fitness 💪</h1>
            <p>
                打造專屬你的健身計畫，成為更好的自己<br>
                專業教練團隊 × 科學訓練方法 × 完整營養指導
            </p>
            <div class="btn-group">
                <a href="<?php echo esc_url(home_url('/shop')); ?>" class="btn btn-primary">
                    🛒 立即開始
                </a>
                <a href="<?php echo esc_url(home_url('/programs')); ?>" class="btn btn-secondary">
                    📋 瀏覽課程
                </a>
            </div>
        </div>
    </div>
    
    <!-- Features Section -->
    <section class="features-section">
        <div class="features-container">
            <h2 class="section-title">為什麼選擇我們？</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <span class="feature-icon">🏋️‍♂️</span>
                    <h3>專業訓練</h3>
                    <p>由國際認證教練團隊量身打造，針對個人目標設計最有效的訓練計畫</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">🥗</span>
                    <h3>營養指導</h3>
                    <p>專業營養師提供科學化飲食建議，讓訓練效果事半功倍</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">📊</span>
                    <h3>數據追蹤</h3>
                    <p>即時監控訓練進度與身體數據，清楚看見每一步的成長</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">👥</span>
                    <h3>社群支持</h3>
                    <p>加入活躍的健身社群，與志同道合的夥伴一起努力成長</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">⏰</span>
                    <h3>彈性時間</h3>
                    <p>24/7 線上課程與教練諮詢，隨時隨地開始你的訓練</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">🏆</span>
                    <h3>成果保證</h3>
                    <p>科學化訓練系統，已幫助超過 10,000 位會員達成健身目標</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="stats-section">
        <h2 class="section-title" style="color: white; margin-bottom: 3rem;">我們的成績</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">10,000+</span>
                <span class="stat-label">活躍會員</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">50+</span>
                <span class="stat-label">專業教練</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">200+</span>
                <span class="stat-label">訓練課程</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">98%</span>
                <span class="stat-label">滿意度</span>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <h2>準備好開始你的健身之旅了嗎？</h2>
        <p>立即加入 Iron Man Fitness，讓我們一起打造更好的自己</p>
        <div class="btn-group">
            <a href="<?php echo esc_url(home_url('/shop')); ?>" class="btn btn-primary">
                🎯 開始訓練
            </a>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-secondary" style="color: #667eea; border-color: #667eea;">
                💬 聯繫我們
            </a>
        </div>
    </section>
    
    <?php wp_footer(); ?>
</body>
</html>

