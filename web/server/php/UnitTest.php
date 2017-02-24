<?php
/*
 * Created on 06.11.2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 include("include.php");
 //Log::unitTest();
 //DB::unitTest();
 //Person::unitTest();
 //User::unitTest();
 //SocketHandler::unitTest();
 $db = new DB();
 $_SERVER['SERVER_NAME'] = "filigrana.cloudapp.net";
 $db->send_mail("lucius.bachmann@stud.unibas.ch", "lucius.bachmann@gmx.ch", "123", "validation");
?>
