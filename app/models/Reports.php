<?php

namespace App\Models;

class Reports extends \Phalcon\Mvc\Model
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
     * @Column(type="integer", length=11, nullable=false)
     */
    public $user_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $report_types_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $services_id;

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
     * @var double
     * @Column(type="double", nullable=true)
     */
    public $price;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $quality;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $active;

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

        $this->belongsTo('report_types_id', 'App\Models\ReportTypes', 'id', ['alias' => 'ReportTypes',
            [
                'foreignKey' => [
                    'message'    => 'Debe existir el tipo de reporte',
                ]
            ]]);
        $this->belongsTo('services_id', 'App\Models\Services', 'id', ['alias' => 'Services',
            [
                'foreignKey' => [
                    'message'    => 'Debe existir el servicio',
                ]
            ]]);
        $this->belongsTo('user_id', 'App\Models\Users', 'id', ['alias' => 'Users',
            [
                'foreignKey' => [
                    'message'    => 'Debe existir el usuario',
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
        return 'reports';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Reports[]|Reports
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Reports
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
