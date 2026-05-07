<?php

namespace App\Http\Controllers;

use App\Models\LeagueMember;
use Illuminate\Http\Request;

class LeagueMembersController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'league_id'             => 'required|integer|exists:leagues,league_id',
            'user_id'               => 'required|integer|exists:users,id',
            'role'                  => 'required|string|in:commish,player',
            'team_name'             => 'sometimes|string',
            'remaining_faab_budget' => 'sometimes|integer',
            'is_vamp'               => 'sometimes|boolean',
        ]);

        // Enforce max 10 members per league
        if (LeagueMember::where('league_id', $request->league_id)->count() >= 10) {
            return response()->json(['status' => 'error', 'message' => 'League has reached the maximum of 10 members'], 400);
        }

        // Enforce unique user per league
        if (LeagueMember::where('league_id', $request->league_id)->where('user_id', $request->user_id)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'User is already a member of this league'], 400);
        }

        // Enforce only one vampire per league
        if ($request->input('is_vamp', 0) && LeagueMember::where('league_id', $request->league_id)->where('is_vamp', 1)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'A vampire already exists in this league'], 400);
        }

        $member = LeagueMember::create([
            'league_id'             => $request->league_id,
            'user_id'               => $request->user_id,
            'role'                  => $request->input('role', 'player'),
            'team_name'             => $request->team_name,
            'remaining_faab_budget' => $request->input('remaining_faab_budget', 1000),
            'is_vamp'               => $request->input('is_vamp', 0),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'League member created successfully',
            'id'      => $member->id,
        ], 201);
    }

    public function getLeagueMembersByUserId($user_id)
    {
        $members = LeagueMember::with('league')
            ->where('user_id', $user_id)
            ->get()
            ->map(fn($m) => [
                'id'                    => $m->id,
                'league_id'             => $m->league_id,
                'user_id'               => $m->user_id,
                'role'                  => $m->role,
                'team_name'             => $m->team_name,
                'remaining_faab_budget' => $m->remaining_faab_budget,
                'is_vamp'               => $m->is_vamp,
                'name'                  => $m->league?->name,
                'is_active'             => $m->league?->is_active,
                'created_at'            => $m->league?->created_at,
                'updated_at'            => $m->league?->updated_at,
            ]);

        if ($members->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No league members found'], 404);
        }

        return response()->json($members);
    }

    public function getLeagueMembersByLeagueId($league_id)
    {
        $members = LeagueMember::with('user')
            ->where('league_id', $league_id)
            ->get()
            ->map(fn($m) => [
                'id'                    => $m->id,
                'league_id'             => $m->league_id,
                'user_id'               => $m->user_id,
                'role'                  => $m->role,
                'team_name'             => $m->team_name,
                'remaining_faab_budget' => $m->remaining_faab_budget,
                'is_vamp'               => $m->is_vamp,
                'username'              => $m->user ? $m->user->username : null,
                'email_address'         => $m->user ? $m->user->email_address : null,
                'first_name'            => $m->user ? $m->user->first_name : null,
                'last_name'             => $m->user ? $m->user->last_name : null,
            ]);

        if ($members->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No league members found'], 404);
        }

        return response()->json($members);
    }

    public function getLeagueMemberByLeagueAndUserId($league_id, $user_id)
    {
        $member = LeagueMember::where('league_id', $league_id)->where('user_id', $user_id)->first();

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'League member not found'], 404);
        }

        return response()->json($member);
    }

    public function updateRole(Request $request, $league_id, $user_id)
    {
        $request->validate([
            'role' => 'required|string|in:commish,player',
        ]);

        $member = LeagueMember::where('league_id', $league_id)->where('user_id', $user_id)->first();

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'League member not found'], 404);
        }

        $member->update(['role' => $request->role]);

        return response()->json(['status' => 'success', 'message' => 'Role updated successfully']);
    }

    public function updateTeamName(Request $request, $league_id, $user_id)
    {
        $request->validate([
            'team_name' => 'required|string',
        ]);

        $member = LeagueMember::where('league_id', $league_id)->where('user_id', $user_id)->first();

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'League member not found'], 404);
        }

        $member->update(['team_name' => $request->team_name]);

        return response()->json(['status' => 'success', 'message' => 'Team name updated successfully']);
    }

    public function updateRemainingFaabBudget(Request $request, $league_id, $user_id)
    {
        $request->validate([
            'remaining_faab_budget' => 'required|integer',
        ]);

        $member = LeagueMember::where('league_id', $league_id)->where('user_id', $user_id)->first();

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'League member not found'], 404);
        }

        $member->update(['remaining_faab_budget' => $request->remaining_faab_budget]);

        return response()->json(['status' => 'success', 'message' => 'Remaining FAAB budget updated successfully']);
    }

    public function deleteLeagueMember($league_id, $user_id)
    {
        $member = LeagueMember::where('league_id', $league_id)->where('user_id', $user_id)->first();

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'League member not found'], 404);
        }

        $member->delete();

        return response()->json(['status' => 'success', 'message' => 'League member deleted successfully']);
    }
}
