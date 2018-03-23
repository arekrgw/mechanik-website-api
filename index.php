<?php 


if (isset($_GET['id']) && $_GET['text']){
	$txt = $_GET['id'];
	echo $txt.' '.$_GET['text'];
}
else
echo 'works'
	
?>