<?php

class ORMTry extends EKEModel {

  public function __construct() {

    parent::__construct();

    // Declare database connection
    $this->connectDB();

    // Define Accound DB table (apply immediately filters)
    $this->products = $this->db->in('agrishop__products')->filter(['name','price']);

  }

  public function getResult(){

    // retrieve all the accounts
    return $this->products->search();

  }


}
