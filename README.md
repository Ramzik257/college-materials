# Учебные материалы (практика)

Сайт-каталог учебных материалов для студентов колледжа.  
Стек: **HTML, CSS, JavaScript, PHP, MySQL**. Совместная работа через **Git**.

**Репозиторий:** https://github.com/Ramzik257/college-materials  
**Issues:** https://github.com/Ramzik257/college-materials/issues

## Участники

| Участник | GitHub | Зона ответственности |
|----------|--------|----------------------|
| A | Ramzik257 | База данных, каталог, поиск, фильтр, страница материала |
| B | Aivaki888 | Вход админа, CRUD, проверка форм JS + PHP |

*(допишите ФИО перед сдачей)*

## Что уже есть

- Структура проекта и `.gitignore`
- SQL: схема, сиды (3 категории, 10 материалов), примеры запросов
- Подключение к MySQL через PDO
- Главная с поиском и фильтром
- Страница материала
- Заглушка `/admin/login.php` под будущую админку

## Быстрый запуск (локально)

На этой машине уже стоит **Laragon** (`C:\laragon`). Проект связан в `C:\laragon\www\college-materials`.

1. Скопируйте конфиг (если ещё нет):
   ```text
   includes/config.example.php  →  includes/config.php
   ```
2. Импортируйте SQL в MySQL (root без пароля в учебной установке):
   - `sql/schema.sql`
   - `sql/seed.sql`
3. Запуск для проверки:
   ```text
   C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe -S 127.0.0.1:8080 -t C:\Users\User\college-materials
   ```
   Откройте http://127.0.0.1:8080/
4. Либо в окне Laragon нажмите **Start All** и откройте сайт из `www`.

Учебный админ из сидов: логин `admin`, пароль `password`.

## Git

Репозиторий и Issues уже созданы. Правило: **одна задача → одна ветка → коммиты → Pull Request → ревью напарника → merge в `main`**.

Участник B (Aivaki888) берёт Issues 5–7 (админка) и работает в своих ветках.

## Документы для сдачи

- [Схема БД](docs/database-schema.md)
- [Таблица проверок](docs/checklist.md)
- [Как работать в Git](docs/git-workflow.md)
- [Список Issues](docs/issues.md) — создать на GitHub и назначить

## Структура

```text
college-materials/
  index.php              — каталог, поиск, фильтр
  material.php           — страница материала
  admin/                 — админка (логин, CRUD)
  includes/              — config, db, шапка/подвал
  assets/css/            — стили
  sql/                   — схема, сиды, примеры запросов
  docs/                  — схема БД, чеклист, git, issues
```

## Почему две таблицы

Название категории хранится только в `categories`.  
В `materials` — поле `category_id` (внешний ключ).  
Так название не копируется в каждую строку материала; переименование категории сразу видно у всех через `JOIN`.
