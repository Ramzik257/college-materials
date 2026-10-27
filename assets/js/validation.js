/**
 * Проверка форм на клиенте (до отправки на сервер).
 * Сервер всё равно проверяет данные повторно.
 */
(function () {
  function showErrors(box, messages) {
    if (!box) {
      return;
    }
    box.innerHTML = "";
    if (!messages.length) {
      box.hidden = true;
      return;
    }
    var list = document.createElement("ul");
    messages.forEach(function (msg) {
      var item = document.createElement("li");
      item.textContent = msg;
      list.appendChild(item);
    });
    box.appendChild(list);
    box.hidden = false;
    box.setAttribute("role", "alert");
  }

  function validateLoginForm(form) {
    var errors = [];
    var login = (form.login.value || "").trim();
    var password = form.password.value || "";

    if (!login) {
      errors.push("Введите логин.");
    }
    if (!password) {
      errors.push("Введите пароль.");
    }
    return errors;
  }

  function validateMaterialForm(form) {
    var errors = [];
    var title = (form.title.value || "").trim();
    var description = (form.description.value || "").trim();
    var categoryId = form.category_id.value;
    var url = (form.url.value || "").trim();

    if (!title) {
      errors.push("Укажите название.");
    } else if (title.length > 200) {
      errors.push("Название не длиннее 200 символов.");
    }

    if (!description) {
      errors.push("Укажите описание.");
    }

    if (!categoryId || categoryId === "0") {
      errors.push("Выберите категорию.");
    }

    if (!url) {
      errors.push("Укажите ссылку на ресурс.");
    } else if (!/^https?:\/\/.+/i.test(url)) {
      errors.push("Ссылка должна начинаться с http:// или https://.");
    } else if (url.length > 500) {
      errors.push("Ссылка не длиннее 500 символов.");
    }

    return errors;
  }

  function validateUserLoginForm(form) {
    var errors = [];
    var email = (form.email.value || "").trim();
    var password = form.password.value || "";
    if (!email) {
      errors.push("Введите email.");
    }
    if (!password) {
      errors.push("Введите пароль.");
    }
    return errors;
  }

  function validateUserRegisterForm(form) {
    var errors = [];
    var name = (form.name.value || "").trim();
    var email = (form.email.value || "").trim();
    var password = form.password.value || "";
    if (name.length < 2) {
      errors.push("Укажите имя (минимум 2 символа).");
    }
    if (!email) {
      errors.push("Укажите email.");
    }
    if (password.length < 6) {
      errors.push("Пароль не короче 6 символов.");
    }
    return errors;
  }

  function validateProfileForm(form) {
    var errors = [];
    var name = (form.name.value || "").trim();
    var bio = (form.bio.value || "").trim();
    if (name.length < 2) {
      errors.push("Укажите имя (минимум 2 символа).");
    }
    if (bio.length > 300) {
      errors.push("О себе — не длиннее 300 символов.");
    }
    return errors;
  }

  function validateChangePasswordForm(form) {
    var errors = [];
    var current = form.current_password.value || "";
    var next = form.new_password.value || "";
    var confirm = form.confirm_password.value || "";
    if (!current || !next || !confirm) {
      errors.push("Заполните все поля пароля.");
    }
    if (next && next.length < 6) {
      errors.push("Новый пароль не короче 6 символов.");
    }
    if (next && confirm && next !== confirm) {
      errors.push("Подтверждение пароля не совпадает.");
    }
    return errors;
  }

  function bindForm(selector, validator) {
    var form = document.querySelector(selector);
    if (!form) {
      return;
    }
    form.setAttribute("novalidate", "novalidate");
    form.addEventListener("submit", function (event) {
      var box = document.getElementById("js-errors");
      var errors = validator(form);
      if (errors.length) {
        event.preventDefault();
        showErrors(box, errors);
      }
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    bindForm("form[data-validate='login']", validateLoginForm);
    bindForm("form[data-validate='material']", validateMaterialForm);
    bindForm("form[data-validate='user-login']", validateUserLoginForm);
    bindForm("form[data-validate='user-register']", validateUserRegisterForm);
    bindForm("form[data-validate='profile']", validateProfileForm);
    bindForm("form[data-validate='change-password']", validateChangePasswordForm);
  });
})();
