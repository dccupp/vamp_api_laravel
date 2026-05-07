<?php

namespace App\Http\Controllers;

use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeagueController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name'      => 'required|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $league = League::create([
            'name'      => $request->name,
            'is_active' => $request->input('is_active', 0),
        ]);

        return response()->json([
            'status'    => 'success',
            'message'   => 'League created successfully',
            'league_id' => $league->league_id,
        ], 201);
    }

    public function getLeagues()
    {
        $leagues = League::all(['league_id', 'name', 'is_active', 'created_at', 'updated_at'])
            ->sortByDesc('league_id')
            ->values();

        return response()->json($leagues);
    }

    public function getLeagueById($id)
    {
        $league = League::find($id);

        if (!$league) {
            return response()->json(['status' => 'error', 'message' => 'League not found'], 404);
        }

        return response()->json($league);
    }

    public function updateLeague(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $league = League::find($id);

        if (!$league) {
            return response()->json(['status' => 'error', 'message' => 'League not found'], 404);
        }

        $league->update([
            'name'      => $request->name,
            'is_active' => $request->input('is_active', $league->is_active),
        ]);

        return response()->json(['status' => 'success', 'message' => 'League updated successfully']);
    }

    public function deleteLeague($id)
    {
        $league = League::find($id);

        if (!$league) {
            return response()->json(['status' => 'error', 'message' => 'League not found'], 404);
        }

        $league->delete();

        return response()->json(['status' => 'success', 'message' => 'League deleted successfully']);
    }

    public function activateLeague($league_id, $league_member_id)
    {
        if (!is_numeric($league_id) || (int)$league_id <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Invalid league_id'], 400);
        }
        if (!is_numeric($league_member_id) || (int)$league_member_id <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Invalid league_member_id'], 400);
        }

        $league_id        = (int)$league_id;
        $league_member_id = (int)$league_member_id;

        try {
            return DB::transaction(function () use ($league_id, $league_member_id) {
                $league = League::find($league_id);
                if (!$league) {
                    return response()->json(['status' => 'error', 'message' => 'League not found'], 404);
                }

                if ($league->is_active) {
                    return response()->json(['status' => 'error', 'message' => 'League is already activated'], 400);
                }

                // Verify commissioner role
                $member = DB::table('league_members')
                    ->where('id', $league_member_id)
                    ->where('league_id', $league_id)
                    ->first();

                if (!$member || $member->role !== 'commish') {
                    return response()->json(['status' => 'error', 'message' => 'Only the league commissioner can activate the league'], 403);
                }

                // Verify exactly 10 members
                $members = DB::table('league_members')
                    ->where('league_id', $league_id)
                    ->whereIn('role', ['player', 'commish'])
                    ->get();

                if ($members->count() !== 10) {
                    return response()->json(['status' => 'error', 'message' => 'League must have exactly 10 members with player or commish roles'], 400);
                }

                $commishCount = $members->where('role', 'commish')->count();
                if ($commishCount > 1) {
                    return response()->json(['status' => 'error', 'message' => 'League must have exactly one commissioner'], 400);
                }

                // Generate schedule
                $memberIds     = $members->pluck('id')->toArray();
                $scheduleResult = $this->generateSchedule($league_id, $memberIds);

                if ($scheduleResult['status'] !== 'success') {
                    return response()->json(['status' => 'error', 'message' => $scheduleResult['message']], 400);
                }

                // Insert schedule records
                $scheduleRows = array_map(fn($match) => array_merge($match, [
                    'league_id'  => $league_id,
                    'year'       => date('Y'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]), $scheduleResult['schedule']);

                DB::table('schedules')->insert($scheduleRows);

                // Activate league
                $league->update(['is_active' => 1]);

                return response()->json(['status' => 'success', 'message' => 'League activated successfully']);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to activate league: ' . $e->getMessage()], 400);
        }
    }

    private function generateSchedule($league_id, array $memberIds)
    {
        $teams    = $memberIds;
        $numTeams = count($teams);
        $numWeeks = 9;
        $schedule = [];

        if ($numTeams !== 10) {
            return ['status' => 'error', 'message' => 'Schedule generation requires exactly 10 teams'];
        }

        $half = $numTeams / 2;
        for ($week = 1; $week <= $numWeeks; $week++) {
            $matches = [];
            for ($i = 0; $i < $half; $i++) {
                $home = $teams[$i];
                $away = $teams[$numTeams - 1 - $i];
                if ($week % 2 === 0 && $i % 2 === 0) {
                    [$home, $away] = [$away, $home];
                }
                $matches[] = ['home_league_member' => $home, 'away_league_member' => $away, 'week' => $week];
            }
            $fixed  = array_shift($teams);
            $teams[] = array_shift($teams);
            $teams  = array_merge([$fixed], $teams);
            $schedule = array_merge($schedule, $matches);
        }

        return ['status' => 'success', 'schedule' => $schedule];
    }
}
