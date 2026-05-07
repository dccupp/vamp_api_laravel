<?php

namespace App\Http\Controllers;

use App\Models\RosteredPlayerWeeklyScore;
use Illuminate\Http\Request;

class RosteredPlayerWeeklyScoresController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'league_id'          => 'required|integer|exists:leagues,league_id',
            'league_member_id'   => 'required|integer|exists:league_members,id',
            'rostered_player_id' => 'required|integer|exists:rostered_players,id',
            'year'               => 'required|integer',
            'week'               => 'required|integer',
            'roster_position'    => 'required|string',
            'fantasy_points'     => 'required|numeric',
        ]);

        $score = RosteredPlayerWeeklyScore::create($request->only([
            'league_id', 'league_member_id', 'rostered_player_id', 'year', 'week', 'roster_position', 'fantasy_points',
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'Weekly score added',
            'id'      => $score->id,
        ], 201);
    }

    public function getById($id)
    {
        $score = RosteredPlayerWeeklyScore::find($id);

        if (!$score) {
            return response()->json(['status' => 'error', 'message' => 'Score not found'], 404);
        }

        return response()->json($score);
    }

    public function getByLeagueAndWeek(Request $request, $league_id, $week)
    {
        $year = $request->input('year', 2025);

        $scores = RosteredPlayerWeeklyScore::where('league_id', $league_id)
            ->where('week', $week)
            ->where('year', $year)
            ->orderBy('league_member_id')
            ->orderBy('roster_position')
            ->get();

        return response()->json($scores);
    }

    public function getByMemberAndWeek(Request $request, $league_member_id, $week)
    {
        $year = $request->input('year', 2025);

        $scores = RosteredPlayerWeeklyScore::where('league_member_id', $league_member_id)
            ->where('week', $week)
            ->where('year', $year)
            ->orderBy('roster_position')
            ->get();

        return response()->json($scores);
    }

    public function getByLeagueMemberId(Request $request, $league_member_id)
    {
        $query = RosteredPlayerWeeklyScore::where('league_member_id', $league_member_id);

        if ($request->has('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->has('week')) {
            $query->where('week', $request->input('week'));
        }

        $scores = $query->orderByDesc('year')->orderByDesc('week')->orderBy('roster_position')->get();

        return response()->json($scores);
    }

    public function getByLeagueId(Request $request, $league_id)
    {
        $query = RosteredPlayerWeeklyScore::where('league_id', $league_id);

        if ($request->has('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->has('week')) {
            $query->where('week', $request->input('week'));
        }

        $scores = $query->orderBy('league_member_id')->orderByDesc('year')->orderByDesc('week')->orderBy('roster_position')->get();

        return response()->json($scores);
    }

    public function getByRosteredPlayerId(Request $request, $rostered_player_id)
    {
        $query = RosteredPlayerWeeklyScore::where('rostered_player_id', $rostered_player_id);

        if ($request->has('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->has('week')) {
            $query->where('week', $request->input('week'));
        }

        $scores = $query->orderByDesc('year')->orderByDesc('week')->orderBy('league_member_id')->get();

        return response()->json($scores);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'league_id'          => 'required|integer|exists:leagues,league_id',
            'league_member_id'   => 'required|integer|exists:league_members,id',
            'rostered_player_id' => 'required|integer|exists:rostered_players,id',
            'year'               => 'required|integer',
            'week'               => 'required|integer',
            'roster_position'    => 'required|string',
            'fantasy_points'     => 'required|numeric',
        ]);

        $score = RosteredPlayerWeeklyScore::find($id);

        if (!$score) {
            return response()->json(['status' => 'error', 'message' => 'Score not found'], 404);
        }

        $score->update($request->only([
            'league_id', 'league_member_id', 'rostered_player_id', 'year', 'week', 'roster_position', 'fantasy_points',
        ]));

        return response()->json(['status' => 'success', 'message' => 'Weekly score updated']);
    }

    public function delete($id)
    {
        $score = RosteredPlayerWeeklyScore::find($id);

        if (!$score) {
            return response()->json(['status' => 'error', 'message' => 'Score not found'], 404);
        }

        $score->delete();

        return response()->json(['status' => 'success', 'message' => 'Weekly score removed']);
    }
}
