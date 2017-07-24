<?php

class Production extends EKEApiController {

  /**
   * @var int
   */
  private $target_id;

  /**
   * @var Area (model)
   */
  private $area;

  /**
   * @var Category (model)
   */
  private $category;

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

    // check required parameters
    // and validate inputs
    if (!areset(['o','id']) && !is_numeric($_GET['id'])) {

      $this->response = $this->ERR_BAD_REQUEST;
      return $this;

    } else {

      // set target id
      $this->target_id = $_GET['id'];

      // include and instantiate the models
      require_once MODELS_DIR . '/Area.php';
      require_once MODELS_DIR . '/Category.php';
      $this->area = new Area();
      $this->category = new Category();

    }

    // switch according to action
    switch ($_GET['o']) {

      case 'add_area':

        if ($this->addArea()) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      case 'remove_area':

        if ($this->removeArea()) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      case 'add_category':

        if ($this->addCategory()) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      case 'remove_category':

        if ($this->removeCategory()) {

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

  /**
   * Add new production area to
   * the current logged farm
   *
   * @return boolean (success)
   */
  private function addArea() {

    return $this->area->add($this->target_id);

  }

  /**
   * Remove production area from
   * the current logged farm
   *
   * @return boolean (success)
   */
  private function removeArea() {

    return $this->area->remove($this->target_id);

  }

  /**
   * Add new production category
   * to the current logged farm
   *
   * @return boolean (success)
   */
  private function addCategory() {

    return $this->category->add($this->target_id);

  }

  /**
   * Remove production category
   * from the current logged farm
   *
   * @return boolean (success)
   */
  private function removeCategory() {

    return $this->category->remove($this->target_id);

  }

}
