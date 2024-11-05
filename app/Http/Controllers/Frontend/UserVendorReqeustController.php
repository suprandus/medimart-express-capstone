<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorCondition;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserVendorReqeustController extends Controller
{
    use ImageUploadTrait;

    public function index()
    {
        $content = VendorCondition::first();
        return view('frontend.dashboard.vendor-request.index', compact('content'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'shop_image' => ['required', 'image', 'max:3000'],
            'shop_name' => ['required', 'max:200'],
            'shop_email' => ['required', 'email'],
            'shop_phone' => ['required', 'max:200'],
            'shop_address' => ['required'],
            'about' => ['required'],
            'latitude' => ['required', 'numeric'], // Validate latitude
            'longitude' => ['required', 'numeric'], // Validate longitude
            'tin' => ['required', 'string', 'max:50'], // Validate TIN
            'bir_certificate' => ['required', 'image', 'max:3000'], // Validate BIR certificate
        ]);

        if(Auth::user()->role === 'vendor'){
            return redirect()->back();
        }

        $imagePathProfile = $this->uploadImage($request, 'shop_image', 'uploads');
        $imagePathBir = $this->uploadImage($request, 'bir_certificate', 'uploads');


        $vendor = new Vendor();

        $vendor->banner = $imagePathProfile;
        $vendor->phone = $request->shop_phone;
        $vendor->email = $request->shop_email;
        $vendor->address = $request->shop_address;
        $vendor->description = $request->about;
        $vendor->shop_name = $request->shop_name;
        $vendor->user_id = Auth::user()->id;
        $vendor->status = 0;
        $vendor->latitude = $request->latitude;
        $vendor->longitude = $request->longitude;
        $vendor->tin = $request->tin;
        $vendor->bir_certificate = $imagePathBir;
        $vendor->save();

        toastr('Submitted successfully please wait for approve!', 'success', 'success');

        return redirect()->back();

    }
}
