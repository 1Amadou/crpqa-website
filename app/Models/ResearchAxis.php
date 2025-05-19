<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResearchAxis extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subtitle',
        'description',
        'icon_class',
        'cover_image_path',
        'is_active',
        'display_order',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($researchAxis) {
            if (empty($researchAxis->slug)) {
                $researchAxis->slug = Str::slug($researchAxis->name);
            }

            $originalSlug = $researchAxis->slug;
            $count = 1;
            while (static::where('slug', $researchAxis->slug)->exists()) {
                $researchAxis->slug = $originalSlug . '-' . $count++;
            }
        });

        static::updating(function ($researchAxis) {
            if ($researchAxis->isDirty('slug')) {
                $newSlug = $researchAxis->slug;
                $currentId = $researchAxis->id;
                $count = 1;

                while (static::where('slug', $newSlug)->where('id', '!=', $currentId)->exists()) {
                    $newSlug = $researchAxis->slug . '-' . $count++;
                }

                $researchAxis->slug = $newSlug;
            }
        });
    }

    // public function getRouteKeyName()
    // {
    //     return 'slug';
    // }
}
