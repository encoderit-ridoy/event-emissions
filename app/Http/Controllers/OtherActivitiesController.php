<?php

namespace App\Http\Controllers;

use App\Models\OtherActivitiesManagement;
use Illuminate\Http\Request;

class OtherActivitiesController extends Controller
{
    public function index(Request $request)
    {
        $otherActivities = OtherActivitiesManagement::latest()->paginate($request->per_page ?? 25);

        return response()->json([
            'status' => 'Success',
            'otherActivities'   => $otherActivities
        ], 200);
    }

    public function store(Request $request)
    {
        $validate_data = [
            'meeting_room' => 'required|string|unique:other_activities_management,meeting_room',
            'parameter'    => 'required|string',
            'unit'         => 'required|string',
        ];
        $validator = $request->validate($validate_data);
        $otherActivities = OtherActivitiesManagement::create($validator);

        return $this->getSingleData($otherActivities->id, 'Other Activities Data Created Successfully.', 201);
    }

    public function update(Request $request)
    {
        $validate_data = [
            'id'           => 'required|exists:other_activities_management,id',
            'meeting_room' => 'required|string|unique:other_activities_management,meeting_room,' . $request->id,
            'parameter'    => 'required|string',
            'unit'         => 'required|string',
        ];
        $validator = $request->validate($validate_data);
        $otherActivities = OtherActivitiesManagement::findOrFail($request->id);
        $otherActivities->update($validator);

        return $this->getSingleData($otherActivities->id, 'Other Activities Data Updated Successfully.', 200);
    }

    public function getSingleData($id, $message = 'Other Activities Data Found.', $code = 200)
    {
        $otherActivities = OtherActivitiesManagement::findOrFail($id);

        return response()->json([
            'message' => $message,
            'otherActivities'   => $otherActivities
        ], $code);
    }

    public function destroy($id)
    {
        $otherActivities = OtherActivitiesManagement::findOrFail($id);
        $otherActivities->delete();

        return response()->json([
            'message' => 'Other Activities Data Deleted Successfully.',
        ], 200);
    }
}
