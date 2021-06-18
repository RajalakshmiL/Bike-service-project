<?php
namespace Acc\Controllers;
use Phalcon\Http\Response;
use Acc\Models\Customer;
use Acc\Models\Booking;
use Acc\Models\Service;
use Phalcon\Security;
use Acc\Auth\Exception as AuthException;

class CustomerController extends ControllerBase
{
    /*
     * Registration Action
    */
    public function register()
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try {
            /*
             * checking for exist customer with this email id and mobile number
            */
            $customers = $this->modelsManager->createBuilder()
                    ->columns("customer_mail,customer_mobile")
                    ->addFrom("Acc\Models\Customer")
                    ->where("customer_mail = '".$request->customer_mail."' or customer_mobile = '".$request->customer_mobile."'")
                    ->getQuery()
                    ->getSingleResult();

            if ($customers) {
                // Getting error messages if given mobile number already exists
                if ($customers->customer_mobile == $request->customer_mobile) {
                    $error_message = "Customer with this number already exist";
                }
                // Getting error messages if given mail already exists
                if ($customers->customer_mail == $request->customer_mail) {
                    $error_message = "Customer with this email already exist";
                }
                // Checking the email format
                if (!filter_var($request->customer_mail, FILTER_VALIDATE_EMAIL)) {
                    $error_message = "Please enter valid email";
                }
                /*
                 * Output error response
                */
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => $error_message,
                        'type'=>'danger'
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            /*
             * Registering the Customer
            */
            $customer = new Customer();
            $customer->customer_name = $request->customer_name;
            $customer->customer_mail = $request->customer_mail;
            $security = new Security(); // Declaring Security class 
            $customer->customer_password = $security->hash($request->customer_password); // Storing the password in hash form
            $customer->customer_mobile = $request->customer_mobile;
            $customer->save();
            /*
            * set registration is true
            */
            $this->session->set("register", "true"); 
            /*
            * To check authentication with the register mail and password
            */
            $this->auth->check([
                'email' => $request->customer_mail,
                'password' => $request->customer_password
            ]);
            // And getting session details of that customer
            $auth = $this->auth->getSessionIdentity();
            // output response
            $response->setStatusCode(200, 'Success');
            $response->setJsonContent(
                [
                    'status' => 'Customer has been created successfully !',
                    'session_id' => $this->session->getId(),
                    'customer_id' => $auth["sCus_id"],
                    'type'=>'success'
                ]
            ); 
        } 
        catch(AuthException $e)
        {
            /**
            *output success response
            */
            $response->setStatusCode(201, 'Error');
            $response->setJsonContent(
                [
                    'status' => 'Please try after some time',
                    'data' => [ 
                        $e->getMessage()
                    ]
                ]
            ); 
        }
        return $response;
    }
    /*
     * Login Action
    */
    public function login()
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try {
            if (isset($request->customer_mail) && isset($request->customer_password)) {
                /*
                 * check authentication with the register mail and password
                */
                $this->auth->check([
                    'email' => $request->customer_mail,
                    'password' => $request->customer_password
                ]);
                // And getting session details of that customer
                $auth = $this->auth->getSessionIdentity();
                // output response
                $response->setStatusCode(200, 'Success');
                $response->setJsonContent(
                    [
                        'status' => 'Customer details',
                        'data' => [ 
                            'session_id' => $this->session->getId(),
                            'customer_id' => $auth["sCus_id"],
                            'customer_name' => $auth["sCus_name"],
                            'customer_mail' => $auth["sCus_mail"]
                        ]
                    ]
                ); 
            }
        } 
        catch(AuthException $e)
        {
            /**
            *output success response
            */
            $response->setStatusCode(201, 'Error');
            $response->setJsonContent(
                [
                    'status' => 'Please try after some time',
                    'data' => [ 
                        'error' => $e->getMessage(),
                    ]
                ]
            ); 
        }
        return $response;
    }
    /*
     * Booking the service Action
    */
    public function booking()
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try
        {   
            /*
            * Checking whether the given details are correct for customer and service.
            */
            if(!intval($request->customer_id) || !intval($request->service_id)){
                // Response output
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => 'Please fill correct details.',
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            /*
            * Checking whether the given service date is after today.  
            */
            if(!$request->service_date && $request->service_date < date("Y-m-d")){
                // Response output
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => 'Please Enter the correct date.',
                        'type'=>'danger',
                    ]
                ); 
                // Returing error response if exists
                return $response;
            }
            /*
            * Checking whether the service is booked on that date already 
            */
            $isBookingExist = $this->modelsManager->createBuilder()
                            ->columns("booking_id")
                            ->addFrom("Acc\Models\Booking")
                            ->where("service_date = '".date("Y-m-d",strtotime($request->service_date))."' and status_id != 3")
                            ->getQuery()
                            ->getSingleResult();            
            if($isBookingExist){
                // Response output
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => 'Your requested Date is already Booked by other customer. Please try different date.',
                        'type'=>'danger',
                    ]
                ); 
                // Returing error response if exists
                return $response;
            }
            // Checking whether service id exist or not
            if ($this->ifexistscondition("service","service_id = '".$request->service_id."'") == 0) {
                // Response output
                $response->setStatusCode(200, 'Success');
                $response->setJsonContent(
                    [
                        'status' => 'Service does not exists.',
                        'type'=>'danger'
                    ]
                );  
                // Returing error response if exists
                return $response;
            }
            // Checking whether customer id exist or not
            if ($this->ifexistscondition("customer","customer_id = '".$request->customer_id."'") == 0) {
                // Response output
                $response->setStatusCode(200, 'Success');
                $response->setJsonContent(
                    [
                        'status' => 'Customer does not exists.',
                        'type'=>'danger'
                    ]
                );  
                // Returing error response if exists
                return $response;
            }
            /*
            *  Booking the service
            */
            $booking = new Booking();
            $booking->service_id = $request->service_id;
            $booking->customer_id = $request->customer_id;
            $booking->service_date = date("Y-m-d",strtotime($request->service_date));
            $booking->status_id = '1';
            $booking->save();
            // updating the used count of the service, so that easy to delete the service based on this.
            $this->modelsManager->executeQuery("UPDATE Acc\Models\Service SET is_used = is_used + 1 WHERE service_id = '".$request->service_id."'"); 
            // Getting the details of that booking 
            $booking_details = $this->modelsManager->createBuilder()
                        ->columns("Customer.customer_name,Customer.customer_mail,service_name")
                        ->addFrom("Acc\Models\Booking", "booking")
                        ->leftjoin("Acc\Models\Customer", "Customer.customer_id = booking.customer_id", "Customer")
                        ->leftjoin("Acc\Models\Service", "Service.service_id = booking.service_id", "Service")
                        ->where("booking_id = '".$booking->booking_id."'")
                        ->getQuery()
                        ->getSingleResult();
            
            $app_cli =  BASE_PATH . '/app/cli.php'; // Getting Cli path
            /*
             * Execution the mail.
             * To notify John about the service on the date by one of the customer
             */
            exec('php '.$app_cli.' main servicemail '.$booking_details->customer_mail.' "'.$booking_details->customer_name.'" "'.$booking_details->service_name.'" "'.$booking->service_date.'"> /dev/null &');
            // output response
            $response->setStatusCode(200, 'Success');
            $response->setJsonContent(
                [
                    'status' => 'Your Service is booked successfully !',
                    'type'=>'success',
                ]
            ); 
        } 
        catch(AuthException $e)
        {
            /**
            *output success response
            */
            $response->setStatusCode(201, 'Error');
            $response->setJsonContent(
                [
                    'status' => 'Please try after some time',
                    'data' => [ 
                        $e->getMessage()
                    ]
                ]
            ); 
        }
        return $response;
    }
    /*
     * Booking Status Action
    */
    public function bookingStatus()
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try
        {
            /*
            * Checking whether the given details are correct.
            */
            if(!intval($request->customer_id) || !$request->service_date){
                // output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => "Please provide correct details",
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            /*
            * Getting the status of that booking details based on the given date by the customer
            */
            $service_date = date("Y-m-d",strtotime($request->service_date)); // Converting to date format
            $bookings = $this->modelsManager->createBuilder()
                    ->columns("Service.service_name,ServiceStatus.status_name")
                    ->addFrom("Acc\Models\Booking", "booking")
                    ->leftjoin("Acc\Models\Service", "Service.service_id = booking.service_id", "Service")
                    ->leftjoin("Acc\Models\ServiceStatus", "ServiceStatus.status_id = booking.status_id", "ServiceStatus")
                    ->where("booking.customer_id = '".$request->customer_id."' and booking.service_date = '".$service_date."'")
                    ->getQuery()
                    ->getSingleResult();
            if($bookings){ // If booking exists on the date
                // output response
                $response->setStatusCode(200, 'Success');
                $response->setJsonContent(
                    [
                        'status' => "Your ".$bookings->service_name." service is in '".$bookings->status_name."' Stage !",
                        'type'=>'success',
                    ]
                );
                return $response;
            }else{ // Else part
                // output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => "No Service is booked on that date.",
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
        } 
        catch(AuthException $e)
        {
            /**
            *output success response
            */
            $response->setStatusCode(201, 'Error');
            $response->setJsonContent(
                [
                    'status' => 'Please try after some time',
                    'data' => [ 
                        $e->getMessage()
                    ]
                ]
            ); 
        }
    }
    /*
     * All Bookings of the given customer
    */
    public function AllBookings($customer_id)
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try
        {
            /*
            * Getting all the booking details of that customer
            */
            $bookings = $this->modelsManager->createBuilder()
                    ->columns("booking_id,Service.service_name,service_date,ServiceStatus.status_name")
                    ->addFrom("Acc\Models\Booking", "booking")
                    ->leftjoin("Acc\Models\Service", "Service.service_id = booking.service_id", "Service")
                    ->leftjoin("Acc\Models\ServiceStatus", "ServiceStatus.status_id = booking.status_id", "ServiceStatus")
                    ->where("booking.customer_id = '".$customer_id."'")
                    ->getQuery()
                    ->execute();
            if(!empty($bookings)){ // If Booking exists
                // output response
                $response->setStatusCode(200, 'Success');
                $response->setJsonContent(
                    [
                        'status' => $bookings,
                        'type'=>'success',
                    ]
                );
            }else{ // Else part
                // output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => "Oops! No Service is booked.",
                        'type'=>'danger',
                    ]
                );
            }
        } 
        catch(AuthException $e)
        {
            /**
            *output success response
            */
            $response->setStatusCode(201, 'Error');
            $response->setJsonContent(
                [
                    'status' => 'Please try after some time',
                    'data' => [ 
                        $e->getMessage()
                    ]
                ]
            ); 
        }
        return $response;
    }
    /*
     * Details action
    */
    public function details()
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try
        {   
            // List of all services
            $services = $this->modelsManager->createBuilder()
                        ->columns("service_id,service_name")
                        ->addFrom("Acc\Models\Service")
                        ->where("active_status = 1")
                        ->getQuery()
                        ->execute();
            // List of all statuses
            $status = $this->modelsManager->createBuilder()
                        ->columns("status_id,status_name")
                        ->addFrom("Acc\Models\ServiceStatus")
                        ->where("active_status = 1")
                        ->getQuery()
                        ->execute();
            // output response
            $response->setStatusCode(200, 'Success');
            $response->setJsonContent(
                [
                    'data' => [
                        'services' => $services,
                        'status' => $status,
                    ]
                ]
            ); 
        }
        catch(AuthException $e)
        {
            /**
            *output success response
            */
            $response->setStatusCode(201, 'Error');
            $response->setJsonContent(
                [
                    'status' => 'Please try after some time',
                    'data' => [ 
                        $e->getMessage()
                    ]
                ]
            ); 
        }
        return $response;
    }
}