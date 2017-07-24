<?php

/**
 * Include session class and init new session
 */
require_once MODELS_DIR . '/Session.php';
Session::init();


/**
 * Import PermaLogin class
 */
require_once MODELS_DIR . "/PermaLogin.php";


/**
 * Check if current session contains already the user data
 */
$userLogged = Session::exists('username');


/**
 * Check if the user required to stay logged in
 */
if (!$userLogged) {

	// Get required_logged_id cookie
	$cookie_token = PermaLogin::getCookie();

	if (!is_null($cookie_token)) {

		/**
		 * Perform Autologin
		 */
		require_once MODELS_DIR . "/Credentials.php";
		$account_data = (new Credentials())->autoLogin($cookie_token);

		// Set session data
		if (count($account_data)>0) {

			Session::addArray($account_data);

		}


		/**
		 * Check if current session contains the user data
		 * that is, has been correctly logged in
		 */
    if ($userLogged = Session::exists('username')) {

    	// do nothing

    } else {

    	// Remove cookie permalogin, since there is no match in the db
    	PermaLogin::removeCookie();

    }

  }

}


/**
 * Define useful global variables
 */
$account_id = Session::get('id');
$isFarm = Session::is('role', "farm");
$isCustomer = Session::is('role', "customer");


/**
 * Prepare variables for twig template
 */
$template_variables['sess'] = $_SESSION;
$template_variables['userLogged'] = $userLogged;
