### Структура файлов

`index.php` — точка входа в сервис комментариев, будет обрабатывать все входящие запросы.

`Comment.php` — класс Comment для работы с комментариями.

`User.php` — класс User для работы с пользователями и авторизацией.

`Database.php` — класс Database для работы с базой данных.

`docker-compose.yml` — файл для настройки контейнеров Docker.

### Создание таблиц в базе данных

```mysql
CREATE TABLE users (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE comments (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  text VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  user_id INT(11) NOT NULL,
  parent_id INT(11),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (parent_id) REFERENCES comments(id)
);

```


### Запуск
1. Открыть терминал и перейти в директорию, содержащую файл *docker-compose.yml*.
2. Запусть сервис с помощью команды `docker-compose up -d`.
3. Docker Compose создаст и запустит контейнеры для PHP приложения и MySQL базы данных.
4. После запуска сервис будет доступен по адресу http://localhost:80.
