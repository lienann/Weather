<?php
# 
## Вывод виджета
#

   # Подключение файла с настройками
   require_once 'config.php';

   # Настройки по умолчанию
   $default = array (
      'city'       => 3,
      'time'       => 1,
      'position'   => 1
   );
   $today = date("Y-m-d 00:00:00", time());

   # Проверка передаваемых значений
   $city     = (int)$_GET['city'];
   $time     = (int)$_GET['time'];
   $position = (int)$_GET['position'];

   if ( ! ( $city ) ) $city = $default['city'];
   if ( ! preg_match( "/[137]{1}/", $time ) ) $time = $default['time'];
   if ( ! preg_match( "/[01]{1}/", $position ) ) $position = $default['position'];

   # Подключение к БД   
   $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
   # Проверка соединения
   if ( mysqli_connect_errno() ) {
       die ('Не удалось подключиться');
   }   
   # Выбираем данные за период $time(в днях) начиная с сегодняшнего
   $data = '';

  	$stmt = $mysqli->stmt_init();      
 	if ( $stmt->prepare(" SELECT time, t_min, t_max, cloud, conditions FROM weather_forecast WHERE city_id = ? AND time >= ? LIMIT ? ") === FALSE
  	  || $stmt->bind_param('isi', $city, $today, $time) === FALSE
  	  || $stmt->execute() === FALSE) 
     {
      die('Ошибка запроса');
     }  	      	
   else {
     	$result = $stmt->get_result();

     	# Стили для горизонтального и вертикального расположения блоков
      if ($position == '1') {
        	$style_row = 'float: left; height: 100%; width: 120px;';
        	$style = 'height: 80px;';
      }
      else {
        	$style_row = 'height: 100%';
        	$style = 'width: 120px;';
      }
      while ( $row = $result->fetch_row() ) {
         $data .= '<div class="weather_widget_row" style="border: 1px solid #CCC; font-size: 10px; padding: 3px; ' . $style_row . '">';
         $data .= '<b>' . setlocale(LC_ALL, 'ru_RU.utf8', 'rus_RUS.utf8'). strftime("%A", strtotime($row[0])) . '</b>' . '<br />';
         $data .= '<img src="'.ICON_PATH.'/'.$row[3].'" /> <span>'.$row[1].' | '.$row[2].' °C </span><br />'; 
         $data .= '<i style="display: block; bottom: 0;">' . $row[4] . '</i></div>'; 
         $cities[$row[0]] = $row[1];
         }
     }
      
 	$stmt->close();
   $mysqli->close();   

# Передаем виджет
if ( ! empty ($data) )  {
header("Content-Type: application/x-javascript; charset=UTF-8"); 
echo 'document.write("<div class=\"weather_widget\" style=\"'.$style.'\">'.addslashes($data).'</div>")';	
}
