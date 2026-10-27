# Схема базы данных

## Диаграмма

```mermaid
erDiagram
  categories ||--o{ materials : "имеет"
  categories {
    int id PK
    varchar name
    varchar slug UK
  }
  materials {
    int id PK
    varchar title
    text description
    int category_id FK
    varchar url
    timestamp created_at
  }
  admins {
    int id PK
    varchar login UK
    varchar password_hash
  }
  users {
    int id PK
    varchar name
    varchar email UK
    varchar password_hash
    timestamp created_at
  }
```

## Таблицы

| Таблица | Назначение |
|----------|------------|
| `categories` | Справочник категорий (название только здесь) |
| `materials` | Учебные материалы, ссылка на категорию через `category_id` |
| `admins` | Учётные записи администраторов (пароль только как хеш) |
| `users` | Обычные пользователи сайта (регистрация / вход) |

## Почему так меньше повторений

Название категории не копируется в каждую строку `materials`.  
В материале хранится только `category_id`. Чтобы показать название, делаем `JOIN`.  
Переименование категории в одном месте сразу видно у всех материалов.

`admins` и `users` разделены: админка и регистрация на сайте — разные роли.

## Файлы

- `sql/schema.sql` — создание БД и таблиц (включая `users`)
- `sql/users.sql` — только таблица `users` (для уже существующей БД)
- `sql/seed.sql` — 3 категории, 10 материалов, админ
- `sql/queries.sql` — примеры INSERT / SELECT / UPDATE / DELETE
