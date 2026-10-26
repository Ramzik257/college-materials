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

  document.addEventListener("DOMContentLoaded", function () {
    var loginForm = document.querySelector("form[data-validate='login']");
    if (loginForm) {
      loginForm.setAttribute("novalidate", "novalidate");
      loginForm.addEventListener("submit", function (event) {
        var box = document.getElementById("js-errors");
        var errors = validateLoginForm(loginForm);
        if (errors.length) {
          event.preventDefault();
          showErrors(box, errors);
        }
      });
    }

    var materialForm = document.querySelector("form[data-validate='material']");
    if (materialForm) {
      materialForm.setAttribute("novalidate", "novalidate");
      materialForm.addEventListener("submit", function (event) {
        var box = document.getElementById("js-errors");
        var errors = validateMaterialForm(materialForm);
        if (errors.length) {
          event.preventDefault();
          showErrors(box, errors);
        }
      });
    }
  });
})();
