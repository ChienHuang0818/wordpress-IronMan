<?php
/**
 * Welcome Page Shortcode
 * Usage: Add [welcome] to any page or post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function ironman_welcome_shortcode() {
	// Enqueue the CSS file
	wp_enqueue_style(
		'ironman-welcome-style',
		get_template_directory_uri() . '/custom-templates/welcome/style.css',
		array(),
		HELLO_ELEMENTOR_VERSION
	);
	
	// Enqueue the carousel JavaScript
	wp_enqueue_script(
		'ironman-carousel-js',
		get_template_directory_uri() . '/custom-templates/welcome/carousel.js',
		array(),
		HELLO_ELEMENTOR_VERSION,
		true
	);
	
	// Define carousel slides (you can modify these)
	$slides = array(
		array(
			'image' => get_template_directory_uri() . '/assets/images/hero-bg-1.jpg',
			'title' => 'Welcome to IronMan',
			'subtitle' => 'Transform Your Body, Empower Your Mind'
		),
		array(
			'image' => get_template_directory_uri() . '/assets/images/hero-bg-2.jpg',
			'title' => 'Build Your Strength',
			'subtitle' => 'Professional Training Programs Tailored For You'
		),
		array(
			'image' => get_template_directory_uri() . '/assets/images/hero-bg-3.jpg',
			'title' => 'Achieve Your Goals',
			'subtitle' => 'Join The Ultimate Fitness Community'
		)
	);
	
	ob_start();
	?>
	
	<!-- Hero Carousel Section -->
	<section class="ironman-hero hero-carousel">
		<?php foreach ($slides as $index => $slide) : ?>
			<div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('<?php echo esc_url($slide['image']); ?>');">
				<div class="hero-content">
					<h3><?php echo esc_html($slide['title']); ?></h3>
					<p><?php echo esc_html($slide['subtitle']); ?></p>
					<a href="<?php echo esc_url( home_url('/shop') ); ?>" class="hero-btn">Start Now</a>
				</div>
			</div>
		<?php endforeach; ?>
		
		<!-- Carousel Controls -->
		<button class="carousel-prev" aria-label="Previous slide">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>
		<button class="carousel-next" aria-label="Next slide">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>
		
		<!-- Carousel Dots -->
		<div class="carousel-dots"></div>
	</section>
	
	<!-- Video Section -->
	<section class="ironman-video-section">
		<div class="video-container">
			<h2 class="section-title">Experience IronMan</h2>
			<p class="section-subtitle">Watch Our Journey To Greatness</p>
			
			<div class="video-grid">
				<!-- Video 1 -->
				<div class="video-card">
					<div class="video-wrapper">
						<video controls controlsList="nodownload" poster="<?php echo esc_url( get_template_directory_uri() . '/assets/images/video-poster-1.jpg' ); ?>">
							<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/videos/video-1.mp4' ); ?>" type="video/mp4">
							<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/videos/video-2.mp4
							' ); ?>" type="video/webm">
							Your browser does not support the video tag.
						</video>
					</div>
					<div class="video-info">
						<h3>Transform Your Body</h3>
						<p>Witness real transformations from our members</p>
					</div>
				</div>
				
				<!-- Video 2 -->
				<div class="video-card">
					<div class="video-wrapper">
						<video controls controlsList="nodownload" poster="<?php echo esc_url( get_template_directory_uri() . '/assets/images/video-poster-2.jpg' ); ?>">
							<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/videos/video-2.mp4' ); ?>" type="video/mp4">
							<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/videos/video-2.webm' ); ?>" type="video/webm">
							Your browser does not support the video tag.
						</video>
					</div>
					<div class="video-info">
						<h3>Elite Training Programs</h3>
						<p>See our state-of-the-art facilities and expert trainers</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<!-- Features Section -->
	<section class="welcome-features-section">
		<div class="features-container">
			<h2 class="section-title">Why Choose IronMan?</h2>
			
			<div class="features-grid">
				<div class="feature-card">
					<span class="feature-icon">🏋️</span>
					<h3>Professional Training</h3>
					<p>Customized fitness programs designed by expert trainers to help you achieve your goals.</p>
				</div>
				
				<div class="feature-card">
					<span class="feature-icon">🥗</span>
					<h3>Nutrition Guidance</h3>
					<p>Science-based meal plans and dietary advice to fuel your transformation journey.</p>
				</div>
				
				<div class="feature-card">
					<span class="feature-icon">📊</span>
					<h3>Progress Tracking</h3>
					<p>Real-time monitoring of your training results and performance metrics.</p>
				</div>
				
				<div class="feature-card">
					<span class="feature-icon">💪</span>
					<h3>Expert Coaches</h3>
					<p>Work with certified fitness professionals dedicated to your success and transformation.</p>
				</div>
				
				<div class="feature-card">
					<span class="feature-icon">🏆</span>
					<h3>Proven Results</h3>
					<p>Join thousands of members who have successfully achieved their fitness goals with us.</p>
				</div>
				
				<div class="feature-card">
					<span class="feature-icon">🤝</span>
					<h3>Community Support</h3>
					<p>Be part of a motivating community that pushes you to become the best version of yourself.</p>
				</div>
			</div>
		</div>
	</section>
	
	<!-- Footer Section -->
	<footer class="ironman-footer">
		<div class="footer-container">
			<!-- Footer Top -->
			<div class="footer-top">
				<div class="footer-column">
					<div class="footer-logo">
						<h3>IRONMAN</h3>
						<p class="footer-tagline">Transform Your Body, Empower Your Mind</p>
					</div>
					<p class="footer-description">
						Join the ultimate fitness community and unlock your full potential with professional training, nutrition guidance, and expert support.
					</p>
					<div class="footer-social">
						<a href="#" aria-label="Facebook" class="social-link">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
								<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
							</svg>
						</a>
						<a href="#" aria-label="Instagram" class="social-link">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
								<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
							</svg>
						</a>
						<a href="#" aria-label="YouTube" class="social-link">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
								<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
							</svg>
						</a>
						<a href="#" aria-label="Twitter" class="social-link">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
								<path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
							</svg>
						</a>
					</div>
				</div>
				
				<div class="footer-column">
					<h4>Quick Links</h4>
					<ul class="footer-links">
						<li><a href="<?php echo esc_url( home_url('/') ); ?>">Home</a></li>
						<li><a href="<?php echo esc_url( home_url('/about') ); ?>">About Us</a></li>
						<li><a href="<?php echo esc_url( home_url('/shop') ); ?>">Programs</a></li>
						<li><a href="<?php echo esc_url( home_url('/trainers') ); ?>">Trainers</a></li>
						<li><a href="<?php echo esc_url( home_url('/blog') ); ?>">Blog</a></li>
						<li><a href="<?php echo esc_url( home_url('/contact') ); ?>">Contact</a></li>
					</ul>
				</div>
				
				<div class="footer-column">
					<h4>Services</h4>
					<ul class="footer-links">
						<li><a href="<?php echo esc_url( home_url('/personal-training') ); ?>">Personal Training</a></li>
						<li><a href="<?php echo esc_url( home_url('/group-classes') ); ?>">Group Classes</a></li>
						<li><a href="<?php echo esc_url( home_url('/nutrition') ); ?>">Nutrition Plans</a></li>
						<li><a href="<?php echo esc_url( home_url('/online-coaching') ); ?>">Online Coaching</a></li>
						<li><a href="<?php echo esc_url( home_url('/membership') ); ?>">Membership</a></li>
						<li><a href="<?php echo esc_url( home_url('/store') ); ?>">Store</a></li>
					</ul>
				</div>
				
				<div class="footer-column">
					<h4>Contact Info</h4>
					<ul class="footer-contact">
						<li>
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
								<circle cx="12" cy="10" r="3"></circle>
							</svg>
							<span>123 Fitness Street, Gym City, GC 12345</span>
						</li>
						<li>
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
							</svg>
							<span>+1 (555) 123-4567</span>
						</li>
						<li>
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
								<polyline points="22,6 12,13 2,6"></polyline>
							</svg>
							<span>info@ironmanfitness.com</span>
						</li>
						<li>
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<circle cx="12" cy="12" r="10"></circle>
								<polyline points="12 6 12 12 16 14"></polyline>
							</svg>
							<span>Mon-Fri: 5AM - 11PM<br>Sat-Sun: 7AM - 9PM</span>
						</li>
					</ul>
				</div>
			</div>
			
			<!-- Footer Bottom -->
			<div class="footer-bottom">
				<div class="footer-bottom-content">
					<p class="copyright">&copy; <?php echo date('Y'); ?> IronMan Fitness. All Rights Reserved.</p>
					<ul class="footer-legal">
						<li><a href="<?php echo esc_url( home_url('/privacy-policy') ); ?>">Privacy Policy</a></li>
						<li><a href="<?php echo esc_url( home_url('/terms-of-service') ); ?>">Terms of Service</a></li>
						<li><a href="<?php echo esc_url( home_url('/cookie-policy') ); ?>">Cookie Policy</a></li>
					</ul>
				</div>
			</div>
		</div>
	</footer>
	
	<?php
	return ob_get_clean();
}
add_shortcode( 'welcome', 'ironman_welcome_shortcode' );
