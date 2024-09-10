<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'finger_id' => $this->finger_id,
            'fingerprint_template' => base64_encode($this->fingerprint_template),
            'pin' => $this->pin,
            'subjects' => SubjectResource::collection($this->whenLoaded('subjects')),
            'school_year' => $this->school_year,
            'semester' => $this->semester,
        ];
    }
}
