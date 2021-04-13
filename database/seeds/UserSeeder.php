<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\SeedAdditionalFunctions;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->function = new SeedAdditionalFunctions;
    }
	
    
    public function run()
    {
        //
        DB::table('users')->insert(
            [
                'first_name' => "Super",
                "last_name" => "Admin",
                'contact_number' => $this->function->randomNumber(),
                'email' => 'super@admin.com',
                "address" => Str::random(50),
                "is_admin" => 1,
                "password" => bcrypt('super_password')
            ]
        );
        for($i=0;$i<10;$i++){
        	DB::table('users')->insert(
	    		[
	    			'first_name' => $this->function->randomName(true),
	    			'last_name' => $this->function->randomName(false),
	    			'contact_number'=> $this->function->randomNumber(),
	    			'email' => Str::random(5).'@gmail.com',
	    			'address' => Str::random(50),
	    			'password' => bcrypt('test123123'),
	    		]
	    	);
        }
    }
}
