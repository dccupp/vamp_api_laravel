<?php

namespace App\Http\Controllers;

use App\Models\FantasyWeek;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FantasyWeeksController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'year'           => 'required|integer',
            'week'           => [
                'required',
                'integer',
                Rule::unique('fantasy_weeks')->where(fn($query) => $query->where('year', $request->year)),
            ],
            'begin_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after:begin_datetime',
        ]);

        $fantasyWeek = FantasyWeek::create([
            'year'           => $request->year,
            'week'           => $request->week,
            'begin_datetime' => $request->begin_datetime,
            'end_datetime'   => $request->end_datetime,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Fantasy week created successfully',
            'id'      => $fantasyWeek->id,
        ], 201);
    }

    public function getFantasyWeeks()
    {
        $weeks = FantasyWeek::all();

        if ($weeks->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No fantasy weeks found'], 404);
        }

        return response()->json($weeks);
    }

    public function getFantasyWeekById($id)
    {
        $week = FantasyWeek::find($id);

        if (!$week) {
            return response()->json(['status' => 'error', 'message' => 'Fantasy week not found'], 404);
        }

        return response()->json($week);
    }

    public function getFantasyWeeksByYear($year)
    {
        $weeks = FantasyWeek::where('year', $year)->get();

        if ($weeks->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No fantasy weeks found for this year'], 404);
        }

        return response()->json($weeks);
    }

    public function getFantasyWeekByYearAndWeek($year, $week)
    {
        $fantasyWeek = FantasyWeek::where('year', $year)->where('week', $week)->first();

        if (!$fantasyWeek) {
            return response()->json(['status' => 'error', 'message' => 'Fantasy week not found for this year and week'], 404);
        }

        return response()->json($fantasyWeek);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'year'           => 'required|integer',
            'week'           => 'required|integer',
            'begin_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after:begin_datetime',
        ]);

        $fantasyWeek = FantasyWeek::find($id);

        if (!$fantasyWeek) {
            return response()->json(['status' => 'error', 'message' => 'Fantasy week not found'], 404);
        }

        $fantasyWeek->update([
            'year'           => $request->year,
            'week'           => $request->week,
            'begin_datetime' => $request->begin_datetime,
            'end_datetime'   => $request->end_datetime,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Fantasy week updated successfully']);
    }

    public function delete($id)
    {
        $fantasyWeek = FantasyWeek::find($id);

        if (!$fantasyWeek) {
            return response()->json(['status' => 'error', 'message' => 'Fantasy week not found'], 404);
        }

        $fantasyWeek->delete();

        return response()->json(['status' => 'success', 'message' => 'Fantasy week deleted successfully']);
    }
}
