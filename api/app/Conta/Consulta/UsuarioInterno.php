<?php

namespace App\Conta\Consulta;

use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

use App\Conta\Modelo\UsuarioInterno as UsuarioInternoModelo;

final class UsuarioInterno extends Query
{
    protected $attributes = [
        'name' => 'UsuarioInterno',
        'description' => 'Usuario'
    ];

    public function type()
    {
        return GraphQL::type('UsuarioInterno');
    }

    public function args()
    {
        return [
            'usu_codigo' => [
                'type' => Type::nonNull(Type::id())
            ],

        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $usuario = UsuarioInternoModelo::find($args['usu_codigo']);

        return $usuario;
    }
}
