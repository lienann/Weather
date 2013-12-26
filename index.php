<?php
#
## Окно с настройками виджета.
## Внимание! Сначала необходимо запустить файд install_plugin.php 
#
# Подключение файла с настройками
require_once 'config.php';

#
## Список городов. Функция возвращает данные в формате для поля формы "select".
#

function get_citieslist() {

   # Подключение к БД   
   $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
   # Проверка соединения
   if ( mysqli_connect_errno() ) {
       die ('Не удалось подключиться (' . $mysqli->connect_errno . '): ' . $mysqli->connect_error);
   }   

   # Получаем список городов
   $citieslist = '';
   if ( $result = $mysqli->query("SELECT id, name FROM weather_cities ORDER BY name") ) {
   
      while ( $row = $result->fetch_row() ) {
         $citieslist .= '<option value="' . $row[0] . '">'. $row[1] . '</option>';
      }
      
      $result->close();
      
   }
   $mysqli->close();
   return $citieslist;
	
}

#
## Вывод блока с кодом информера. Если JS выключен (форма передает данные), обрабатываем результаты запроса.
#

function get_hiddenarea() {

   $style = 'display: none;';
   $url_attr = '';
   if ( isset($_POST['submit']) ) {
      $style = '';
      $url_attr = '?city=' . (int)$_POST['city'];
      $url_attr .= '&time=' . (int)$_POST['time'];
      $url_attr .= '&position=' . (int)$_POST['position'];
   }
   
   echo '<div class="formrow" id="hiddenarea" style="' . $style . '">
      <textarea id="informer" name="informer"><script src="' . URL_PATH . '/getwidget.php' . $url_attr . '" type="text/javascript"></script></textarea>
     	<br class="wrapper" />
   </div>';
   
}


# Загрузка шаблона страницы
include_once 'index.tpl.php';

