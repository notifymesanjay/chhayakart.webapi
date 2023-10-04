<?php

namespace App\Http\Controllers\API;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class WithdrawalRequestsApiController extends Controller
{
    public function index(){
        //$withdrawalRequests = WithdrawalRequest::select('withdrawal_requests.*','users.name','sellers.name','delivery_boys.name','users.balance','sellers.balance','delivery_boys.balance')
        $withdrawalRequests = WithdrawalRequest::select('withdrawal_requests.*','users.name as user_name','sellers.name as seller_name','delivery_boys.name as delivery_boy_name',
            'users.balance as user_balance','sellers.balance as seller_balance','delivery_boys.balance as delivery_boy_balance')
            ->leftJoin('users', function($join){
                $join->where('withdrawal_requests.type', '=', 'user')
                    ->on('withdrawal_requests.type_id', '=', 'users.id');
            })
            ->leftJoin('sellers', function($join){
                $join->where('withdrawal_requests.type', '=', 'seller')
                    ->on('withdrawal_requests.type_id', '=', 'sellers.id');
            })
            ->leftJoin('delivery_boys', function($join){
                $join->where('withdrawal_requests.type', '=', 'delivery_boy')
                    ->on('withdrawal_requests.type_id', '=', 'delivery_boys.id');
            })
            //->expect('withdrawal_requests.created_at')
            ->orderBy('withdrawal_requests.id','DESC')
            ->get()->toArray();
        $data = array();
        foreach ($withdrawalRequests as $key =>$request){
            $subData = array();
            $subData["id"] = $request["id"];
            $subData["type"] = $request["type"];
            if($request["type"] == "user"){
                $subData["name"] = $request["user_name"];
                $subData["balance"] = $request["user_balance"];
            }elseif ($request["type"] == "seller"){
                $subData["name"] = $request["seller_name"];
                $subData["balance"] = $request["seller_balance"];
            }else{
                $subData["name"] = $request["delivery_boy_name"];
                $subData["balance"] = $request["delivery_boy_balance"];
            }
            $subData["amount"] = $request["amount"];
            $subData["message"] = $request["message"];
            $subData["status"] = $request["status"];
            $subData["created_at"] = $request["created_at"];
            $data[] = $subData;
        }
        return CommonHelper::responseWithData($data);
    }
    public function update(Request $request){
        $validator = Validator::make($request->all(),[
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }
        if(isset($request->id)){
            $withdrawalRequest = WithdrawalRequest::find($request->id);
            $withdrawalRequest->status = $request->status;
            $withdrawalRequest->save();
        }
        return CommonHelper::responseSuccess("Withdrawal Request Status Updated Successfully!");
    }
    public function delete(Request $request){
        if(isset($request->id)){
            $withdrawalRequest = WithdrawalRequest::find($request->id);
            if($withdrawalRequest){
                $withdrawalRequest->delete();
                return CommonHelper::responseSuccess("Withdrawal Request Deleted Successfully!");
            }else{
                return CommonHelper::responseSuccess("Withdrawal Request Already Deleted!");
            }
        }
    }
}
