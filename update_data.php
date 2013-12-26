<?php

# Подключение файла с настройками
require_once 'config.php';

#
## Обработка лога погоды с сайта http://wunderground.com. Файл доступен по адресу http://api.wunderground.com/api/{код учетной записи}/forecast10day/lang:RU/q/zmw:00000.1.{код города}.json.
## Ввод данных лога в таблицу weather_forecast
#
function update_data() {

   # Подключение к БД   
   $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
   # Проверка соединения
   if ( mysqli_connect_errno() ) {
       die ('Не удалось подключиться (' . $mysqli->connect_errno . '): ' . $mysqli->connect_error);
   }   

   # Получаем список городов
   $cities = array();
   if ( $result = $mysqli->query("SELECT id, cid FROM weather_cities") ) {
   
      while ( $row = $result->fetch_row() ) {
         $cities[$row[0]] = $row[1];
      }
      
      $result->close();
      
   }

   # Временная метка
   $today  = mktime( 0, 0, 0, date("m"), date("d"), date("Y") );
   # Забираем данные для каждого из городов и записываем их в базу     
   foreach ( (array) $cities as $key => $city ) {
   
      $file_name = 'http://api.wunderground.com/api/'.API_KEY.'/forecast10day/lang:RU/q/zmw:00000.1.'.$city.'.json';
      $json_string = file_get_contents($file_name); 
      if ( !$json_string ) continue;
 
      $parsed_json = json_decode($json_string); 
      if ( $parsed_json->{response}->{error} ) continue;

      $forecastday = $parsed_json->{forecast}->{simpleforecast}->{forecastday};
   	
   	$i = 1;
   	# Период, за который снимаем даные
   	$cdays = 7;
      
      foreach ( (array) $forecastday as $forecast ) {
         
         # Обрабатываем данные только для дат позднее сегодняшней
         $cur_time_unix = (int)$forecast->date->epoch;
         if ( $cur_time_unix < $today && ! preg_match( "/[0-9]{10}/", $cur_time_unix ) ) continue;
         
         $cur_time = date("Y-m-d H:i:00", $cur_time_unix);

         # Начало и конец измеряемого периода/дня
         $cur_ftime = date("Y-m-d 00:00:00", $cur_time_unix);
         $cur_etime = date("Y-m-d 23:59:59", $cur_time_unix);
            
         # Параметры погоды, проверка получаемых значений
         $t_min = (int) $forecast->low->celsius;
         $t_max = (int) $forecast->high->celsius;
         $cloud = $forecast->icon;
         $conditions = htmlspecialchars($forecast->conditions, ENT_QUOTES);
            
         # Заносим данные в БД
         if ( preg_match( "/[0-9a-z_]{1,127}/", $cloud ) ) {

            $stmt = $mysqli->stmt_init();
            # Удаление старой записи за ту же дату, если она есть в БД
          	if ( $stmt->prepare(" DELETE FROM weather_forecast WHERE city_id = ? AND time >= ? AND time <= ? ") === FALSE
            	  || $stmt->bind_param('iss', $key, $cur_ftime, $cur_etime) === FALSE
            	  || $stmt->execute() === FALSE
            	  || $stmt->close() === FALSE ) 
               {
            	die('Ошибка удаления данных (' . $stmt->errno . ') ' . $stmt->error);
               }
            else {
            	$stmt = $mysqli->stmt_init();
            	$stmt->prepare(" INSERT INTO weather_forecast (city_id, time, t_min, t_max, cloud, conditions) VALUES (?, ?, ?, ?, ?, ?); ");
            	$stmt->bind_param("isiiss", $key, $cur_time, $t_min, $t_max, $cloud, $conditions);
            	$stmt->execute();
            	$stmt->close();
               }  
               
            }

         $i++; if ( $i > $cdays ) break;
         }
      }
   
   $mysqli->close();      
	
}

#
## Чистка таблицы weather_forecast от устаревших записей (3 месяца)
#
function clean_data() {
   
   # Дата, старее которой данные будут удаляться
   $date_unix = time() - 60*60*24*30*3;
   $date = date("Y-m-d 00:00:00", $date_unix);

   # Подключение к БД   
   $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
   # Проверка соединения
   if ( mysqli_connect_errno() ) {
       die ('Не удалось подключиться (' . $mysqli->connect_errno . '): ' . $mysqli->connect_error);
   }
   
   $stmt = $mysqli->stmt_init();
  	if ( $stmt->prepare(" DELETE FROM weather_forecast WHERE time <= ? ") === FALSE
      || $stmt->bind_param('s', $date) === FALSE
      || $stmt->execute() === FALSE
      || $stmt->close() === FALSE ) 
      {
      die('Ошибка удаления данных (' . $stmt->errno . ') ' . $stmt->error);
      }   
   
   $mysqli->close();    
      
}

update_data();
clean_data();
