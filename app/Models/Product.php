<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product'; //Da ne bi Laravel "pretpostavljao" ime tabele
    protected $primaryKey = ['product_number', 'upc']; //Kombinovani primarni kljuc

    protected $fillable = ['sku',
                            'regular_price',
                            'sale_price',
                            'description',
                            'category_id',
                            'department_id',
                            'manufacturer_id'];

//    protected $casts = [
//        'sku' => 'integer',
//    ];

    public $timestamps = false;
    public $incrementing = false;

    //Veze sa modelima Category, Department i Manufacturer
    //Svaki proizvod ima tj. pripada samo jednoj kategoriji, odeljenju i proizvodjacu
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }
}
