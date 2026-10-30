# CI/CD (бонус ТЗ)

## Что настроено

### 1. CI — автоматическая проверка при Pull Request
Файл: `.github/workflows/ci.yml`

При каждом PR и push в `main`:
1. Устанавливается PHP 8.3
2. Проверка синтаксиса всех `.php` (`php -l`)
3. Smoke-тесты `tests/smoke.php` (валидация логина + наличие ключевых файлов)

Секреты для CI **не нужны**.

### 2. Deploy — публикация после успешного CI на main
Файл: `.github/workflows/deploy.yml`

Заливка по FTP на хостинг. Доступы только в **GitHub Secrets** (не в репозитории):

| Secret | Пример |
|--------|--------|
| `FTP_HOST` | `ftpupload.net` |
| `FTP_USER` | логин панели |
| `FTP_PASS` | пароль FTP |
| `FTP_DIR` | `/htdocs/` (опционально) |

Пока секретов нет — deploy **пропускается**, CI не падает.

Добавить: Repository → **Settings** → **Secrets and variables** → **Actions** → New repository secret.

## Как посмотреть результат

GitHub → вкладка **Actions** → workflow **CI**.
