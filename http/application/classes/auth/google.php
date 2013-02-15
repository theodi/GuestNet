<?php defined('SYSPATH') or die('No direct access allowed.');

class Auth_Google extends Auth {

        /**
         * Logs a user in.
         *
         * @param   string   username
         * @param   string   password
         * @param   boolean  remember (not supported)
         * @return  boolean
         */
        protected function _login($username, $password, $remember)
        {
                if($this->check_credentials($username, $password))
                {
                        // Complete the login
                        return $this->complete_login($username);
                }

                // Login failed
                return FALSE;
        }

        /**
         * Checks that the a user credentials can be validated by Google.
         *
         * @param   string   username
         * @param   string   password
         * @return  boolean
         */
        private function check_credentials($username, $password)
        {
                //TODO: Logic needs writing
                return FALSE;
        }
	/**
         * Get the stored password for a username. (Not supported by this auth driver, obviously).
         *
         * @param   mixed   username
         * @return  string
         */
        public function password($username)
        {
                return NULL;
        }

        /**
         * Compare password with original (plain text). Works for current (logged in) user.
         *
         * @param   string  password
         * @return  boolean
         */
        public function check_password($password)
        {
                $username = $this->get_user();

                if ($username === FALSE)
                {
                        return FALSE;
                }

                return $this->check_credentials($username, $password);
        }

        /**
         * Change the authenticated users password. (Not supported by this auth driver, obviously).
         *
         * @param   string  old
         * @param   string  new
         * @return  boolean
         */
        public function change_password($old, $new)
        {
                return FALSE;
        }

	public function force_login($credentials, $mark_session_as_forced = FALSE)
	{
		$user = Doctrine::em()->getRepository('Model_User')->findOneByEmail($credentials['email']);
                if (!is_object($user)) {
                	$newuser = new Model_User();
                        $newuser_and_domain = explode("@", $credentials['email']);
                        $newuser->email = $credentials['email'];
                        $newuser->name = $credentials['name'];
                        $newuser->picPath = (empty($credentials['picture']) ? NULL : $credentials['picture']);
			$newuser->validTo = new \DateTime('2030-12-31 23:59:00');
			$newuser->active = TRUE;
                        $newuser->save();
			$newuser->username = $newuser_and_domain[0];
			$newuser->save();
			$user = Doctrine::em()->getRepository('Model_User')->findOneByEmail($credentials['email']);
			$radcheck = Model_Radcheck::addNTPassword($user->username, GuestNetUtils::generateRandomString());
			if (!is_object($radcheck))
				return FALSE;

                }
		
		if ($mark_session_as_forced === TRUE)
    		{
        		// Mark the session as forced, to prevent users from changing account information
        		$this->_session->set($this->_config['forced_key'], TRUE);
    		}
 
    		// Run the standard completion
		if (is_object($user))
		{	
    			$this->complete_login($user->username);
			return $this->logged_in();
		}
		return FALSE;
	}
}
