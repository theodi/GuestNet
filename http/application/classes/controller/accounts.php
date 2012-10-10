<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Accounts extends Controller_AuthTemplate
{
	public function action_default()
        {
		$user = $this->check_login();
		if ($this->request->method() == 'POST')
                {
                        $post = $this->request->post();	
			if (empty($post["current"]) || empty($post["new1"]) || empty($post["new2"]))
				$this->template->message = "Not all fields were filled in";
			elseif ($post["new1"] != $post["new2"])
				$this->template->message = "Passwords did not match";
      			elseif (!$user->checkPassword($post["current"]))
                  		$this->template->message = "Current Password Incorrect";
     			elseif ($user->checkPassword($post["current"]) && ($post["new1"] == $post["new2"])) {
            			if ($user->setPassword($post["new1"])) 
                  			$this->template->message = "Password successfully changed";
            			else 
                  			$this->template->message = "Failed to change password";
            		}
      		}

		$this->template->title = "My Account";
		$this->template->content = View::factory('pages/myaccount');              
        }

	public function action_guests()
	{
		$guest_accounts = Kohana::$config->load('system.default.guest_accounts');
		$valid_to_default = date("Y-m-d " . $guest_accounts['expire_time'], strtotime("+" . $guest_accounts['expire_period']));
		$user = $this->check_login('admin');
		if ($this->request->method() == 'POST')
                {
			$post = $this->request->post();
			if (isset($post['addguest'])) 
			{
				list($guest, $this->template->message) = Model_User::addGuestUser($post, $user);
				if (is_object($guest))
					$post=array();
			}
			elseif (isset($post['reactivate']))
			{
				$this->template->message = Model_User::reactivateUser($post['username'], $valid_to_default);
			}
			elseif (isset($post['expire']))
			{
				$this->template->message = Model_User::expireUser($post['username']);
			}
			elseif (isset($post['reset_pass']))
                        {
				$this->template->message = Model_User::resetPassword($post['username']);
                        }
		}
		$this->template->heading = "Add New Guest Account";
		$this->template->title = "Manage Guest Accounts";
		$guests = Doctrine::em()->getRepository('Model_User')->findBySponsor($user);		
                $this->template->content = View::factory('pages/guests')->bind('guests', $guests)->bind('valid_to_default', $valid_to_default)->bind('post', $post);

	}
}

