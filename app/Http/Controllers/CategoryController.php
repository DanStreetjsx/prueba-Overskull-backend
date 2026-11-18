<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class CategoryController 
{
    #[OA\Get(
        path: '/categories',
        summary: 'Obtener todas las categorías',
        tags: ['Categorías'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        description: 'Devuelve una lista de todas las categorías',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de categorías'
            )
        ]
    )]


    #[OA\Post(
        path: '/categories',
        summary: 'Crear una nueva categoría',
        tags: ['Categorías'],
        description: 'Crea una nueva categoría con los datos proporcionados',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['name'],
                    properties: [
                        new OA\Property(
                            property: 'name',
                            type: 'string',
                            description: 'Nombre de la categoría'
                        ),
                        new OA\Property(
                            property: 'description',
                            type: 'string',
                            description: 'Descripción de la categoría'
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Categoría creada exitosamente'
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]

    public function index()
    {
        return response()->json(Category::all());
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada exitosamente',
            'data' => $category
        ], 201);
    }
    #[OA\Get(
        path: '/categories/{category}',
        summary: 'Obtener una categoría con sus productos',
        tags: ['Categorías'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Categoría encontrada'),
            new OA\Response(response: 404, description: 'Categoría no encontrada')
        ]
    )]
    public function show(Category $category)
    {
        return response()->json($category->load('products'));
    }   
    #[OA\Put(
        path: '/categories/{category}',
        summary: 'Actualizar una categoría existente',
        tags: ['Categorías'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'name', type: 'string', description: 'Nombre de la categoría'),
                        new OA\Property(property: 'description', type: 'string', description: 'Descripción de la categoría')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Categoría actualizada exitosamente'),
            new OA\Response(response: 404, description: 'Categoría no encontrada'),
            new OA\Response(
                response: 422,
                description: 'Error de validación',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id),
            ],
            'description' => 'sometimes|nullable|string',
        ]);

        $category->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente',
            'data' => $category
        ], 200);
    }

    #[OA\Delete(
        path: '/categories/{category}',
        summary: 'Eliminar una categoría',
        tags: ['Categorías'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Categoría eliminada exitosamente'),
            new OA\Response(response: 404, description: 'Categoría no encontrada'),
            new OA\Response(response: 409, description: 'La categoría tiene productos asociados')
        ]
    )]
    public function destroy(Category $category)
    {
        if($category->products()->exists()) {
            return response()->json(['success' => false, 'message' => 'La categoria tiene productos asociados.'], 409);
        } 
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada exitosamente'
        ], 200);
    }
}
