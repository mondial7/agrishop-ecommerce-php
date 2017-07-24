<?php

class Account extends EKEApiController {

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

    // switch between operations and field requested

    // o => operation [ delete, update ]
    // f => field [ email, password, ... ]
    // v => new value
    // c => control value [ e.g. old password ]
    if (!areset(['o','f','v'])) {

      $this->response = $this->ERR_BAD_REQUEST;
      return $this;

    }

    // init a new profile object
    require_once MODELS_DIR . '/Profile.php';
    $this->profile = new Profile();

    // switch according to operation requested
    switch ($_POST['o']) {

      case 'update':

        if ($this->update()) {

          $this->response = $this->STATUS_OK;

        } else {

          $this->response = $this->ERROR;

        }

        break;

      case 'delete':

        if ($this->delete()) {

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
   * Update profile fields
   *
   * @return boolean
   */
  private function update() {

    // init options model
    require_once MODELS_DIR . '/Options.php';
    $Options = new Options();

    // switch according to field param
    switch ($_POST['f']) {

      case 'email':

        $this->profile->setEmail($_POST['v']);
        $status = $Options->updateEmail($this->profile);

        break;

      case 'username':

        $this->profile->setUsername($_POST['v']);
        $status = $Options->updateUsername($this->profile);

        break;

      case 'password':

        // check if the control value has been submitted
        // the control value now would be the old password
        if (!isset($_POST['c']) || empty($_POST['c'])) {

          return false;

        }

        // check passsword length
        if (strlen($_POST['v']) < 5) {

          return false;

        }

        $this->profile->setPassword($_POST['v']);
        $status = $Options->updatePassword($this->profile, $_POST['c']);

        break;

      default:

        $status = false;

        break;
    }

    return $status;

  }

  /**
   * Delete Profile
   *
   * @return boolean
   */
  private function delete() {

    // TODO: to be implemented
    return false;

  }

}
