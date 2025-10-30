/**
 * 文字逐字跳出動畫效果
 * 針對 elementor-heading-title elementor-size-default 類別的 h2 元素
 */

document.addEventListener("DOMContentLoaded", function () {
  // 查找所有符合條件的 h2 標題
  const headings = document.querySelectorAll("h2.elementor-heading-title.elementor-size-default");

  if (headings.length === 0) {
    console.log("未找到符合條件的 h2 標題元素");
    return;
  }

  // 為每個標題添加動畫
  headings.forEach(function (heading) {
    animateText(heading);
  });

  function animateText(element) {
    let text = element.textContent;
    const originalText = text;

    // 移除開頭的 | 符號
    if (text.startsWith("|")) {
      text = text.substring(1);
    }

    // 清空原始文字
    element.textContent = "";

    // 添加動畫類別
    element.classList.add("typing-animation");

    // 逐字顯示文字
    let currentIndex = 0;
    const typingInterval = setInterval(function () {
      if (currentIndex < text.length) {
        // 添加當前字符
        element.textContent += text[currentIndex];
        currentIndex++;

        // 添加跳動效果
        element.classList.add("typing-bounce");
        setTimeout(function () {
          element.classList.remove("typing-bounce");
        }, 150);
      } else {
        // 動畫完成
        clearInterval(typingInterval);
        element.classList.add("typing-complete");

        // 觸發完成事件
        element.dispatchEvent(
          new CustomEvent("typingComplete", {
            detail: { originalText: originalText },
          })
        );
      }
    }, 100); // 每100毫秒顯示一個字符，可以調整速度

    // 移除光標閃爍效果 - 不添加光標
    // addCursorBlink(element);
  }

  function addCursorBlink(element) {
    // 創建光標元素
    const cursor = document.createElement("span");
    cursor.className = "typing-cursor";
    cursor.textContent = "|";

    // 在動畫期間添加光標
    element.appendChild(cursor);

    // 動畫完成後移除光標
    element.addEventListener("typingComplete", function () {
      setTimeout(function () {
        if (cursor.parentNode) {
          cursor.parentNode.removeChild(cursor);
        }
      }, 1000); // 1秒後移除光標
    });
  }

  // 可選：添加重新播放功能
  function replayAnimation(element) {
    // 移除完成狀態
    element.classList.remove("typing-complete");

    // 重新開始動畫
    animateText(element);
  }

  // 可選：點擊重新播放
  headings.forEach(function (heading) {
    heading.addEventListener("click", function () {
      replayAnimation(this);
    });

    // 添加提示樣式
    heading.style.cursor = "pointer";
    heading.title = "點擊重新播放動畫";
  });
});

// 可選：添加 Intersection Observer 來實現滾動觸發動畫
if ("IntersectionObserver" in window) {
  const observerOptions = {
    threshold: 0.5, // 當元素50%可見時觸發
    rootMargin: "0px 0px -50px 0px",
  };

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        const element = entry.target;
        if (!element.classList.contains("typing-complete")) {
          // 延遲一點開始動畫，讓用戶注意到
          setTimeout(function () {
            animateText(element);
          }, 300);
        }
        observer.unobserve(element);
      }
    });
  }, observerOptions);

  // 觀察所有標題元素
  document.addEventListener("DOMContentLoaded", function () {
    const headings = document.querySelectorAll("h2.elementor-heading-title.elementor-size-default");
    headings.forEach(function (heading) {
      observer.observe(heading);
    });
  });
}
