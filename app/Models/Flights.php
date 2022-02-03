<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flights extends Model
{
    use HasFactory;

    public function departure(){
        return $this->belongsTo(Airports::class, 'departure_id');
    }
    
    public function destination(){
        return $this->belongsTo(Airports::class, 'destination_id');
    }
    
    public function airline(){
        return $this->belongsTo(Airlines::class, 'airline_id');
    }
}
