<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;
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
        // Fetch the current semester and academic year from the settings
        $settings = Setting::first();
        $currentSemester = $settings->current_semester;
        $currentSchoolYear = $settings->academic_year;

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
            'school_year' => $this->school_year ?? $currentSchoolYear, // Use subject's school year or fall back to current
            'semester' => $this->semester ?? $currentSemester, // Use subject's semester or fall back to current
            'type' => $this->type,
            'specific_date' => $this->specific_date,
        ];
    }
}
