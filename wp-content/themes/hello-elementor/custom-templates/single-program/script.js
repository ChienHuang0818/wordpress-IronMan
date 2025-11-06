/**
 * Single Program Page Scripts
 * Single training program page scripts
 *
 * @package HelloElementor
 * @since 1.0.0
 */

(function ($) {
  "use strict";

  /**
   * Copy link to clipboard
   */
  window.copyToClipboard = function (text) {
    // Create temporary textarea
    const tempInput = document.createElement("textarea");
    tempInput.value = text;
    tempInput.style.position = "fixed";
    tempInput.style.opacity = "0";
    document.body.appendChild(tempInput);

    // Select and copy
    tempInput.select();
    tempInput.setSelectionRange(0, 99999);

    try {
      document.execCommand("copy");
      showNotification("Link copied to clipboard!", "success");
    } catch (err) {
      console.error("Copy failed:", err);
      showNotification("Copy failed, please copy manually", "error");
    }

    // Remove temporary element
    document.body.removeChild(tempInput);
  };

  /**
   * Show notification message
   */
  function showNotification(message, type = "info") {
    // Remove old notifications
    $(".copy-notification").remove();

    // Create new notification
    const notification = $('<div class="copy-notification"></div>')
      .addClass("notification-" + type)
      .text(message)
      .css({
        position: "fixed",
        top: "20px",
        right: "20px",
        padding: "16px 24px",
        background: type === "success" ? "#4caf50" : "#f44336",
        color: "white",
        borderRadius: "8px",
        boxShadow: "0 4px 16px rgba(0,0,0,0.2)",
        zIndex: "10000",
        fontWeight: "600",
        animation: "slideInRight 0.3s ease",
        opacity: "0",
      });

    // Add to page
    $("body").append(notification);

    // Fade in animation
    notification.animate({ opacity: 1 }, 300);

    // Fade out and remove after 3 seconds
    setTimeout(function () {
      notification.animate({ opacity: 0 }, 300, function () {
        $(this).remove();
      });
    }, 3000);
  }

  /**
   * CTA button click event
   */
  $(".cta-button").on("click", function () {
    // Add enrollment logic here
    // For example: open modal, redirect to enrollment page, etc.

    // Example: show notification
    showNotification("Enrollment feature coming soon!", "info");

    // Or redirect to contact page
    // window.location.href = '/contact/';

    // Or open external link
    // window.open('https://example.com/register', '_blank');
  });

  /**
   * Smooth scroll animation
   */
  $('a[href^="#"]').on("click", function (e) {
    const target = $(this.getAttribute("href"));

    if (target.length) {
      e.preventDefault();
      $("html, body")
        .stop()
        .animate(
          {
            scrollTop: target.offset().top - 100,
          },
          800,
          "swing"
        );
    }
  });

  /**
   * Lazy load images (if needed)
   */
  function lazyLoadImages() {
    const images = document.querySelectorAll("img[data-src]");

    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.removeAttribute("data-src");
          imageObserver.unobserve(img);
        }
      });
    });

    images.forEach((img) => imageObserver.observe(img));
  }

  /**
   * Scroll animation effects
   */
  function initScrollAnimations() {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("animate-in");
        }
      });
    }, observerOptions);

    // Add animation to elements
    document.querySelectorAll(".info-card, .related-program-card").forEach((el) => {
      el.style.opacity = "0";
      el.style.transform = "translateY(20px)";
      el.style.transition = "all 0.6s ease";
      observer.observe(el);
    });

    // Add CSS class
    const style = document.createElement("style");
    style.textContent = `
            .animate-in {
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
        `;
    document.head.appendChild(style);
  }

  /**
   * Sticky sidebar
   */
  function initStickySidebar() {
    if (window.innerWidth > 992) {
      const sidebar = $(".program-sidebar");
      const mainContent = $(".program-main-content");

      if (sidebar.length && mainContent.length) {
        const sidebarTop = sidebar.offset().top;
        const sidebarHeight = sidebar.outerHeight();
        const mainContentHeight = mainContent.outerHeight();

        $(window).on("scroll", function () {
          const scrollTop = $(window).scrollTop();
          const windowHeight = $(window).height();

          if (sidebarHeight < mainContentHeight && sidebarHeight < windowHeight) {
            if (scrollTop > sidebarTop - 100) {
              sidebar.css({
                position: "sticky",
                top: "100px",
              });
            } else {
              sidebar.css({
                position: "static",
              });
            }
          }
        });
      }
    }
  }

  /**
   * Reading progress bar
   */
  function initReadingProgress() {
    // Create progress bar
    const progressBar = $('<div class="reading-progress"></div>').css({
      position: "fixed",
      top: "0",
      left: "0",
      width: "0%",
      height: "4px",
      background: "linear-gradient(90deg, #667eea 0%, #764ba2 100%)",
      zIndex: "9999",
      transition: "width 0.2s ease",
    });

    $("body").prepend(progressBar);

    // Update progress
    $(window).on("scroll", function () {
      const windowHeight = $(window).height();
      const documentHeight = $(document).height();
      const scrollTop = $(window).scrollTop();

      const progress = (scrollTop / (documentHeight - windowHeight)) * 100;
      progressBar.css("width", progress + "%");
    });
  }

  /**
   * Social share tracking
   */
  $(".share-btn").on("click", function () {
    const platform = $(this).hasClass("share-facebook")
      ? "Facebook"
      : $(this).hasClass("share-twitter")
      ? "Twitter"
      : "Link";

    // If using Google Analytics
    if (typeof gtag !== "undefined") {
      gtag("event", "share", {
        event_category: "Social",
        event_label: platform,
        value: 1,
      });
    }

    console.log("Share to:", platform);
  });

  /**
   * Image click to enlarge
   */
  function initImageLightbox() {
    $(".program-content img").on("click", function () {
      const src = $(this).attr("src");
      const alt = $(this).attr("alt") || "";

      // Create lightbox
      const lightbox = $(`
                <div class="image-lightbox" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0,0,0,0.9);
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                ">
                    <img src="${src}" alt="${alt}" style="
                        max-width: 90%;
                        max-height: 90%;
                        border-radius: 8px;
                        box-shadow: 0 8px 32px rgba(0,0,0,0.5);
                    ">
                    <button style="
                        position: absolute;
                        top: 20px;
                        right: 20px;
                        background: white;
                        border: none;
                        border-radius: 50%;
                        width: 40px;
                        height: 40px;
                        font-size: 24px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    ">×</button>
                </div>
            `);

      $("body").append(lightbox);
      lightbox.hide().fadeIn(300);

      lightbox.on("click", function () {
        $(this).fadeOut(300, function () {
          $(this).remove();
        });
      });
    });
  }

  /**
   * Initialize all features
   */
  $(document).ready(function () {
    // Initialize scroll animations
    initScrollAnimations();

    // Initialize sticky sidebar
    initStickySidebar();

    // Initialize reading progress bar
    initReadingProgress();

    // Initialize image lightbox
    initImageLightbox();

    // Lazy load images (if needed)
    if ("IntersectionObserver" in window) {
      lazyLoadImages();
    }

    console.log("Single Program page initialized");
  });
})(jQuery);
