<?php

namespace App\Http\Controllers;

use App\Models\ScoringRule;
use Illuminate\Http\Request;

class ScoringRulesController extends Controller
{
    private array $scoringFields = [
        'passing_yards', 'passing_touchdowns', 'interceptions_thrown', 'two_point_pass',
        'passing_300_399', 'passing_400_plus', 'rushing_yards', 'rushing_touchdowns',
        'two_point_rush', 'rushing_100_199', 'rushing_200_plus', 'receiving_yards',
        'receptions', 'receiving_touchdowns', 'two_point_reception', 'receiving_100_199',
        'receiving_200_plus', 'kickoff_return_touchdown', 'punt_return_touchdown',
        'fumble_recovered_touchdown', 'fumbles_lost', 'interception_return_touchdown',
        'fumble_return_touchdown', 'blocked_return_touchdown', 'two_point_return',
        'one_point_safety',
    ];

    public function create(Request $request)
    {
        $rules = ['league_id' => 'required|integer|exists:leagues,league_id'];
        foreach ($this->scoringFields as $field) {
            $rules[$field] = 'sometimes|numeric';
        }
        $request->validate($rules);

        $data = ['league_id' => $request->league_id];
        foreach ($this->scoringFields as $field) {
            $data[$field] = $request->input($field, 0);
        }

        ScoringRule::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Scoring rules created successfully',
        ], 201);
    }

    public function getScoringRulesByLeagueId($league_id)
    {
        $scoringRules = ScoringRule::find($league_id);

        if (!$scoringRules) {
            return response()->json(['status' => 'error', 'message' => 'Scoring rules not found'], 404);
        }

        return response()->json($scoringRules);
    }

    public function updateScoringRules(Request $request, $league_id)
    {
        $rules = [];
        foreach ($this->scoringFields as $field) {
            $rules[$field] = 'sometimes|numeric';
        }
        $request->validate($rules);

        $scoringRules = ScoringRule::find($league_id);

        if (!$scoringRules) {
            return response()->json(['status' => 'error', 'message' => 'Scoring rules not found'], 404);
        }

        $data = [];
        foreach ($this->scoringFields as $field) {
            $data[$field] = $request->input($field, 0);
        }

        $scoringRules->update($data);

        return response()->json(['status' => 'success', 'message' => 'Scoring rules updated successfully']);
    }

    public function deleteScoringRules($league_id)
    {
        $scoringRules = ScoringRule::find($league_id);

        if (!$scoringRules) {
            return response()->json(['status' => 'error', 'message' => 'Scoring rules not found'], 404);
        }

        $scoringRules->delete();

        return response()->json(['status' => 'success', 'message' => 'Scoring rules deleted successfully']);
    }
}
