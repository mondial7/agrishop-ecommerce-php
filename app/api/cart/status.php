<?php

class Status extends EKEApiController {

  /**
   * Main method, automatically run
   */
  public function run(){

    global $userLogged;

    // logged users only
    if (!$userLogged) {

      $this->response = $this->ERR_NOT_LOGGED;
      return $this;

    }

    // require cart model
    require_once MODELS_DIR . '/Cart.php';

    // retrieve items
    $this->response = json_encode((new Cart())->getItems());

  	return $this;

  }


}
