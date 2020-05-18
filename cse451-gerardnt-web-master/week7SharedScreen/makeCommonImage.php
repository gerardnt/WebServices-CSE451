<?php
require "db.php";
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

  $width=410; 
  $height=410; 

  Header("Content-type: image/gif"); 
  $im = imagecreate($width,$height); 
  $gray=ImageColorAllocate($im,225,225,225); 

  $black=ImageColorAllocate($im,0,0,0); 

  // get the uid of who is currently requesting the image add them to the current user list 
  addCurrentUsers($_REQUEST['uid']);

  $points = getPoints();
  foreach ($points as $p) {
	  $a =print_r($p,true);
	  error_log($a);
	  $color = ImageColorAllocate($im,$p['r'],$p['g'],$p['b']);
	  imagefilledrectangle($im,$p['x'],$p['y'],$p['x1'],$p['y1'],$color);
  }
  imagePng($im); 
  ImageDestroy($im); 


?>
