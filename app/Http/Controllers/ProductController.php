<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use OpenApi\Attributes as OA;

class ProductController
{
    #[OA\Get(
        path: '/products',
        summary: 'Obtener todos los productos',
        tags: ['Productos'],
        parameters: [
            new OA\Parameter(
                name: 'category_id',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de productos obtenida exitosamente'
            )
        ]
    )]

    #[OA\Post(
        path: '/products',
        summary: 'Crear un nuevo producto',
        tags: ['Productos'],
        description: 'Crea un nuevo producto con los datos proporcionados',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['name', 'price', 'stock', 'category_id'],
                    properties: [
                        new OA\Property(
                            property: 'name',
                            type: 'string',
                            description: 'Nombre del producto'
                        ),
                        new OA\Property(
                            property: 'description',
                            type: 'string',
                            description: 'Descripción del producto'
                        ),
                        new OA\Property(
                            property: 'price',
                            type: 'number',
                            format: 'float',
                            description: 'Precio del producto'
                        ),
                        new OA\Property(
                            property: 'stock',
                            type: 'integer',
                            description: 'Cantidad en stock del producto'
                        ),
                        new OA\Property(
                            property: 'category_id',
                            type: 'integer',
                            description: 'ID de la categoría a la que pertenece el producto'
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Producto creado exitosamente'
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]

    #[OA\Put(
        path: '/products/{product}',
        summary: 'Actualizar un producto existente',
        tags: ['Productos'],
        description: 'Actualiza un producto existente con los datos proporcionados',
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'name',
                            type: 'string',
                            description: 'Nombre del producto'
                        ),
                        new OA\Property(
                            property: 'description',
                            type: 'string',
                            description: 'Descripción del producto'
                        ),
                        new OA\Property(
                            property: 'price',
                            type: 'number',
                            format: 'float',
                            description: 'Precio del producto'
                        ),
                        new OA\Property(
                            property: 'stock',
                            type: 'integer',
                            description: 'Cantidad en stock del producto'
                        ),
                        new OA\Property(
                            property: 'category_id',
                            type: 'integer',
                            description: 'ID de la categoría a la que pertenece el producto'
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto actualizado exitosamente'
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            )
        ]
    )]
    public function index(Request $request)
    {
        return response()->json(Product::with('category')
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->paginate(10));
        return response()->json($products);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'data' => $product->load('category')
        ], 201);
    }
    
    #[OA\Get(
        path: '/products/{product}',
        summary: 'Obtener un producto',
        tags: ['Productos'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Producto encontrado'),
            new OA\Response(response: 404, description: 'Producto no encontrado')
        ]
    )]
    public function show(Product $product)
    {
        return response()->json($product->load('category'));
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        $product->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente',
            'data' => $product->load('category')
        ], 200);
    }

    #[OA\Delete(
        path: '/products/{product}',
        summary: 'Eliminar un producto',
        tags: ['Productos'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Producto eliminado exitosamente'),
            new OA\Response(response: 404, description: 'Producto no encontrado')
        ]
    )]
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente',
            'data' => $product->load('category')
        ], 200);
    }
}
