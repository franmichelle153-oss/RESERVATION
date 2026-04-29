<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table    = 'feedbacks'; // ← DAGDAG ITO

    protected $fillable = ['user_id', 'reservation_id', 'rating', 'comment'];

    public function user()        { return $this->belongsTo(User::class); }
    public function reservation() { return $this->belongsTo(Reservation::class); }
}