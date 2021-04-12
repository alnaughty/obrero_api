<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\CustomerController;
use Faker\Factory as Faker; //use Faker

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	$faker = Faker::create('en_GB');
        DB::table('users')->insert(
            [
                'first_name' => "Super",
                "last_name" => "Admin",
                'contact_number' => $faker->phoneNumber,
                'email' => 'super@admin.com',
                "address" => $faker->address,
                "is_admin" => 1,
                "password" => bcrypt('super_password')
            ]
        );
    	for($i=0;$i<10;$i++){
    		DB::table('users')->insert(
    		[
    			'first_name' => $faker->name,
    			'last_name' => $faker->lastName,
    			'contact_number'=> $faker->phoneNumber,
    			'email' => $faker->email,
    			'address' => $faker->address,
    			'password' => bcrypt('test123123'),
    		]
    	);
            DB::table('customers')->insert(
            [
                'first_name' => $faker->name,
                'last_name' => $faker->lastName,
                'contact_number'=> $faker->phoneNumber,
                'email' => $faker->email,
                'address' => $faker->address,
            ]
        );
            (new CustomerController)->createPaymentFromArray(['customer_id' => $i+1, 'amount' => $faker->numerify('####.##')]);
    	}
    }
}
