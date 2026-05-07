<?php

namespace App\Http\Controllers;

use App\Models\WeeklyStat;
use Illuminate\Http\Request;

class WeeklyStatsController extends Controller
{
    private array $statFields = [
        'passing_yards', 'passing_tds', 'interceptions', 'two_point_passes',
        'rushing_yards', 'rushing_tds', 'two_point_rushes',
        'receptions', 'receiving_yards', 'receiving_tds', 'two_point_receptions',
        'special_teams_tds',
    ];

    public function create(Request $request)
    {
        $rules = [
            'player_id' => 'required|string|exists:players,id',
            'season'    => 'required|integer',
            'week'      => 'required|integer',
        ];
        foreach ($this->statFields as $field) {
            $rules[$field] = 'sometimes|integer';
        }
        $request->validate($rules);

        $data = [
            'player_id' => $request->player_id,
            'season'    => $request->season,
            'week'      => $request->week,
        ];
        foreach ($this->statFields as $field) {
            $data[$field] = $request->input($field, 0);
        }

        $stat = WeeklyStat::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Weekly stats created successfully',
            'id'      => $stat->id,
        ], 201);
    }

    public function getWeeklyStatById($id)
    {
        $stat = WeeklyStat::find($id);

        if (!$stat) {
            return response()->json(['status' => 'error', 'message' => 'Weekly stat not found'], 404);
        }

        return response()->json($stat);
    }

    public function getWeeklyStatsByPlayerId($player_id)
    {
        $stats = WeeklyStat::where('player_id', $player_id)->get();

        return response()->json($stats);
    }

    public function getWeeklyStatsBySeasonAndWeek($season, $week)
    {
        $stats = WeeklyStat::where('season', $season)->where('week', $week)->get();

        return response()->json($stats);
    }

    public function getWeeklyStatsByPlayerIdsSeasonAndWeek(Request $request)
    {
        $request->validate([
            'player_ids'   => 'required|array',
            'player_ids.*' => 'string',
            'season'       => 'required|integer',
            'week'         => 'required|integer',
        ]);

        $stats = WeeklyStat::whereIn('player_id', $request->player_ids)
            ->where('season', $request->season)
            ->where('week', $request->week)
            ->get();

        return response()->json($stats);
    }

    public function updateWeeklyStat(Request $request, $id)
    {
        $rules = [
            'player_id' => 'required|string|exists:players,id',
            'season'    => 'required|integer',
            'week'      => 'required|integer',
        ];
        foreach ($this->statFields as $field) {
            $rules[$field] = 'sometimes|integer';
        }
        $request->validate($rules);

        $stat = WeeklyStat::find($id);

        if (!$stat) {
            return response()->json(['status' => 'error', 'message' => 'Weekly stat not found'], 404);
        }

        $data = [
            'player_id' => $request->player_id,
            'season'    => $request->season,
            'week'      => $request->week,
        ];
        foreach ($this->statFields as $field) {
            $data[$field] = $request->input($field, 0);
        }

        $stat->update($data);

        return response()->json(['status' => 'success', 'message' => 'Weekly stats updated successfully']);
    }

    public function deleteWeeklyStat($id)
    {
        $stat = WeeklyStat::find($id);

        if (!$stat) {
            return response()->json(['status' => 'error', 'message' => 'Weekly stat not found'], 404);
        }

        $stat->delete();

        return response()->json(['status' => 'success', 'message' => 'Weekly stats deleted successfully']);
    }
}
