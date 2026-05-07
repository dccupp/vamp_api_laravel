<?php

namespace App\Http\Controllers;

use App\Models\NFLSchedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NFLScheduleController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'year'       => 'required|integer',
            'team'       => 'required|string',
            'week'       => [
                'required',
                'integer',
                Rule::unique('nfl_schedule')->where(fn($query) => $query->where('year', $request->year)->where('team', $request->team)),
            ],
            'day'        => 'sometimes|string',
            'date'       => 'sometimes|string',
            'local_time' => 'sometimes|string',
            'est_time'   => 'sometimes|string',
            'location'   => 'sometimes|string',
        ]);

        $schedule = NFLSchedule::create($request->only([
            'year', 'team', 'week', 'day', 'date', 'local_time', 'est_time', 'location',
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'NFL schedule created successfully',
            'id'      => $schedule->id,
        ], 201);
    }

    public function getNFLSchedules()
    {
        $schedules = NFLSchedule::all();

        if ($schedules->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No NFL schedules found'], 404);
        }

        return response()->json($schedules);
    }

    public function getNFLScheduleById($id)
    {
        $schedule = NFLSchedule::find($id);

        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'NFL schedule not found'], 404);
        }

        return response()->json($schedule);
    }

    public function getNFLSchedulesByYearAndWeek($year, $week)
    {
        $schedules = NFLSchedule::where('year', $year)->where('week', $week)->get();

        if ($schedules->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No NFL schedules found for this year and week'], 404);
        }

        return response()->json($schedules);
    }

    public function getNFLScheduleByYearWeekAndTeam($year, $week, $team)
    {
        $schedule = NFLSchedule::where('year', $year)->where('week', $week)->where('team', $team)->first();

        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'No NFL schedule found for this year, week, and team'], 404);
        }

        return response()->json($schedule);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'year'       => 'required|integer',
            'team'       => 'required|string',
            'week'       => 'required|integer',
            'day'        => 'sometimes|string',
            'date'       => 'sometimes|string',
            'local_time' => 'sometimes|string',
            'est_time'   => 'sometimes|string',
            'location'   => 'sometimes|string',
        ]);

        $schedule = NFLSchedule::find($id);

        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'NFL schedule not found'], 404);
        }

        $schedule->update($request->only([
            'year', 'team', 'week', 'day', 'date', 'local_time', 'est_time', 'location',
        ]));

        return response()->json(['status' => 'success', 'message' => 'NFL schedule updated successfully']);
    }

    public function delete($id)
    {
        $schedule = NFLSchedule::find($id);

        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'NFL schedule not found'], 404);
        }

        $schedule->delete();

        return response()->json(['status' => 'success', 'message' => 'NFL schedule deleted successfully']);
    }
}
