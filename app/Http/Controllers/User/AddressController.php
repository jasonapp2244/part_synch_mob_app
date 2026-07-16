<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAddress;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = DeliveryAddress::where('user_id', auth()->id())
            ->where('status', 'active')
            ->whereNotNull('full_name')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Saved addresses fetched.',
            'data' => $addresses,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ]);

        $address = DeliveryAddress::create([
            'user_id' => auth()->id(),
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'status' => 'active',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address saved successfully.',
            'data' => $address,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $address = DeliveryAddress::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ]);

        $address->update($request->only([
            'full_name', 'phone_number', 'address_line1', 'address_line2',
            'city', 'state', 'country', 'postal_code'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully.',
            'data' => $address,
        ]);
    }

    public function destroy($id)
    {
        $address = DeliveryAddress::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $address->delete();

        return response()->json([
            'status' => true,
            'message' => 'Address deleted successfully.',
        ]);
    }
}
