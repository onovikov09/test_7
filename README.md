Тестовое задание
============================

### Как развернуть проект:

1. Скачать архив с проектом в подготовленную папку либо клонировать через репозиторий
   
     ```
     git clone https://github.com/onovikov09/test_7 ./
     ```

2. Создать папку logs в runtime и установить на нее права
   
    ```
    cd runtime
    mkdir logs
    chmod 777 logs
    ```

3. Установить на папку public_html/assets права
   
    ```
    cd ../public_html 
    chmod 777 assets
    ```
4. Создать базу данных
   
    ```
    CREATE DATABASE `test7` /*!40100 DEFAULT CHARACTER SET utf8 */;
    ```
    
5. Отредактировать в файле конфига БД (db.php) имя пользователя и пароль

6. Применить миграции
    
    ```
    cd ../ 
    ./yii migrate
    ```
    
7. Все готово!