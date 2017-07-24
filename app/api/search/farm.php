<?php

class Farm extends EKEApiController {

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

  	// Check filters based on the get parameters
  	if ($this->defineFilters()) {

  		$this->response = json_encode($this->search->getFarms($this->filters));

  	} else {

  		$this->response = json_encode($this->search->getFarms());

	  }

  	return $this;

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

    // Check if n is set (aka name of the farm)
    if (isset($_GET['s'])) {

      $filters['name'] = $_GET['s'];

      $this->filters = $filters;

      return true;

    }

    return false;

  }

}
