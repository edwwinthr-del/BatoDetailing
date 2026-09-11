<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['brand', 'model', 'year', 'type', 'license_plate', 'color', 'image_path', 'notes'])]
class Vehicle extends Model
{
    use HasFactory;

    public const TYPES = ['coupe', 'hatchback', 'sedan', 'suv', 'minivan', 'pickup'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function displayName(): string
    {
        return trim($this->brand.' '.$this->model.($this->year ? ' ('.$this->year.')' : ''));
    }
}
