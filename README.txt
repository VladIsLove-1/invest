Проект INVEST (версия без MySQL, на JSON-файлах)

1. Залейте ВСЕ файлы (из фронтенд и бекенд архивов) в одну папку на хостинге (обычно public_html или www).
2. Убедитесь, что на хостинге включён PHP (7.4+).
3. Откройте в браузере index.html — это лендинг.
4. Регистрация: registration.html -> register.php (создаётся users.json, balances.json).
5. Вход: login.html -> login.php -> dashboard.php.
6. Депозиты: deposits.php + deposit_process.php + deposits.json.
7. Баланс и транзакции: add_funds/withdraw + balances.json + transactions.json.

JSON-файлы хранят все данные:
- users.json        — пользователи
- balances.json     — балансы
- deposits.json     — депозиты
- transactions.json — операции

Файлы создаются автоматически, если их нет.
