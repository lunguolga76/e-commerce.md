<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->delete();
        $adminRecords =[
           [ 'id'=>1, 'name'=>'admin','type'=>'admin','mobile'=>'79405653','email'=>'admin@admin.com', 'password'=>'$2y$10$TuvKYUCWS7S37j/CuC2wtuYH17dof7YoF8G3NeZZFLcuUrR/PrIP2','image'=>'','status'=>1],

        [ 'id'=>2, 'name'=>'screlea','type'=>'subadmin','mobile'=>'79405653','email'=>'screlea@admin.com', 'password'=>'$2y$10$TuvKYUCWS7S37j/CuC2wtuYH17dof7YoF8G3NeZZFLcuUrR/PrIP2','image'=>'','status'=>1],

[ 'id'=>3, 'name'=>'zotea','type'=>'subadmin','mobile'=>'79405653','email'=>'zotea@admin.com', 'password'=>'$2y$10$TuvKYUCWS7S37j/CuC2wtuYH17dof7YoF8G3NeZZFLcuUrR/PrIP2','image'=>'','status'=>1],

[ 'id'=>4, 'name'=>'florea','type'=>'subadmin','mobile'=>'79405653','email'=>'florea@admin.com', 'password'=>'$2y$10$TuvKYUCWS7S37j/CuC2wtuYH17dof7YoF8G3NeZZFLcuUrR/PrIP2','image'=>'','status'=>1],
        ];
        DB::table('admins')->insert($adminRecords);
       /* foreach ($adminRecords as $key=>$record){
            \App\Admin::create($record);
        }*/

    }
}
