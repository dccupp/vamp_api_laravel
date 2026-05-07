<?php

namespace App\Http\Controllers;

use App\Models\WaiverClaim;
use Illuminate\Http\Request;

class WaiverClaimsController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'league_id'               => 'required|integer|exists:leagues,league_id',
            'league_member_id'        => 'required|integer|exists:league_members,id',
            'player_id'               => 'required|string|exists:players,id',
            'faab_claim_amount'       => 'required|integer|min:0',
            'rostered_player_to_drop' => 'sometimes|nullable|integer|exists:rostered_players,id',
            'league_member_priority'  => 'sometimes|nullable|integer',
            'is_active'               => 'sometimes|boolean',
            'week'                    => 'sometimes|nullable|integer',
            'season'                  => 'sometimes|nullable|integer',
        ]);

        $claim = WaiverClaim::create([
            'league_id'               => $request->league_id,
            'league_member_id'        => $request->league_member_id,
            'player_id'               => $request->player_id,
            'faab_claim_amount'       => $request->faab_claim_amount,
            'rostered_player_to_drop' => $request->input('rostered_player_to_drop'),
            'league_member_priority'  => $request->input('league_member_priority'),
            'is_active'               => $request->input('is_active', 1),
            'week'                    => $request->input('week'),
            'season'                  => $request->input('season'),
            'last_modified'           => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Waiver claim created successfully',
            'id'      => $claim->id,
        ], 201);
    }

    public function getWaiverClaims()
    {
        $claims = WaiverClaim::all();

        if ($claims->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No waiver claims found'], 404);
        }

        return response()->json($claims);
    }

    public function getWaiverClaimById($id)
    {
        $claim = WaiverClaim::find($id);

        if (!$claim) {
            return response()->json(['status' => 'error', 'message' => 'Waiver claim not found'], 404);
        }

        return response()->json($claim);
    }

    public function getWaiverClaimsByLeagueMemberId($league_member_id)
    {
        $claims = WaiverClaim::where('league_member_id', $league_member_id)
            ->where('is_active', 1)
            ->get();

        return response()->json($claims);
    }

    public function getWaiverClaimsByLeagueId($league_id)
    {
        $claims = WaiverClaim::where('league_id', $league_id)
            ->where('is_active', 1)
            ->get();

        return response()->json($claims);
    }

    public function getWaiverClaimsByPlayerIdAndLeagueId($player_id, $league_id)
    {
        $claims = WaiverClaim::where('player_id', $player_id)
            ->where('league_id', $league_id)
            ->get();

        return response()->json($claims);
    }

    public function updateWaiverClaim(Request $request, $id)
    {
        $request->validate([
            'league_id'               => 'required|integer|exists:leagues,league_id',
            'league_member_id'        => 'required|integer|exists:league_members,id',
            'player_id'               => 'required|string|exists:players,id',
            'faab_claim_amount'       => 'required|integer|min:0',
            'rostered_player_to_drop' => 'sometimes|nullable|integer|exists:rostered_players,id',
            'league_member_priority'  => 'sometimes|nullable|integer',
            'is_active'               => 'sometimes|nullable|boolean',
            'week'                    => 'sometimes|nullable|integer',
            'season'                  => 'sometimes|nullable|integer',
        ]);

        $claim = WaiverClaim::find($id);

        if (!$claim) {
            return response()->json(['status' => 'error', 'message' => 'Waiver claim not found'], 404);
        }

        $claim->update([
            'league_id'               => $request->league_id,
            'league_member_id'        => $request->league_member_id,
            'player_id'               => $request->player_id,
            'faab_claim_amount'       => $request->faab_claim_amount,
            'rostered_player_to_drop' => $request->input('rostered_player_to_drop'),
            'league_member_priority'  => $request->input('league_member_priority'),
            'is_active'               => $request->input('is_active'),
            'week'                    => $request->input('week'),
            'season'                  => $request->input('season'),
            'last_modified'           => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Waiver claim updated successfully']);
    }

    public function deleteWaiverClaim($id)
    {
        $claim = WaiverClaim::find($id);

        if (!$claim) {
            return response()->json(['status' => 'error', 'message' => 'Waiver claim not found'], 404);
        }

        $claim->delete();

        return response()->json(['status' => 'success', 'message' => 'Waiver claim deleted successfully']);
    }
}
