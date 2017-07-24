<?php

// First level whitelist ('features whitelist')
$feature_whitelist = ['options','search','cart','products','help','upload'];

// Actions whitelists
$options_whitelist = ['addresses','account','production','managefarm'];
$search_whitelist = ['farm','product'];
$cart_whitelist = ['status','update','checkout'];
$products_whitelist = ['add','delete','review'];
$help_whitelist = ['contact'];
$upload_whitelist = ['farmpicture'];





/**
 * ***************************************************************************
 * ***************************************************************************
 * ***************************************************************************
 * ***************************************************************************
 * NOTE next section should not change
 * TODO implement the next code in the core (might even fit the router class)
 * ***************************************************************************
 * ***************************************************************************
 * ***************************************************************************
 * ***************************************************************************
 */

// Validate whitelists
if (!isset($_GET['feature']) ||
    !isset($_GET['action'])) {

    	exit("{'error':'Wrong request'}");

} else if (!in_array($_GET['feature'], $feature_whitelist) ||
  		     !in_array($_GET['action'], ${$_GET['feature'] . "_whitelist"})) {

    	exit("{'error':'Access forbidden'}");

} else {

  $partial_path = $_GET['feature'] . DIRECTORY_SEPARATOR . $_GET['action'] . ".php";

}

// include api script
include_once API_DIR . DIRECTORY_SEPARATOR . $partial_path;
(new $_GET['action']())->run()->answer();
