<?php

class ProductManager extends EKEModel {

	/**
	 * @var product (model)
	 */
	private $product;

	function __construct() {

    parent::__construct();

    // Declare database connection
    $this->connectDB();

	}

	/**
	 * Add the created product
	 *
	 * @param Product
	 * @return boolean (success)
	 */
	public function add(Product $product) {

		// parameterized query
		$query = "INSERT INTO agrishop__products (category, area, farm, price, quantity, name, produced)
							VALUES ( ? , ? , ? , ? , ? , ? , ? );";

		$parameters = [

			$product->getCategory(),
			$product->getArea(),
			$product->getFarm(),
			$product->getPrice(),
			$product->getQuantity(),
			$product->getName(),
			$product->getProduced()

		];

		// set up parameters
		$options = ['types' => ['iiiddss'], 'params' => $parameters ];

		// execute query
    $this->db->directQuery($query, $options);

		// check if the query has been successful
    return ($this->db->getAffectedNum() === 1);

	}

	/**
	 * Delete the product
	 *
	 * @param Product
	 * @return boolean (success)
	 */
	public function delete(Product $product) {

		// delete product verifying ownership of logged farm
		$query = "DELETE FROM agrishop__products WHERE id = ? AND farm = ? ;";

		$options = [ 'types' => ['ii'], 'params' => [$product->getId(), Session::get('id')] ];

		$this->db->directQuery($query, $options);

		return ($this->db->getAffectedNum() === 1);

	}

}
