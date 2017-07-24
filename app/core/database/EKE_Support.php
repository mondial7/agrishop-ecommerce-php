<?php

// TODO: Add join function in order to perform better queries
class EKE_Support extends EKEModel
{
	public function __construct()
	{

		parent::__construct();

		$this->connectDB();
	}


	public function getProduct($filters = null)
	{
		return $products = $this->db->in('agrishop__products')->filter(['id','name','price','quantity','description','date'])->search($filters);
	}

	public function getFarm($filters = null)
	{
		return $farms = $this->db->in('agrishop__farms')->filter(['id','name','owner_name','owner_surname'])->search($filters);
	}

	public function getArea($filters = null)
	{
		return $areas = $this->db->in('agrishop__areas')->filter(['id','area'])->search($filters);
	}

	public function getCategory($filters = null)
	{
		return $category = $this->db->in('agrishop__category')->filter(['id','category'])->search($filters);
	}

	public function getHistory($filters = null)
	{
		return $history = $this->db->in('agrishop__sale')->filter(['id','product','profile','payment_type','review','date'])->search($filters);
	}

	public function getAddress($filters = null)
	{
		return $address = $this->db->in('agrishop__address')->filter(['id','profile','cap','street','name','city'])->search($filters);
	}

	public function getUser($filters = null)
	{
		return $user = $this->db->in('agrishop__user')->filter(['id','last_payment'])->search($filters);
	}
}
