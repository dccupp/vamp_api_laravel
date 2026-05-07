<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\LeagueMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchedulesController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'league_id'  => 'required|integer|exists:leagues,league_id',
            'year'       => 'sometimes|integer',
            'member_ids' => 'sometimes|array',
            'member_ids.*' => 'integer|exists:league_members,id',
        ]);

        $league_id = $request->league_id;
        $year      = $request->input('year', date('Y'));
        $memberIds = $request->input('member_ids', []);

        if (empty($memberIds)) {
            $memberIds = LeagueMember::where('league_id', $league_id)->pluck('id')->toArray();
        }

        if (count($memberIds) !== 10) {
            return response()->json(['status' => 'error', 'message' => 'Schedule generation requires exactly 10 teams'], 400);
        }

        $numWeeks = 9;
        $schedule = [];
        $teams    = $memberIds;
        $half     = count($teams) / 2;

        for ($week = 1; $week <= $numWeeks; $week++) {
            $matches = [];
            for ($i = 0; $i < $half; $i++) {
                $home = $teams[$i];
                $away = $teams[count($teams) - 1 - $i];
                if ($week % 2 === 0 && $i % 2 === 0) {
                    [$home, $away] = [$away, $home];
                }
                $matches[] = [
                    'home_league_member' => $home,
                    'away_league_member' => $away,
                    'week'               => $week,
                    'home_score'         => null,
                    'away_score'         => null,
                    'winner'             => null,
                ];
            }
            $fixed  = array_shift($teams);
            $teams[] = array_shift($teams);
            $teams  = array_merge([$fixed], $teams);
            $schedule = array_merge($schedule, $matches);
        }

        DB::transaction(function () use ($schedule, $league_id, $year) {
            foreach ($schedule as $match) {
                Schedule::create([
                    'league_id'          => $league_id,
                    'home_league_member' => $match['home_league_member'],
                    'away_league_member' => $match['away_league_member'],
                    'week'               => $match['week'],
                    'year'               => $year,
                    'home_score'         => $match['home_score'],
                    'away_score'         => $match['away_score'],
                    'winner'             => $match['winner'],
                ]);
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Schedules created successfully'], 201);
    }

    public function getSchedulesByLeagueId($league_id)
    {
        $schedules = Schedule::where('league_id', $league_id)->get();

        if ($schedules->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No schedules found for this league'], 404);
        }

        return response()->json($schedules);
    }

    public function updateSchedule(Request $request, $id)
    {
        $request->validate([
            'home_score' => 'sometimes|nullable|numeric',
            'away_score' => 'sometimes|nullable|numeric',
            'winner'     => 'sometimes|nullable|integer|exists:league_members,id',
        ]);

        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'Schedule not found'], 404);
        }

        $schedule->update($request->only(['home_score', 'away_score', 'winner']));

        return response()->json(['status' => 'success', 'message' => 'Schedule updated successfully']);
    }
}
