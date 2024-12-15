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
        // Validate incoming data
        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'user_address' => 'required|string',
        ], [
            'latitude.required' => 'Please select a valid location on the map.',
            'longitude.required' => 'Please select a valid location on the map.',
            'user_address.required' => 'Please enter a valid address.',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $selectedLocation = $request->user_address;

        // Clean up the address by removing Plus Code (e.g., "7WMQ+G7C")
        if ($selectedLocation) {
            $selectedLocation = preg_replace('/^[A-Z0-9\+]+,? /', '', $selectedLocation);
        }

        $radius = 5; // Radius in kilometers

        // Calculate distance and fetch vendors within radius
        $vendors = Vendor::selectRaw(
            "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        )
        ->having("distance", "<", $radius)
        ->orderBy("distance", "asc")
        ->get();

        // Pass data to the view
        return view('frontend.pages.nearby-pharmacies', compact('vendors', 'selectedLocation', 'latitude', 'longitude'));
    }

    public function show($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('frontend.vendor.show', compact('vendor'));
    }
}
