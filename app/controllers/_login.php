<?php

/**
 * Login function
 *
 * @return boolean login success status
 *
 */
function login($login_userkey, $login_password, $isPermaLogin = false, $attempts = null, $reCaptcha = 0){

	/**
	 * Declare global scope
	 */
	global $login_data;

	/**
	 * Include and instatiate models
	 */
	require_once MODELS_DIR . '/Credentials.php';
	require_once MODELS_DIR . '/Profile.php';
	require_once MODELS_DIR . '/Attempts.php';
	$account = new Profile();
	$credential_func = new Credentials();
	$attempts = $attempts ?: new Attempts();

	/**
	 * Validate captcha
	 */
	if ($reCaptcha !== 0) {
		require_once MODELS_DIR . '/Captcha.php';
		$captcha = new Captcha($reCaptcha);
		if (!$captcha->isValid()) {

			return false;

		}
	}

	/**
	 * Set user inputs
	 */
	$account->setUserkey($login_userkey);
	$account->setPassword($login_password);

	/**
	 * Perform login
	 */
	$account_data = $credential_func->login($account);

	/**
	 * Set defalut values of email/username userkey
	 */
	$login_data['userkey'] = $login_userkey;

	/**
	 * default global variable to switch and redirect if login is successful
	 * or show error message on login_form
	 */
	$loginOk = (count($account_data) > 0);

	/**
	 * If login is successful:
	 * - save session data
	 * - save cookie for permanent login
	 * - redirect to home page
	 */
	if ($loginOk) {

		require_once MODELS_DIR . '/Session.php';
		require_once MODELS_DIR . '/PermaLogin.php';

		// Set session data
		Session::addArray($account_data);

		// Reset attempts
		$attempts->reset();

		// Check if a persistent login has been required
		if  ($isPermaLogin) {

			// Generate, Store and Set Cookie for persistent login
			if (!$credential_func->addPermaLogin()) {
				// catch error, handle exception
			}

		}

	} else {

		// login was not seccessful
		// track this wrong attempt
		$attempts->track();

		// set the reCaptha visible again
		if (!$attempts->valid()) {

			global $require_captcha;
			$require_captcha = true;

		}

	}

	return $loginOk;
}

/**
 * Reset password
 *
 * @return boolean password reset success status
 */
function reset_password($email){

	/**
	 * Declare global scope
	 */
	global $login_data;

	// Set defalut values of email
  $login_data = ['email' => $email];

	/**
	 * Include and instatiate models
	 */
	require_once MODELS_DIR . '/Credentials.php';

	return (new Credentials())->reset_password($email);

}


/**
 * Set the new password
 *
 * @return boolean password reset success status
 */
function set_new_password($password){

	/**
	 * Include and instatiate models
	 */
	require_once MODELS_DIR . '/Credentials.php';

	return (new Credentials())->setNewPassword($password);

}
