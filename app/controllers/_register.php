<?php

/**
 * Register function
 *
 * @return array registration status
 * ["status"=>,"error_message"=>,"data"=>[]]
 *
 */
function register($email, $username, $role, $password, $passwordCheck, $reCaptcha = 0){

	/**
	 * Default return array
	 */
	$registration_data = ["email" => $email,
												"username" => $username,
												"role" => $role,
									      "password" => $password];
	$registration_ = ["status"=>false, "error_message"=>"", "data"=>$registration_data];

	/**
	 * Check if passwords match
	 */
	if ($password !== $passwordCheck) {

		$registration_["error_message"] = "Passwords do not match.";
		return $registration_;
	}

	/**
	 * Check if password is long enough
	 */
	if (strlen($password) < 5) {

		$registration_["error_message"] = "Password must be more than 4 characters.";
		return $registration_;
	}

	/**
	 * Validate captcha
	 */
	if ($reCaptcha !== 0) {
		require_once MODELS_DIR . '/Captcha.php';
		$captcha = new Captcha($reCaptcha);
		if (!$captcha->isValid()) {

			$registration_["error_message"] = "Please try again, don't forget to click on 'I am not a robot'";
			return $registration_;

		}
	}

	/**
	 * Include and instatiate models
	 */
	require_once MODELS_DIR . '/Profile.php';
	$account = new Profile();
	require_once MODELS_DIR . '/Credentials.php';
	$credentials = new Credentials();

	/**
	 * Set account data
	 */
	$account->setEmail($email);
	$account->setUsername($username);
	$account->setRole($role);
	$account->setPassword($password);

	/**
	 * Check if email already exists
	 */
	if ($credentials->emailExists($account->getEmail())) {

		$registration_["error_message"] = "Email already exists.";
		return $registration_;

	}

	/**
	 * Check if username already exists
	 */
	if ($credentials->usernameExists($account->getUsername())) {

		$registration_["error_message"] = "Username already exists.";
		return $registration_;

	}

	/**
	 * Validate inputs
	 */
	if ($account->isValid()) {

		// Execute query and evaluate result
		if ($credentials->register($account)) {

		    // Send "Welcome email"
		    // ...
				$registration_["status"] = true;

		} else {

			// DB answered with error status
			$registration_["error_message"] = "We had some problem creating your account, try again and if the problem persist, please contact us at info@startuppuccino.com";

		}

	} else {

		$registration_["error_message"] = "Inputs are not valid.";

	}


	return $registration_;
}
