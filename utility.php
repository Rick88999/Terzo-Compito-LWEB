<?php
function streamChanger($filePath){
  $xmlstream="";
  foreach (file($filePath) as $node) {
    $xmlstream.=trim($node);
  }
  return $xmlstream;
}
 ?>
