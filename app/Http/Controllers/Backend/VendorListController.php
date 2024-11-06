<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\VendorListDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;


class VendorListController extends Controller
{
    public function index(VendorListDataTable $dataTable)
    {
        return $dataTable->render('admin.vendor-list.index');
    }

    public function changeStatus(Request $request)
    {
        $customer = User::findOrFail($request->id);
        $customer->status = $request->status == 'true' ? 'active' : 'inactive';
        $customer->save();

        return response(['message' => 'Status has been updated!']);
    }

    public function nearestVendors(Request $request)
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $selectedLocation = $request->user_address;

        // Radius in kilometers (e.g., 5km)
        $radius = 5;

        $vendors = Vendor::selectRaw(
            "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        )
            ->having("distance", "<", $radius)
            ->orderBy("distance", "asc")
            ->get();

        return view('frontend.pages.nearby-pharmacies', compact('vendors', 'selectedLocation'));
    }
}
