<?php

class Delete extends EKEApiController {

  /**
   * Main method, automatically run
   */
  public function run(){

    // get global scope variables
    global $isFarm, $userLogged;

    // logged users only
    if (!$userLogged) {

      $this->response = $this->ERR_NOT_LOGGED;
      return $this;

    }

    // check user rights
    if (!$isFarm) {

      // keep generic error
      // suppose to be an ambiguous error
      $this->response = $this->ERROR;
      return $this;

    }

    // check if the farm is active
    require_once MODELS_DIR . '/FarmManager.php';
    if (!(new FarmManager())->isActive()) {

      $this->response = $this->ERROR;
      return $this;

    }

    // check required parameter
    if (!isset($_GET['id']) || empty($_GET['id'])) {

      $this->response = $this->ERR_BAD_REQUEST;
      return $this;

    }

    // instantiate product model
  	require_once MODELS_DIR . '/Product.php';
    $product = new Product();

    // set and validate product id
    $product->setId($_GET['id']);

    // validate product
    if ($product->isValid()) {

      // load product manager class
      require_once MODELS_DIR . '/ProductManager.php';

      // try to delete the product
      // NOTE the ownership of the product is evaluated in the query itself
      if ((new ProductManager())->delete($product)) {

        $this->response = $this->STATUS_OK;

      } else {

        $this->response = $this->ERROR;

      }

    }

  	return $this;

  }


}
