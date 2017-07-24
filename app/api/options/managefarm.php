<?php

class Managefarm extends EKEApiController {

  /**
   * @var Farm (model)
   */
  private $farm;

  /**
   * @var FarmManager (model)
   */
  private $manager;

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

    // switch between operations and field requested

    // o => operation [ update, get ]
    // f => field [ name, owner_name, owner_surname ]
    // v => new value
    // id => farm id (to get info)
    if (!isset($_GET['o']) || empty($_GET['o'])) {

      $this->response = $this->ERR_BAD_REQUEST;
      return $this;

    }

    // init a new farm object
    require_once MODELS_DIR . '/Farm.php';
    require_once MODELS_DIR . '/FarmManager.php';
    $this->farm = new Farm();
    $this->manager = new FarmManager();

    // switch according to operation requested
    switch ($_GET['o']) {

      case 'active':

        if ($this->manager->isActive()) {

          $this->response = '{"active":true}';

        } else {

          $this->response = '{"active":false}';

        }

        break;

      case 'update':

        if (areset(['f','v']) && $this->update()) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      case 'add':

        if (areset(['name','owner_name','owner_surname']) && $this->add()) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      case 'get':

        if (!isset($_GET['id']) || empty($_GET['id'])) {

          $this->response = $this->ERR_BAD_REQUEST;

        } else {

          // set farm id
          $this->farm->setId($_GET['id']);

          // get farm info
          $this->response = json_encode($this->manager->getInfo($this->farm));

        }

        break;

      default:

        $this->response = $this->ERR_BAD_REQUEST;

        break;
    }

  	return $this;

  }

  /**
   * Update Farm details
   *
   * @return boolean
   */
  private function update() {

    $value = $_GET['v'];

    // switch according to the field
    switch ($_GET['f']) {

      case 'name':

        $this->farm->setName($value);

        $result = $this->manager->updateName($this->farm);

        break;

      case 'owner_name':

        $this->farm->setOwnerName($value);

        $result = $this->manager->updateOwnerName($this->farm);

        break;

      case 'owner_surname':

        $this->farm->setOwnerSurname($value);

        $result = $this->manager->updateOwnerSurname($this->farm);

        break;

      default:

        $result = false;

        break;

    }

    return $result;

  }

  /**
   * Add a new farm
   *
   * @return boolean
   */
  private function add() {

    // check if the farm is already active
    if ($this->manager->isActive()) {

      return false;

    }

    // fill farm with details
    $this->farm->setName($_GET['name']);
    $this->farm->setOwnerName($_GET['owner_name']);
    $this->farm->setOwnerSurname($_GET['owner_surname']);

    // validate and store new farm
    if ($this->farm->isValid()) {

      return $this->manager->add($this->farm);

    }

    return false;

  }

}
