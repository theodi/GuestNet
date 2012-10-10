<?php defined('SYSPATH') or die('No direct access allowed.');

class Auth_Hmac extends Auth {

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
		$hash = new smbHash();
                $hash = $hash->nthash($password);
		
		return $this->_check_radius($username, $hash);
        }

	/**
 	 * See whether credentials match a Radcheck entry
	 * 
	 * @param   string   username
	 * @param   string   hash
	 * @return  boolean
	 */
	private function _check_radius($username, $hash)
	{
		$radcheck = Doctrine::em()->getRepository('Model_Radcheck')->findOneBy(array('username' => $username, 'attribute' => "NT-Password", 'op' => ":="));
                
                if (empty($radcheck))
                        return FALSE;
                elseif ($radcheck->value != $hash)
                        return FALSE;
                return TRUE;
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
		$hash = new smbHash();
            	$hash = $hash->nthash($password);
		$username = $this->get_user();

		return $this->_check_radius($username, $hash);
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

	 /**
         * Is the Auth instance for a user who is currently logged in
         *
         * @return  boolean
         */
        public function logged_in($role = NULL)
        {
		if ($role === NULL)
                	return !is_null($this->get_user());
		elseif ($role == "admin") 
		{
			$user = Doctrine::em()->getRepository('Model_User')->findOneByUsername($this->get_user());
			return !empty($user) && $user->isAdminUser;
		}
		return FALSE;
        }


}
