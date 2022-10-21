<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\CartResource;
use App\Http\Resources\QuestionResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'orders' => OrderResource::collection($this->orders),
            'cart' => $this->cart,
            'questions' => QuestionResource::collection($this->questions),
            'answers' => AnswerResource::collection($this->answers),
        ];
    }
}
