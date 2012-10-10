<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Login extends Controller_AuthTemplate
{
	public function action_login_page() {
		if ($this->request->method() == 'POST')
		{
			$post = $this->request->post();
			if (!empty($post['login'])) 
			{
				$success = Auth::instance()->login($post['username'], $post['password']);

				if($success)
				{
					$user = Doctrine::em()->getRepository('Model_User')->findOneByUsername($post['username']);
					if ($this->request->query('url'))	
						$this->request->redirect($this->request->query('url'));
					else
						$this->request->redirect(Route::url('main'));
				}
				else
					$this->template->message = "Login Failed";
			}
			elseif (!empty($post['redeem']))
			{
	//			Model_Regcode::redeemRegistrationCode($post['regcode'];
			}
		}
		elseif (Auth::instance()->logged_in()) 
		{
			$this->request->redirect(Route::url('main'));	
		}
		$this->template->title = "Wireless Control Panel";
		$this->template->content = View::Factory("pages/login");
		
	}			
	
	public function action_logout()
	{
		$success = Auth::instance()->logout();
		if ($success)
			$this->request->redirect(Route::url('login'));
	}

}
