<?php

class Update extends EKEApiController {

  /**
   * @var Cart (Model)
   */
  private $cart;

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

    // init cart model
    require_once MODELS_DIR . '/Cart.php';
    $this->cart = new Cart();

    // switch between operations and field requested

    // o => operation [ add, remove ]
    // i => item [ product id ]
    if (!areset(['o','i'])) {

      $this->response = $this->ERR_BAD_REQUEST;
      return $this;

    }

    // validate input
    if (!is_numeric($item = $_GET['i'])) {

      $this->response = $this->ERR_BAD_REQUEST;
      return $this;

    }

  	// switch according to operation
    switch ($_GET['o']) {

      case 'add':

        if ($this->cart->add($item)) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      case 'remove':

        if ($this->cart->remove($item)) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      case 'empty':

        if ($this->cart->clean()) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      default:

        $this->response = $this->ERR_BAD_REQUEST;

        break;
    }


  	return $this;

  }

}
