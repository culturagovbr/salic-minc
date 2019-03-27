<?php

namespace App\GraphQL\Query;

use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class FooQuery extends Query
{
    protected $attributes = [
        'name' => 'FooQuery',
        'description' => 'A query'
    ];

    public function type()
    {
        return GraphQL::type('FooType');
    }

    public function args()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id())
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        return ['id' => 1, 'nome' => 'foo'];
    }
}
