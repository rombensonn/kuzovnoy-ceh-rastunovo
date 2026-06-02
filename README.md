# Кузовной цех

Standalone PHP-лендинг для автосервиса.

## Запуск локально

```powershell
php -S 127.0.0.1:8000
```

Откройте `http://127.0.0.1:8000`.

## Telegram-заявки

1. Скопируйте `config.example.php` в `config.php`.
2. Укажите `TG_BOT_TOKEN` и `TG_CHAT_ID`.
3. Проверьте отправку формы.

`config.php` добавлен в `.gitignore`, чтобы не публиковать токены.

## Перед публикацией

- Замените `https://example.com/` в `robots.txt` и `sitemap.xml` на реальный домен.
- Укажите `BASE_URL` в `config.php`, чтобы включить canonical-ссылку.
- Добавьте реальные фото сервиса в `assets/img/` с именами:
  - `hero-industrial.png`
  - `work-painting.png`
  - `work-welding.png`
  - `work-polishing.png`
