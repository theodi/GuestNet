<?php defined('SYSPATH') or die('No direct script access.');
class GuestNetUtils 
{
	
	public static function generateRandomString($stringLength = NULL)
	{
		if(is_null($stringLength))
			$stringLength = Kohana::$config->load('system.default.random_string_length');
		$string = "";
		$stringChars = "0123456789";
                $stringChars .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $stringChars .= "abcdefghijklmnopqrstuvwxyz";

                for ($i=0; $i<$stringLength; $i++){
                	$string .= $stringChars[(rand() % strlen($stringChars))];
                }
            	return $string;
	}
}	
