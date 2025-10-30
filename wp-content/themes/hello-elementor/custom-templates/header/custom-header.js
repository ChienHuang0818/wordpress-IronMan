/**
 * Custom Header JavaScript
 * 手機版菜單切換功能
 */

(function () {
  "use strict";

  // 等待 DOM 加載完成
  document.addEventListener("DOMContentLoaded", function () {
    // 手機版菜單切換
    const toggleButton = document.querySelector(".custom-header-toggle");
    const mobileMenu = document.querySelector(".custom-header-mobile-menu");

    if (toggleButton && mobileMenu) {
      // 點擊切換按鈕
      toggleButton.addEventListener("click", function () {
        // 切換按鈕的 active 狀態
        this.classList.toggle("active");

        // 切換手機菜單的 active 狀態
        mobileMenu.classList.toggle("active");

        // 更新 aria-label
        const isActive = this.classList.contains("active");
        this.setAttribute("aria-label", isActive ? "關閉選單" : "開啟選單");

        // 防止背景滾動
        if (isActive) {
          document.body.style.overflow = "hidden";
        } else {
          document.body.style.overflow = "";
        }
      });

      // 點擊菜單項後關閉菜單
      const mobileMenuLinks = mobileMenu.querySelectorAll("a");
      mobileMenuLinks.forEach(function (link) {
        link.addEventListener("click", function () {
          toggleButton.classList.remove("active");
          mobileMenu.classList.remove("active");
          document.body.style.overflow = "";
        });
      });
    }

    // 滾動時添加陰影效果
    const header = document.querySelector(".custom-header");
    if (header) {
      window.addEventListener("scroll", function () {
        if (window.scrollY > 50) {
          header.classList.add("scrolled");
        } else {
          header.classList.remove("scrolled");
        }
      });
    }
  });
})();
