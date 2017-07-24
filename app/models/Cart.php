<?php

class Cart extends EKEModel {

  /**
   * @var string key for session array
   */
  private $label = 'cart';

  /**
   * @var array list of products in the cart
   */
  private $items = [];

  /**
   * Fill the items array in the constructor
   */
  public function __construct() {

  	parent::__construct();

    // Declare database connection
    $this->connectDB();

    $this->items = $this->loadItems();

  }

  /**
   * Add product to cart
   *
   * @param int product id
   * @return boolean
   */
  public function add($item) {

    // check if element already exists
    if ($this->contains($item)) {

      return true;

    }

    // save current count
    $n1 = count($this->items);

    // push element in the array
    $this->items[] = $item;

    $n2 = count($this->items);

    // check if counter increased of one item
    if (($n1 + 1) === $n2) {

      return $this->pushChanges();

    } else {

      return false;

    }

  }

  /**
   * Remove product from cart
   *
   * @param int product id
   * @return boolean
   */
  public function remove($item) {

    // check if element exists
    if (!$this->contains($item)) {

      return true;

    }


    // save current count
    $n1 = count($this->items);

    // copy the array and override it
    $items = [];
    foreach ($this->items as $value) {

      if ($value != $item) {

        $items[] = $value;

      }

    }
    $this->items = $items;

    // NOTE old method --> bugged because adds keys
    // unset the element from array
    // unset($this->items[array_search($item, $this->items)]);

    // push element in the array
    $n0 = count($this->items);

    // check if counter increased of one item
    if ($n1 === ($n0 + 1)) {

      return $this->pushChanges();

    } else {

      return false;

    }

  }

  /**
   * Retrieve all products in cart
   *
   * @return array
   */
  public function getItems() {

    return $this->items;

  }

  /**
   * Empty the cart
   *
   */
  public function clean() {

    // empty local items array
    $this->items = [];

    // push changes to empty cart array of current session

    return (count($this->items) === 0) && $this->pushChanges();

  }

  /**
   * Buy the items and empty the cart
   *
   * Update the products table setting
   * all products in cart to 0 quantity,
   * then add products to sales table
   *
   * @param int
   * @return boolean
   */
  public function buyProducts($payment_type = 13) {

    // validate paramter
    if (!is_numeric($payment_type)) {

      return false;

    }

    if (($items_count = count($this->items)) <= 0) {

      return false;

    }

    // prepare queries

    $update_query = "INSERT INTO agrishop__products
                     (id, quantity) VALUES ";

    $insert_query = "INSERT INTO agrishop__sales
                     ( product, profile, payment_type ) VALUES ";

    $update_types = '';
    $insert_types = '';

    for ($i = 0; $i < $items_count; $i++) {

        $update_query .= ' ( ? , 0 ) ';
        $update_params[] = $this->items[$i];
        $update_types .= 'i';

        $insert_query .= ' ( ? , ? , ? ) ';
        $insert_params[] = $this->items[$i];
        $insert_params[] = Session::get('id');
        $insert_params[] = $payment_type;
        $insert_types .= 'iii';

        if ($i != ($items_count - 1)) {

          $update_query .= ' , ';
          $insert_query .= ' , ';

        }

    }

    $update_query .= ' ON DUPLICATE KEY UPDATE quantity = VALUES( quantity );';
    $insert_query .= ';';

    $options = [ 'types' => [$update_types], 'params' => $update_params ];

    // execute update query
    $this->db->directQuery($update_query, $options);

    if ($this->db->getAffectedNum() === ($items_count*2)) {

      $options['types'] = [$insert_types];
      $options['params'] = $insert_params;

      // execute insert query
      $this->db->directQuery($insert_query, $options);

      if ($this->db->getAffectedNum() === $items_count) {

        // epmty cart
        $this->clean();

        return true;

      } else {

        // TODO catch exception
        echo 'not added to sales table';

      }

    } else {

      // TODO catch exception
      echo 'not update in products';

    }

    return false;

  }

  /**
   * Retrieves products in the cart from the session
   *
   * @return array
   */
  private function loadItems() {

    return Session::get($this->label, []);

  }

  /**
   * Push changes to the session
   * update the cart variable in the current session
   *
   * @return boolean
   */
  private function pushChanges() {

    Session::add($this->label, $this->items);

    // unfortunately the add method of session returns void
    // TODO we need a good refactor here
    return true;

  }

  /**
   * Check if item is already in the array
   *
   * @param int item
   * @return boolean
   */
  private function contains($item) {

    return in_array($item, $this->items);

  }

}
