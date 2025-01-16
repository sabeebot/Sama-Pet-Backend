<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodeUsage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'date_of_usage',
        'code_id',
    ];

    // Define any relationships if needed
    public function owner()
    {
        return $this->belongsTo(PetOwner::class); // Adjust if you have a specific model for owner
    }
}