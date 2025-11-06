/**
 * Trainer List JavaScript
 * Trainer list interactive functionality
 *
 * @package HelloElementor
 * @since 1.0.0
 */

(function ($) {
  "use strict";

  /**
   * Trainer List Class
   */
  class TrainerList {
    constructor() {
      this.container = $(".trainer-list-container");
      this.trainerItems = $(".trainer-item");

      if (this.container.length === 0) {
        return;
      }

      this.init();
    }

    /**
     * Initialize
     */
    init() {
      this.setupLazyLoading();
      this.setupFilterAnimation();
      this.setupSocialLinks();
      this.setupAccessibility();

      console.log("Trainer List initialized");
    }

    /**
     * Setup Lazy Loading
     */
    setupLazyLoading() {
      if ("IntersectionObserver" in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              const img = entry.target;
              if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute("data-src");
                observer.unobserve(img);
              }
            }
          });
        });

        this.trainerItems.find("img[data-src]").each(function () {
          imageObserver.observe(this);
        });
      }
    }

    /**
     * Setup Filter Animation
     */
    setupFilterAnimation() {
      // Filter functionality can be added here
      this.trainerItems.each((index, item) => {
        $(item).css({
          opacity: "0",
          transform: "translateY(30px)",
        });

        setTimeout(() => {
          $(item).css({
            opacity: "1",
            transform: "translateY(0)",
            transition: "all 0.6s ease",
          });
        }, 100 * index);
      });
    }

    /**
     * Setup Social Links
     */
    setupSocialLinks() {
      $(".social-link").on("click", function (e) {
        // Ensure external links open in new window
        const href = $(this).attr("href");
        if (href && href.startsWith("http")) {
          e.preventDefault();
          window.open(href, "_blank", "noopener,noreferrer");
        }
      });
    }

    /**
     * Setup Accessibility
     */
    setupAccessibility() {
      // Keyboard navigation support
      this.trainerItems.find("a, button").on("keydown", function (e) {
        if (e.key === "Enter" || e.key === " ") {
          $(this).click();
        }
      });

      // Add alt attribute to images (if missing)
      this.trainerItems.find("img:not([alt])").attr("alt", "Trainer photo");
    }
  }

  /**
   * Trainer Filter (Optional Feature)
   */
  class TrainerFilter {
    constructor() {
      this.filterButtons = $(".trainer-filter-btn");
      this.trainerItems = $(".trainer-item");

      if (this.filterButtons.length === 0) {
        return;
      }

      this.init();
    }

    init() {
      this.filterButtons.on("click", (e) => this.handleFilter(e));
    }

    handleFilter(e) {
      e.preventDefault();
      const button = $(e.currentTarget);
      const specialty = button.data("specialty");

      // Update button state
      this.filterButtons.removeClass("active");
      button.addClass("active");

      // Filter trainers
      if (specialty === "all") {
        this.showAll();
      } else {
        this.filterBySpecialty(specialty);
      }
    }

    showAll() {
      this.trainerItems.fadeIn(300);
    }

    filterBySpecialty(specialty) {
      this.trainerItems.each(function () {
        const item = $(this);
        const specialties = item
          .find(".specialty-badge")
          .map(function () {
            return $(this).text().toLowerCase();
          })
          .get();

        if (specialties.includes(specialty.toLowerCase())) {
          item.fadeIn(300);
        } else {
          item.fadeOut(300);
        }
      });
    }
  }

  /**
   * Trainer Search Functionality
   */
  class TrainerSearch {
    constructor() {
      this.searchInput = $("#trainer-search");
      this.trainerItems = $(".trainer-item");

      if (this.searchInput.length === 0) {
        return;
      }

      this.init();
    }

    init() {
      let searchTimeout;

      this.searchInput.on("input", (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          this.performSearch(e.target.value);
        }, 300);
      });
    }

    performSearch(query) {
      const searchTerm = query.toLowerCase().trim();

      if (searchTerm === "") {
        this.trainerItems.fadeIn(300);
        return;
      }

      this.trainerItems.each(function () {
        const item = $(this);
        const name = item.find(".trainer-name").text().toLowerCase();
        const excerpt = item.find(".trainer-excerpt").text().toLowerCase();
        const specialties = item
          .find(".specialty-badge")
          .map(function () {
            return $(this).text().toLowerCase();
          })
          .get()
          .join(" ");

        const searchableText = `${name} ${excerpt} ${specialties}`;

        if (searchableText.includes(searchTerm)) {
          item.fadeIn(300);
        } else {
          item.fadeOut(300);
        }
      });
    }
  }

  /**
   * Enhanced Trainer Card Hover Effects
   */
  class TrainerCardEffects {
    constructor() {
      this.trainerItems = $(".trainer-item");

      if (this.trainerItems.length === 0) {
        return;
      }

      this.init();
    }

    init() {
      this.setupParallaxEffect();
      this.setupClickTracking();
    }

    /**
     * Parallax Effect (Optional)
     */
    setupParallaxEffect() {
      this.trainerItems.on("mousemove", function (e) {
        const card = $(this);
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotateX = (y - centerY) / 20;
        const rotateY = (centerX - x) / 20;

        card.css({
          transform: `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px)`,
        });
      });

      this.trainerItems.on("mouseleave", function () {
        $(this).css({
          transform: "perspective(1000px) rotateX(0) rotateY(0) translateY(0)",
        });
      });
    }

    /**
     * Click Tracking (for Analytics)
     */
    setupClickTracking() {
      this.trainerItems.find("a").on("click", function () {
        const trainerId = $(this).closest(".trainer-item").data("trainer-id");
        const trainerName = $(this).closest(".trainer-item").find(".trainer-name").text().trim();

        // Google Analytics or other tracking code can be added here
        console.log("Trainer clicked:", trainerId, trainerName);

        // Example: Google Analytics event
        if (typeof gtag !== "undefined") {
          gtag("event", "trainer_view", {
            trainer_id: trainerId,
            trainer_name: trainerName,
          });
        }
      });
    }
  }

  /**
   * AJAX Load More Functionality (Optional)
   */
  class TrainerLoadMore {
    constructor() {
      this.loadMoreBtn = $("#trainer-load-more");
      this.container = $(".trainer-list-grid");
      this.currentPage = 1;
      this.isLoading = false;

      if (this.loadMoreBtn.length === 0) {
        return;
      }

      this.init();
    }

    init() {
      this.loadMoreBtn.on("click", (e) => this.loadMore(e));
    }

    loadMore(e) {
      e.preventDefault();

      if (this.isLoading) {
        return;
      }

      this.isLoading = true;
      this.loadMoreBtn.text("Loading...").prop("disabled", true);

      $.ajax({
        url: TrainerListConfig.ajaxUrl,
        type: "POST",
        data: {
          action: "load_more_trainers",
          nonce: TrainerListConfig.nonce,
          page: this.currentPage + 1,
        },
        success: (response) => {
          if (response.success) {
            this.container.append(response.data.html);
            this.currentPage++;

            if (!response.data.has_more) {
              this.loadMoreBtn.remove();
            }

            // Reinitialize newly loaded items
            new TrainerList();
          } else {
            alert("Failed to load, please try again later.");
          }
        },
        error: () => {
          alert("An error occurred, please try again later.");
        },
        complete: () => {
          this.isLoading = false;
          this.loadMoreBtn.text("Load More").prop("disabled", false);
        },
      });
    }
  }

  /**
   * Initialize on Document Ready
   */
  $(document).ready(function () {
    new TrainerList();
    new TrainerFilter();
    new TrainerSearch();
    new TrainerCardEffects();
    new TrainerLoadMore();
  });

  /**
   * Optimization After Window Load
   */
  $(window).on("load", function () {
    // Remove initial loading animation
    $(".trainer-item").css("animation", "none");
  });
})(jQuery);
