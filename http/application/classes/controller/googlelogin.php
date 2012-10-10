<?php defined('SYSPATH') or die('No direct script access.');

class Controller_GoogleLogin extends Controller_AuthTemplate
{
	
	protected $scope = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';

	public function _before() {
		parent::before();
	}

	public function action_default() {
		global $use_auth_type;
		$use_auth_type = "Google";
		require_once Kohana::find_file('vendor', 'class-xhttp-php/class.xhttp', 'php');
		$google_oauth2 = Kohana::$config->load('system.default.google_oauth2');
		$get = $this->request->query();
		if (isset($get['signin'])) {
			# STEP 2:
			# Build URL for OAuth2 authorization
      			$url = "https://accounts.google.com/o/oauth2/auth?".http_build_query(array(
				'client_id' => $google_oauth2['client_id'],
            			'redirect_uri' => URL::base(TRUE, TRUE) . $this->request->uri(),
            			'scope' => $this->scope,
            			'response_type' => 'code'
      			));
      			
			# STEP 3:
      			# Redirect user to URL for authorization;
			$this->request->redirect($url);
		}
		elseif (isset($get['code'])) {
			# STEP 4:
      			# User granted access to us; User is redirected back to our application; code parameter is included

			# STEP 5:
	      		# Exchange code for access token and secret
      			$data = array('post' => array(
            			'code' => $_GET['code'],
            			'client_id' => $google_oauth2['client_id'],
            			'client_secret' => $google_oauth2['client_secret'],
            			'redirect_uri' => URL::base(TRUE, TRUE) . $this->request->uri(),
            			'grant_type' => 'authorization_code',
      			));
      			$response = xhttp::fetch('https://accounts.google.com/o/oauth2/token', $data);

      			if ($response['successful']) {

            			# STEP 6:
	            		# We got the access token; User is now logged in
				$response1 = xhttp::fetch('https://www.googleapis.com/oauth2/v1/userinfo?alt=json', array(
                                	'headers' => array(
                                        'Authorization' => "OAuth " . $response['json']['access_token'],
	                        )));
				$user = Doctrine::em()->getRepository('Model_User')->findOneByEmail($response1['json']['email']);
				if ($user->isAdminUser()) 
				{
					if (Auth::instance()->force_login($response1['json'])) {
						$this->request->redirect(Route::url('main'));
					}
					else {
						die("Credentials were wrong");
						# Something wrong with credentials
					}
				}	
				else {
					die("Not an admin user");
					# Not a admin user
				}	
      			} 
			else {
				die("No success response");
            			# STEP 6: Alternate
           			# Unable to get access token; repeat STEP 5 or give up
      			}
		}
		elseif(isset($get['error'])) {

      			# STEP 4: Alternate
      			# User refused to give access to his email address; Ask feedback, optional; Repeat STEP 1

		} 
		elseif(isset($get['logout'])) {
      			# STEP 10:
      			# Log out of session; delete cookies
			$success = Auth::instance()->logout();
      			if ($success)
                        	$this->request->redirect(Route::url('login'));
		}
		if(!Auth::instance()->logged_in()) {
      			# STEP 1: Provide link to user to Sign in with Google
      			#echo '<a href="?signin">Sign in with Google</a>.';
      			$this->request->redirect(Route::url('login'));
		}
	}


	public function action_logout()
	{
		$success = Auth::instance()->logout();
		if ($success)
			$this->request->redirect(Route::url('login'));
	}

