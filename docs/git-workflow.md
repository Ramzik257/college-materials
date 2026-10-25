# Как работаем в Git

Правило: **одна задача → одна ветка → коммиты → Pull Request → ревью друга → merge в `main`**.

## Первый раз у друга

```bash
git clone <ссылка-на-репозиторий>
cd college-materials
copy includes\config.example.php includes\config.php
```

Дальше настроить `config.php` и импортировать SQL (см. README).

## Обычный цикл задачи

```bash
# 1. Актуальный main
git checkout main
git pull

# 2. Ветка под задачу (номер Issue удобно в имени)
git checkout -b feature/3-search-materials

# 3. Правки → небольшие коммиты
git add .
git commit -m "Добавляет поиск материалов по названию"

# 4. Отправка ветки и Pull Request на GitHub
git push -u origin HEAD
# на сайте: Compare & pull request → назначить ревьюера (друга)
```

## Ревью

- Друг смотрит код в PR, пишет замечания или Approve.
- После Approve — Merge в `main`.
- Каждый участник делает **хотя бы один** review чужого PR.

## Учебный конфликт слияния

1. Оба меняют один и тот же файл в разных ветках (например `README.md`).
2. Один смержил раньше, второй при merge видит conflict.
3. Вручную правите файл, оставляете нужный текст, коммитите разрешение конфликта.

Так на защите можно показать, что умеете чинить merge conflict.
