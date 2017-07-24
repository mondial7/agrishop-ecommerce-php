<?php

/*
 * Model class for a product
 */
class Product extends EKEEntityModel {

	/**
	 * @var int
	 */
	private $id,
					$category,
					$area,
					$farm,
					$quantity,
					$price;

	/**
	 * @var date
	 */
	private	$produced,
					$created;

	/**
	 * @var string
	 */
	private $name;


	function __construct(){

		parent::__construct();

		$this->properties = ['id','category','area','farm','quantity','price','produced','created','name'];

	}

	/**
	 * SETTERS
	 */

	public function setId($id) {

		$this->id = $this->cleanNumber($id);

	}

	public function setCategory($c) {

		$this->category = $this->cleanNumber($c);

	}

	public function setArea($a) {

		$this->area = $this->cleanNumber($a);

	}

	public function setFarm($f) {

		$this->farm = $this->cleanNumber($f);

	}

	public function setQuantity($q) {

		$this->quantity = $this->cleanNumber($q);

	}

	public function setPrice($p) {

		$this->price = $this->cleanNumber($p);

	}

	public function setProduced($p) {

		$this->produced = $this->validateDate($p);

	}

	public function setCreated($c) {

		$this->created = $this->validateDate($c);

	}

	public function setName($n) {

		$this->name = $this->cleanText($n);

	}

	/**
	 * GETTERS
	 */

	public function getId() {

		return $this->id;

	}

	public function getCategory() {

		return $this->category;

	}

	public function getArea() {

		return $this->area;

	}

	public function getFarm() {

		return $this->farm;

	}

	public function getQuantity() {

		return $this->quantity;

	}

	public function getPrice() {

		return $this->price;

	}

	public function getProduced() {

		return $this->produced;

	}

	public function getCreated() {

		return $this->created;

	}

	public function getName() {

		return $this->name;

	}

}
