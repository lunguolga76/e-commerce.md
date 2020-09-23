<?php

use Illuminate\Database\Seeder;
use App\ProductsImage;

class ProsductsImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productImageRecords=[
            ['id'=>1,'product_id'=>1,'image'=>'t-shirt.jpg-88907.jpg','status'=>1]
        ];
        ProductsImage::insert($productImageRecords);
    }
}
