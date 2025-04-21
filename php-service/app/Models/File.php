<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $primaryKey = 'file_id'; 
    public $incrementing = true; 
    protected $keyType = 'int'; 
    protected $fillable = [
        'name',
        'type',
        'size',
        'uploader_ip',
        'status',
        'result_path',
    ];
}
