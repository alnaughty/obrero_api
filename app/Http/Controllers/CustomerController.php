<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomerPayment;
use App\Customer;
use App\Services\GeneralService;
use App\Services\CustomerService;
class CustomerController extends Controller
{
    public function __construct(){
    	$this->customer = new Customer;
    	$this->payment = new CustomerPayment;
    	$this->gen_service = new GeneralService;
    	$this->serve = new CustomerService;
    }

    public function index($page) {
        $data = $this->customer::with('status','projects')->paginate($page);
        return response()->json($data);
    }
    public function createCustomer(Request $request)
    {
    	$data = $this->serve->validate($request);
    	if($data['status']){
    		$created = $this->gen_service->create($this->customer,$data['data']);
            if($created != null){
                $pay = $this->serve->validatePayment([
                    "customer_id" => $created->id,
                    "amount" => $request->amount
                ]);
                if($pay['status']){
                    $this->gen_service->create($this->payment, $pay['data']);
                    $res = $this->customer::find($created->id)->load('status', 'projects');
                    return response()->json($res,200);
                }
                return response()->json($pay,422);

            }
            return response()->json($created,422);
    		
    	}
    	return response()->json($data,422);
    }

    public function delete($customer_id)
    {
    	$toDelete = $this->customer::find($customer_id);
    	return $this->gen_service->delete($toDelete);
    }

    public function updateCustomer(Request $request)
    {
        $toUpdate = $this->customer::find($request->id);
        if($toUpdate != null)
        {
            $updated = $this->serve->updater($toUpdate, $request);
            return response()->json(['message' => "Update successful",$data = $updated],200);
        }
        return response()->json(['message' => 'Customer not found'],404);
    }

    //Payment
    public function createPaymentFromArray($arr)
    {
        $created = $this->gen_service->create($this->payment, $arr);
        return $created;
    }
    public function createPayment(Request $request)
    {
        $created = $this->gen_service->create($this->payment, $request->all());
        return response()->json($created,200);
    }
    public function deletePayment($customer_id){
        $toDelete = $this->payment::where('customer_id', $customer_id)->first();
        return $this->gen_service->delete($toDelete);
    }

    public function updatePayment(Request $request)
    {
        $toUpdate = $this->payment::find($request->id);
        if($toUpdate != null)
        {
            $updated = $this->serve->paymentUpdater($toUpdate, $request);
            return response()->json(['message' => "Update successful",$data = $updated],200);
        }
        return response()->json(['message' => "Payment not found, nothing to update"],404);
    }
}
