<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['building_id', 'name', 'floor', 'room'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}

