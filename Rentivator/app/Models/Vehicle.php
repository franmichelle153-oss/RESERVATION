<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

   protected $fillable = [
    'name', 'type', 'rate', 'max_hectares', 'status', 'image_path', 'image_data', 'estimated_fix_days',
    'driver_name', 'driver_pay',
    'helper1_name', 'helper2_name', 'helper3_name', 'helper_pay_each',
    'diesel_cost',
];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}