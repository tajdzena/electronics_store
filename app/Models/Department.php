<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'department';
    protected $primaryKey = 'department_id';

    protected $fillable = ['department_name'];
    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class, 'department_id');
    }
}
