<?php
namespace App\Models;

class UserTracks extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=20, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $timestamp;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $movement_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $pos_x;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $pos_y;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $classes;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('movement_id', '\Movements', 'id', ['alias' => 'Movements']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user_tracks';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserTracks[]|UserTracks
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserTracks
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
