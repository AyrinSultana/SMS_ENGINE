<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PendingListResource extends JsonResource
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
            'template_name' => $this->template_name,
            'msg_details' => $this->msg_details,
            'file_path' => $this->file_path,
            'original_filename' => $this->original_filename,
            'status' => $this->status,
            'timestamp' => $this->timestamp,
            'queue_count' => $this->whenLoaded('smsQueue', function() {
                return $this->smsQueue->count();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
