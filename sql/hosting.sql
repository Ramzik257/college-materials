-- Импорт на shared-хостинг (SpaceWeb и т.п.).
-- Базу создай в панели (у тебя: antonovan4), затем в phpMyAdmin
-- выбери эту БД слева и импортируй ТОЛЬКО этот файл.
-- Без CREATE DATABASE / USE — на хостинге на это нет прав.

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE materials (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  url VARCHAR(500) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_materials_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  login VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  avatar_path VARCHAR(255) NULL DEFAULT NULL,
  bio VARCHAR(300) NULL DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE favorites (
  user_id INT UNSIGNED NOT NULL,
  material_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, material_id),
  CONSTRAINT fk_favorites_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_favorites_material
    FOREIGN KEY (material_id) REFERENCES materials(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_materials_title ON materials (title);

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

-- Логин: admin  /  Пароль: password
INSERT INTO admins (login, password_hash) VALUES
  (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
  );
