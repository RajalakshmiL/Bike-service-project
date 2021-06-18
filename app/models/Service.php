<?php
namespace Acc\Models;

class Service extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     */

    public $service_id;
    /**
     *
     * @var string
     */

    public $service_name;
    /**
     *
     * @var integer
     */

    public $active_status;
    /**
         * Returns table name mapped in the model.
        *
        * @return string
    */

    public function getSource()
    {
        return 'service';
    }
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Service[]|Service|\Phalcon\Mvc\Model\ResultSetInterface
     */

    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }
    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Service|\Phalcon\Mvc\Model\ResultInterface
     */

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}