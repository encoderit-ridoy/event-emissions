<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function totalEmissions()
    {
        $user = Auth::user();
        // return response()->json([
        //     'check' => $user
        // ]);
        $query = Event::with('user', 'meetingRooms', 'transportations', 'onlineMeetings', 'otherActivities');
        if ($user->hasRole('user')) {
            // $query->where('user_id', $user->company->id);
            $query->whereHas('user', function ($q) use ($user) {
                return $q->where('company_id', $user->company->id);
            });
        }
        $events = $query->get();

        $mr_emissions = array();
        $tp_emissions = array();
        $om_emissions = array();
        $oa_emissions = array();
        $total_mr_emissions = 0;
        $total_tp_emissions = 0;
        $total_om_emissions = 0;
        $total_oa_emissions = 0;
        $event_wise_emissions = array();
        $mr_data2 = 0;
        $tp_data2 = 0;
        $om_data2 = 0;
        $oa_data2 = 0;
        foreach ($events as $event) {
            foreach ($event->meetingRooms as $meetingRoom) {
                $calculation = $meetingRoom->electicity_parameter * $meetingRoom->pivot->meeting_time;
                $mr_emissions[] = $calculation;
                $total_mr_emissions += $calculation;
            }

            foreach ($event->transportations as $transport) {
                $calculation = $transport->parameter * $transport->pivot->no_of_people * $transport->pivot->transportation_fee;
                $tp_emissions[] = $calculation;
                $total_tp_emissions += $calculation;
            }
            foreach ($event->onlineMeetings as $onlineMeeting) {
                $calculation = $onlineMeeting->parameter * $onlineMeeting->pivot->no_of_pc * $onlineMeeting->pivot->times;
                $om_emissions[] = $calculation;
                $total_om_emissions += $calculation;
            }
            foreach ($event->otherActivities as $activities) {
                if ($activities->pivot->no_of_nights)
                    $calculation = $activities->parameter * $activities->pivot->no_of_nights * $activities->pivot->no_of_people;
                else
                    $calculation = $activities->parameter * $activities->pivot->no_of_people;

                $oa_emissions[] = $calculation;
                $total_oa_emissions += $calculation;
            }

            $mr_data = $total_mr_emissions;
            $mr_data1 = $mr_data - $mr_data2;
            $mr_data2 = $mr_data;

            $tp_data = $total_tp_emissions;
            $tp_data1 = $tp_data - $tp_data2;
            $tp_data2 = $tp_data;

            $om_data = $total_om_emissions;
            $om_data1 = $om_data - $om_data2;
            $om_data2 = $om_data;

            $oa_data = $total_oa_emissions;
            $oa_data1 = $oa_data - $oa_data2;
            $oa_data2 = $oa_data;

            $event_wise_emissions[] = array(
                'event_id' => $event->id,
                'event_name' => $event->name,
                'meetingRoom_emissions' => $mr_data1,
                'transport_emissions' => $tp_data1,
                'onlineMeeting_emissions' => $om_data1,
                'otherActivities_emissions' => $oa_data1,
                'total' => $mr_data1 + $tp_data1 + $om_data1 + $oa_data1
            );
        }

        $total_events = count($events);
        $total_emissions = $total_mr_emissions + $total_tp_emissions + $total_om_emissions + $total_oa_emissions;
        if ($total_events == 0)
            $average_emissions = 0;
        else
            $average_emissions = $total_emissions / $total_events;

        if ($total_emissions == 0) {
            $meetingRoom_percentage     = 0;
            $transportation_percentage  = 0;
            $onlineMeeting_percentage   = 0;
            $otherActivities_percentage = 0;
        } else {
            $meetingRoom_percentage     = ($total_mr_emissions / $total_emissions) * 100;
            $transportation_percentage  = ($total_tp_emissions / $total_emissions) * 100;
            $onlineMeeting_percentage   = ($total_om_emissions / $total_emissions) * 100;
            $otherActivities_percentage = ($total_oa_emissions / $total_emissions) * 100;
        }


        return response()->json([
            'total_event' => $total_events,
            'total_emissions' => $total_emissions,
            'average_emissions' => $average_emissions,
            'meetingRoom_percentage' => $meetingRoom_percentage,
            'transportation_percentage' => $transportation_percentage,
            'onlineMeeting_percentage' => $onlineMeeting_percentage,
            'otherActivities_percentage' => $otherActivities_percentage,
            'event_wise_emissions' => $event_wise_emissions,
        ]);
    }

    public function eventsData(Request $request)
    {
        $request->validate([
            'ids' => 'sometimes|required|array',
        ]);

        $user = Auth::user();

        $query = Event::with('user', 'meetingRooms', 'transportations', 'onlineMeetings', 'otherActivities');
        if ($user->hasRole('user')) {
            // $query->where('user_id', $user->company->id);
            $query->whereHas('user', function ($q) use ($user) {
                return $q->where('company_id', $user->company->id);
            });
        }
        if ($request->has('ids')) {
            $query->whereIn('id', $request->ids);
        } else {
            $query->take(4)->latest();
        }

        $events = $query->get();

        $mr_emissions = array();
        $tp_emissions = array();
        $om_emissions = array();
        $oa_emissions = array();
        $total_mr_emissions = 0;
        $total_tp_emissions = 0;
        $total_om_emissions = 0;
        $total_oa_emissions = 0;
        $event_wise_emissions = array();
        $mr_data2 = 0;
        $tp_data2 = 0;
        $om_data2 = 0;
        $oa_data2 = 0;
        foreach ($events as $event) {
            foreach ($event->meetingRooms as $meetingRoom) {
                $calculation = $meetingRoom->electicity_parameter * $meetingRoom->pivot->meeting_time;
                $mr_emissions[] = $calculation;
                $total_mr_emissions += $calculation;
            }

            foreach ($event->transportations as $transport) {
                $calculation = $transport->parameter * $transport->pivot->no_of_people * $transport->pivot->transportation_fee;
                $tp_emissions[] = $calculation;
                $total_tp_emissions += $calculation;
            }
            foreach ($event->onlineMeetings as $onlineMeeting) {
                $calculation = $onlineMeeting->parameter * $onlineMeeting->pivot->no_of_pc * $onlineMeeting->pivot->times;
                $om_emissions[] = $calculation;
                $total_om_emissions += $calculation;
            }
            foreach ($event->otherActivities as $activities) {
                if ($activities->pivot->no_of_nights)
                    $calculation = $activities->parameter * $activities->pivot->no_of_nights * $activities->pivot->no_of_people;
                else
                    $calculation = $activities->parameter * $activities->pivot->no_of_people;

                $oa_emissions[] = $calculation;
                $total_oa_emissions += $calculation;
            }

            $mr_data = $total_mr_emissions;
            $mr_data1 = $mr_data - $mr_data2;
            $mr_data2 = $mr_data;

            $tp_data = $total_tp_emissions;
            $tp_data1 = $tp_data - $tp_data2;
            $tp_data2 = $tp_data;

            $om_data = $total_om_emissions;
            $om_data1 = $om_data - $om_data2;
            $om_data2 = $om_data;

            $oa_data = $total_oa_emissions;
            $oa_data1 = $oa_data - $oa_data2;
            $oa_data2 = $oa_data;

            $event_wise_emissions[] = array(
                'event_id' => $event->id,
                'event_name' => $event->name,
                'meetingRoom_emissions' => $mr_data1,
                'transport_emissions' => $tp_data1,
                'onlineMeeting_emissions' => $om_data1,
                'otherActivities_emissions' => $oa_data1,
            );
        }
        return response()->json([
            'event_wise_emissions' => $event_wise_emissions,
        ]);
    }
}
