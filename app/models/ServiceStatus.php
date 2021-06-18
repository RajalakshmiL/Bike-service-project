<?php
namespace Acc\Models;

class ServiceStatus extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     */

    public $status_id;
    /**
     *
     * @var string
     */

    public $status_name;
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
        return 'service_status';
    }
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ServiceStatus[]|ServiceStatus|\Phalcon\Mvc\Model\ResultSetInterface
     */

    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }
    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ServiceStatus|\Phalcon\Mvc\Model\ResultInterface
     */

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}