<?php
/*
 * Created on 20.03.2013
 *
 * Author:  Lucius Bachmann
 * 
 */
 
 function utf8_encode_deep($array){
	if(!is_array($array)){
		return utf8_encode($array);
	}
	foreach($array as $key=> &$value)
	{
		$value=utf8_encode_deep($value);
		
		
	}
	
	return $array;
	
}

function utf8_decode_deep($array){
	if(!is_array($array)){
		$array=preg_replace("/'/", "&#39;", $array);

		$array=preg_replace("/’/", "&#39;", $array);
		
		$array = str_replace('´', "&#180;", $array);
		
		$array = str_replace('`', "&#96;", $array);
		return utf8_decode($array);
	}
	foreach($array as $key=> &$value)
	{
		$value=utf8_decode_deep($value);
		
		
	}
	
	return $array;
	
}
?>
