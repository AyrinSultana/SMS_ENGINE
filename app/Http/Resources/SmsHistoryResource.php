<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SmsHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'template' => $this->when($this->template, new TemplateResource($this->template)),
            'template_id' => $this->template_id,
            'recipient' => $this->recipient,
            'mobile_no' => $this->mobile_no,
            'status' => $this->status,
            'message' => $this->message,
            'modified_at' => $this->modified_at,
            'source' => $this->source,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'authorizer' => $this->authorizer,
        ];
    }
}
