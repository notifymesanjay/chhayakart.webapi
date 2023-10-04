<?php

namespace App\Http\Controllers\API;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\DeliveryBoy;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashCollectionApiController extends Controller
{
    public function getCashCollection(Request $request){
        $deliveryBoys = DeliveryBoy::orderBy('id','DESC')->get();

        $transactions = Transaction::select('delivery_boys.name','delivery_boys.mobile','delivery_boys.address', 'transactions.*')
            ->leftJoin('delivery_boys', 'transactions.user_id', '=', 'delivery_boys.id');
        if(isset($request->startDate) && $request->startDate != "" && isset($request->endDate) && $request->endDate != ""){
            $startDate = Carbon::parse($request->input('startDate'))->startOfDay();
            $endDate = Carbon::parse($request->input('endDate'))->endOfDay();

            $transactions = $transactions->whereBetween('transactions.created_at', [$startDate, $endDate]);
        }
        $transactions = $transactions->orderBy('transactions.id','DESC')->get();

        $data = array(
            'deliveryBoys' => $deliveryBoys,
            'transactions' => $transactions
        );
        return CommonHelper::responseWithData($data);
    }
    public function save(Request $request){
        //print_r($request->deliveryBoy);
        \Log::info('Save : ',[$request->all()]);
        $validator = Validator::make($request->all(),[
            'deliveryBoy' => 'required',
            'amount' => 'required',
            'transaction_date' => 'required',
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }
        $deliveryBoyData = json_decode($request->deliveryBoy);
        $transaction = new Transaction();
        $transaction->user_id = $deliveryBoyData->id;
        $transaction->order_id = "";
        $transaction->txn_id = "";
        $transaction->payu_txn_id = "";
        $transaction->type = 'delivery_boy_cash_collection';
        $transaction->amount = $request->amount;
        $transaction->status = 1;
        $transaction->message = $request->message;
        $transaction->transaction_date = $request->transaction_date;
        $transaction->save();

        $deliveryBoy = DeliveryBoy::find($deliveryBoyData->id);
        $deliveryBoy->cash_received =($deliveryBoyData->cash_received-$request->amount);
        $deliveryBoy->save();
        return CommonHelper::responseSuccess("Fund Transfer Saved Successfully!");
    }
}
