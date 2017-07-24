<?php

if ($userLogged) {

	require_once MODELS_DIR . "/PermaLogin.php";

	// Delete Permalogin
	if (!is_null($cookie_token = PermaLogin::getCookie())) {

		// delete record
		require_once MODELS_DIR . '/Credentials.php';
		if (!(new Credentials())->removePermaLogin($cookie_token)) {
			// here set an application log error, handle exception
		}

		// delete cookie
		PermaLogin::removeCookie();

	}

	Session::end();

}

header("Location: ../");
