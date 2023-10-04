<?php

namespace App\Http\Controllers\API;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WebHeaderApiController extends Controller
{
    public function index()
    {
        $headerArray = array(
            "front_end_settings" => 1,
            "android_app_url" => "",
            "call_back_url" => "",
            "common_meta_keywords" => "",
            "common_meta_description" => "",
            "color" => "",
            "show_color_picker_in_website" => false,
            "favicon" => "",
            "web_logo" => "",
            "loading" => "",
            "screenshots" => "null",
            "google_play" => "null"
        );
        $variables = array_keys($headerArray);
        $policies = Setting::whereIn('variable',$variables )->get();
        $data = array(
            "headerObject" => $headerArray,
            "header" => $policies
        );
        return CommonHelper::responseWithData($data);
    }
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'android_app_url' => 'required',
            'call_back_url' => 'required',
            'favicon' => $request->hasFile('favicon')?'mimes:jpeg,jpg,png,gif':'',
            'web_logo' => $request->hasFile('web_logo')?'mimes:jpeg,jpg,png,gif':'',
            'loading' => $request->hasFile('loading')?'mimes:jpeg,jpg,png,gif':'',
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        if($request->hasFile('favicon'))
        {
            $file = $request->file('favicon');
            $fileName = time().'_'.rand(1111,99999).'.'.$file->getClientOriginalExtension();

            $favicon = Storage::disk('public')->putFileAs('front_end/favicon', $file, $fileName);
        }
        if($request->hasFile('web_logo'))
        {
            $file = $request->file('web_logo');
            $fileName = time().'_'.rand(1111,99999).'.'.$file->getClientOriginalExtension();
            $web_logo = Storage::disk('public')->putFileAs('front_end/web_logo', $file,$fileName);
        }
        if($request->hasFile('loading'))
        {
            $file = $request->file('loading');
            $fileName = time().'_'.rand(1111,99999).'.'.$file->getClientOriginalExtension();
            $loading = Storage::disk('public')->putFileAs('front_end/loading', $file,$fileName);
        }
        foreach ($request->all() as $key => $value){
            $is_file_seleted = false;
            if($key=='favicon' && isset($favicon)){
                $value = $favicon;
                $is_file_seleted = true;
            }elseif($key=='web_logo' && isset($web_logo)){
                $value = $web_logo ;
                $is_file_seleted = true;
            }elseif($key=='loading' && isset($loading)){
                $value = $loading;
                $is_file_seleted = true;
            }

            $setting = Setting::where('variable', $key)->first();
            if ($setting) {
                if($is_file_seleted){
                    @Storage::disk('public')->delete($setting->$key);
                }
                $setting->variable = $key;
                $setting->value = $value;
                $setting->save();
            } else {
                $setting = new Setting();
                $setting->variable = $key;
                $setting->value = $value;
                $setting->save();
            }
        }
        return CommonHelper::responseSuccess("Web Header Setting Saved Successfully!");
    }
}
