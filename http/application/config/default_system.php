<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
        'default' => array
        (
		'admin_users' => array (
                        '.+@example.org',
                ),
		'company' => array(
			'name' => "My Company Name",
			'subname' => "My Company Subname or Slogan",
			'wifi_net' => "My WiFi Network Application Name",
			'wifi_net_domain' => 'wifi-net.example.org',
      			'website' => "http://www.example.org",
			'cookie_policy_url' => "http://www.example.org/cookie-policy",
		),
		'events' => array(
			'start_time' => '21:00:00',
			'start_period' => '1 days',
			'end_time' => '21:00:00',
                        'end_period' => '3 days',
		),
		'google_oauth2' => array(
			'client_id' => 'SOME_NUMBER.apps.googleusercontent.com',
      			'client_secret' => 'CLIENT_SECRET',
		),
		'guest_accounts' => array(
			'prefix' => 'MyCompany',
			'number_length' => '4',
			'expire_time' => '21:00:00',
			'expire_period' => '3 days',
			'moderate' => FALSE,
		),
		'random_string_length' => 8,
	),
);
