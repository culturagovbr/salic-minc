<?php
/**
 * Created by PhpStorm.
 * User: clebersantos
 * Date: 10/05/18
 * Time: 18:19
 */

namespace App\Conta\Modelo;

use Illuminate\Database\Eloquent\Model;

final class UsuarioInterno extends Model
{
    protected $table = 'Tabelas.dbo.Usuarios';
    protected $primaryKey = 'usu_codigo';


}