<?php

class Address extends EKEEntityModel
{
		/**
     * Address properties : string
     *
     * @var string
     */
		 private $street,
						 $name,
						 $city;

		/**
		  * Address properties : int
	    *
		  * @var int
		  */
		private  $profile_id,
		   	 		 $cap,
						 $id;

    function __construct() {

			parent::__construct();

			$this->properties = ['street','name','city','profile_id','cap','id'];

    }

    public function setStreet($street)
    {
    	$this->street = $this->cleanText($street);
    }

    public function setName($name)
    {
    	$this->name = $this->cleanText($name);
    }

    public function setCity($city)
    {
    	$this->city = $this->cleanText($city);
    }

    public function setProfileId($profile_id)
    {
    	$this->profile_id = $this->cleanNumber($profile_id);
    }

    public function setCap($cap)
    {
    	$this->cap = $this->cleanNumber($cap);
    }

    public function setId($id)
    {
    	$this->id = $this->cleanNumber($id);
    }


    /**
     * Getters
     */

    public function getStreet()
    {
    	return $this->street;
    }

    public function getName()
    {
    	return $this->name;
    }

    public function getCity()
    {
    	return $this->city;
    }

		public function getProfileId()
    {
    	return $this->profile_id;
    }

    public function getCap()
    {
    	return $this->cap;
    }

		public function getId()
		{
			return $this->id;
		}

}
