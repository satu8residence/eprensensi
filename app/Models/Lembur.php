<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'hrd_lembur';
    protected $primaryKey = 'kode_lembur';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}
