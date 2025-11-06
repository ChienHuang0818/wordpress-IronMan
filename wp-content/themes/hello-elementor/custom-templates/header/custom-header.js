/**
 * Custom Header JavaScript
 * Mobile menu toggle functionality
 */

(function () {
  "use strict";

  // Wait for DOM to load
  document.addEventListener("DOMContentLoaded", function () {
    // Mobile menu toggle
    const toggleButton = document.querySelector(".custom-header-toggle");
    const mobileMenu = document.querySelector(".custom-header-mobile-menu");

    if (toggleButton && mobileMenu) {
      // Click toggle button
      toggleButton.addEventListener("click", function () {
        // Toggle button active state
        this.classList.toggle("active");

        // Toggle mobile menu active state
        mobileMenu.classList.toggle("active");

        // Update aria-label
        const isActive = this.classList.contains("active");
        this.setAttribute("aria-label", isActive ? "Close menu" : "Open menu");

        // Prevent background scrolling
        if (isActive) {
          document.body.style.overflow = "hidden";
        } else {
          document.body.style.overflow = "";
        }
      });

      // Close menu after clicking a menu item
      const mobileMenuLinks = mobileMenu.querySelectorAll("a");
      mobileMenuLinks.forEach(function (link) {
        link.addEventListener("click", function () {
          toggleButton.classList.remove("active");
          mobileMenu.classList.remove("active");
          document.body.style.overflow = "";
        });
      });
    }

    // Add shadow effect on scroll
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
