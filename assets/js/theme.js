/**
 * Переключение тёмной / светлой темы.
 * Выбор хранится в localStorage.
 */
(function () {
  var STORAGE_KEY = "campus-notes-theme";

  function getPreferred() {
    try {
      var saved = localStorage.getItem(STORAGE_KEY);
      if (saved === "light" || saved === "dark") {
        return saved;
      }
    } catch (e) {
      /* ignore */
    }
    if (window.matchMedia && window.matchMedia("(prefers-color-scheme: light)").matches) {
      return "light";
    }
    return "dark";
  }

  function applyTheme(theme) {
    document.documentElement.setAttribute("data-theme", theme);
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch (e) {
      /* ignore */
    }
    document.querySelectorAll("[data-theme-toggle]").forEach(function (btn) {
      btn.setAttribute("aria-label", theme === "dark" ? "Включить светлую тему" : "Включить тёмную тему");
      btn.setAttribute("title", theme === "dark" ? "Светлая тема" : "Тёмная тема");
    });
  }

  function toggleTheme() {
    var current = document.documentElement.getAttribute("data-theme") || "dark";
    applyTheme(current === "dark" ? "light" : "dark");
  }

  applyTheme(getPreferred());

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[data-theme-toggle]").forEach(function (btn) {
      btn.addEventListener("click", function (event) {
        event.preventDefault();
        toggleTheme();
      });
    });
  });
})();
