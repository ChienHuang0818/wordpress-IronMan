/**
 * Trainer List JavaScript
 * 教练列表交互功能
 *
 * @package HelloElementor
 * @since 1.0.0
 */

(function ($) {
  "use strict";

  /**
   * 教练列表类
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
     * 初始化
     */
    init() {
      this.setupLazyLoading();
      this.setupFilterAnimation();
      this.setupSocialLinks();
      this.setupAccessibility();

      console.log("Trainer List initialized");
    }

    /**
     * 设置懒加载
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
     * 设置筛选动画
     */
    setupFilterAnimation() {
      // 可以在此添加筛选功能
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
     * 设置社交链接
     */
    setupSocialLinks() {
      $(".social-link").on("click", function (e) {
        // 确保外部链接在新窗口打开
        const href = $(this).attr("href");
        if (href && href.startsWith("http")) {
          e.preventDefault();
          window.open(href, "_blank", "noopener,noreferrer");
        }
      });
    }

    /**
     * 设置无障碍功能
     */
    setupAccessibility() {
      // 键盘导航支持
      this.trainerItems.find("a, button").on("keydown", function (e) {
        if (e.key === "Enter" || e.key === " ") {
          $(this).click();
        }
      });

      // 为图片添加 alt 属性（如果缺失）
      this.trainerItems.find("img:not([alt])").attr("alt", "教练照片");
    }
  }

  /**
   * 教练筛选器（可选功能）
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

      // 更新按钮状态
      this.filterButtons.removeClass("active");
      button.addClass("active");

      // 筛选教练
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
   * 教练搜索功能
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
   * 教练卡片悬停效果增强
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
     * 视差效果（可选）
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
     * 点击追踪（用于分析）
     */
    setupClickTracking() {
      this.trainerItems.find("a").on("click", function () {
        const trainerId = $(this).closest(".trainer-item").data("trainer-id");
        const trainerName = $(this).closest(".trainer-item").find(".trainer-name").text().trim();

        // 可以在此添加 Google Analytics 或其他追踪代码
        console.log("Trainer clicked:", trainerId, trainerName);

        // 示例：Google Analytics 事件
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
   * AJAX 加载更多功能（可选）
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
      this.loadMoreBtn.text("加载中...").prop("disabled", true);

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

            // 重新初始化新加载的项目
            new TrainerList();
          } else {
            alert("加载失败，请稍后再试。");
          }
        },
        error: () => {
          alert("发生错误，请稍后再试。");
        },
        complete: () => {
          this.isLoading = false;
          this.loadMoreBtn.text("加载更多").prop("disabled", false);
        },
      });
    }
  }

  /**
   * 文档就绪时初始化
   */
  $(document).ready(function () {
    new TrainerList();
    new TrainerFilter();
    new TrainerSearch();
    new TrainerCardEffects();
    new TrainerLoadMore();
  });

  /**
   * 窗口加载完成后的优化
   */
  $(window).on("load", function () {
    // 移除初始加载动画
    $(".trainer-item").css("animation", "none");
  });
})(jQuery);
