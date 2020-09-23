<?php

use Illuminate\Database\Seeder;
use App\Banner;

class BannersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $bannerRecords=[
           ['id'=>1,'image'=>'banner1.png','link'=>'','title'=>'Black Jacket','alt'=>'Black Jecket','status'=>1],
           ['id'=>2,'image'=>'banner2.png','link'=>'','title'=>'Half Sleev T-Shirt','alt'=>'alf Sleev T-Shirt','status'=>1],
           ['id'=>3,'image'=>'banner3.png','link'=>'','title'=>'Full Sleev T-Shirt','alt'=>'Full Sleev T-Shirt','status'=>1]
       ];
       Banner::insert($bannerRecords);
    }
}
