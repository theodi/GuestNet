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
				if (GuestNetUtils::isAdminUser($response1['json']['email'])) 
				{
					if (Auth::instance()->force_login($response1['json'])) 
					{
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

}
