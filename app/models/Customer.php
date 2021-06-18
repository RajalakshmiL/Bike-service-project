<?php
namespace Acc\Models;

class Customer extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     */

    public $customer_id;
    /**
     *
     * @var string
     */

    public $customer_name;
    /**
     *
     * @var string
     */

    public $customer_mail;
    /**
     *
     * @var string
     */

    public $customer_mobile;
    /**
     *
     * @var integer
     */

    public $service_id;
    /**
     *
     * @var string
     */

    public $service_date;
    /**
     *
     * @var string
     */

    public $delivery_date;
    /**
     *
     * @var integer
     */

    public $status_id;
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
        return 'customer';
    }
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Customer[]|Customer|\Phalcon\Mvc\Model\ResultSetInterface
     */

    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }
    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Customer|\Phalcon\Mvc\Model\ResultInterface
     */

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}