<?php

namespace App\Http\Controllers;

use App\Models\TransportationManagement;
use Illuminate\Http\Request;

class TransportationController extends Controller
{
    public function index(Request $request)
    {
        $transportations = TransportationManagement::latest()->paginate($request->per_page ?? 25);

        return response()->json([
            'status' => 'Success',
            'transportation'   => $transportations
        ], 200);
    }

    public function store(Request $request)
    {
        $validate_data = [
            'transporation_way' => 'required|string|unique:transportation_management,transporation_way',
            'parameter'         => 'required|string',
            'unit'              => 'required|string',
        ];
        $validator = $request->validate($validate_data);
        $transportation = TransportationManagement::create($validator);

        return $this->getSingleData($transportation->id, 'Transportation Created Successfully.', 201);
    }

    public function update(Request $request)
    {
        $validate_data = [
            'id'                => 'required|exists:transportation_management,id',
            'transporation_way' => 'required|string|unique:transportation_management,transporation_way,' . $request->id,
            'parameter'         => 'required|string',
            'unit'              => 'required|string',
        ];
        $validator = $request->validate($validate_data);
        $transportation = TransportationManagement::findOrFail($request->id);
        $transportation->update($validator);

        return $this->getSingleData($transportation->id, 'Transportation Updated Successfully.', 200);
    }

    public function getSingleData($id, $message = 'Transportation Found.', $code = 200)
    {
        $transportation = TransportationManagement::findOrFail($id);

        return response()->json([
            'message' => $message,
            'transportaton'   => $transportation
        ], $code);
    }

    public function destroy($id)
    {
        $transportation = TransportationManagement::findOrFail($id);
        $transportation->delete();

        return response()->json([
            'message' => 'Transportation Deleted Successfully.',
        ], 200);
    }
}
