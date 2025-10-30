/**
 * Program List JavaScript
 * 训练项目列表交互功能
 *
 * @package HelloElementor
 * @since 1.0.0
 */

(function ($) {
  "use strict";

  // 当 DOM 准备好时执行
  $(document).ready(function () {
    // 初始化课程列表
    initProgramList();

    // 添加筛选功能（如果需要）
    initProgramFilters();

    // 添加加载更多功能（如果需要）
    initLoadMore();
  });

  /**
   * 初始化课程列表
   */
  function initProgramList() {
    const $programItems = $(".program-item");

    // 添加淡入动画（备用方案，CSS 已有动画）
    $programItems.each(function (index) {
      const $item = $(this);

      // 懒加载图片（可选）
      const $img = $item.find(".program-thumbnail img");
      if ($img.length && $img.attr("data-src")) {
        loadImage($img);
      }

      // 添加点击统计（可选）
      $item.find(".program-button, .program-title a").on("click", function () {
        trackProgramClick($item.data("program-id"));
      });
    });

    // 添加滚动动画效果
    observePrograms();
  }

  /**
   * 初始化筛选功能
   */
  function initProgramFilters() {
    // 难度筛选
    $(".program-filter-difficulty").on("click", function (e) {
      e.preventDefault();
      const difficulty = $(this).data("difficulty");
      filterByDifficulty(difficulty);

      // 更新活动状态
      $(".program-filter-difficulty").removeClass("active");
      $(this).addClass("active");
    });

    // 分类筛选
    $(".program-filter-category").on("click", function (e) {
      e.preventDefault();
      const category = $(this).data("category");
      filterByCategory(category);

      // 更新活动状态
      $(".program-filter-category").removeClass("active");
      $(this).addClass("active");
    });

    // 重置筛选
    $(".program-filter-reset").on("click", function (e) {
      e.preventDefault();
      resetFilters();
    });
  }

  /**
   * 按难度筛选
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
   * 按分类筛选
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
   * 重置筛选
   */
  function resetFilters() {
    $(".program-item").fadeIn(300);
    $(".program-filter-difficulty, .program-filter-category").removeClass("active");
    $('.program-filter-difficulty[data-difficulty="all"]').addClass("active");
  }

  /**
   * 初始化加载更多功能
   */
  function initLoadMore() {
    const $loadMoreBtn = $(".program-load-more");

    if ($loadMoreBtn.length === 0) return;

    $loadMoreBtn.on("click", function (e) {
      e.preventDefault();

      const $btn = $(this);
      const page = parseInt($btn.data("page")) || 1;
      const nextPage = page + 1;

      // 显示加载状态
      $btn.addClass("loading").text("加载中...");

      // AJAX 请求
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
            // 添加新项目
            const $newItems = $(response.data.html);
            $(".program-list-grid").append($newItems);

            // 更新页码
            $btn.data("page", nextPage);

            // 如果没有更多了
            if (!response.data.has_more) {
              $btn.text("没有更多了").prop("disabled", true);
            } else {
              $btn.removeClass("loading").text("加载更多");
            }

            // 初始化新项目
            observePrograms();
          } else {
            $btn.text("加载失败").prop("disabled", true);
          }
        },
        error: function () {
          $btn.removeClass("loading").text("加载失败");
        },
      });
    });
  }

  /**
   * 观察程序项目（Intersection Observer）
   */
  function observePrograms() {
    // 检查浏览器支持
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

    // 观察所有项目
    $(".program-item").each(function () {
      observer.observe(this);
    });
  }

  /**
   * 懒加载图片
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
   * 追踪项目点击（用于分析）
   */
  function trackProgramClick(programId) {
    if (!programId) return;

    // 发送到 Google Analytics（如果已安装）
    if (typeof gtag !== "undefined") {
      gtag("event", "program_click", {
        event_category: "Programs",
        event_label: "Program ID: " + programId,
        value: programId,
      });
    }

    // 发送到自定义追踪端点（可选）
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
   * 平滑滚动到元素
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
   * 添加到收藏（如果需要）
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
          alert("已添加到收藏！");
        }
      },
    });
  }

  /**
   * 搜索功能
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
   * 搜索项目
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
