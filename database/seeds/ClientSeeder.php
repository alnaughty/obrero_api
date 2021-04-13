<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\CustomerController;
use App\Services\SeedAdditionalFunctions;

class ClientSeeder extends Seeder
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
        for($i=0;$i<10;$i++){
        	DB::table('customers')->insert(
	    		[
	    			'first_name' => $this->function->randomName(true),
	    			'last_name' => $this->function->randomName(false),
	    			'contact_number'=> $this->function->randomNumber(),
	    			'email' => Str::random(5).'@gmail.com',
	    			'address' => Str::random(50),
	    		]
	    	);
	    	(new CustomerController)->createPaymentFromArray(['customer_id' => $i+1, 'amount' => $this->function->randomAmount(), 'status' => mt_rand(0,2)]);
        }
    }
}
