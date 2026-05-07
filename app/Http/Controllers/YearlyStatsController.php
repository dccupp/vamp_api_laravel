<?php

namespace App\Http\Controllers;

use App\Models\YearlyStat;
use Illuminate\Http\Request;

class YearlyStatsController extends Controller
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
        ];
        foreach ($this->statFields as $field) {
            $rules[$field] = 'sometimes|integer';
        }
        $request->validate($rules);

        $data = [
            'player_id' => $request->player_id,
            'season'    => $request->season,
        ];
        foreach ($this->statFields as $field) {
            $data[$field] = $request->input($field, 0);
        }

        $stat = YearlyStat::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Yearly stat created successfully',
            'id'      => $stat->id,
        ], 201);
    }

    public function getYearlyStats()
    {
        $stats = YearlyStat::all();

        if ($stats->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No yearly stats found'], 404);
        }

        return response()->json($stats);
    }

    public function getYearlyStatById($id)
    {
        $stat = YearlyStat::find($id);

        if (!$stat) {
            return response()->json(['status' => 'error', 'message' => 'Yearly stat not found'], 404);
        }

        return response()->json($stat);
    }

    public function getYearlyStatsByPlayerId($player_id)
    {
        $stats = YearlyStat::where('player_id', $player_id)->get();

        if ($stats->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No yearly stats found for this player'], 404);
        }

        return response()->json($stats);
    }

    public function getYearlyStatsBySeason($season)
    {
        $stats = YearlyStat::where('season', $season)->get();

        if ($stats->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No yearly stats found for this season'], 404);
        }

        return response()->json($stats);
    }

    public function updateYearlyStat(Request $request, $id)
    {
        $rules = [
            'player_id' => 'required|string|exists:players,id',
            'season'    => 'required|integer',
        ];
        foreach ($this->statFields as $field) {
            $rules[$field] = 'sometimes|integer';
        }
        $request->validate($rules);

        $stat = YearlyStat::find($id);

        if (!$stat) {
            return response()->json(['status' => 'error', 'message' => 'Yearly stat not found'], 404);
        }

        $data = [
            'player_id' => $request->player_id,
            'season'    => $request->season,
        ];
        foreach ($this->statFields as $field) {
            $data[$field] = $request->input($field, 0);
        }

        $stat->update($data);

        return response()->json(['status' => 'success', 'message' => 'Yearly stat updated successfully']);
    }

    public function deleteYearlyStat($id)
    {
        $stat = YearlyStat::find($id);

        if (!$stat) {
            return response()->json(['status' => 'error', 'message' => 'Yearly stat not found'], 404);
        }

        $stat->delete();

        return response()->json(['status' => 'success', 'message' => 'Yearly stat deleted successfully']);
    }
}
