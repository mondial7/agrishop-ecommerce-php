<?php

/**
 * Filter documents access to only logged users
 */

if (!$userLogged) {

	exit(':)');

}

/**
 * Define file path
 */
function buildPath($file, $subpath) : string {

	$subpath = str_replace("-", "/", $subpath);
	$file = urldecode($file);

	return DOCS_DIR . "/" . $subpath . "/" . $file;

}

// Include models
require_once MODELS_DIR . "/ResourcesUtility.php";

// Print out file according to resource type
ResourcesUtility::showFile(buildPath($_GET['_f'], $_GET['_s']), $_GET['_t']);
