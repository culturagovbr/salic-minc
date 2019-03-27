<?php

namespace App\Conta\Tipo;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;

final class UsuarioInterno extends BaseType
{
    protected $attributes = [
        'name' => 'UsuarioInterno',
        'description' => 'Usuario interno do sistema'
    ];

    public function fields()
    {
        return [
            'usu_codigo' => [
                'type' => Type::id(),
                'description' => 'Identificador.'
            ],
            'usu_nome' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Nome'
            ]
        ];
    }
}
