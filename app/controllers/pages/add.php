<?php

if (!$userLogged) {
    header("Location: ../");
    exit;
}



$submitted = isset($_POST['submit']);
$error_message = false;
$success = false;

// check if the farm is already active
require_once MODELS_DIR . '/FarmManager.php';
$active = (new FarmManager())->isActive();


if ($submitted && $active) {

  // check the random number
  if (!isset($_POST['rand']) ||
      !(Session::exists('add_product_rand') &&
        Session::is('add_product_rand', intval($_POST['rand'])))) {

    // reload the page without post parameters
    header('Location: ../add/');

  }
    // check for csfr and prevent user reload the page

    if (!isset($_POST['category_id']) ||
        !isset($_POST['area_id']) ||
        !isset($_POST['name']) ||
        !isset($_POST['quantity']) ||
        !isset($_POST['date']) ||
        !isset($_POST['price']) ||
        empty($_POST['category_id']) ||
        empty($_POST['area_id']) ||
        empty($_POST['name']) ||
        empty($_POST['quantity']) ||
        empty($_POST['date']) ||
        empty($_POST['price'])) {

          $error_message = 'All fields are required!';

    } else {

      if (strtotime($_POST['date']) > time()) {

          // date is in the future
          $error_message = 'Product can not be produced in the future!';

      } else {

        // instantiate product model
        require_once MODELS_DIR . '/Product.php';
        $product = new Product();

        // fill product info
        $product->setCategory($_POST['category_id']);
      	$product->setArea($_POST['area_id']);
      	$product->setQuantity(floatval($_POST['quantity']));
      	$product->setPrice(floatval($_POST['price']));
      	$product->setProduced($_POST['date']);
      	$product->setName($_POST['name']);

      	$product->setFarm(Session::get('id'));

        // prepare product, validate inputs
        if ($product->isValid()) {

          // load product manager class
          require_once MODELS_DIR . '/ProductManager.php';

          // try to add the product
          if ((new ProductManager())->add($product)) {

              $success = true;

              // reset rand
              Session::add('add_product_rand', '');

          } else {

              $error_message = 'An error occurred while inserting the product. Please try later';

          }

        } else {

          $error_message = 'Some input is wrong, please try again.';

        }

      }

    }

} else {

  $random = mt_rand(0,999999999);
  Session::add('add_product_rand', $random);
  $template_variables['rand'] = $random;

}

require_once MODELS_DIR . '/Area.php';
require_once MODELS_DIR . '/Category.php';
$template_variables['areas'] = (new Area())->getAreas();
$template_variables['categories'] = (new Category())->getCategories();
$template_variables['need_activation'] = !$active;

// Include header and footer controllers
include 'page__init.php';


// Set template variables
$template_variables['page_title'] = "Add Product - Agrishop";
$template_variables['success'] = $success;
$template_variables['submitted'] = $submitted;
$template_variables['error_message'] = $error_message;


// Render the template
EKETwig::show('add.twig', $template_variables);
