<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CodSetting;
use App\Models\PaypalSetting;
use App\Models\RazorpaySetting;
use App\Models\StripeSetting;
use App\Models\PayMongoSetting;
use Illuminate\Http\Request;


class PaymentSettingController extends Controller
{
    public function index()
    {
        $paymongoSetting = PayMongoSetting::first();
        $paypalSetting = PaypalSetting::first();
        $stripeSetting = StripeSetting::first();
        $razorpaySetting = RazorpaySetting::first();
        $codSetting = CodSetting::first();


        return view('admin.payment-settings.index', compact('paymongoSetting', 'paypalSetting', 'stripeSetting', 'razorpaySetting', 'codSetting'));
        
    }
}
