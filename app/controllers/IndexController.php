<?php
namespace Acc\Controllers;
use Phalcon\Http\Response;
use Acc\Auth\Exception as AuthException;

class IndexController extends ControllerBase
{
    /**
    *index method
    */
    public function index()
    {
        /**
        *declare the response to send back 
        */
        $response = new Response();
        $request = $this->request->getJsonRawBody();
        /**
		*output success response
        */       
		$response->setStatusCode(200, 'Success');
		$response->setJsonContent(
            [
                'data' => [
                    'message' => 'You reached this page successfully !',
                ]
            ]
        );
        /**
	    *return response
	    */
        return $response;
    }
}