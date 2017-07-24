<?php

/**
 * Include parent class Profile
 */

require_once MODELS_DIR . '/Profile.php';

/**
 * Customer Model
 *
 * child of Profile model
 *
 */
class Customer extends Profile {

    /**
     * @var date
     */
    private $last_payment;

    function __construct(){

      parent::__construct();

      // Add properties to parent class
			$this->properties[] = 'last_payment';

    }

    /**
     * Set last payment date
     */
    public function setLastPayment($d) {

      $this->last_payment = $this->validateDate($d);

    }

    /**
     * Get last payment date
     */
    public function getLastPayment() {

      return $this->last_payment;

    }

}
