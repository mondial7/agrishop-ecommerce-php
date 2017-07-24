<?php

if (!$userLogged) {

    header("Location: ../");
    exit;

}


$submitted = isset($_POST['order']);
$error_message = false;
$success = false;

require_once MODELS_DIR .'/Cart.php';
$cart = new Cart();
$cart_items = $cart->getItems();

// count total price
$total_price = 0;
foreach ($cart_items as $product_id) {

  require_once MODELS_DIR . '/Search.php';
  $total_price += (new Search())->getProduct($product_id)['price'];

}


if ($submitted) {

  if (!isset($_POST['credit_card_number']) ||
      !isset($_POST['address']) ||
      empty($_POST['credit_card_number']) ||
      empty($_POST['address'])) {

        $error_message = 'A required field is missing';

   } else if (!(is_numeric($_POST['credit_card_number']))) {

      $error_message = 'Invalid credit card number!';

   } else {

     	if (rand(1,6) % 6) {

        if (count($cart_items) > 0) {

     	      if ($cart->buyProducts()) {

              $success = true;

            } else {

              $error_message = 'Error, it was not possible to proceed to checkout.';

            }

        } else {

         	 	$error_message = 'Cart is empty!';

        }

      } else {

       	$error_message = 'Something went wrong, please try again later';

      }

   }

}





// Include header and footer controllers
include 'page__init.php';


// Set template variables
$template_variables['page_title'] = "Checkout - Agrishop";
$template_variables['success'] = $success;
$template_variables['submitted'] = $submitted;
$template_variables['error_message'] = $error_message;
$template_variables['number_of_items'] = count($cart_items);
$template_variables['total_price'] = $total_price;

// Render the template
EKETwig::show("checkout.twig", $template_variables);
