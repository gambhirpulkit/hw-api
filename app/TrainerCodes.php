<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainerCodes extends Model
{
    protected $table = 'trainer_codes';

    protected $fillable = [
        'trainer_id', 'codes','expires' 
    ];    
    
}
