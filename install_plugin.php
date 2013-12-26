<?php

# Подключение файла с настройками
require_once 'config.php';

#
## Функция инсталяции: создание таблиц, справочников
## таблица weather_forecast   - хранилище данных о погоде
## city_id                    - код города
## time                       - дата, для которой расчитана погода
## t_min, t_max               - предполагаемые темепературные минимум и максимум
## cloud                      - название иконки
## conditions                 - текстовое описание погоды
## таблица weather_cities     - список городов
## name                       - название города
## cid                        - код города на сайте
#
function install_plugin() {
   # Подключение к БД   
   $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

   # Проверка соединения
   if ( mysqli_connect_errno() ) {
       die ('Не удалось подключиться (' . $mysqli->connect_errno . '): ' . $mysqli->connect_error);
   }

   # Создание таблиц 
   $sql = "CREATE TABLE weather_forecast (
      	id int(10) unsigned NOT NULL auto_increment,
      	city_id int(10) unsigned NOT NULL,
      	time timestamp NOT NULL,
      	t_min tinyint(4) NOT NULL,
      	t_max tinyint(4) NOT NULL,  
      	cloud varchar(128) NOT NULL,
      	conditions varchar(255) NOT NULL,
         UNIQUE KEY id (id)
      ) CHARACTER SET = utf8, COLLATE = utf8_general_ci;";

   if ( $mysqli->query($sql) === TRUE ) {
      echo 'Таблица weather_forecast успешно создана.' . PHP_EOL;
   }      
   $sql = "CREATE TABLE weather_cities (
      	id int(10) unsigned NOT NULL auto_increment,
      	name varchar(255) NOT NULL,
      	cid int(10) unsigned NOT NULL,
         UNIQUE KEY id (id)
      ) CHARACTER SET = utf8, COLLATE = utf8_general_ci;";

   if ( $mysqli->query($sql) === TRUE ) {
      echo 'Таблица weather_cities успешно создана.' . PHP_EOL;
   }      
   
   # Ввод данных в справочник городов
   $mysqli->query("DELETE FROM weather_cities;");
   $mysqli->query("INSERT INTO weather_cities (name, cid) VALUES ('Нижний Новгород', '27459');");
   $mysqli->query("INSERT INTO weather_cities (name, cid) VALUES ('Москва', '27612');");
   $mysqli->query("INSERT INTO weather_cities (name, cid) VALUES ('Санкт-Петербург', '26063');");
   
   $mysqli->close();      
   
}
install_plugin();