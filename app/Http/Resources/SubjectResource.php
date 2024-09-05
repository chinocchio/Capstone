<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SubjectResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'section' => $this->section, // Added section
            'image' => $this->image,
            'qr' => $this->qr,
            'day' => $this->day,
            'start_time' => $this->start_time, // Added start_time
            'end_time' => $this->end_time, // Added end_time
            'school_year' => $this->schoolYear,
            'semester' => $this->semester,
        ];
    }

    // If I want the data to appear in 12 hour format
    // public function toArray(Request $request): array
    // {
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name,
    //         'code' => $this->code,
    //         'description' => $this->description,
    //         'section' => $this->section,
    //         'start_time' => $this->start_time ? Carbon::parse($this->start_time)->format('g:i A') : null,
    //         'end_time' => $this->end_time ? Carbon::parse($this->end_time)->format('g:i A') : null,
    //     ];
    // }
}
