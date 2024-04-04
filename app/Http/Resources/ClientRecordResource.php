<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientRecordResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'type' => $this->type,
            'reference' => $this->reference,
            'values' => $this->getValuesArray(),
            'tags' => $this->getTagsAssocArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
