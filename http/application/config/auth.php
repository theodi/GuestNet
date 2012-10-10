<?php defined('SYSPATH') or die('No direct access allowed.');

global $use_auth_type;

if (empty($use_auth_type))
	$use_auth_type = "Hmac";

//echo "Auth type: $use_auth_type";
return array(

        'driver'       => $use_auth_type,
        'hash_method'  => 'sha256',
        'hash_key'     => '123454321',
        'lifetime'     => 1209600,
        'session_type' => Session::$default,
//        'session_key'  => ( $use_auth_type == "Google" ? 'googleoauth2' : 'auth_user' ),
	'session_key'  =>  'auth_user',
);

