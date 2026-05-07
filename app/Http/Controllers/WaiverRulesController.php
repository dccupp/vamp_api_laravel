<?php

namespace App\Http\Controllers;

use App\Models\WaiverRule;
use Illuminate\Http\Request;

class WaiverRulesController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'league_id'      => 'required|integer|exists:leagues,league_id',
            'waivers_length' => 'sometimes|nullable|integer',
            'waiver_day'     => 'sometimes|nullable|string',
        ]);

        $rule = WaiverRule::create([
            'league_id'      => $request->league_id,
            'waivers_length' => $request->input('waivers_length'),
            'waiver_day'     => $request->input('waiver_day'),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Waiver rule created successfully',
            'id'      => $rule->id,
        ], 201);
    }

    public function getWaiverRules()
    {
        $rules = WaiverRule::all();

        if ($rules->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No waiver rules found'], 404);
        }

        return response()->json($rules);
    }

    public function getWaiverRuleById($id)
    {
        $rule = WaiverRule::find($id);

        if (!$rule) {
            return response()->json(['status' => 'error', 'message' => 'Waiver rule not found'], 404);
        }

        return response()->json($rule);
    }

    public function getWaiverRulesByLeagueId($league_id)
    {
        $rules = WaiverRule::where('league_id', $league_id)->get();

        if ($rules->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No waiver rules found for this league'], 404);
        }

        return response()->json($rules);
    }

    public function updateWaiverRule(Request $request, $id)
    {
        $request->validate([
            'league_id'      => 'required|integer|exists:leagues,league_id',
            'waivers_length' => 'sometimes|nullable|integer',
            'waiver_day'     => 'sometimes|nullable|string',
        ]);

        $rule = WaiverRule::find($id);

        if (!$rule) {
            return response()->json(['status' => 'error', 'message' => 'Waiver rule not found'], 404);
        }

        $rule->update([
            'league_id'      => $request->league_id,
            'waivers_length' => $request->input('waivers_length'),
            'waiver_day'     => $request->input('waiver_day'),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Waiver rule updated successfully']);
    }

    public function deleteWaiverRule($id)
    {
        $rule = WaiverRule::find($id);

        if (!$rule) {
            return response()->json(['status' => 'error', 'message' => 'Waiver rule not found'], 404);
        }

        $rule->delete();

        return response()->json(['status' => 'success', 'message' => 'Waiver rule deleted successfully']);
    }
}
