-- Примеры SQL из ТЗ (для отчёта и защиты).
USE college_materials;

-- CREATE (добавить материал)
-- INSERT INTO materials (title, description, category_id, url)
-- VALUES (?, ?, ?, ?);

-- READ (один материал с названием категории)
SELECT
  m.id,
  m.title,
  m.description,
  m.url,
  m.created_at,
  c.name AS category_name
FROM materials m
JOIN categories c ON c.id = m.category_id
WHERE m.id = ?;

-- READ (список с поиском и фильтром)
SELECT
  m.id,
  m.title,
  m.description,
  m.url,
  c.name AS category_name
FROM materials m
JOIN categories c ON c.id = m.category_id
WHERE
  (? = '' OR m.title LIKE CONCAT('%', ?, '%'))
  AND (? = 0 OR m.category_id = ?)
ORDER BY m.created_at DESC;

-- UPDATE (изменить материал)
-- UPDATE materials
-- SET title = ?, description = ?, category_id = ?, url = ?
-- WHERE id = ?;

-- DELETE (удалить материал)
-- DELETE FROM materials WHERE id = ?;

/*
Почему две таблицы уменьшают повторение данных:
название категории хранится один раз в categories.
В materials только category_id. Если переименовать «Лекции»,
все материалы сразу показывают новое имя через JOIN.
*/
