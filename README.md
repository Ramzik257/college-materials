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

Подробный список задач: [docs/tasks.md](docs/tasks.md).

## Что уже есть

- Структура проекта и `.gitignore`
- SQL: схема, сиды (3 категории, 10 материалов), примеры запросов, users, favorites
- Подключение к MySQL через PDO
- Стартовая страница Campus Notes: регистрация / вход / гость — PR #16
- Профиль: аватар, «о себе», смена пароля, избранные материалы — PR #17
- Главная с поиском, фильтром и сбросом фильтров
- Страница материала
- Вход администратора (сессия) — PR #11
- CRUD материалов — PR #12
- Валидация форм JS + PHP — PR #13

## Быстрый запуск (локально)

1. Нужны **PHP** и **MySQL** (Laragon / OpenServer / XAMPP).
2. Скопируйте конфиг:
   ```text
   includes/config.example.php  →  includes/config.php
   ```
3. Импортируйте SQL:
   - `sql/schema.sql`
   - `sql/seed.sql`
4. Импортируйте также `sql/users.sql` (если БД создавали раньше без таблицы users).
5. Для профиля и избранного: `sql/profile.sql` (колонки avatar/bio + таблица favorites).
6. Запуск:
   ```text
   php -S 127.0.0.1:8080 router.php
   ```
   Откройте http://127.0.0.1:8080/ — сначала регистрация/вход (Campus Notes).
   Каталог без аккаунта: ссылка «Смотреть каталог без регистрации».

Учебный админ: логин `admin`, пароль `password`.

## Git

Правило: **одна задача → одна ветка → коммиты → Pull Request → ревью напарника → merge в `main`**.

Каждый участник:
- делает минимум один свой PR;
- проводит минимум одно ревью чужого PR;
- участвует в учебном merge conflict (#9).

## Документы для сдачи

- [Схема БД](docs/database-schema.md)
- [Таблица проверок](docs/checklist.md)
- [Список задач участников](docs/tasks.md)
- [Как работать в Git](docs/git-workflow.md)
- [Список Issues](docs/issues.md)
- [Хостинг и HTTPS](docs/hosting.md)
- [Учебный merge conflict](docs/merge-conflict-drill.md)
- [Продвижение](docs/promotion.md)

## Структура

```text
college-materials/
  welcome.php / guest.php — вход и гостевой режим
  profile.php            — профиль, аватар, пароль, избранное
  index.php              — каталог, поиск, фильтр
  material.php           — страница материала
  router.php             — маршруты для php -S
  admin/                 — админка (логин, CRUD)
  includes/              — config, db, auth, user_auth, profile
  uploads/avatars/       — загруженные аватарки
  assets/                — css, js
  sql/                   — схема, сиды, users, profile
  docs/                  — схема БД, чеклист, задачи, git, хостинг
```

## Почему две таблицы

Название категории хранится только в `categories`.  
В `materials` — поле `category_id` (внешний ключ).  
Так название не копируется в каждую строку материала; переименование категории сразу видно у всех через `JOIN`.
