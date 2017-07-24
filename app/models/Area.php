<?php

class Area extends EKEModel {

    function __construct() {

        parent::__construct();

        // Declare database connection
        $this->connectDB();

    }

    /**
     * Get all areas
     *
     * @return array
     */
    public function getAreas() {

        return $this->db->directQuery("SELECT id, area from agrishop__areas") ?: [];

    }

    /**
     * Get production areas
     *
     * @return array
     */
    public function getProduction() {

      $query = "SELECT area
                FROM agrishop__production_areas
                WHERE farm = ? ";

      $options = [ 'types' => ['i'], 'params' => [Session::get('id')] ];

      return $this->db->directQuery($query, $options) ?: [];

    }

    /**
     * Add new area to the current logged farm
     *
     * @param int
     * @return boolean (success)
     */
    public function add($area_id) {

      $query = "INSERT INTO agrishop__production_areas (area, farm)
                VALUES ( ? , ? );";

      $options = [ 'types' => ['ii'], 'params' => [$area_id, Session::get('id')] ];

      $this->db->directQuery($query, $options);

      return ($this->db->getAffectedNum() === 1);

    }

    /**
     * Remove area from the current logged farm
     *
     * @param int
     * @return boolean (success)
     */
    public function remove($area_id) {

      $query = "DELETE FROM agrishop__production_areas
                WHERE farm = ? AND area = ? ;";

      $options = [ 'types' => ['ii'], 'params' => [Session::get('id'), $area_id] ];

      $this->db->directQuery($query, $options);

      return ($this->db->getAffectedNum() === 1);

    }

}
