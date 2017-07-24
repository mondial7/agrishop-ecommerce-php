<?php

class Product extends EKEApiController {

  /**
   * @var Search (model)
   */
  private $search;

  /**
   * @var array
   */
  private $filters;

  /**
   * Main method, automatically run
   */
  public function run(){

    // Include and instantiate the search model
    require_once MODELS_DIR . '/Search.php';
    $this->search = new Search();

    // check if a single product is required
    if (isset($_GET['id']) && !empty($_GET['id'])) {

      $this->response = json_encode($this->load($_GET['id']));
      return $this;

    }

  	// Check the filters based on get parameters
    if ($this->defineFilters()) {

      $this->response = json_encode($this->load());

    } else {

      // Load all products
      $this->response = json_encode($this->loadAll());

    }

  	return $this;

  }

  /**
   * Load all results without any filter
   *
   * @return array
   */
  private function loadAll() {

    return $this->search->getProducts();

  }

  /**
   * Load all results applying filters
   *
   * @param id
   * @return array
   */
  private function load($id = null) {

    if (!is_null($id)) {

      return $this->search->getProduct($id);

    }

    return $this->search->getProducts($this->filters);

  }

  /**
   * Check filters based on get parameters
   * return false if there are no filters
   *
   * @return boolean
   */
  private function defineFilters() {

    // init local filters variable as empty array
    $filters = [];

    // parse each filter to apply
    // and add the valid once to filters

    // filters by name
    if (isset($_GET['s'])) {

      $filters['name'] = $_GET['s'];

    }

    // filter by categories
    if (isset($_GET['c'])) {

      $categories = explode("_", $_GET['c']);
      foreach ($categories as $key => $value) {
        if (is_numeric($value)) {
          $filters['categories'][] = $value;
        }
      }

    }

    // filter by areas
    if (isset($_GET['a'])) {

      $areas = explode("_", $_GET['a']);
      foreach ($areas as $key => $value) {
        if (is_numeric($value)) {
          $filters['areas'][] = $value;
        }
      }

    }

    // Check if any filter has been set

    if (count($filters)>0) {

      $this->filters = $filters;
      return true;

    }


    return false;

  }


}
