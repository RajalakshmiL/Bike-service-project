<?php

namespace Acc\Auth;
use Acc\Models\Customer;
use Phalcon\Mvc\User\Component;
use Phalcon\Security;

class Auth extends Component
{
    public function check($credentials)
    {
        $security = new Security(); // Create security Object
        $customers = Customer::findFirstByCustomerMail($credentials['email']);
        /**
         * Checking Mail ID present 
         */
        if ($customers == false) {
            throw new Exception('No such email id exists');
        }
        /**
         * Checking Password present 
         */
        if (!$security->checkHash($credentials['password'], $customers->customer_password)) { // Checking the password with hashed password
            throw new Exception('Wrong email/password combination');
        }
        /* set session */
        $this->session->set('auth-identity', [
            'sCus_id' => $customers->customer_id,
            'sCus_name' => $customers->customer_name,
            'sCus_mail' => $customers->customer_mail,
        ]);
        /* remove unwanted session variables */
        $this->session->remove("register");
    }
    
    public function getSessionIdentity()
    {
        /* get session details */
        return $this->session->get('auth-identity');
    }
}