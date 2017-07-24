<?php

class Addresses extends EKEApiController {

  /**
   * @var Address (model)
   */
  private $address;

  /**
   * @var AddressManager (model)
   */
  private $manager;

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

  	// TODO add check on number of addresses??

    // TODO check difference between farm and customer??

    // check parameters
    if (!isset($_GET['o']) || empty($_GET['o'])) {

      $this->response = $this->ERR_BAD_REQUEST;
      return $this;

    }

    // include and instatiate models
    require_once MODELS_DIR . '/Address.php';
    require_once MODELS_DIR . '/AddressManager.php';
    $this->address = new Address();
    $this->manager = new AddressManager();

    // switch according to the operation
    switch ($_GET['o']) {

      case 'add':

        if (!areset(['cap','city','name','street'])) {

          $this->response = $this->ERR_BAD_REQUEST;

        } else if ($this->addAddress()) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      case 'update':

        if (!areset(['id','cap','city','name','street'])) {

          $this->response = $this->ERR_BAD_REQUEST;

        } else if ($this->updateAddress()) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      case 'remove':

        if (!isset($_GET['id']) || empty($_GET['id'])) {

          $this->response = $this->ERR_BAD_REQUEST;

        } else if ($this->removeAddress()) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      case 'get':

        $this->response = json_encode($this->manager->getAddresses());

        break;

      default:

        $this->response = $this->ERROR;

        break;

    }

  	return $this;

  }


  /**
   * Add address
   *
   * @return boolean
   */
  private function addAddress() {

    // prepare and validate address
    $this->address->setCap($_GET['cap']);
    $this->address->setCity($_GET['city']);
    $this->address->setName($_GET['name']);
    $this->address->setStreet($_GET['street']);

    // add new address
    return $this->address->isValid() && $this->manager->add($this->address);

  }

  /**
   * Remove address
   *
   * @return boolean
   */
  private function removeAddress() {

    // prepare and validate address
    $this->address->setId($_GET['id']);

    // add new address
    return $this->address->isValid() && $this->manager->remove($this->address);

  }

  /**
   * Update address
   *
   * @return boolean
   */
  private function updateAddress() {

    // prepare and validate address
    $this->address->setId($_GET['id']);
    $this->address->setCap($_GET['cap']);
    $this->address->setCity($_GET['city']);
    $this->address->setName($_GET['name']);
    $this->address->setStreet($_GET['street']);

    // add new address
    return $this->address->isValid() && $this->manager->update($this->address);

  }

}
