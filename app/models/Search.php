<?php

class Search extends EKEModel {

    /**
     * @var int
     */
    private $PRODUCTS = 2,
            $FARMS = 3;

    function __construct() {

        parent::__construct();

        // Declare database connection
        $this->connectDB();

    }

    /**
     * Get product based on the id
     *
     * @param id product
     * @return array [result]
     */
    public function getProduct($id) {

      if (!is_numeric($id)) {

        return [];

      }

      $query = "SELECT p.id, p.name, p.quantity, p.price, p.produced,
                       p.category as category_id, p.area as area_id,
                       p.farm as farm_id, a.area, c.category, f.name as farm,
                       f.owner_name, f.owner_surname
                FROM agrishop__products as p
                JOIN agrishop__farm as f ON p.farm = f.id
                JOIN agrishop__areas as a ON p.area = a.id
                JOIN agrishop__categories as c ON p.category = c.id
                WHERE p.id = ? AND p.quantity > 0.0 ";

      $options = [ 'types' => ['i'], 'params' => [$id] ];

      return $this->db->directQuery($query, $options)[0] ?? [];

    }

    /**
     * Get products based on the filters
     *
     * @param array [filters]
     * @return array [results]
     */
    public function getProducts($filters = []) {

      return $this->getResults($this->PRODUCTS, $filters);

    }

    /**
     * Get farms based on the filters
     *
     * @param array [filters]
     * @return array [results]
     */
    public function getFarms($filters = []) {

      return $this->getResults($this->FARMS, $filters);

    }

    /**
     * Get results based on the filters
     *
     * @param int to switch between all/products/farm
     * @param array [filters]
     * @return array [results]
     */
    public function getResults($target = 2, $filters = []) {

      // when there are no filters we can use the private safe methods
      if (count($filters)<1) {

        switch ($target) {

          case $this->PRODUCTS:

            $results = $this->loadProducts();

            break;

          case $this->FARMS:

            $results = $this->loadFarms();

            break;

          default:

            $results = [];

            break;

        }

      } else {

        switch ($target) {

          case $this->PRODUCTS:

            $query_data = $this->defineProductsFilterQueryData($filters);
            $results = $this->db->directQuery($query_data['query'], $query_data['options']) ?: [];

            break;

          case $this->FARMS:

            $query_data = $this->defineFarmsFilterQueryData($filters);
            $results = $this->db->directQuery($query_data['query'], $query_data['options']) ?: [];

            break;

          default:

            $results = [];

            break;

        }

      }

      return $results;

    }


    /**
     * Evaluate filters and define the query
     *
     * @param array
     * @return array
     */
    private function defineProductsFilterQueryData($filters) {

      $query_data = ['query' => '', 'options' => []];
      $options = [];
      $options_types = '';
      $options_params = [];

      // Implementation of option
      $base = "SELECT p.id, p.name, p.quantity, p.price, p.produced,
                      p.category as category_id, p.area as area_id,
                      p.farm as farm_id, a.area, c.category, f.name as farm,
                      f.owner_name, f.owner_surname
               FROM agrishop__products as p
               JOIN agrishop__farm as f ON p.farm = f.id
               JOIN agrishop__areas as a ON p.area = a.id
               JOIN agrishop__categories as c ON p.category = c.id
               WHERE p.quantity > 0.0 ";

      // prepare selection
      $where = "";

      // filter by prodcut name
      if (isset($filters['name'])) {

        $where .= " AND ( p.name LIKE ? OR f.name LIKE ? )";
        $options_types .= 'ss';
        $options_params[] = '%' . $filters['name'] . '%';
        $options_params[] = '%' . $filters['name'] . '%';

      }

      // filter by category
      if (isset($filters['categories'])) {

        $categories_count = count($filters['categories']);

        if ($categories_count == 1) {

          $where .= " AND p.category = ? ";
          $options_types .= 'i';
          $options_params[] = $filters['categories'][0];

        } else if ($categories_count > 1) {

          $where .= " AND (";

          for ($i=0; $i < $categories_count; $i++) {

            if ($i != 0 && $i != $categories_count) {

              $where .= " OR ";

            }

            $where .= " p.category = ? ";
            $options_types .= 'i';
            $options_params[] = $filters['categories'][$i];

          }

          $where .= ") ";

        }

      }

      // filter by areas
      if (isset($filters['areas'])) {

        $areas_count = count($filters['areas']);

        if ($areas_count == 1) {

          $where .= " AND p.area = ? ";
          $options_types .= 'i';
          $options_params[] = $filters['areas'][0];

        } else if ($areas_count > 1) {

          $where .= " AND (";

          for ($i=0; $i < $areas_count; $i++) {

            if ($i != 0 && $i != $areas_count) {

              $where .= " OR ";

            }

            $where .= " p.area = ? ";
            $options_types .= 'i';
            $options_params[] = $filters['areas'][$i];

          }

          $where .= ") ";

        }

      }

      $options['types'] = [$options_types];
      $options['params'] = $options_params;
      $query_data['query'] = $base . $where;
      $query_data['options'] = $options;

      return $query_data;

    }


    /**
     * Evaluate filters and define the query
     *
     * @param array
     * @return array
     */
    private function defineFarmsFilterQueryData($filters) {

      $query_data = ['query' => '', 'options' => []];
      $options = [];
      $options_types = '';
      $options_params = [];

      // Implementation of option
      $base = "SELECT f.id, f.name, f.owner_name, f.owner_surname, p.email
               FROM agrishop__farm as f
               JOIN agrishop__profile as p ON p.id = f.id ";

      // prepare selection
      $where = "";

      // filter by farm name
      if (isset($filters['name'])) {

        $where .= " AND f.name LIKE ? ";
        $options_types .= 's';
        $options_params[] = '%' . $filters['name'] . '%';

      }

      $options['types'] = [$options_types];
      $options['params'] = $options_params;
      $query_data['query'] = $base . $where;
      $query_data['options'] = $options;

      return $query_data;

    }

    /**
     * Get all Farms
     */
    private function loadFarms() {

        // Change the query to the exact name and make a join on the tables
        $query = "SELECT id, owner_name, owner_surname, name FROM agrishop__farm;";

        return $this->db->directQuery($query) ?: [];

    }

    /**
     * Get all products
     */
    private function loadProducts() {

        $query = "SELECT p.id, p.name, p.quantity, p.price, p.produced,
                        p.category as category_id, p.area as area_id,
                        p.farm as farm_id, a.area, c.category, f.name as farm,
                        f.owner_name, f.owner_surname
                 FROM agrishop__products as p
                 JOIN agrishop__farm as f ON p.farm = f.id
                 JOIN agrishop__areas as a ON p.area = a.id
                 JOIN agrishop__categories as c ON p.category = c.id
                 WHERE p.quantity > 0.0;";

        return $this->db->directQuery($query) ?: [];

    }

}
