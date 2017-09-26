<?php
namespace App\Models;

class PriceRanges extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $service_type_id;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=true)
     */
    public $price;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'App\Models\Reports', 'price', ['alias' => 'Reports']);
        $this->hasMany('id', 'App\Models\Services', 'price', ['alias' => 'Services']);
        $this->belongsTo('service_type_id', 'App\Models\ServiceTypes', 'id', ['alias' => 'ServiceTypes']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'price_ranges';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return PriceRanges[]|PriceRanges
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return PriceRanges
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
