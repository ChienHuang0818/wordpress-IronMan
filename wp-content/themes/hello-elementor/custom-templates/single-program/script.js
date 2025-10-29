/**
 * Single Program Page Scripts
 * 训练项目单页脚本
 *
 * @package HelloElementor
 * @since 1.0.0
 */

(function ($) {
  "use strict";

  /**
   * 复制链接到剪贴板
   */
  window.copyToClipboard = function (text) {
    // 创建临时文本区域
    const tempInput = document.createElement("textarea");
    tempInput.value = text;
    tempInput.style.position = "fixed";
    tempInput.style.opacity = "0";
    document.body.appendChild(tempInput);

    // 选择并复制
    tempInput.select();
    tempInput.setSelectionRange(0, 99999);

    try {
      document.execCommand("copy");
      showNotification("链接已复制到剪贴板！", "success");
    } catch (err) {
      console.error("复制失败:", err);
      showNotification("复制失败，请手动复制", "error");
    }

    // 移除临时元素
    document.body.removeChild(tempInput);
  };

  /**
   * 显示通知消息
   */
  function showNotification(message, type = "info") {
    // 移除旧通知
    $(".copy-notification").remove();

    // 创建新通知
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

    // 添加到页面
    $("body").append(notification);

    // 淡入动画
    notification.animate({ opacity: 1 }, 300);

    // 3秒后淡出并移除
    setTimeout(function () {
      notification.animate({ opacity: 0 }, 300, function () {
        $(this).remove();
      });
    }, 3000);
  }

  /**
   * CTA 按钮点击事件
   */
  $(".cta-button").on("click", function () {
    // 这里可以添加报名逻辑
    // 例如：打开模态框、跳转到报名页面等

    // 示例：显示提示
    showNotification("报名功能开发中，敬请期待！", "info");

    // 或者跳转到联系页面
    // window.location.href = '/contact/';

    // 或者打开外部链接
    // window.open('https://example.com/register', '_blank');
  });

  /**
   * 平滑滚动动画
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
   * 图片懒加载（如果需要）
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
   * 滚动动画效果
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

    // 为元素添加动画
    document.querySelectorAll(".info-card, .related-program-card").forEach((el) => {
      el.style.opacity = "0";
      el.style.transform = "translateY(20px)";
      el.style.transition = "all 0.6s ease";
      observer.observe(el);
    });

    // 添加 CSS 类
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
   * 粘性侧边栏
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
   * 阅读进度条
   */
  function initReadingProgress() {
    // 创建进度条
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

    // 更新进度
    $(window).on("scroll", function () {
      const windowHeight = $(window).height();
      const documentHeight = $(document).height();
      const scrollTop = $(window).scrollTop();

      const progress = (scrollTop / (documentHeight - windowHeight)) * 100;
      progressBar.css("width", progress + "%");
    });
  }

  /**
   * 社交分享追踪
   */
  $(".share-btn").on("click", function () {
    const platform = $(this).hasClass("share-facebook")
      ? "Facebook"
      : $(this).hasClass("share-twitter")
      ? "Twitter"
      : "Link";

    // 如果使用 Google Analytics
    if (typeof gtag !== "undefined") {
      gtag("event", "share", {
        event_category: "Social",
        event_label: platform,
        value: 1,
      });
    }

    console.log("分享到:", platform);
  });

  /**
   * 图片点击放大
   */
  function initImageLightbox() {
    $(".program-content img").on("click", function () {
      const src = $(this).attr("src");
      const alt = $(this).attr("alt") || "";

      // 创建灯箱
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
   * 初始化所有功能
   */
  $(document).ready(function () {
    // 初始化滚动动画
    initScrollAnimations();

    // 初始化粘性侧边栏
    initStickySidebar();

    // 初始化阅读进度条
    initReadingProgress();

    // 初始化图片灯箱
    initImageLightbox();

    // 懒加载图片（如果需要）
    if ("IntersectionObserver" in window) {
      lazyLoadImages();
    }

    console.log("Single Program page initialized");
  });
})(jQuery);
