<?php

use Illuminate\Database\Seeder;
use App\Brand;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $brandsRecord=[
           ['id'=>1,'name'=>'Adidas','status'=>1],
           ['id'=>2,'name'=>'Nike','status'=>1],
           ['id'=>3,'name'=>'Puma','status'=>1],
           ['id'=>4,'name'=>'Asics','status'=>1],
           ['id'=>5,'name'=>'Diadora','status'=>1],
       ];
       Brand::insert($brandsRecord);
    }
}
