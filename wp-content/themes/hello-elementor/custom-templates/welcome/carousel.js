/**
 * IronMan Hero Carousel
 */
document.addEventListener("DOMContentLoaded", function () {
  const carousel = document.querySelector(".hero-carousel");
  if (!carousel) return;

  const slides = carousel.querySelectorAll(".hero-slide");
  const dotsContainer = carousel.querySelector(".carousel-dots");
  const prevBtn = carousel.querySelector(".carousel-prev");
  const nextBtn = carousel.querySelector(".carousel-next");

  let currentSlide = 0;
  let autoplayInterval;
  const autoplayDelay = 5000; // 5 seconds

  // Create dots
  slides.forEach((_, index) => {
    const dot = document.createElement("button");
    dot.classList.add("carousel-dot");
    dot.setAttribute("aria-label", `Go to slide ${index + 1}`);
    if (index === 0) dot.classList.add("active");
    dot.addEventListener("click", () => goToSlide(index));
    dotsContainer.appendChild(dot);
  });

  const dots = dotsContainer.querySelectorAll(".carousel-dot");

  // Show slide function
  function showSlide(index) {
    // Wrap around
    if (index >= slides.length) {
      currentSlide = 0;
    } else if (index < 0) {
      currentSlide = slides.length - 1;
    } else {
      currentSlide = index;
    }

    // Update slides
    slides.forEach((slide, i) => {
      slide.classList.remove("active");
      if (i === currentSlide) {
        slide.classList.add("active");
      }
    });

    // Update dots
    dots.forEach((dot, i) => {
      dot.classList.remove("active");
      if (i === currentSlide) {
        dot.classList.add("active");
      }
    });
  }

  // Go to specific slide
  function goToSlide(index) {
    showSlide(index);
    resetAutoplay();
  }

  // Next slide
  function nextSlide() {
    showSlide(currentSlide + 1);
  }

  // Previous slide
  function prevSlide() {
    showSlide(currentSlide - 1);
  }

  // Autoplay
  function startAutoplay() {
    autoplayInterval = setInterval(nextSlide, autoplayDelay);
  }

  function stopAutoplay() {
    if (autoplayInterval) {
      clearInterval(autoplayInterval);
    }
  }

  function resetAutoplay() {
    stopAutoplay();
    startAutoplay();
  }

  // Event listeners
  if (prevBtn) {
    prevBtn.addEventListener("click", () => {
      prevSlide();
      resetAutoplay();
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      nextSlide();
      resetAutoplay();
    });
  }

  // Pause on hover
  carousel.addEventListener("mouseenter", stopAutoplay);
  carousel.addEventListener("mouseleave", startAutoplay);

  // Keyboard navigation
  document.addEventListener("keydown", (e) => {
    if (e.key === "ArrowLeft") {
      prevSlide();
      resetAutoplay();
    } else if (e.key === "ArrowRight") {
      nextSlide();
      resetAutoplay();
    }
  });

  // Touch support for mobile
  let touchStartX = 0;
  let touchEndX = 0;

  carousel.addEventListener("touchstart", (e) => {
    touchStartX = e.changedTouches[0].screenX;
    stopAutoplay();
  });

  carousel.addEventListener("touchend", (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
    startAutoplay();
  });

  function handleSwipe() {
    if (touchEndX < touchStartX - 50) {
      nextSlide();
    }
    if (touchEndX > touchStartX + 50) {
      prevSlide();
    }
  }

  // Start autoplay
  startAutoplay();

  // ============================================
  // Features Carousel
  // ============================================
  const featuresCarousel = document.querySelector(".features-carousel");
  if (featuresCarousel) {
    const featureCards = featuresCarousel.querySelectorAll(".feature-card");
    const featuresDotsContainer = featuresCarousel.querySelector(".features-carousel-dots");
    const featuresPrevBtn = featuresCarousel.querySelector(".features-carousel-prev");
    const featuresNextBtn = featuresCarousel.querySelector(".features-carousel-next");

    let currentFeature = 0;
    let featuresAutoplayInterval;
    const featuresAutoplayDelay = 4000; // 4 seconds

    // Create dots for features
    featureCards.forEach((_, index) => {
      const dot = document.createElement("button");
      dot.classList.add("carousel-dot");
      dot.setAttribute("aria-label", `Go to feature ${index + 1}`);
      if (index === 0) dot.classList.add("active");
      dot.addEventListener("click", () => goToFeature(index));
      featuresDotsContainer.appendChild(dot);
    });

    const featuresDots = featuresDotsContainer.querySelectorAll(".carousel-dot");

    // Show feature function
    function showFeature(index) {
      if (index >= featureCards.length) {
        currentFeature = 0;
      } else if (index < 0) {
        currentFeature = featureCards.length - 1;
      } else {
        currentFeature = index;
      }

      featureCards.forEach((card, i) => {
        card.classList.remove("active");
        if (i === currentFeature) {
          card.classList.add("active");
        }
      });

      featuresDots.forEach((dot, i) => {
        dot.classList.remove("active");
        if (i === currentFeature) {
          dot.classList.add("active");
        }
      });
    }

    function goToFeature(index) {
      showFeature(index);
      resetFeaturesAutoplay();
    }

    function nextFeature() {
      showFeature(currentFeature + 1);
    }

    function prevFeature() {
      showFeature(currentFeature - 1);
    }

    function startFeaturesAutoplay() {
      featuresAutoplayInterval = setInterval(nextFeature, featuresAutoplayDelay);
    }

    function stopFeaturesAutoplay() {
      if (featuresAutoplayInterval) {
        clearInterval(featuresAutoplayInterval);
      }
    }

    function resetFeaturesAutoplay() {
      stopFeaturesAutoplay();
      startFeaturesAutoplay();
    }

    // Event listeners for features
    if (featuresPrevBtn) {
      featuresPrevBtn.addEventListener("click", () => {
        prevFeature();
        resetFeaturesAutoplay();
      });
    }

    if (featuresNextBtn) {
      featuresNextBtn.addEventListener("click", () => {
        nextFeature();
        resetFeaturesAutoplay();
      });
    }

    // Pause on hover
    featuresCarousel.addEventListener("mouseenter", stopFeaturesAutoplay);
    featuresCarousel.addEventListener("mouseleave", startFeaturesAutoplay);

    // Touch support for features
    let featuresTouchStartX = 0;
    let featuresTouchEndX = 0;

    featuresCarousel.addEventListener("touchstart", (e) => {
      featuresTouchStartX = e.changedTouches[0].screenX;
      stopFeaturesAutoplay();
    });

    featuresCarousel.addEventListener("touchend", (e) => {
      featuresTouchEndX = e.changedTouches[0].screenX;
      handleFeaturesSwipe();
      startFeaturesAutoplay();
    });

    function handleFeaturesSwipe() {
      if (featuresTouchEndX < featuresTouchStartX - 50) {
        nextFeature();
      }
      if (featuresTouchEndX > featuresTouchStartX + 50) {
        prevFeature();
      }
    }

    // Start features autoplay
    startFeaturesAutoplay();
  }
});
