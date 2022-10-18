<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductImage;
use App\Http\Requests\ProductImageRequest;
use Illuminate\Http\Response;
use App\Http\Resources\ProductImageResource;

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
