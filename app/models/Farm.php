<?php

/**
 * Include parent class Profile
 */

require_once MODELS_DIR . '/Profile.php';

/**
 * Farm Model
 *
 * child of Profile model
 */
class Farm extends Profile {

		/**
     * Farm properties : string
     *
     * @var string
     */
    private $owner_name,
    				$owner_surname,
						$name;

    function __construct() {

    	parent::__construct();

			// Add properties to parent class
			$this->properties = array_merge($this->properties,
															  			['owner_name','owner_surname','name']);

		}

    public function setOwnerName($owner_name) {

    	$this->owner_name = $this->cleanText($owner_name);

		}

    public function setOwnerSurname($owner_surname) {

    	$this->owner_surname = $this->cleanText($owner_surname);

		}

    public function setName($name) {

    	$this->name = $this->cleanText($name);

		}

    /**
     * Getters
     */

    public function getOwnerName() {

    	return $this->owner_name;

		}

    public function getOwnerSurname() {

    	return $this->owner_surname;

		}

    public function getName() {
    	return $this->name;

		}
}
