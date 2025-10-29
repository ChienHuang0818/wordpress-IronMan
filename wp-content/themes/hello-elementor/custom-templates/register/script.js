/**
 * Custom Register Page Scripts
 * 自定义注册页面交互脚本
 *
 * @package HelloElementor
 * @since 1.0.0
 */

(function ($) {
  "use strict";

  $(document).ready(function () {
    initPasswordToggle();
    initPasswordStrength();
    initFormValidation();
    initFormSubmit();
  });

  // ============================================
  // 密码显示/隐藏切换
  // ============================================
  function initPasswordToggle() {
    $(".toggle-password").on("click", function () {
      const targetId = $(this).data("target");
      const $input = $("#" + targetId);
      const type = $input.attr("type");

      if (type === "password") {
        $input.attr("type", "text");
        $(this).addClass("active");
      } else {
        $input.attr("type", "password");
        $(this).removeClass("active");
      }
    });
  }

  // ============================================
  // 密码强度检测
  // ============================================
  function initPasswordStrength() {
    $("#password").on("input", function () {
      const password = $(this).val();
      const $strengthBar = $("#password-strength");

      if (password.length === 0) {
        $strengthBar.removeClass("active weak medium strong");
        return;
      }

      $strengthBar.addClass("active");

      let strength = 0;

      // 检查长度
      if (password.length >= 8) strength++;
      if (password.length >= 12) strength++;

      // 检查是否包含数字
      if (/\d/.test(password)) strength++;

      // 检查是否包含小写字母
      if (/[a-z]/.test(password)) strength++;

      // 检查是否包含大写字母
      if (/[A-Z]/.test(password)) strength++;

      // 检查是否包含特殊字符
      if (/[^A-Za-z0-9]/.test(password)) strength++;

      // 设置强度等级
      $strengthBar.removeClass("weak medium strong");
      if (strength <= 2) {
        $strengthBar.addClass("weak");
      } else if (strength <= 4) {
        $strengthBar.addClass("medium");
      } else {
        $strengthBar.addClass("strong");
      }
    });
  }

  // ============================================
  // 表单实时验证
  // ============================================
  function initFormValidation() {
    const $form = $("#custom-register-form");

    // 用户名验证
    $("#username").on("blur", function () {
      const username = $(this).val();
      const $group = $(this).closest(".form-group");

      // 移除旧的错误提示
      $group.find(".field-error").remove();
      $(this).removeClass("error");

      if (username.length < 3) {
        showFieldError($(this), "用户名至少需要 3 个字符");
        return;
      }

      if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        showFieldError($(this), "用户名只能包含字母、数字和下划线");
        return;
      }

      // AJAX 检查用户名是否已存在
      checkUsernameAvailability(username);
    });

    // 邮箱验证
    $("#email").on("blur", function () {
      const email = $(this).val();
      const $group = $(this).closest(".form-group");

      $group.find(".field-error").remove();
      $(this).removeClass("error");

      if (!isValidEmail(email)) {
        showFieldError($(this), "请输入有效的电子邮箱地址");
        return;
      }

      // AJAX 检查邮箱是否已存在
      checkEmailAvailability(email);
    });

    // 确认密码验证
    $("#confirm_password").on("input", function () {
      const password = $("#password").val();
      const confirmPassword = $(this).val();
      const $group = $(this).closest(".form-group");

      $group.find(".field-error").remove();
      $(this).removeClass("error");

      if (confirmPassword && password !== confirmPassword) {
        showFieldError($(this), "两次输入的密码不一致");
      }
    });
  }

  // ============================================
  // 显示字段错误
  // ============================================
  function showFieldError($input, message) {
    $input.addClass("error");
    const $group = $input.closest(".form-group");
    $group.find(".field-error").remove();
    $group.append(
      '<small class="field-error" style="color: #e63946; display: block; margin-top: 5px; font-size: 12px;">' +
        message +
        "</small>"
    );
  }

  // ============================================
  // 邮箱格式验证
  // ============================================
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // ============================================
  // 检查用户名是否可用
  // ============================================
  function checkUsernameAvailability(username) {
    $.ajax({
      url: registerAjax.ajaxurl,
      type: "POST",
      data: {
        action: "check_username",
        username: username,
        nonce: registerAjax.nonce,
      },
      success: function (response) {
        if (!response.success) {
          showFieldError($("#username"), response.data.message);
        }
      },
    });
  }

  // ============================================
  // 检查邮箱是否可用
  // ============================================
  function checkEmailAvailability(email) {
    $.ajax({
      url: registerAjax.ajaxurl,
      type: "POST",
      data: {
        action: "check_email",
        email: email,
        nonce: registerAjax.nonce,
      },
      success: function (response) {
        if (!response.success) {
          showFieldError($("#email"), response.data.message);
        }
      },
    });
  }

  // ============================================
  // 表单提交处理
  // ============================================
  function initFormSubmit() {
    const $form = $("#custom-register-form");
    const $submitBtn = $("#register-submit-btn");
    const $btnText = $submitBtn.find(".btn-text");
    const $btnLoader = $submitBtn.find(".btn-loader");
    const $successMsg = $("#register-success");
    const $errorMsg = $("#register-error");

    $form.on("submit", function (e) {
      e.preventDefault();

      // 隐藏之前的消息
      $successMsg.hide();
      $errorMsg.hide();
      $(".field-error").remove();
      $(".form-group input").removeClass("error");

      // 基本验证
      if (!validateForm()) {
        return;
      }

      // 禁用按钮并显示加载状态
      $submitBtn.prop("disabled", true);
      $btnText.hide();
      $btnLoader.show();

      // 收集表单数据
      const formData = {
        action: "custom_register_user",
        nonce: registerAjax.nonce,
        username: $("#username").val(),
        email: $("#email").val(),
        password: $("#password").val(),
        first_name: $("#first_name").val(),
        last_name: $("#last_name").val(),
        gender: $("#gender").val(),
        fitness_goal: $("#fitness_goal").val(),
        subscribe_newsletter: $("#subscribe_newsletter").is(":checked") ? 1 : 0,
      };

      // AJAX 提交
      $.ajax({
        url: registerAjax.ajaxurl,
        type: "POST",
        data: formData,
        success: function (response) {
          if (response.success) {
            // 显示成功消息
            $successMsg.show();
            $form.hide();

            // 2秒后跳转
            setTimeout(function () {
              if (response.data.redirect) {
                window.location.href = response.data.redirect;
              } else {
                window.location.href = registerAjax.homeUrl || "/";
              }
            }, 2000);
          } else {
            // 显示错误消息
            $errorMsg.find("#error-text").text(response.data.message || "注册失败，请稍后重试");
            $errorMsg.show();

            // 重置按钮状态
            $submitBtn.prop("disabled", false);
            $btnText.show();
            $btnLoader.hide();

            // 滚动到错误消息
            $("html, body").animate(
              {
                scrollTop: $errorMsg.offset().top - 100,
              },
              300
            );
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", error);
          $errorMsg.find("#error-text").text("网络错误，请检查您的网络连接后重试");
          $errorMsg.show();

          // 重置按钮状态
          $submitBtn.prop("disabled", false);
          $btnText.show();
          $btnLoader.hide();
        },
      });
    });
  }

  // ============================================
  // 表单验证
  // ============================================
  function validateForm() {
    let isValid = true;

    // 验证用户名
    const username = $("#username").val();
    if (username.length < 3) {
      showFieldError($("#username"), "用户名至少需要 3 个字符");
      isValid = false;
    } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
      showFieldError($("#username"), "用户名只能包含字母、数字和下划线");
      isValid = false;
    }

    // 验证邮箱
    const email = $("#email").val();
    if (!isValidEmail(email)) {
      showFieldError($("#email"), "请输入有效的电子邮箱地址");
      isValid = false;
    }

    // 验证密码
    const password = $("#password").val();
    if (password.length < 8) {
      showFieldError($("#password"), "密码至少需要 8 个字符");
      isValid = false;
    }

    // 验证确认密码
    const confirmPassword = $("#confirm_password").val();
    if (password !== confirmPassword) {
      showFieldError($("#confirm_password"), "两次输入的密码不一致");
      isValid = false;
    }

    // 验证服务条款
    if (!$('input[name="agree_terms"]').is(":checked")) {
      alert("请阅读并同意服务条款和隐私政策");
      isValid = false;
    }

    return isValid;
  }

  // ============================================
  // 输入框错误状态样式
  // ============================================
  $("<style>")
    .html(".form-group input.error, .form-group select.error { border-color: #e63946 !important; }")
    .appendTo("head");
})(jQuery);
