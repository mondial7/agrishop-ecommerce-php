<?php

class Checkout extends EKEApiController {

	/**
	 * Main method, automatically run
	 */
	public function run() {

		global $userLogged;

    // logged users only
    if (!$userLogged) {

      $this->response = $this->ERR_NOT_LOGGED;
      return $this;

    }

		// check parameters
		if (!isset($_POST['password']) ||
        !isset($_POST['paytype']) ||
				empty($_POST['paytype'])) {

			$this->response = $this->ERR_BAD_REQUEST;
			return $this;

		}

		// verify user
		require_once MODELS_DIR . '/Credentials.php';
		require_once MODELS_DIR . '/Profile.php';
		$user = new Profile();
		$user->setPassword($_POST['password']);
		if (!(new Credentials())->verify($user)) {

			$this->response = $this->ERROR;
			return $this;

		}

		// Add the Cart model to the class and instantiates it
		require_once MODELS_DIR . '/Cart.php';

		if ((new Cart())->buyProducts($_POST['paytype'])) {

			$this->response = $this->STATUS_OK;

		} else {

			$this->response = $this->ERROR;

		}

		// Do something with the cart, like remove products
		return $this;
	}

}
