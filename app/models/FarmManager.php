<?php

class FarmManager extends EKEModel {

	function __construct() {

    parent::__construct();

    // Declare database connection
    $this->connectDB();

	}

  /**
   * Get Farm Info
   *
   * @param Farm (model)
   * @return array
   */
  public function getInfo($farm) {

    $query = "SELECT id, owner_name, owner_surname, name FROM agrishop__farm WHERE id = ? ;";

    $options = [ 'types' => ['i'], 'params' => [$farm->getId()] ];

    return $this->db->directQuery($query, $options)[0] ?? [];

  }

  /**
   * Check if a Farm has correctly set up its profile
   *
   * @return boolean
   */
  public function isActive() {

    $query = "SELECT id FROM agrishop__farm WHERE id = ? ;";

    $options = [ 'types' => ['i'], 'params' => [Session::get('id')] ];

    $this->db->directQuery($query, $options);

    return $this->db->getResultNum() === 1;

  }

  /**
   * Create a new Farm record
   *
   * @param Farm
   * @return boolean
   */
  public function add(Farm $farm) {

    $query = "INSERT INTO agrishop__farm (id, owner_name, owner_surname, name)
              VALUES ( ? , ? , ? , ? );";

    $parameters = [
                    Session::get('id'),
                    $farm->getOwnerName(),
                    $farm->getOwnerSurname(),
                    $farm->getName()
                  ];

    $options = [ 'types' => ['isss'], 'params' => $parameters ];

    $this->db->directQuery($query, $options);

    return $this->db->getAffectedNum() === 1;

  }

  /**
   * Update Farm name
   *
   * @param Farm (model)
   * @return boolean
   */
  public function updateName($f) {

    return $this->update($f->getName(), 'name');

  }

  /**
   * Update Farm owner_name
   *
   * @param Farm (model)
   * @return boolean
   */
  public function updateOwnerName($f) {

    return $this->update($f->getOwnerName(), 'owner_name');

  }

  /**
   * Update Farm owner_surname
   *
   * @param Farm (model)
   * @return boolean
   */
  public function updateOwnerSurname($f) {

    return $this->update($f->getOwnerSurname(), 'owner_surname');

  }

  /**
   * Update Farm field
   *
   * @param string new name/surname value
   * @param string field to be updated
   * @return boolean
   */
  private function update($name, $field = false) {

    if (!$field) {

      return false;

    }

    $query = "UPDATE agrishop__farm SET $field = ? WHERE id = ? ;";

    $options = [ 'types' => ['si'], 'params' => [$name, Session::get('id')] ];

    $this->db->directQuery($query, $options);

    return $this->db->getAffectedNum() === 1;

  }

}
