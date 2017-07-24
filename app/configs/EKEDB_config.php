<?php

define("DB_HOST", "localhost");
define("DB_USER", "agrilogin");
define("DB_PASSWORD", "agrilogin123");
define("DB_DATABASE", "AGRISHOP");

/**
 * Database tables name
 */

//define("DB_TABLE_PREFIX", "agrishop__");

// Define tables and fields for each.
// Suggestion, write only accessible tables/columns and if asked values are not within the one needed, then deny query

$DB_TABLES_LIST = [

						'agrishop__products' => [
																			'id' => 'i',
																			'name' => 's',
																			'farm' => 'i',
																			'area' => 'i',
																			'category' => 'i',
																			'quantity' => 'i',
																			'price' => 'i',
																			'produced' => 's',
																			'created' => 's'
																		],

						'agrishop__sale' => [
																	'id' => 'i',
																	'product' => 'i',
																	'customer' => 'i',
																	'review' => 's',
																	'payment_type' => 'i',
																	'created' => 's'
																],

						'agrishop__profile' => [
																		'id' => 'i',
																		'username' => 's',
																		'email' => 's',
																		'password' => 's',
																		'role' => 's',
																		'created' => 's'
																	 ],

						'agrishop__profile_logged' => [
																						'id' => 'i',
																						'profile_id' => 'i',
																						'cookie_token' => 's'
																					],

	  				'agrishop__farm' => [
																 'id' => 'i',
																 'name' => 's',
																 'owner_name' => 's',
																 'owner_surname' => 's'
															  ],

	  				'agrishop__customer' => [
																			'id' => 'i',
																			'last_payment' => 's'
																		],

	   				'agrishop__address' => [
																		'id' => 'i',
																		'profile_id' => 'i',
																		'cap' => 's',
																		'street' => 's',
																		'name' => 's',
																		'city' => 's'
																	],

						'agrishop__categories' => [
																				'id' => 'i',
																				'category' => 's'
																			],

						'agrishop__areas' => [
																	'id' => 'i',
																	'area' => 's'],

					// Not anymore accessible tables (from the users)
					'agrishop__production_areas' => [
																					'farm' => 'i',
																					'area' => 'i'
																				 ],

					'agrishop__production_categories' => [
																								'farm' => 'i',
																								'category' => 'i'
																				 			 ]

				];
