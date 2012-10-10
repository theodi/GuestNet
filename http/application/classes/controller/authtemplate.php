<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_AuthTemplate extends Controller_Template
{
        public $template = 'template';

	public function before()
	{
		$current_uri = $this->request->uri();
		View::bind_global('current_uri', $current_uri);
		$company = Kohana::$config->load('system.default.company');
		View::bind_global('company', $company);
		parent::before();
	}
        protected function check_login($role = NULL)
        {
                if (!Auth::instance()->logged_in($role))
                {
                        if (!Auth::instance()->logged_in())
                                $this->request->redirect(Route::url('login').URL::query(array('url' => $this->request->url())));
                        else
                                throw new HTTP_Exception_403('You do not have permission to access this page.');
                }
		$user = Doctrine::em()->getRepository('Model_User')->findOneByUsername(Auth::Instance()->get_user());
		View::bind_global('user', $user);
		return $user;
        }

        protected function test_login($role = NULL)
        {
                if (!Auth::instance()->logged_in($role))
                        return FALSE;
                return TRUE;
        }

}

