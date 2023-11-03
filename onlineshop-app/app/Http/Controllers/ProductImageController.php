<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductImageRequest;
use App\Http\Resources\ProductImageResource;
use App\Models\ProductImage;
use Illuminate\Http\Response;

class ProductImageController extends Controller
{
    public function imageStore(ProductImageRequest $request)
    {
        $validatedData = $request->validated();

        $validatedData['image'] = $request->file('image')->store('image');

        $data = ProductImage::create($validatedData);

        return response(new ProductImageResource($data), Response::HTTP_CREATED);
    }
}
