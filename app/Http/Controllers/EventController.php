<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('user', 'meetingRooms', 'transportations', 'onlineMeetings', 'otherActivities');

        $user = Auth::user();
        if ($user->hasRole('user')) {
            $query->whereHas('user', function ($q) use ($user) {
                return $q->where('company_id', $user->company->id);
            });
        } else {
            $query->when($request->has('user_id'), function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }
        $events = $query->latest()->paginate($request->per_page ?? 25);

        return response()->json([
            'status' => 'Success',
            'events'   => $events
        ], 200);
    }

    public function store(Request $request)
    {
        $validate_data = [
            'user_id'          => 'required|exists:users,id',
            'name'             => 'required|string',
            'event_holder'     => 'required|string',
            'starting_date'    => 'required',
            'end_date'         => 'required',
            'people_in_charge' => 'required|string',
            'meeting_rooms'    => 'sometimes|required|array',
            'trasportations'   => 'sometimes|required|array',
            'online_meetings'  => 'sometimes|required|array',
            'other_activities' => 'sometimes|required|array',
        ];
        $validator = $request->validate($validate_data);
        $event = Event::create($validator);
        if ($event) {
            if ($request->has('meeting_rooms')) {
                foreach ($request->meeting_rooms as $room) {
                    $event->meetingRooms()->attach($room['id'], ['meeting_time' => $room['times'], 'mr_assumption' => $room['mr_assumption']]);
                }
            }
            if ($request->has('trasportations')) {
                foreach ($request->trasportations as $transport) {
                    $event->transportations()->attach($transport['id'], ['no_of_people' => $transport['people'], 'transportation_fee' => $transport['fee'], 'tp_assumption' => $transport['tp_assumption']]);
                }
            }
            if ($request->has('online_meetings')) {
                foreach ($request->online_meetings as $online) {
                    $event->onlineMeetings()->attach($online['id'], ['no_of_pc' => $online['pc'], 'times' => $online['times'], 'om_assumption' => $online['om_assumption']]);
                }
            }
            if ($request->has('other_activities')) {
                foreach ($request->other_activities as $activities) {
                    $event->otherActivities()->attach($activities['id'], ['no_of_people' => $activities['people'], 'no_of_nights' => $activities['night'] ?? null, 'oa_assumption' => $activities['oa_assumption']]);
                }
            }
        }
        return $this->getSingleData($event->id, 'Event Created Successfully.', 201);
    }

    public function update(Request $request)
    {
        $validate_data = [
            'event_id'         => 'required|exists:events,id',
            'user_id'          => 'required|exists:users,id',
            'name'             => 'required|string',
            'event_holder'     => 'required|string',
            'starting_date'    => 'required',
            'end_date'         => 'required',
            'people_in_charge' => 'required|string',
            'meeting_rooms'    => 'sometimes|required|array',
            'trasportations'   => 'sometimes|required|array',
            'online_meetings'  => 'sometimes|required|array',
            'other_activities' => 'sometimes|required|array',
        ];
        $validator = $request->validate($validate_data);
        $event = Event::findOrFail($request->event_id);
        $event->update($validator);

        if ($event) {
            if ($request->has('meeting_rooms')) {
                $event->meetingRooms()->detach();
                foreach ($request->meeting_rooms as $room) {
                    $event->meetingRooms()->attach($room['id'], ['meeting_time' => $room['times'], 'mr_assumption' => $room['mr_assumption']]);
                }
            } else {
                $event->meetingRooms()->detach();
            }
            if ($request->has('trasportations')) {
                $event->transportations()->detach();
                foreach ($request->trasportations as $transport) {
                    $event->transportations()->attach($transport['id'], ['no_of_people' => $transport['people'], 'transportation_fee' => $transport['fee'], 'tp_assumption' => $transport['tp_assumption']]);
                }
            } else {
                $event->transportations()->detach();
            }
            if ($request->has('online_meetings')) {
                $event->onlineMeetings()->detach();
                foreach ($request->online_meetings as $online) {
                    $event->onlineMeetings()->attach($online['id'], ['no_of_pc' => $online['pc'], 'times' => $online['times'], 'om_assumption' => $online['om_assumption']]);
                }
            } else {
                $event->onlineMeetings()->detach();
            }
            if ($request->has('other_activities')) {
                $event->otherActivities()->detach();
                foreach ($request->other_activities as $activities) {
                    $event->otherActivities()->attach($activities['id'], ['no_of_people' => $activities['people'], 'no_of_nights' => $activities['night'] ?? null, 'oa_assumption' => $activities['oa_assumption']]);
                }
            } else {
                $event->otherActivities()->detach();
            }
        }
        return $this->getSingleData($event->id, 'Event Updated Successfully.', 200);
    }

    public function getSingleData($id, $message = ' Event Found.', $code = 200)
    {
        $event = Event::with('user', 'meetingRooms', 'transportations', 'onlineMeetings', 'otherActivities')->findOrFail($id);

        return $this->calculations($event);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'message' => 'Event Deleted Successfully.',
        ], 200);
    }

    public function report(Request $request)
    {
        $validate_data = [
            'id' => 'required|exists:events,id'
        ];
        $request->validate($validate_data);
        $event = Event::with('user', 'meetingRooms', 'transportations', 'onlineMeetings', 'otherActivities')->findOrFail($request->id);

        return $this->calculations($event);
    }

    public function calculations($event)
    {
        $mr_emissions = array();
        $tp_emissions = array();
        $om_emissions = array();
        $oa_emissions = array();
        $total_mr_emissions = 0;
        $total_tp_emissions = 0;
        $total_om_emissions = 0;
        $total_oa_emissions = 0;
        $event_wise_emissions = array();

        foreach ($event->meetingRooms as $meetingRoom) {
            $calculation = $meetingRoom->electicity_parameter * $meetingRoom->pivot->meeting_time;
            $mr_emissions[] = array(
                'id' => $meetingRoom->id,
                'pivot' => $meetingRoom->pivot,
                'result' => $calculation
            );
            $total_mr_emissions += $calculation;
        }
        foreach ($event->transportations as $transport) {
            $calculation = $transport->parameter * $transport->pivot->no_of_people * $transport->pivot->transportation_fee;
            $tp_emissions[] = array(
                'id' => $transport->id,
                'pivot' => $transport->pivot,
                'result' => $calculation
            );
            $total_tp_emissions += $calculation;
        }
        foreach ($event->onlineMeetings as $onlineMeeting) {
            $calculation = $onlineMeeting->parameter * $onlineMeeting->pivot->no_of_pc * $onlineMeeting->pivot->times;
            $om_emissions[] = array(
                'id' => $onlineMeeting->id,
                'pivot' => $onlineMeeting->pivot,
                'result' => $calculation
            );
            $total_om_emissions += $calculation;
        }
        foreach ($event->otherActivities as $activities) {
            if ($activities->pivot->no_of_nights)
                $calculation = $activities->parameter * $activities->pivot->no_of_nights * $activities->pivot->no_of_people;
            else
                $calculation = $activities->parameter * $activities->pivot->no_of_people;

            $oa_emissions[] = array(
                'id' => $activities->id,
                'pivot' => $activities->pivot,
                'result' => $calculation
            );
            $total_oa_emissions += $calculation;
        }

        $event_wise_emissions[] = array(
            'meetingRoom_emissions' => $total_mr_emissions,
            'transport_emissions' => $total_tp_emissions,
            'onlineMeeting_emissions' => $total_om_emissions,
            'otherActivities_emissions' => $total_oa_emissions,
            'total' => $total_mr_emissions + $total_tp_emissions + $total_om_emissions + $total_oa_emissions
        );


        return response()->json([
            'meetingRoom_results' => $mr_emissions,
            'transportation_results' => $tp_emissions,
            'onlinemeeting_results' => $om_emissions,
            'otherActivities_results' => $oa_emissions,
            'event_wise_total_emissions' => $event_wise_emissions,
            'event' => $event
        ]);
    }
}
