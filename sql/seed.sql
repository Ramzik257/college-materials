-- Тестовые данные: 3 категории, 10 материалов, 1 админ.
-- Логин: admin
-- Пароль: password
-- (учебный хеш; на боевом сервере смените пароль)

USE college_materials;

INSERT INTO categories (id, name, slug) VALUES
  (1, 'Лекции', 'lectures'),
  (2, 'Практика', 'practice'),
  (3, 'Полезное', 'useful');

INSERT INTO materials (title, description, category_id, url) VALUES
  (
    'Введение в HTML',
    'Базовая структура HTML-документа, теги и семантическая разметка.',
    1,
    'https://developer.mozilla.org/ru/docs/Learn/HTML/Introduction_to_HTML'
  ),
  (
    'Основы CSS',
    'Селекторы, каскад, блочная модель и простая вёрстка.',
    1,
    'https://developer.mozilla.org/ru/docs/Learn/CSS'
  ),
  (
    'JavaScript для начинающих',
    'Переменные, функции, события и работа с DOM.',
    1,
    'https://developer.mozilla.org/ru/docs/Learn/JavaScript'
  ),
  (
    'Работа с формами',
    'Практика: валидация полей на клиенте и отправка данных.',
    2,
    'https://developer.mozilla.org/ru/docs/Learn/Forms'
  ),
  (
    'Git: первые коммиты',
    'Практика: init, status, add, commit, ветки.',
    2,
    'https://git-scm.com/book/ru/v2'
  ),
  (
    'Pull Request на практике',
    'Как оформить PR и провести code review в команде.',
    2,
    'https://docs.github.com/ru/pull-requests'
  ),
  (
    'PHP и MySQL',
    'Как PHP подключается к базе и выполняет запросы.',
    1,
    'https://www.php.net/manual/ru/book.pdo.php'
  ),
  (
    'Безопасность паролей',
    'Хеширование паролей и почему нельзя хранить их открытым текстом.',
    3,
    'https://www.php.net/manual/ru/function.password-hash.php'
  ),
  (
    'Чеклист публикации сайта',
    'HTTPS, домен, проверка на телефоне, метатеги.',
    3,
    'https://web.dev/learn/'
  ),
  (
    'SQL: JOIN и внешние ключи',
    'Практика связки таблиц materials и categories.',
    2,
    'https://dev.mysql.com/doc/'
  );

-- password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO admins (login, password_hash) VALUES
  (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
  );
