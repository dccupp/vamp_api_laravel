<?php

namespace App\Http\Controllers;

use App\Models\RosterRule;
use Illuminate\Http\Request;

class RosterRulesController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'league_id'                      => 'required|integer|exists:leagues,league_id',
            'roster_type_id'                 => 'required|integer|exists:roster_types,roster_type_id',
            'quarterback_count'              => 'sometimes|integer',
            'running_back_count'             => 'sometimes|integer',
            'wide_receiver_count'            => 'sometimes|integer',
            'tight_end_count'                => 'sometimes|integer',
            'wide_receiver_tight_end_count'  => 'sometimes|integer',
            'flex_count'                     => 'sometimes|integer',
            'bench_count'                    => 'sometimes|integer',
            'ir_count'                       => 'sometimes|integer',
            'max_roster_size'                => 'sometimes|integer',
            'max_qb_count'                   => 'sometimes|integer',
            'max_rb_count'                   => 'sometimes|integer',
            'max_wr_count'                   => 'sometimes|integer',
            'max_te_count'                   => 'sometimes|integer',
            'beginning_faab'                 => 'sometimes|integer',
        ]);

        RosterRule::create([
            'league_id'                     => $request->league_id,
            'roster_type_id'                => $request->roster_type_id,
            'quarterback_count'             => $request->input('quarterback_count', 1),
            'running_back_count'            => $request->input('running_back_count', 2),
            'wide_receiver_count'           => $request->input('wide_receiver_count', 2),
            'tight_end_count'               => $request->input('tight_end_count', 1),
            'wide_receiver_tight_end_count' => $request->input('wide_receiver_tight_end_count', 0),
            'flex_count'                    => $request->input('flex_count', 1),
            'bench_count'                   => $request->input('bench_count', 6),
            'ir_count'                      => $request->input('ir_count', 2),
            'max_roster_size'               => $request->input('max_roster_size', 13),
            'max_qb_count'                  => $request->input('max_qb_count', 4),
            'max_rb_count'                  => $request->input('max_rb_count', 8),
            'max_wr_count'                  => $request->input('max_wr_count', 8),
            'max_te_count'                  => $request->input('max_te_count', 4),
            'beginning_faab'                => $request->input('beginning_faab', 100),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Roster rules created successfully',
        ], 201);
    }

    public function getRosterRulesByLeagueId($league_id, $roster_type_id)
    {
        $rosterRules = RosterRule::where('league_id', $league_id)
            ->where('roster_type_id', $roster_type_id)
            ->first();

        if (!$rosterRules) {
            return response()->json(['status' => 'error', 'message' => 'Roster rules not found'], 404);
        }

        return response()->json($rosterRules);
    }

    public function updateRosterRules(Request $request, $league_id, $roster_type_id)
    {
        $request->validate([
            'quarterback_count'              => 'sometimes|integer',
            'running_back_count'             => 'sometimes|integer',
            'wide_receiver_count'            => 'sometimes|integer',
            'tight_end_count'                => 'sometimes|integer',
            'wide_receiver_tight_end_count'  => 'sometimes|integer',
            'flex_count'                     => 'sometimes|integer',
            'bench_count'                    => 'sometimes|integer',
            'ir_count'                       => 'sometimes|integer',
            'max_roster_size'                => 'sometimes|integer',
            'max_qb_count'                   => 'sometimes|integer',
            'max_rb_count'                   => 'sometimes|integer',
            'max_wr_count'                   => 'sometimes|integer',
            'max_te_count'                   => 'sometimes|integer',
            'beginning_faab'                 => 'sometimes|integer',
        ]);

        $rosterRules = RosterRule::where('league_id', $league_id)
            ->where('roster_type_id', $roster_type_id)
            ->first();

        if (!$rosterRules) {
            return response()->json(['status' => 'error', 'message' => 'Roster rules not found'], 404);
        }

        $rosterRules->update($request->only([
            'quarterback_count', 'running_back_count', 'wide_receiver_count',
            'tight_end_count', 'wide_receiver_tight_end_count', 'flex_count',
            'bench_count', 'ir_count', 'max_roster_size', 'max_qb_count',
            'max_rb_count', 'max_wr_count', 'max_te_count', 'beginning_faab',
        ]));

        return response()->json(['status' => 'success', 'message' => 'Roster rules updated successfully']);
    }

    public function deleteRosterRules($league_id, $roster_type_id)
    {
        $rosterRules = RosterRule::where('league_id', $league_id)
            ->where('roster_type_id', $roster_type_id)
            ->first();

        if (!$rosterRules) {
            return response()->json(['status' => 'error', 'message' => 'Roster rules not found'], 404);
        }

        $rosterRules->delete();

        return response()->json(['status' => 'success', 'message' => 'Roster rules deleted successfully']);
    }
}
