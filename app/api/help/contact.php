<?php

class Contact extends EKEApiController {

  /**
   * Main method, automatically run
   */
  public function run(){

    // check parameters
    if (!areset(['from','message'])) {

      $this->response = $this->ERR_BAD_REQUEST;
      return $this;

    }

    // Remove all illegal characters from email
    $email = filter_var($_GET['from'], FILTER_SANITIZE_EMAIL);

    // Validate sender e-mail
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {

      $this->response = $this->ERROR;
      return $this;

    }

    // send email
    if (mail('agrishop@thatsmy.name', 'Help Request', $message, "From: $email")) {

      $this->response = $this->STATUS_OK;

    } else {

      $this->response = $this->ERROR;

    }

  	return $this;

  }


}
