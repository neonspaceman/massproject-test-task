# Что реализовано

- Система принятия и обработки заявок пользователей, через Endpoints
- Обработка отправки email через Queued Message Handling. В докере "send-email-handler"
- Создание новых пользователей через команду bin/console app:create-user <email> <password>
- Тесты

# Запуск проекта

В папке с docker-composer.yaml выполнить
```php
docker compose up --build -d
```

Войти в контейнер
```php
docker exec -it mass-project-app-php-cli bash
```

Установить зависимости
```php
composer install
```

Провести миграции и установить фикстуры
```php
php bin/console doctrine:migrations:migrate -n
php bin/console --env=test doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
```

Перезапустить контейнеры
```php
docker compose down
docker compose up --build -d
```

После будут достпны по адресу http://localhost:8888:

GET http://localhost:8888/requests/ - получение заявок ответственным лицом, с фильтрацией по статусу

PUT http://localhost:8888/requests/{id}/ - ответ на конкретную задачу ответственным лицом

POST http://localhost:8888/requests/ - отправка заявки пользователями системы

Информацию об использовании можно получить тут:

http://localhost:8888/api/doc

Данные для авторизации (если были установлены фикстуры):
```
X-AUTH-USER: admin@test.com
X-AUTH-PASSWORD: root
```
