<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Str;
use Midtrans;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        request()->validate([
            'amount'=>['required','numeric'],
            'method'=>['required']
        ]);
        $order = Order::create([
            'amount'=>request('amount'),
            'method'=>request('method')
        ]);
        $order->order_id = $order->id.'-'. Str::random(5);
        $order->save();
        
        $response_midtrans = $this->midtrans_store($order);

        return response()->json([
            'response_code'=>'00',
            'response_status'=>'success',
            'data'=>$response_midtrans
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function midtrans_store(Order $order){
        $server_key = base64_encode(config('app.midtrans.server_key'));
        $base_uri = config('app.midtrans.base_uri');
        // dd($base_uri);
        $client = new Client([
            'base_uri'=>$base_uri
        ]);
        $headers = [
            'Accept'=>'application/json',
            'Authorization'=>'Basic '.$server_key,
            'Content-Type'=>'application/json',
        ];

        switch ($order->method){
            case 'bca':
                $body=[
                    'payment_type'=> 'bank_transfer',
                    'transaction_details'=> [
                        'order_id'=> $order->order_id,
                        'gross_amount'=> $order->amount
                    ],
                    'bank_transfer'=>[
                        'bank'=> 'bca'
                    ]
                ];
                break;
            case 'permata':
                $body=[
                    'payment_type'=> 'permata',
                    'transaction_details'=> [
                        'order_id'=> $order->order_id,
                        'gross_amount'=> $order->amount
                    ]
                ];
                break;
            default:
                $body=[];
            break;
        }
        $res = $client->post('/v2/charge',[
            'headers'=>$headers,
            'body'=>json_encode($body)
        ]);
            return json_decode($res->getBody());
    }
    public function generate(Request $request){
        // Set your Merchant Server Key
        Midtrans\Config::$serverKey = config('app.midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        Midtrans\Config::$is3ds = true;

        $midtrans_transaction =  \Midtrans\Snap::createTransaction($request->data);

        return response()->json([
            'response_code'=>'00',
            'response_status'=>'success',
            'data'=>$midtrans_transaction
        ]);
    }
}
