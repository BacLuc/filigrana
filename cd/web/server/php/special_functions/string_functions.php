<?php
function checkEmail($str)
{
	return preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $str);
}


function hashMe($phrase, &$salt = null){
	$key = '!@#$%^&*()_+=-{}][;";/?<>.,';
	if ($salt == ''){
		$salt = substr(hash('sha512',uniqid(rand(), true).$key.microtime()), 0, SALT_LENGTH);
	}else{
		$salt = substr($salt, 0, SALT_LENGTH);
	}
	
	return hash('sha512',$salt . $key . $phrase).$salt;
}

?>