	public function action_forgot_password()
	{
		$domain = Kohana::$config->load('system.default.domain');

                $this->template->title = "Forgot Password";
                $content =  View::Factory("pages/forgot_password");
		$content->info = array();

		if ($this->request->method() == 'POST')
                {
			$user = null;
                        $post = $this->request->post();
			$username_email = strtolower($post['username_email']);
                        if (preg_match("/^[a-zA-Z0-9\-_.]+@" . $domain . "$/", $post['username_email']))
			{
				$user = Doctrine::em()->getRepository('Model_User')->findOneByUsername($username_email); 
			}
			else if (preg_match("/^[a-zA-Z0-9\-_.]+$/", $post['username_email']))
			{
				$user = Doctrine::em()->getRepository('Model_User')->findOneByUsername($username_email . "@" . $domain);
			}
			else if (preg_match("/^[a-zA-Z0-9\-_.]+@[a-zA-Z0-9\-_.]+$/", $username_email))
			{
				$users = Doctrine::em()->getRepository('Model_User')->findByEmail($username_email);
				foreach ($users as $tempuser){
					if (strpos($tempuser->username, "@sown.org.uk") > 0)
					{
						$user = $tempuser;
						break;
					}
				}
			}

			if (!empty($user))
			{
				$admin_system_url = Kohana::$config->load('system.default.admin_system.url');
				$sender_name = Kohana::$config->load('system.default.admin_system.sender_name');
				$user->resetPasswordHash = md5($user->username . date('U') . rand());
				$user->resetPasswordTime = new \DateTime();
				$user->save();
				$email_body = "Hi " . $user->username . ",\n\nSomeone has requested a password reset for your account.  If this was not you, just ignore this email and the request will expire in 24 hours.  Otherwise, click the following link by " . date('H:i', time()) . " tomorrow to reset your password:\n\n" . $admin_system_url . "/reset_password/" . $user->resetPasswordHash . "\n\nRegards\n\n$sender_name\n" . Kohana::$config->load('system.default.admin_system.contact_email');
				mail($user->email, Kohana::$config->load('system.default.admin_system.email_subject_prefix') . " Password reset", $email_body, "From: $sender_name <" . Kohana::$config->load('system.default.admin_system.sender_email') . ">");
				$content->info['notice'][] = "An email has been sent to you with a reset password URL";
			}
			else 
				$content->info['error'][] = "Username / Email address does not belong to a @". $domain ." user";
		}
		
		$this->template->content = $content;
	}
	
        public function action_change_password()
        {
                $this->check_login();
                $this->template->title = "Change Password";
                $this->template->sidebar = View::factory('partial/sidebar');

                if(!Auth::instance()->is_local())
                {
                        $this->template->content = "<p style=\"text-align: center; font-weight: bold; font-size: 1em;\">Sorry, but your account password cannot be changed via our system.</p>";
                }
                else
                {
                        $content = View::factory('pages/change_password');
                        $content->username = Auth::instance()->get_user();
                        $content->info = array();
                        if($this->request->method() == "POST")
                        {
                                $oldpassword = $this->request->post('oldpassword');
                                $password1 = $this->request->post('password1');
                                $password2 = $this->request->post('password2');
                                if($password1 != $password2)
                                {
                                        $content->info['error'][] = "New passwords do not match";
                                }
                                else
                                {
                                        if(!Auth::instance()->change_password($oldpassword, $password1))
                                        {
                                                $content->info['error'][] = "Failed to update password";
                                        }
                                        else
                                        {
                                                $content->info['notice'][] = "Password updated successfully";
                                        }
                                }
                        }
                        $this->template->content = $content;
                }
        }

        public function action_reset_password()
        {
                $this->template->title = "Reset Password";
                $content = View::factory('pages/reset_password');
                $content->username = Auth::instance()->get_user();
                $content->info = array();
                $content->show_form = true;

                $user = NULL;
                $reset_password_hash = $this->request->param('hash');
                if (!empty($reset_password_hash))
                        $user = Doctrine::em()->getRepository('Model_User')->findOneByResetPasswordHash($reset_password_hash);

                if (!empty($user))
                {
                        if ($user->resetPasswordTime !== NULL && $user->resetPasswordTime->getTimestamp()+86400 > time())
                        {
                                $content->username = $user->username;
                                if($this->request->method() == "POST")
                                {
                                        $password1 = $this->request->post('password1');
                                        $password2 = $this->request->post('password2');
                                        if($password1 != $password2)
                                        {
                                                $content->info['error'][] = "New passwords do not match";
                                        }
                                        else
                                        {
                                                if(!RadAcctUtils::ResetPassword($user->username, $password1))
                                                {
                                                        $content->info['error'][] = "Failed to update password";
                                                }
                                                else
                                                {
                                                        $user->resetPasswordHash = "";
                                                        $user->resetPasswordTime = NULL;
                                                        $user->save();
                                                        $content->info['notice'][] = "Password updated successfully.  <a href='/'>Click here</a> to login.";
                                                        $content->show_form = false;
                                                }
                                        }
                                }
                        }
                        else
                        {
                                $user->resetPasswordHash = "";
                                $user->resetPasswordTime = NULL;
                                $user->save();
                                $content->info['error'][] = "Reset password URL has expired";
                                $content->show_form = false;
                        }

                }
                else
                {
                        $content->info['error'][] = "User account cannot be found for reset password hash";
                        $content->show_form = false;
                }
                $this->template->content = $content;
        }

}
