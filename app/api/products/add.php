<?php

class Add extends EKEApiController {

  /**
   * @var product (model)
   */
  private $product;

  /**
   * Main method, automatically run
   */
  public function run() {

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

    // check if required parameters are set
    $info_parameters = ['category_id', 'area_id', 'quantity', 'price', 'produced', 'name'];


    if (!areset($info_parameters)) {

      $this->response = $this->ERR_BAD_REQUEST;
      return $this;

    }

    // instantiate product model
  	require_once MODELS_DIR . '/Product.php';
    $this->product = new Product();

    // prepare product, validate inputs
    if ($this->fillProduct()) {

      // load product manager class
      require_once MODELS_DIR . '/ProductManager.php';

      // try to add the product
      if ((new ProductManager())->add($this->product)) {

        $this->response = $this->STATUS_OK;

      } else {

        $this->response = $this->ERROR;

      }

    }

  	return $this;

  }

  /**
   * Set product info from parameters
   *
   * @return boolean (isValid)
   */
  private function fillProduct() {

  	$this->product->setCategory($_GET['category_id']);
  	$this->product->setArea($_GET['area_id']);
  	$this->product->setQuantity($_GET['quantity']);
  	$this->product->setPrice($_GET['price']);
  	$this->product->setProduced($_GET['produced']);
  	$this->product->setName($_GET['name']);

  	$this->product->setFarm(Session::get('id'));

    return $this->product->isValid();

  }




}
