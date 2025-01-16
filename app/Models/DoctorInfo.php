<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorInfo extends Model
{
    use HasFactory;

    protected $table = 'doctor_info';
    protected $fillable = [
        'provider_id',
        'years_of_experience',
        'medical_degree_and_specializtion',
        'availbiltyDay',
        'contantEng',
        'contentAra',
        'educationEng',
        'educationAra',
        'filterDate',
        'filterTime',
        'imageUrl',
        'introAra',
        'introEng',
        'nameAra',
        'nameEng',
        'noOfYearAra',
        'noOfYearEng',
        'status',
    ];
}
