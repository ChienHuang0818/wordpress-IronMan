/**
 * Program List JavaScript
 * Training program list interactive functionality
 *
 * @package HelloElementor
 * @since 1.0.0
 */

(function ($) {
  "use strict";

  // Execute when DOM is ready
  $(document).ready(function () {
    // Initialize program list
    initProgramList();

    // Add filter functionality (if needed)
    initProgramFilters();

    // Add load more functionality (if needed)
    initLoadMore();
  });

  /**
   * Initialize program list
   */
  function initProgramList() {
    const $programItems = $(".program-item");

    // Add fade-in animation (fallback, CSS already has animation)
    $programItems.each(function (index) {
      const $item = $(this);

      // Lazy load images (optional)
      const $img = $item.find(".program-thumbnail img");
      if ($img.length && $img.attr("data-src")) {
        loadImage($img);
      }

      // Add click tracking (optional)
      $item.find(".program-button, .program-title a").on("click", function () {
        trackProgramClick($item.data("program-id"));
      });
    });

    // Add scroll animation effect
    observePrograms();
  }

  /**
   * Initialize filter functionality
   */
  function initProgramFilters() {
    // Difficulty filter
    $(".program-filter-difficulty").on("click", function (e) {
      e.preventDefault();
      const difficulty = $(this).data("difficulty");
      filterByDifficulty(difficulty);

      // Update active state
      $(".program-filter-difficulty").removeClass("active");
      $(this).addClass("active");
    });

    // Category filter
    $(".program-filter-category").on("click", function (e) {
      e.preventDefault();
      const category = $(this).data("category");
      filterByCategory(category);

      // Update active state
      $(".program-filter-category").removeClass("active");
      $(this).addClass("active");
    });

    // Reset filters
    $(".program-filter-reset").on("click", function (e) {
      e.preventDefault();
      resetFilters();
    });
  }

  /**
   * Filter by difficulty
   */
  function filterByDifficulty(difficulty) {
    const $programItems = $(".program-item");

    if (difficulty === "all") {
      $programItems.fadeIn(300);
      return;
    }

    $programItems.each(function () {
      const $item = $(this);
      const itemDifficulty = $item.data("difficulty");

      if (itemDifficulty === difficulty) {
        $item.fadeIn(300);
      } else {
        $item.fadeOut(300);
      }
    });
  }

  /**
   * Filter by category
   */
  function filterByCategory(category) {
    const $programItems = $(".program-item");

    if (category === "all") {
      $programItems.fadeIn(300);
      return;
    }

    $programItems.each(function () {
      const $item = $(this);
      const itemCategory = $item.data("category");

      if (itemCategory === category) {
        $item.fadeIn(300);
      } else {
        $item.fadeOut(300);
      }
    });
  }

  /**
   * Reset filters
   */
  function resetFilters() {
    $(".program-item").fadeIn(300);
    $(".program-filter-difficulty, .program-filter-category").removeClass("active");
    $('.program-filter-difficulty[data-difficulty="all"]').addClass("active");
  }

  /**
   * Initialize load more functionality
   */
  function initLoadMore() {
    const $loadMoreBtn = $(".program-load-more");

    if ($loadMoreBtn.length === 0) return;

    $loadMoreBtn.on("click", function (e) {
      e.preventDefault();

      const $btn = $(this);
      const page = parseInt($btn.data("page")) || 1;
      const nextPage = page + 1;

      // Show loading state
      $btn.addClass("loading").text("Loading...");

      // AJAX request
      $.ajax({
        url: ProgramListConfig.ajaxUrl,
        type: "POST",
        data: {
          action: "load_more_programs",
          page: nextPage,
          nonce: ProgramListConfig.nonce,
        },
        success: function (response) {
          if (response.success && response.data.html) {
            // Add new items
            const $newItems = $(response.data.html);
            $(".program-list-grid").append($newItems);

            // Update page number
            $btn.data("page", nextPage);

            // If no more items
            if (!response.data.has_more) {
              $btn.text("No More Programs").prop("disabled", true);
            } else {
              $btn.removeClass("loading").text("Load More");
            }

            // Initialize new items
            observePrograms();
          } else {
            $btn.text("Load Failed").prop("disabled", true);
          }
        },
        error: function () {
          $btn.removeClass("loading").text("Load Failed");
        },
      });
    });
  }

  /**
   * Observe program items (Intersection Observer)
   */
  function observePrograms() {
    // Check browser support
    if (!("IntersectionObserver" in window)) return;

    const options = {
      root: null,
      rootMargin: "0px",
      threshold: 0.1,
    };

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
        }
      });
    }, options);

    // Observe all items
    $(".program-item").each(function () {
      observer.observe(this);
    });
  }

  /**
   * Lazy load images
   */
  function loadImage($img) {
    const src = $img.attr("data-src");
    if (!src) return;

    const img = new Image();
    img.onload = function () {
      $img.attr("src", src).removeAttr("data-src").addClass("loaded");
    };
    img.src = src;
  }

  /**
   * Track program clicks (for analytics)
   */
  function trackProgramClick(programId) {
    if (!programId) return;

    // Send to Google Analytics (if installed)
    if (typeof gtag !== "undefined") {
      gtag("event", "program_click", {
        event_category: "Programs",
        event_label: "Program ID: " + programId,
        value: programId,
      });
    }

    // Send to custom tracking endpoint (optional)
    $.ajax({
      url: ProgramListConfig.ajaxUrl,
      type: "POST",
      data: {
        action: "track_program_click",
        program_id: programId,
        nonce: ProgramListConfig.nonce,
      },
    });
  }

  /**
   * Smooth scroll to element
   */
  function smoothScrollTo($element, offset = 100) {
    if (!$element.length) return;

    $("html, body").animate(
      {
        scrollTop: $element.offset().top - offset,
      },
      600
    );
  }

  /**
   * Add to favorites (if needed)
   */
  function addToFavorites(programId) {
    $.ajax({
      url: ProgramListConfig.ajaxUrl,
      type: "POST",
      data: {
        action: "add_program_to_favorites",
        program_id: programId,
        nonce: ProgramListConfig.nonce,
      },
      success: function (response) {
        if (response.success) {
          alert("Added to favorites!");
        }
      },
    });
  }

  /**
   * Search functionality
   */
  function initSearch() {
    const $searchInput = $(".program-search-input");

    if ($searchInput.length === 0) return;

    let searchTimeout;

    $searchInput.on("input", function () {
      clearTimeout(searchTimeout);

      const query = $(this).val().toLowerCase();

      searchTimeout = setTimeout(function () {
        searchPrograms(query);
      }, 300);
    });
  }

  /**
   * Search programs
   */
  function searchPrograms(query) {
    const $programItems = $(".program-item");

    if (query === "") {
      $programItems.fadeIn(300);
      return;
    }

    $programItems.each(function () {
      const $item = $(this);
      const title = $item.find(".program-title").text().toLowerCase();
      const excerpt = $item.find(".program-excerpt").text().toLowerCase();

      if (title.includes(query) || excerpt.includes(query)) {
        $item.fadeIn(300);
      } else {
        $item.fadeOut(300);
      }
    });
  }
})(jQuery);
