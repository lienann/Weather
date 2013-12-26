function GetCode(url) {

   var InformerId = document.getElementById('informer');
   var HiddenareaId = document.getElementById('hiddenarea');
   var CityId = document.getElementById('city');
   var TimeId = document.getElementById('time');

   var PositionId = document.getElementById('position');
   var position = '0';
   if (PositionId.checked) { position = '1'; }
   
   InformerId.value = "";
   InformerId.value = '<script src="'+url+'/getwidget.php?city='+CityId.value+'&time='+TimeId.value+'&position='+position+'" type="text/javascript"></script>';
   HiddenareaId.style.display="";	
   return false;
   
}

