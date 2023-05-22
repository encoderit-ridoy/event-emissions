<?php

namespace App\Http\Controllers;

use App\Models\OnlineMeetingManagement;
use Illuminate\Http\Request;

class OnlineMeetingController extends Controller
{
    public function index(Request $request)
    {
        // $onlineMeetings = OnlineMeetingManagement::latest()->paginate($request->per_page ?? 25);
        $onlineMeetings = OnlineMeetingManagement::all();

        return response()->json([
            'status' => 'Success',
            'online_meeting'   => $onlineMeetings
        ], 200);
    }

    public function store(Request $request)
    {
        $validate_data = [
            'item'      => 'required|string|unique:online_meeting_management,item',
            'parameter' => 'required|string',
            'unit'      => 'required|string',
        ];
        $validator = $request->validate($validate_data);
        $onlineMeeting = OnlineMeetingManagement::create($validator);

        return $this->getSingleData($onlineMeeting->id, 'Online Meeting Created Successfully.', 201);
    }

    public function update(Request $request)
    {
        $validate_data = [
            'id'        => 'required|exists:online_meeting_management,id',
            'item'      => 'required|string|unique:online_meeting_management,item,' . $request->id,
            'parameter' => 'required|string',
            'unit'      => 'required|string',
        ];
        $validator = $request->validate($validate_data);
        $onlineMeeting = OnlineMeetingManagement::findOrFail($request->id);
        $onlineMeeting->update($validator);

        return $this->getSingleData($onlineMeeting->id, 'Online Meeting Updated Successfully.', 200);
    }

    public function getSingleData($id, $message = 'Online Meeting Found.', $code = 200)
    {
        $onlineMeeting = OnlineMeetingManagement::findOrFail($id);

        return response()->json([
            'message' => $message,
            'online_meeting'   => $onlineMeeting
        ], $code);
    }

    public function destroy($id)
    {
        $onlineMeeting = OnlineMeetingManagement::findOrFail($id);
        $onlineMeeting->delete();

        return response()->json([
            'message' => 'Online Meeting Deleted Successfully.',
        ], 200);
    }
}
