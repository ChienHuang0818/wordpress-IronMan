/**
 * Single Trainer Page Scripts
 * 教练详情页交互脚本
 *
 * @package HelloElementor
 * @since 1.0.0
 */

(function ($) {
  "use strict";

  // ============================================
  // 页面加载完成后初始化
  // ============================================
  $(document).ready(function () {
    initScrollIndicator();
    initSmoothScroll();
    initScrollAnimations();
    initStickyElements();
    initReadingProgress();
    initSocialShare();
    initCopyLink();
  });

  // ============================================
  // 向下滚动指示器
  // ============================================
  function initScrollIndicator() {
    $(".scroll-indicator").on("click", function () {
      const targetPosition = $(".trainer-main-content").offset().top;
      $("html, body").animate(
        {
          scrollTop: targetPosition - 20,
        },
        800,
        "swing"
      );
    });

    // 滚动时隐藏指示器
    $(window).on("scroll", function () {
      if ($(window).scrollTop() > 100) {
        $(".scroll-indicator").fadeOut();
      } else {
        $(".scroll-indicator").fadeIn();
      }
    });
  }

  // ============================================
  // 平滑滚动
  // ============================================
  function initSmoothScroll() {
    $('a[href^="#"]').on("click", function (e) {
      const href = $(this).attr("href");
      if (href === "#" || href === "") return;

      const target = $(href);
      if (target.length) {
        e.preventDefault();
        $("html, body").animate(
          {
            scrollTop: target.offset().top - 80,
          },
          600,
          "swing"
        );
      }
    });
  }

  // ============================================
  // 滚动显示动画
  // ============================================
  function initScrollAnimations() {
    // 为需要动画的元素添加 reveal 类
    const animateElements = [
      ".info-card",
      ".trainer-description",
      ".related-trainer-card",
      ".sidebar-widget",
    ];

    animateElements.forEach((selector) => {
      $(selector).addClass("reveal");
    });

    // 使用 Intersection Observer API
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("active");
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    document.querySelectorAll(".reveal").forEach((element) => {
      observer.observe(element);
    });
  }

  // ============================================
  // 固定侧边栏（粘性定位）
  // ============================================
  function initStickyElements() {
    if ($(window).width() > 992) {
      const sidebar = $(".trainer-sidebar");
      if (!sidebar.length) return;

      const sidebarTop = sidebar.offset().top - 100;
      const contentArea = $(".content-area");
      const contentBottom = contentArea.offset().top + contentArea.outerHeight();

      $(window).on("scroll", function () {
        const scrollTop = $(window).scrollTop();

        if (scrollTop > sidebarTop && scrollTop < contentBottom - sidebar.outerHeight() - 200) {
          sidebar.css({
            position: "sticky",
            top: "100px",
          });
        } else {
          sidebar.css({
            position: "relative",
            top: "auto",
          });
        }
      });
    }
  }

  // ============================================
  // 阅读进度条
  // ============================================
  function initReadingProgress() {
    const progressBar = $(".reading-progress-bar");
    if (!progressBar.length) return;

    $(window).on("scroll", function () {
      const windowHeight = $(window).height();
      const documentHeight = $(document).height();
      const scrollTop = $(window).scrollTop();

      const scrollPercent = (scrollTop / (documentHeight - windowHeight)) * 100;
      progressBar.css("width", scrollPercent + "%");
    });
  }

  // ============================================
  // 社交分享功能
  // ============================================
  function initSocialShare() {
    const pageUrl = encodeURIComponent(window.location.href);
    const pageTitle = encodeURIComponent(document.title);

    // Facebook 分享
    $(".share-facebook").on("click", function (e) {
      e.preventDefault();
      const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`;
      openShareWindow(shareUrl, "Facebook");
      trackShare("facebook");
    });

    // Twitter 分享
    $(".share-twitter").on("click", function (e) {
      e.preventDefault();
      const shareUrl = `https://twitter.com/intent/tweet?url=${pageUrl}&text=${pageTitle}`;
      openShareWindow(shareUrl, "Twitter");
      trackShare("twitter");
    });

    // 社交链接点击追踪
    $(".social-link").on("click", function () {
      const platform = $(this).data("platform");
      if (platform) {
        console.log("Social link clicked:", platform);
        trackShare(platform);
      }
    });
  }

  // ============================================
  // 打开分享窗口
  // ============================================
  function openShareWindow(url, title) {
    const width = 600;
    const height = 400;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;

    window.open(
      url,
      title,
      `width=${width},height=${height},left=${left},top=${top},toolbar=0,status=0`
    );
  }

  // ============================================
  // 追踪分享（可集成分析工具）
  // ============================================
  function trackShare(platform) {
    // 如果有 Google Analytics
    if (typeof gtag !== "undefined") {
      gtag("event", "share", {
        event_category: "Social",
        event_label: platform,
        page_path: window.location.pathname,
      });
    }

    // 如果有其他分析工具，在这里添加
    console.log(`Shared on ${platform}`);
  }

  // ============================================
  // 复制链接功能
  // ============================================
  function initCopyLink() {
    $(".share-copy").on("click", function (e) {
      e.preventDefault();
      const url = $(this).data("url") || window.location.href;

      // 使用现代 Clipboard API
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard
          .writeText(url)
          .then(function () {
            showCopyNotification("链接已复制到剪贴板！");
            trackShare("copy-link");
          })
          .catch(function (err) {
            console.error("复制失败:", err);
            fallbackCopyToClipboard(url);
          });
      } else {
        // 降级方案
        fallbackCopyToClipboard(url);
      }
    });
  }

  // ============================================
  // 复制到剪贴板的降级方案
  // ============================================
  function fallbackCopyToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-9999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
      const successful = document.execCommand("copy");
      if (successful) {
        showCopyNotification("链接已复制到剪贴板！");
        trackShare("copy-link");
      } else {
        showCopyNotification("复制失败，请手动复制链接", "error");
      }
    } catch (err) {
      console.error("复制失败:", err);
      showCopyNotification("复制失败，请手动复制链接", "error");
    }

    document.body.removeChild(textArea);
  }

  // ============================================
  // 显示复制通知
  // ============================================
  function showCopyNotification(message, type = "success") {
    // 移除已存在的通知
    $(".copy-notification").remove();

    const bgColor = type === "success" ? "#e63946" : "#666";
    const notification = $(`
            <div class="copy-notification" style="
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: ${bgColor};
                color: white;
                padding: 15px 25px;
                border-radius: 10px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
                z-index: 10000;
                font-size: 14px;
                font-weight: 500;
                animation: slideInUp 0.3s ease;
            ">
                ${message}
            </div>
        `);

    // 添加动画样式
    if (!$("#copy-notification-styles").length) {
      $("head").append(`
                <style id="copy-notification-styles">
                    @keyframes slideInUp {
                        from {
                            opacity: 0;
                            transform: translateY(20px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                    @keyframes slideOutDown {
                        from {
                            opacity: 1;
                            transform: translateY(0);
                        }
                        to {
                            opacity: 0;
                            transform: translateY(20px);
                        }
                    }
                </style>
            `);
    }

    $("body").append(notification);

    // 3秒后自动消失
    setTimeout(function () {
      notification.css("animation", "slideOutDown 0.3s ease");
      setTimeout(function () {
        notification.remove();
      }, 300);
    }, 3000);
  }

  // ============================================
  // 图片懒加载优化
  // ============================================
  function initLazyLoading() {
    if ("loading" in HTMLImageElement.prototype) {
      // 浏览器原生支持懒加载
      const images = document.querySelectorAll('img[loading="lazy"]');
      images.forEach((img) => {
        img.src = img.dataset.src || img.src;
      });
    } else {
      // 使用 Intersection Observer 降级方案
      const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src || img.src;
            img.classList.add("loaded");
            observer.unobserve(img);
          }
        });
      });

      document.querySelectorAll("img[data-src]").forEach((img) => {
        imageObserver.observe(img);
      });
    }
  }

  // ============================================
  // 图片灯箱效果（可选）
  // ============================================
  function initImageLightbox() {
    const contentImages = $(".description-content img, .trainer-card-image img");

    contentImages.on("click", function (e) {
      e.preventDefault();
      const imgSrc = $(this).attr("src");
      if (!imgSrc) return;

      const lightbox = $(`
                <div class="image-lightbox" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.9);
                    z-index: 99999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: zoom-out;
                ">
                    <img src="${imgSrc}" style="
                        max-width: 90%;
                        max-height: 90%;
                        object-fit: contain;
                        border-radius: 10px;
                        box-shadow: 0 10px 50px rgba(0, 0, 0, 0.5);
                    " />
                    <button style="
                        position: absolute;
                        top: 20px;
                        right: 20px;
                        width: 40px;
                        height: 40px;
                        background: rgba(255, 255, 255, 0.2);
                        border: none;
                        border-radius: 50%;
                        color: white;
                        font-size: 24px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    " class="lightbox-close">×</button>
                </div>
            `);

      $("body").append(lightbox);
      lightbox.fadeIn(300);

      // 点击关闭
      lightbox.on("click", function (e) {
        if (e.target === this || $(e.target).hasClass("lightbox-close")) {
          lightbox.fadeOut(300, function () {
            lightbox.remove();
          });
        }
      });

      // ESC 键关闭
      $(document).on("keyup.lightbox", function (e) {
        if (e.key === "Escape") {
          lightbox.fadeOut(300, function () {
            lightbox.remove();
          });
          $(document).off("keyup.lightbox");
        }
      });
    });
  }

  // ============================================
  // 响应式调整
  // ============================================
  $(window).on(
    "resize",
    debounce(function () {
      // 重新初始化需要响应式调整的功能
      initStickyElements();
    }, 250)
  );

  // ============================================
  // 防抖函数
  // ============================================
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // ============================================
  // 初始化可选功能
  // ============================================
  initLazyLoading();
  initImageLightbox();
})(jQuery);
