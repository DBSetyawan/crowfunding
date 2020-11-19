<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use DB;
use Config;
use Illuminate\Http\Request;
use Auth;
use App\Donatur;
use App\Midtran;
use Validator;
class PaymentController extends Controller
{


    public function __construct() {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = Config::get('app.midtrans_server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = Config::get('app.midtrans_production');;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
     }

    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */
    public function start_payment($tipe,$id)
    {
        $program = DB::table('programs')->where('id',$id)->first();
        if(!$program){
            return abort(404);
        }
        $midtrans_client_key = Config::get('app.midtrans_client_key');
        $donasi_minimum = DB::table('settings')->where('key','site.donasi_minimum')->first()->value;
        return view('payment.start',compact('midtrans_client_key','program','donasi_minimum'));
    }


    public function get_snap_token(Request $request){

        $donasi_minimum = DB::table('settings')->where('key','site.donasi_minimum')->first();
        $validator = Validator::make($request->all(), [
            'donation_id'=>'required|exists:programs,id',
            'amount'=>'required|numeric|min:'.$donasi_minimum->value,
        ]);


        if ($validator->fails()) {    
            return response()->json($validator->messages(), 400);
        }

        // Save donasi ke database
        $donatur = Donatur::where('user_id',Auth::user()->id)->first();
        if(!$donatur){
            return response('donatur not found',404);
        }

        $program = DB::table('programs')->where('id',$request->donation_id)->first();
        if(!$program){
            return response('program not found',404);
        }
        $midtran_id = DB::table('midtrans')->insertGetId([
            'program_id'=>$program->id,
            'donatur_id'=>$donatur->id,
            'amount' => $request->amount,
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s'),
        ]);

        // Buat transaksi ke midtrans kemudian save snap tokennya.
        $payload = [
            'transaction_details' => [
                'order_id'      => $midtran_id,
                'gross_amount'  => $request->amount,
            ],
            'customer_details' => [
                'first_name'    => Auth::user()->name,
                'email'         => Auth::user()->email,
            ],
            'item_details' => [
                [
                    'id'       => $program->id,
                    'price'    => $request->amount,
                    'quantity' => 1,
                    'name'     => ucwords(str_replace('_', ' ', $program->program_name))
                ]
            ]
        ];
        $snapToken = \Midtrans\Snap::getSnapToken($payload);
        // echo json_encode(\Midtrans\Transaction::status(1));
        // save snap token to table
        DB::table('midtrans')->where('id',$midtran_id)->update([
            'snap_token'=>$snapToken
        ]);

        return $snapToken;
    }

    public function notificationHandler(Request $request)
    {
        $notif = new \Midtrans\Notification();

        // {
        //     "transaction_time":"2020-07-05 14:18:06",
        //     "gross_amount":"23233.00",
        //     "currency":"IDR",
        //     "order_id":"1",
        //     "payment_type":"gopay",
        //     "signature_key":"4f217ef90afe9ed6396f5457e3b96d301b93c8db5bc479a11f18c9f72156e5ac7ae4af512b76aa43a19e052fe13561f91d12f0b6335126fc01eb8bbcd24ab6b0",
        //     "status_code":"407",
        //     "transaction_id":"c47fc992-824f-436c-884b-0718e7d10ff5",
        //     "transaction_status":"expire","fraud_status":"accept",
        //     "status_message":"Success, transaction is found",
        //     "merchant_id":"G329848150"
        // }
        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraud = $notif->fraud_status;

        $donation = Midtran::findOrFail($orderId);
        $donation->payment_gateway	 = $notif->payment_gateway;
        $donation->payment_status  = $notif->payment_status;
        $donation->transaction_id  = $notif->transaction_id;
        $donation->transaction_time = $notif->transaction_time;
        
        // $donation->
 
        if ($transaction == 'capture') {

        // For credit card transaction, we need to check whether transaction is challenge by FDS or not
        if ($type == 'credit_card') {

            if($fraud == 'challenge') {
            // TODO set payment status in merchant's database to 'Challenge by FDS'
            // TODO merchant should decide whether this transaction is authorized or not in MAP
            // $donation->addUpdate("Transaction order_id: " . $orderId ." is challenged by FDS");
            $donation->setPending();
            } else {
            // TODO set payment status in merchant's database to 'Success'
            // $donation->addUpdate("Transaction order_id: " . $orderId ." successfully captured using " . $type);
            $donation->setSuccess();
            }

        }

        } elseif ($transaction == 'settlement') {

        // TODO set payment status in merchant's database to 'Settlement'
        // $donation->addUpdate("Transaction order_id: " . $orderId ." successfully transfered using " . $type);
        $donation->setSuccess();

        } elseif($transaction == 'pending'){

        // TODO set payment status in merchant's database to 'Pending'
        // $donation->addUpdate("Waiting customer to finish transaction order_id: " . $orderId . " using " . $type);
        $donation->setPending();

        } elseif ($transaction == 'deny') {

        // TODO set payment status in merchant's database to 'Failed'
        // $donation->addUpdate("Payment using " . $type . " for transaction order_id: " . $orderId . " is Failed.");
        $donation->setFailed();

        } elseif ($transaction == 'expire') {

        // TODO set payment status in merchant's database to 'expire'
        // $donation->addUpdate("Payment using " . $type . " for transaction order_id: " . $orderId . " is expired.");
        $donation->setExpired();

        } elseif ($transaction == 'cancel') {

        // TODO set payment status in merchant's database to 'Failed'
        // $donation->addUpdate("Payment using " . $type . " for transaction order_id: " . $orderId . " is canceled.");
        $donation->setFailed();

        }
 
 
        return;
    }


    // public function check_midtrans_status($transaction_id){
     
    //     //The URL of the resource that is protected by Basic HTTP Authentication.
    //    $url = 'https://api.midtrans.com/v2/'.$transaction_id.'/status';
    //    // echo $transaction_id;
    //    // die;
    //    //Your username.
    //    $username = "Mid-server-wezjRE0MhUzxSvGKV71hsf9v";
   
    //    //Your password.
    //    $password = '';
   
    //    //Initiate cURL.
    //    $ch = curl_init($url);
       
    //    $headers = array(
    //      'Content-Type: application/json',
    //      'Authorization: Basic '. base64_encode("$username:$password")
    //    );
    //    //Set the headers that we want our cURL client to use.
    //    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   
    //    //Tell cURL to return the output as a string instead
    //    //of dumping it to the browser.
    //    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   
    //    //Execute the cURL request.
    //    $response = curl_exec($ch);
       
    //    //Check for errors.
    //    if(curl_errno($ch)){
    //        //If an error occured, throw an Exception.
    //        throw new Exception(curl_error($ch));
    //    }
   
    //    //Print out the response.
    //    // echo $response;
    //    return json_decode($response);
     
    //   }

}