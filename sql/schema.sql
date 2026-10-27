-- Создание базы и таблиц для каталога учебных материалов.
-- Запуск: в phpMyAdmin или: mysql -u root < sql/schema.sql

CREATE DATABASE IF NOT EXISTS college_materials
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE college_materials;

-- Категории: название хранится только здесь (без повторения в материалах)
CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Материалы: category_id — внешний ключ на categories
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

-- Администраторы: пароль только в виде хеша
CREATE TABLE admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  login VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Обычные пользователи (регистрация на сайте)
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  avatar_path VARCHAR(255) NULL DEFAULT NULL,
  bio VARCHAR(300) NULL DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Избранные материалы пользователя
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

-- Индекс для поиска по названию
CREATE INDEX idx_materials_title ON materials (title);
