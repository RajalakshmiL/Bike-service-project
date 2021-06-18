<?php
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Micro\Collection as MicroCollection;
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

/**
 * Index Controller end point
 */
$index = new MicroCollection();
$index->setHandler('Acc\Controllers\IndexController', true);
$index->get('/', 'index'); 
$app->mount($index);
/**
 * Customer Controller End points and its action
 */
$customer = new MicroCollection();
$customer->setHandler('Acc\Controllers\CustomerController', true);
$customer->setPrefix('/customer');
$customer->post('/register', 'register'); // registeration part
$customer->post('/login', 'login');  // login part
$customer->post('/booking', 'booking'); // Service booking by the customer
$customer->post('/bookingStatus', 'bookingStatus'); // To see the booking status of the customer
$customer->get('/{customer_id}', 'AllBookings'); // To see all the previous bookings of each customer
$customer->get('/details', 'details'); // Getting details of status and service lists
$app->mount($customer);
/**
 * Service Controller End points and its action
 */
$service = new MicroCollection();
$service->setHandler('Acc\Controllers\ServiceController', true);
$service->setPrefix('/service');
$service->post('/', 'create');  // To create the service
$service->put('/{id}', 'update'); // To edit the service
$service->delete('/{id}', 'delete'); // To delete the service
$service->get('/{id}', 'bookingDetail'); // To view the details of each booking
$service->get('/list', 'bookingList');  // To view list of all bookings ( pending,Ready for delivery and Completed )
$service->post('/delivery', 'deliveryStage');  // To mark a booking as ready for delivery
$service->post('/complete', 'MarkasComplete');  // To mark a booking as completed
$app->mount($service);
/**
**
 * Not found handler
 */
$app->notFound(function () use ($app) {
	$app->response->setStatusCode(404, "Not Found"); //status not found response
	$app->response->sendHeaders();
	return $app->response->setJsonContent(
		[
			'status' => 'ERROR_404_NOT_FOUND',
		]
	);
});