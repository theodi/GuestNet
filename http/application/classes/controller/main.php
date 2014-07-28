<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Main extends Controller_AuthTemplate
{
        public function action_default()
        {
		$user = $this->check_login();
	
		$this->template->title = "Home";
		$this->template->heading = "Welcome " . $user->name . " to " .  Kohana::$config->load('system.default.company.wifi_net');
		$this->template->content = View::Factory("pages/myaccount");
		$this->template->user = $user;
	
        }
}

