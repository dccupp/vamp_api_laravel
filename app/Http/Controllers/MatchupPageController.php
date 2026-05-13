<?php

namespace App\Http\Controllers;

use App\Models\LeagueMember;
use App\Models\NFLSchedule;
use App\Models\RosteredPlayer;
use App\Models\RosteredPlayerWeeklyScore;
use App\Models\Schedule;
use App\Models\ScoringRule;
use App\Models\WeeklyStat;

class MatchupPageController extends Controller
{
    public function getMatchupPageData($league_id, $schedule_id)
    {
        $week = (int) request('week', 0);
        $year = (int) request('year', (int) date('Y'));
        $historical = filter_var(request('historical', false), FILTER_VALIDATE_BOOLEAN);

        $schedule = Schedule::find($schedule_id);
        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'Matchup not found'], 404);
        }

        $homeMember = LeagueMember::find($schedule->home_league_member);
        $awayMember = LeagueMember::find($schedule->away_league_member);

        if (!$homeMember || !$awayMember) {
            return response()->json(['status' => 'error', 'message' => 'League members not found'], 404);
        }

        if ($historical) {
            return response()->json([
                'home_roster'    => $this->buildHistoricalRoster($league_id, $schedule->home_league_member, $week, $year),
                'away_roster'    => $this->buildHistoricalRoster($league_id, $schedule->away_league_member, $week, $year),
                'home_team_name' => $homeMember->team_name ?? 'Unknown',
                'away_team_name' => $awayMember->team_name ?? 'Unknown',
            ]);
        }

        $scoringRules = ScoringRule::find($league_id);

        $homeRosteredPlayers = RosteredPlayer::with('player')
            ->where('league_member_id', $schedule->home_league_member)
            ->where('is_rostered', 1)
            ->get()
            ->filter(fn($rp) => $rp->player)
            ->values();

        $awayRosteredPlayers = RosteredPlayer::with('player')
            ->where('league_member_id', $schedule->away_league_member)
            ->where('is_rostered', 1)
            ->get()
            ->filter(fn($rp) => $rp->player)
            ->values();

        $allRosteredPlayers = $homeRosteredPlayers->concat($awayRosteredPlayers);

        // One query for all weekly stats across both rosters
        $allPlayerIds = $allRosteredPlayers->pluck('player_id')->unique()->values();
        $weeklyStats = $week > 0
            ? WeeklyStat::whereIn('player_id', $allPlayerIds)
                ->where('season', $year)
                ->where('week', $week)
                ->get()
                ->keyBy('player_id')
            : collect();

        // One query for all NFL game schedules across both rosters
        $allTeams = $allRosteredPlayers->map(fn($rp) => $rp->player->team)->unique()->filter()->values();
        $nflGames = $week > 0
            ? NFLSchedule::where('year', $year)
                ->where('week', $week)
                ->whereIn('team', $allTeams)
                ->get()
                ->keyBy('team')
            : collect();

        $buildRoster = function ($rosteredPlayers) use ($weeklyStats, $nflGames, $scoringRules) {
            return $rosteredPlayers->map(function ($rp) use ($weeklyStats, $nflGames, $scoringRules) {
                $stat = $weeklyStats->get($rp->player_id);
                $game = $nflGames->get($rp->player->team);

                $score = 0;
                if ($stat && $scoringRules) {
                    $passYds = $stat->passing_yards ?? 0;
                    $rushYds = $stat->rushing_yards ?? 0;
                    $recYds  = $stat->receiving_yards ?? 0;

                    $score += $passYds * ($scoringRules->passing_yards ?? 0);
                    $score += ($stat->passing_tds ?? 0) * ($scoringRules->passing_touchdowns ?? 0);
                    $score += ($stat->interceptions ?? 0) * ($scoringRules->interceptions_thrown ?? 0);
                    $score += ($stat->two_point_passes ?? 0) * ($scoringRules->two_point_pass ?? 0);
                    if ($passYds >= 400) $score += $scoringRules->passing_400_plus ?? 0;
                    elseif ($passYds >= 300) $score += $scoringRules->passing_300_399 ?? 0;

                    $score += $rushYds * ($scoringRules->rushing_yards ?? 0);
                    $score += ($stat->rushing_tds ?? 0) * ($scoringRules->rushing_touchdowns ?? 0);
                    $score += ($stat->two_point_rushes ?? 0) * ($scoringRules->two_point_rush ?? 0);
                    if ($rushYds >= 200) $score += $scoringRules->rushing_200_plus ?? 0;
                    elseif ($rushYds >= 100) $score += $scoringRules->rushing_100_199 ?? 0;

                    $score += ($stat->receptions ?? 0) * ($scoringRules->receptions ?? 0);
                    $score += $recYds * ($scoringRules->receiving_yards ?? 0);
                    $score += ($stat->receiving_tds ?? 0) * ($scoringRules->receiving_touchdowns ?? 0);
                    $score += ($stat->two_point_receptions ?? 0) * ($scoringRules->two_point_reception ?? 0);
                    if ($recYds >= 200) $score += $scoringRules->receiving_200_plus ?? 0;
                    elseif ($recYds >= 100) $score += $scoringRules->receiving_100_199 ?? 0;

                    }

                return [
                    'id'               => $rp->id,
                    'league_member_id' => $rp->league_member_id,
                    'player_id'        => $rp->player_id,
                    'is_rostered'      => (bool) $rp->is_rostered,
                    'roster_position'  => $rp->roster_position ? strtoupper($rp->roster_position) : 'BENCH',
                    'player_name'      => $rp->player->player_name ?? 'Unknown',
                    'position'         => $rp->player->position ?? 'Unknown',
                    'team'             => $rp->player->team ?? 'Unknown',
                    'external_player_id' => $rp->player->external_player_id,
                    'is_injured'       => false,
                    'fantasyScore'     => round($score, 2),
                    'weeklyStats'      => $stat ? [
                        'passing_yards'        => $stat->passing_yards ?? 0,
                        'passing_tds'          => $stat->passing_tds ?? 0,
                        'interceptions'        => $stat->interceptions ?? 0,
                        'two_point_passes'     => $stat->two_point_passes ?? 0,
                        'rushing_yards'        => $stat->rushing_yards ?? 0,
                        'rushing_tds'          => $stat->rushing_tds ?? 0,
                        'two_point_rushes'     => $stat->two_point_rushes ?? 0,
                        'receptions'           => $stat->receptions ?? 0,
                        'receiving_yards'      => $stat->receiving_yards ?? 0,
                        'receiving_tds'        => $stat->receiving_tds ?? 0,
                        'two_point_receptions' => $stat->two_point_receptions ?? 0,
                        'special_teams_tds'    => $stat->special_teams_tds ?? 0,
                    ] : null,
                    'schedule' => $game ? [
                        'date'     => $game->date,
                        'day'      => $game->day,
                        'est_time' => $game->est_time ?? 'TBD',
                        'location' => $game->location ?? 'Unknown',
                    ] : null,
                ];
            })->values();
        };

        return response()->json([
            'home_roster'    => $buildRoster($homeRosteredPlayers),
            'away_roster'    => $buildRoster($awayRosteredPlayers),
            'home_team_name' => $homeMember->team_name ?? 'Unknown',
            'away_team_name' => $awayMember->team_name ?? 'Unknown',
        ]);
    }

    private function buildHistoricalRoster($league_id, $league_member_id, $week, $year)
    {
        $scores = RosteredPlayerWeeklyScore::where('league_id', $league_id)
            ->where('league_member_id', $league_member_id)
            ->where('week', $week)
            ->where('year', $year)
            ->get();

        $rosteredPlayers = RosteredPlayer::with('player')
            ->whereIn('id', $scores->pluck('rostered_player_id'))
            ->get()
            ->keyBy('id');

        return $scores->map(function ($score) use ($rosteredPlayers) {
            $rp = $rosteredPlayers->get($score->rostered_player_id);
            if (!$rp || !$rp->player) return null;

            return [
                'id'                 => $rp->id,
                'league_member_id'   => $rp->league_member_id,
                'player_id'          => $rp->player_id,
                'is_rostered'        => true,
                'roster_position'    => strtoupper($score->roster_position),
                'player_name'        => $rp->player->player_name ?? 'Unknown',
                'position'           => $rp->player->position ?? 'Unknown',
                'team'               => $rp->player->team ?? 'Unknown',
                'external_player_id' => $rp->player->external_player_id,
                'is_injured'         => false,
                'fantasyScore'       => (float) $score->fantasy_points,
                'weeklyStats'        => null,
                'schedule'           => null,
            ];
        })->filter()->values();
    }
}