<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product'; //Da ne bi Laravel "pretpostavljao" ime tabele
    protected $primaryKey = 'upc'; //Kombinovani kljucevi ne rade u Laravelu bas kako sam mislila :) , ideja je bila za upc i product_number
                                    //Jer product_number ne mora uvek da bude unique, moze da se desi da dve kompanije potrefe isti
    public $incrementing = false;

    protected $fillable = ['product_number',
                            'sku',
                            'regular_price',
                            'sale_price',
                            'description',
                            'category_id',
                            'department_id',
                            'manufacturer_id'];


    //Ispisivanje cena u JSON moze da ispadne kao string umesto decimal kao u bazi
    protected $casts = [
        'sku' => 'integer',
        'regular_price' => 'float',
        'sale_price' => 'float'
    ];

    public $timestamps = false;


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
