<?php

namespace App\Models;

use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;

class Users extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=60, nullable=true)
     */
    public $username;

    /**
     *
     * @var string
     * @Column(type="string", length=60, nullable=true)
     */
    public $email;

    /**
     *
     * @var string
     * @Column(type="string", length=120, nullable=true)
     */
    public $public_name;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $confiability;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $password;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $roles_id;

    public $created_at;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {

        $this->hasMany('id', 'App\Models\Reports', 'user_id', ['alias' => 'Reports',
            'foreignKey' => array('message' => 'No es posible eliminar usuario, ya está asociada a reportes')]);
        $this->belongsTo('roles_id', 'App\Models\\Roles', 'id', ['alias' => 'Roles',
            [
                'foreignKey' => [
                    'message'    => 'Debe existir el rol',
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
        return 'users';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users[]|Users
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function beforeValidationOnCreate()
    {

        if (!empty($this->password)) {

            $this->must_change_password = 0;
            $this->password = $this->getDI()
                ->getSecurity()
                ->hash($this->password);

        }
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function beforeValidationOnUpdate()
    {
        if (!empty($this->password)) {


            if (trim($this->password) != "") {

                $this->password = $this->getDI()
                    ->getSecurity()
                    ->hash($this->password);
            }
        }


    }

}
