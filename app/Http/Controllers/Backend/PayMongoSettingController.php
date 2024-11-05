<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PayMongoSetting;

class PayMongoSettingController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => ['required', 'integer'],
            'mode' => ['required', 'integer'],
            'public_key' => ['required', 'max:200'],
            'secret_key' => ['required', 'max:200']
        ]);

        PayMongoSetting::updateOrCreate(
            ['id' => $id],
            [
                'paymongo_status' => $request->status,
                'live_mode' => $request->mode,
                'public_key' => $request->public_key,
                'secret_key' => $request->secret_key
            ]
        );

        toastr('Updated Successfully!', 'success', 'Success');
        return redirect()->back();
    }
}
