<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordResource extends JsonResource
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
            'process_id' => $this->process->id,
            'run' => $this->run,
            'type' => $this->type,
            'reference' => $this->reference,
            'values' => $this->getValuesArray(),
            'tags' => $this->getTagsAssocArray(),
            'status' => $this->processStatus->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'retain_days' => $this->retain_days,
        ];
    }
}
