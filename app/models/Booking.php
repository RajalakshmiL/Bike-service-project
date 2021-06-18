<?php
namespace Acc\Models;

class Booking extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     */

    public $booking_id;
    /**
     *
     * @var integer
     */

    public $customer_id;
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
         * Returns table name mapped in the model.
        *
        * @return string
    */

    public function getSource()
    {
        return 'booking';
    }
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Booking[]|Booking|\Phalcon\Mvc\Model\ResultSetInterface
     */

    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }
    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Booking|\Phalcon\Mvc\Model\ResultInterface
     */

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}