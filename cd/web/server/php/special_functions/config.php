<?php
/*
 * Created on 05.01.2013
 *
 * Copyright by Lucius Bachmann
 * 
 */


define("SOCKET_PORT", "12345");
$GLOBALS['dblk'] = new PDO("pgsql:host=".DB_HOST.";dbname=".DB_NAME.";port=".DB_PORT , DB_USER, DB_PASSWORD);

function getDblk(){
	return $GLOBALS['dblk'] ;
}   
  
  
  

?>
