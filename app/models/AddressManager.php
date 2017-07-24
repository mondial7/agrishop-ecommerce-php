<?php

class AddressManager extends EKEModel {

	function __construct() {

    parent::__construct();

    // Declare database connection
    $this->connectDB();

	}

	/**
	 * Add the new address
	 *
	 * @param Address
	 * @return boolean (success)
	 */
	public function add(Address $address) {

		// parameterized query
		$query = "INSERT INTO agrishop__addresses (profile_id, cap, street, name, city)
							VALUES ( ? , ? , ? , ? , ? );";

		$parameters = [

      Session::get('id'),
			$address->getCap(),
			$address->getStreet(),
			$address->getName(),
			$address->getCity()

		];

		// set up parameters
		$options = ['types' => ['issss'], 'params' => $parameters ];

		// execute query
    $this->db->directQuery($query, $options);

		// check if the query has been successful
    return ($this->db->getAffectedNum() === 1);

	}

  /**
   * Update the address
   *
   * @param Address
   * @return boolean (success)
   */
  public function update(Address $address) {

    // parameterized query
    $query = "UPDATE agrishop__addresses
              SET  cap = ? , street = ? , name = ? , city = ?
              WHERE id = ? AND profile_id = ? ;";

    $parameters = [

      $address->getCap(),
      $address->getStreet(),
      $address->getName(),
      $address->getCity(),
      $address->getId(),
      Session::get('id')

    ];

    // set up parameters
    $options = ['types' => ['ssssii'], 'params' => $parameters ];

    // execute query
    $this->db->directQuery($query, $options);

    // check if the query has been successful
    return ($this->db->getAffectedNum() === 1);

  }

	/**
	 * Remove the address
	 *
	 * @param Address
	 * @return boolean (success)
	 */
	public function remove(Address $address) {

		// delete product verifying ownership of logged farm
		$query = "DELETE FROM agrishop__addresses WHERE id = ? AND profile_id = ? ;";

		$options = [ 'types' => ['ii'], 'params' => [$address->getId(), Session::get('id')] ];

		$this->db->directQuery($query, $options);

		return ($this->db->getAffectedNum() === 1);

	}

	/**
	 * Get all the address of the logged user
	 *
	 * @return array
	 */
	public function getAddresses() {

		$query = "SELECT id, cap, street, name, city FROM agrishop__addresses WHERE profile_id = ? ;";

		$options = [ 'types' => ['i'], 'params' => [Session::get('id')] ];

		return $this->db->directQuery($query, $options) ?: [];

	}

}
