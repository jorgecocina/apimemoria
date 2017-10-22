<?php

namespace App\Business;

class BaseBSN extends \Phalcon\DI\Injectable
{

    const ERROR_INVALID_PARAMETERS    = ['code' => '1001', 'message' => 'Parámetros incorrectos.'];

    const ERROR_ALREADY_EXISTS        = ['code' => '1002', 'message' => 'El elemento ya existe.'];

    const ERROR_NO_RECORDS_FOUND      = ['code' => '1003', 'message' => 'No se encontraron registros.'];

    const MISSING_PARAMETERS          = ['code' => '1004', 'message' => 'Faltan parámetros.'];

    const CNT_DEL_SERVICE_MLTPL_RPRTS = ['code' => '1005', 'message' => 'No es posible eliminar el servicio: Posee varios reportes.'];

    const CNT_DEL_SERVICE_NOT_OWNER   = ['code' => '1006', 'message' => 'No es posible eliminar el servicio: No es el usuario creador.'];

    const CNT_DEL_SERVICE_OUT_TIME    = ['code' => '1007', 'message' => 'No es posible eliminar el servicio: Tiempo de edición agotado.'];

    const CNT_DEL_REPORT_NOT_OWNER    = ['code' => '1008', 'message' => 'No es posible eliminar el reporte: No es el usuario creador.'];

    const CNT_DEL_REPORT_OUT_TIME     = ['code' => '1009', 'message' => 'No es posible eliminar el reporte: Tiempo de edición agotado.'];

    const NOT_AUTHORIZED_INFO_ACCESS  = ['code' => '1010', 'message' => 'No tienes acceso a esta información.'];

    const EMAIL_ALREADY_EXISTS        = ['code' => '2001', 'message' => 'El email ya está registrado.'];

    const USERNAME_ALREADY_EXISTS     = ['code' => '2002', 'message' => 'El nombre de usuario ya existe.'];

    const LOGIN_VALIDATION_ERROR      = ['code' => '2003', 'message' => 'Error en validación. Combinación de username/password incorrecta.'];

    const VOTE_ATEMP_ERROR            = ['code' => '2004', 'message' => 'Demasiados intentos de votos seguidos.'];

    const ERROR_DATABASE              = ['code' => '8888', 'message' => 'Query error: '];

    const ERROR_UNKNOW                = ['code' => '9999', 'message' => 'Unknow error: '];

    public $error = [];

}