<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    
    protected $table = 'galleries'; // You already have this

    // Add the provider_id to the fillable array to allow mass assignment
    protected $fillable = [
        'provider_id',
        'type_en',
        'type_ar',
        'description_en',
        'description_ar',
        'image_url', // Add this if uploading an image
        'banner',
        'contract',
        'document',
    ];
    
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
