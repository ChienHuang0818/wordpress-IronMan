/**
 * 自定義 Header JavaScript
 * 處理手機版菜單切換
 */

document.addEventListener("DOMContentLoaded", function () {
  const headerToggle = document.querySelector(".custom-header-toggle");
  const mobileMenu = document.querySelector(".custom-header-mobile-menu");

  if (headerToggle && mobileMenu) {
    headerToggle.addEventListener("click", function () {
      // 切換按鈕動畫
      this.classList.toggle("active");

      // 切換菜單顯示
      mobileMenu.classList.toggle("active");

      // 防止背景滾動
      if (mobileMenu.classList.contains("active")) {
        document.body.style.overflow = "hidden";
      } else {
        document.body.style.overflow = "";
      }
    });

    // 點擊菜單項後關閉手機版菜單
    const mobileLinks = mobileMenu.querySelectorAll("a");
    mobileLinks.forEach(function (link) {
      link.addEventListener("click", function () {
        headerToggle.classList.remove("active");
        mobileMenu.classList.remove("active");
        document.body.style.overflow = "";
      });
    });
  }

  // 滾動時添加陰影效果
  let lastScrollTop = 0;
  const header = document.querySelector(".custom-header");

  if (header) {
    window.addEventListener("scroll", function () {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

      if (scrollTop > 50) {
        header.classList.add("scrolled");
      } else {
        header.classList.remove("scrolled");
      }

      lastScrollTop = scrollTop;
    });
  }
});
