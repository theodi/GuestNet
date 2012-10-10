<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Registrationcodes extends Controller_AuthTemplate
{
	public function action_redeem()
	{
		$this->check_login();
		$this->template->title = "Redeem Registration Codes";
		$this->template->content = View::factory('pages/redeem');
	}

	public function action_generate()
	{
		$this->check_login('admin');
		$content = View::factory('pages/codes');
		$get = $this->request->query();
		if (!empty($get['regcode']))
		{
			$content->regcode = $get['regcode'];
			$this->template->title = "Update Registration Code";
			$event = Doctrine::em()->getRepository('Model_Regcode')->findOneByregcode($get['regcode']);
                	$content->bind('event', $event);
		}
		else
			$this->template->title = "Create New Registration Code";
		$events = Kohana::$config->load('system.default.events');
		$post["validFrom"] = date("Y-m-d " . $events['start_time'], strtotime("+" . $events['start_period']));
            	$post["validTo"] = date("Y-m-d " . $events['end_time'], strtotime("+" . $events['end_period']));
		if ($this->request->method() == 'POST')
                {
                        $post = $this->request->post();
			list($regcode, $this->template->message) = Model_Regcode::manageEvent($post);
			if (is_object($regcode))
			{
				$this->template->title = "Update Registration Code";
				$regcode = ""; 
			}
			if (!empty($post['regcode']))
				$content->regcode = $post['regcode'];
		}
		$events = Doctrine::em()->getRepository('Model_Regcode')->findAll();
                $content->bind('events', $events);
		
		$content->post = $post;	
		$this->template->content = $content;
		
	}
}

