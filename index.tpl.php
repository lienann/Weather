<!DOCTYPE html>
<html lang="ru">
 <head>
  <meta charset="utf-8">
  <title>Погодный инофрмер</title>
  <script type="text/javascript" src="common.js"></script>
  <link rel="stylesheet" type="text/css" media="all" href="style.css" />  
 </head>
 <body>
   <div id="main">
      <div id="header">
         <h1>Погодный инофрмер</h1>
      </div>
      <div id="content">
        <div id="formblock">
         <h3>Настраиваемые параметры:</h3>
         <form action="" method="post" name="form-weather" enctype="multipart/form-data">
  
	     		<div class="formrow">
			   	<div class="label">Город</div>
				  <div class="value">
				     <select id="city" name="city" type="text" class="field">
                   <?php echo get_citieslist(); ?>				     
   				  </select>
	     			</div>
	     			<br class="wrapper" />
		    	</div>			
					
   			<div class="formrow">
	     			<div class="label">На сколько дней выдавать прогноз?</div>
			   	<div class="value">
				      <select id="time" name="time" class="field">
                     <option value="1">1 день</option>
                     <option value="3">3 дня</option>
                     <option value="7">неделя</option>
	               </select>
   				</div>
	     			<br class="wrapper" />
	     		</div>
			
   			<div class="formrow">
	     			<div class="label">Вид блока</div>
			   	<div class="value">
				       <input type="radio" name="position" value="0" checked="checked">&nbsp;вертикальный
                   <input type="radio" id="position" name="position" value="1">&nbsp;горизонтальный
   				</div>
	     			<br class="wrapper" />
   			</div>
   
	     		<div class="formrow" id="submitarea" style="display: block;">
			      <input type="submit" name="submit" value="Получить код информера" onClick="return GetCode('<?php echo URL_PATH;; ?>');">
	     			<br class="wrapper" />
   			</div>
         </form>
  			<?php echo get_hiddenarea(); ?>
        </div>
      </div><!-- #content -->
    </div><!-- #main -->
 </body>
</html>
