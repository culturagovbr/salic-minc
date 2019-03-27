<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class FooType extends BaseType
{
    protected $attributes = [
        'name' => 'FooType',
        'description' => 'A type'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'Identificador.'
            ],
            'nome' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Nome'
            ],
        ];
    }
}
