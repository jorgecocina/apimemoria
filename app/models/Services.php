<?php

namespace App\Models;

class Services extends \Phalcon\Mvc\Model
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
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $x_position;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $y_position;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $confirmed;

    /**
     *
     * @var double
     * @Column(type="double", nullable=true)
     */
    public $price;

    /**
     *
     * @var double
     * @Column(type="double", nullable=true)
     */
    public $quality;

    /**
     *
     * @var double
     * @Column(type="double", nullable=true)
     */
    public $confiability;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $service_types_id;

    /**
     *
     * @var datetime
     * @Column(type="datetime", nullable=false)
     */
    public $created_at;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {

        $this->hasMany('id', 'App\Models\Reports', 'services_id', ['alias' => 'Reports',
            'foreignKey' => array('message' => 'No es posible eliminar servicio, ya está asociada a reportes')]);
        $this->belongsTo('service_types_id', 'App\Models\ServiceTypes', 'id', ['alias' => 'ServiceTypes',
            [
                'foreignKey' => [
                    'message'    => 'Debe existir el tipo de servicio',
                ]
            ]]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'services';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Services[]|Services
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Services
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function beforeValidationOnCreate()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

}
