<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Api de gestion de productos',
    version: '1.0.0',
    description: 'API REST para gestionar productos y categorias',
)]

#[OA\Server(
    url: 'http://localhost:8000/api',
    description: 'Local server'
)]

#[OA\Schema(
    schema: 'ErrorResponse',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: "El campo 'name' es obligatorio"),
        new OA\Property(
            property: 'errors',
            type: 'object',
            example: ['name' => ['El campo nombre es requerido']],
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string')
            )
        ),
    ]
)]
class Documentation
{
    // Esta clase se utiliza para generar la documentación de OpenAPI
}