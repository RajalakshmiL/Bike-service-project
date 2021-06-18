<?php
namespace Acc\Controllers;
use Phalcon\Mvc\Controller;
class ControllerBase extends Controller
{
    // Query for checking the records exists
    public function ifexistscondition($model,$condition){
        $empty = "SELECT 1 FROM ".$model." WHERE ".$condition." LIMIT 1";
        //  get the phalcon di query
        $db = \Phalcon\DI::getDefault()->get('db');
        //  run the query statement
        $stmt = $db->prepare($empty); 
        //  execute the raw query
        $stmt->execute();
        //  fetch all the records
        $oneResult = $stmt->fetch();
        // returning the value
        return $oneResult == null ? 0 : 1;
    }
}