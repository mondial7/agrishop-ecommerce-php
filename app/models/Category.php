<?php

class Category extends EKEModel {

    function __construct() {

        parent::__construct();

        // Declare database connection
        $this->connectDB();

    }

    /**
     * Get all categories
     *
     * @return array
     */
    public function getCategories() {

        return $this->db->directQuery("SELECT id, category from agrishop__categories") ?: [];

    }

    /**
     * Get production categories
     *
     * @return array
     */
    public function getProduction() {

      $query = "SELECT category
                FROM agrishop__production_categories
                WHERE farm = ? ";

      $options = [ 'types' => ['i'], 'params' => [Session::get('id')] ];

      return $this->db->directQuery($query, $options) ?: [];

    }

    /**
     * Add new category to the current logged farm
     *
     * @param int
     * @return boolean (success)
     */
    public function add($category_id) {

      $query = "INSERT INTO agrishop__production_categories (category, farm)
                VALUES ( ? , ? );";

      $options = [ 'types' => ['ii'], 'params' => [$category_id, Session::get('id')] ];

      $this->db->directQuery($query, $options);

      return ($this->db->getAffectedNum() === 1);

    }

    /**
     * Remove category from the current logged farm
     *
     * @param int
     * @return boolean (success)
     */
    public function remove($category_id) {

      $query = "DELETE FROM agrishop__production_categories
                WHERE farm = ? AND category = ? ;";

      $options = [ 'types' => ['ii'], 'params' => [Session::get('id'), $category_id] ];

      $this->db->directQuery($query, $options);

      return ($this->db->getAffectedNum() === 1);

    }

}
