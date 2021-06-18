<?php
use Phalcon\Mvc\View;

class MainTask extends \Phalcon\CLI\Task {
   
    public function initialize()
    {
        // Setting DI container.
        $this->di = $this->getDI(); 
    }
    /*
    *  servicemail Action is to send a mail to John if customer books the service
    */
    public function servicemailAction(array $params) { 
        $mailer =  $this->di->get('mailer');
        $content =  $this->servicerequesttemplate($params[1],$params[2],$params[3]); // Function passing with the required arguments
        $message = $mailer->createMessage()
                ->to(getenv('JOHN_MAIL'))
                ->subject('Service Booking')
                ->content($content);
        if($message->send()){
          exit();
        }
        exit();
    }
    // Calling Function from servicemail Action
    public function servicerequesttemplate($customer_name,$service_name,$service_date)
    {  
        // To have the view for the mail
        $view = clone $this->view;
        $view->start();
        $view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $view->render('email', 'service-notification',['customer_name'=>$customer_name,'service_name'=>$service_name,'service_date'=>$service_date]); // It renders from /views/email/service-notification
        $view->finish();
        $content = $view->getContent();
        return $content;
    } 
    
    /*
    *  deliverymail Action is to send a mail to the Customer if service is ready for delivery
    */
    public function deliverymailAction(array $params) { 
        $mailer =  $this->di->get('mailer');
        $content =  $this->deliverytemplate($params[1]); // Function passing with the required arguments
        $message = $mailer->createMessage()
                ->to($params[0])
                ->subject('Ready for delivery')
                ->content($content);
        if($message->send()){
          exit();
        }
        exit();
    }
    // Calling Function from deliverymail Action
    public function deliverytemplate($customer_name)
    {  
        // To have the view for the mail
        $view = clone $this->view;
        $view->start();
        $view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $view->render('email', 'delivery-notification',['customer_name'=>$customer_name]); // It renders from /views/email/delivery-notification
        $view->finish();
        $content = $view->getContent();
        return $content;
    } 
}