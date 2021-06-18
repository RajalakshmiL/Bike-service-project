<?php
namespace Acc\Controllers;
use Phalcon\Http\Response;
use Acc\Models\Customer;
use Acc\Models\Service;
use Acc\Auth\Exception as AuthException;

class ServiceController extends ControllerBase
{
    /*
     * Service Create Action
    */
    public function create()
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try
        {
            /*
            * Checking whether the given details are filled
            */
            if(!$request->service_name){
                // Output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => 'Invalid Service is given',
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            /*
            * Checking whether the service name already exists
            */
            $services = $this->modelsManager->createBuilder()
                    ->columns("service_id")
                    ->addFrom("Acc\Models\Service")
                    ->where("service_name = '".$request->service_name."'")
                    ->getQuery()
                    ->getSingleResult();
            if($services){
                // Output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => 'Service with this name already exists.',
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            /*
            * Creating new service
            */
            $service = new Service();
            $service->service_name = $request->service_name;
            $service->active_status = 1;
            $service->save();
            // output response
            $response->setStatusCode(200, 'Success');
            $response->setJsonContent(
                [
                    'status' => "Service created successfully !",
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
     * Service Update Action
    */
    public function update($id)
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try
        {
            /*
            * Checking whether the given details are filled
            */
            if(!$request->service_name){
                // Output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => 'Invalid Service is given',
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            /*
            * Checking whether the service id is present
            */
            $service = Service::findFirst("service_id = '$id'");
            if(!$service){
                // output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => "Invalid Service ID",
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            /*
            * Checking whether the service name already present
            */
            $services = $this->modelsManager->createBuilder()
                    ->columns("service_id")
                    ->addFrom("Acc\Models\Service")
                    ->where("service_name = '".$request->service_name."' and service_id != '$id'")
                    ->getQuery()
                    ->getSingleResult();
            if($services){
                // Output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => 'Service with this name already exists.',
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            /*
            * Updating the service name
            */
            $service->service_name = $request->service_name;
            $service->save();
    
            // output response
            $response->setStatusCode(200, 'Success');
            $response->setJsonContent(
                [
                    'status' => "Service updated successfully !",
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
     * Service Delete Action
    */
    public function delete($id)
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try
        {
            /*
            * Checking whether the service id is present
            */
            $service = Service::findFirst("service_id = '$id'");
            if(!$service){
                // output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => "Invalid Service ID",
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            /*
            * Checking whether the service is used in booking or not
            */
            if($service->is_used > '0'){
                // output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => "Service is used. Check and try again !",
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            /*
            * Deleting the service
            */
            $service->delete();
            // output response
            $response->setStatusCode(200, 'Success');
            $response->setJsonContent(
                [
                    'status' => "Service deleted successfully !",
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
     * Getting All Booking lists
    */
    public function bookingList()
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try
        {
            /*
            * Getting all the list of bookings
            */
            $bookings = $this->modelsManager->createBuilder()
                    ->columns("booking_id,booking.customer_id,Customer.customer_name,Customer.customer_mail,Customer.customer_mobile,booking.service_id,Service.service_name,service_date,delivery_date,booking.status_id,ServiceStatus.status_name")
                    ->addFrom("Acc\Models\Booking", "booking")
                    ->leftjoin("Acc\Models\Customer", "Customer.customer_id = booking.customer_id", "Customer")
                    ->leftjoin("Acc\Models\Service", "Service.service_id = booking.service_id", "Service")
                    ->leftjoin("Acc\Models\ServiceStatus", "ServiceStatus.status_id = booking.status_id", "ServiceStatus")
                    ->getQuery()
                    ->execute();
            
            // output response
            $response->setStatusCode(200, 'Success');
            $response->setJsonContent(
                [
                    'lists' => $bookings,
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
     * Getting Booking details of particular booking id
    */
    public function bookingDetail($id)
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try
        {
            /*
            * Getting the details of particular id's booking
            */
            $bookings = $this->modelsManager->createBuilder()
                    ->columns("booking_id,booking.customer_id,Customer.customer_name,Customer.customer_mail,Customer.customer_mobile,booking.service_id,Service.service_name,service_date,delivery_date,booking.status_id,ServiceStatus.status_name")
                    ->addFrom("Acc\Models\Booking", "booking")
                    ->leftjoin("Acc\Models\Customer", "Customer.customer_id = booking.customer_id", "Customer")
                    ->leftjoin("Acc\Models\Service", "Service.service_id = booking.service_id", "Service")
                    ->leftjoin("Acc\Models\ServiceStatus", "ServiceStatus.status_id = booking.status_id", "ServiceStatus")
                    ->where("booking.booking_id = '".$id."'")
                    ->getQuery()
                    ->getSingleResult();
            if($bookings){ // If booking details exists
                // output response
                $response->setStatusCode(200, 'Success');
                $response->setJsonContent(
                    [
                        'details' => $bookings,
                        'type'=>'success',
                    ]
                );
            }else{ // Else part
                // output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'details' => "No such Booking details exists.",
                        'type'=>'error',
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
     * Marking status to Ready for delivery stage
    */
    public function deliveryStage()
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try
        {
            /*
            * Getting the booking details
            */
            $bookings = $this->modelsManager->createBuilder()
                    ->columns("booking_id,Customer.customer_name,Customer.customer_mail")
                    ->addFrom("Acc\Models\Booking", "booking")
                    ->leftjoin("Acc\Models\Customer", "Customer.customer_id = booking.customer_id", "Customer")
                    ->where("booking_id = '".$request->booking_id."'")
                    ->getQuery()
                    ->getSingleResult();
            if($bookings){
                /*
                * If exists, update the status as 2 (Ready for delivery)
                */
                $this->modelsManager->executeQuery("UPDATE Acc\Models\Booking SET status_id = '2' WHERE booking_id = '".$request->booking_id."'"); 
            }else{ // else part
                // Output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => 'Invalid Booking ID',
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            
            $app_cli =  BASE_PATH . '/app/cli.php'; // Getting Cli path
            /*
             * Execution the mail.
             * To notify the Customer about the delivery by John
             */
            exec('php '.$app_cli.' main deliverymail '.$bookings->customer_mail.' "'.$bookings->customer_name.'" > /dev/null &');
            // output response
            $response->setStatusCode(200, 'Success');
            $response->setJsonContent(
                [
                    'status' => "You marked 'Ready for delivery' for $bookings->customer_name",
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
     * Marking status to Complete stage after delivered
    */
    public function MarkasComplete()
    {
        $request = $this->request->getJsonRawBody();
        $response = new Response();
        try
        {
            /*
            * Getting the booking details
            */
            $bookings = $this->modelsManager->createBuilder()
                    ->columns("booking_id,Customer.customer_name,status_id")
                    ->addFrom("Acc\Models\Booking", "booking")
                    ->leftjoin("Acc\Models\Customer", "Customer.customer_id = booking.customer_id", "Customer")
                    ->where("booking_id = '".$request->booking_id."'")
                    ->getQuery()
                    ->getSingleResult();
            // Checking the status of that booking,(If status_id == 1(Pending), then give error message)  
            if($bookings && $bookings->status_id == '1'){
                // Output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => "You can mark as 'Completed' only when it is in 'Ready for Delivery' Stage.",
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
            else if($bookings){ // Else
                // Update the booking with status = 3 (Completed)
                $this->modelsManager->executeQuery("UPDATE Acc\Models\Booking SET status_id = '3',delivery_date = '".date("Y-m-d")."' WHERE booking_id = '".$request->booking_id."'"); 
            }
            else{ // Else part
                // Output response
                $response->setStatusCode(201, 'Error');
                $response->setJsonContent(
                    [
                        'status' => 'Invalid Booking ID',
                        'type'=>'danger',
                    ]
                );
                // Returing error response if exists
                return $response;
            }
    
            // output response
            $response->setStatusCode(200, 'Success');
            $response->setJsonContent(
                [
                    'status' => "You marked as 'Completed' for $bookings->customer_name",
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
}