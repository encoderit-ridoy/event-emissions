<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoomManagement;
use Illuminate\Http\Request;

class MeetingRoomController extends Controller
{
    public function index(Request $request)
    {
        $meetingRooms = MeetingRoomManagement::latest()->paginate($request->per_page ?? 25);

        return response()->json([
            'status' => 'Success',
            'meetingRooms'   => $meetingRooms
        ], 200);
    }

    public function store(Request $request)
    {
        $validate_data = [
            'meeting_room'           => 'required|string|unique:meeting_room_management,meeting_room',
            'electicity_parameter'   => 'required|string',
            'other_energy_parameter' => 'sometimes|required|string',
            'unit'                   => 'required|string',
        ];
        $validator = $request->validate($validate_data);
        $meetingRoom = MeetingRoomManagement::create($validator);

        return $this->getSingleData($meetingRoom->id, 'Meeting Room Created Successfully.', 201);
    }

    public function update(Request $request)
    {
        $validate_data = [
            'id'                     => 'required|exists:meeting_room_management,id',
            'meeting_room'           => 'required|string|unique:meeting_room_management,meeting_room,' . $request->id,
            'electicity_parameter'   => 'required|string',
            'other_energy_parameter' => 'sometimes|required|string',
            'unit'                   => 'required|string',
        ];
        $validator = $request->validate($validate_data);
        $meetingRoom = MeetingRoomManagement::findOrFail($request->id);
        $meetingRoom->update($validator);

        return $this->getSingleData($meetingRoom->id, 'Meeting Room Updated Successfully.', 200);
    }

    public function getSingleData($id, $message = 'Meeting Room Found.', $code = 200)
    {
        $meetingRoom = MeetingRoomManagement::findOrFail($id);

        return response()->json([
            'message' => $message,
            'meetingRoom'   => $meetingRoom
        ], $code);
    }

    public function destroy($id)
    {
        $meetingRoom = MeetingRoomManagement::findOrFail($id);
        $meetingRoom->delete();

        return response()->json([
            'message' => 'Meeting Room Deleted Successfully.',
        ], 200);
    }
}
