-- Профиль пользователя и избранные материалы.
-- Для уже существующей БД (выполнить один раз):
--   mysql -h 127.0.0.1 -u root college_materials < sql/profile.sql
USE college_materials;

-- Если колонки уже есть, эти два ALTER выдадут ошибку — можно пропустить.
ALTER TABLE users
  ADD COLUMN avatar_path VARCHAR(255) NULL DEFAULT NULL AFTER password_hash;

ALTER TABLE users
  ADD COLUMN bio VARCHAR(300) NULL DEFAULT NULL AFTER avatar_path;

CREATE TABLE IF NOT EXISTS favorites (
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
