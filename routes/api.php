<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User routes
Route::post('/users/register', [App\Http\Controllers\UserController::class, 'register']);
Route::post('/users/login', [App\Http\Controllers\UserController::class, 'login']);
Route::get('/users/getUsers', [App\Http\Controllers\UserController::class, 'getUsers']);
Route::get('/users/getUserById/{id}', [App\Http\Controllers\UserController::class, 'getUserById']);
Route::get('/users/getUserByUsername/{username}', [App\Http\Controllers\UserController::class, 'getUserByUsername']);
Route::put('/users/update/{id}', [App\Http\Controllers\UserController::class, 'updateUser']);
Route::delete('/users/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteUser']);

// League Divisions routes
Route::post('/league_divisions/create', [App\Http\Controllers\LeagueDivisionsController::class, 'create']);
Route::get('/league_divisions/getDivisionsByLeagueId/{id}', [App\Http\Controllers\LeagueDivisionsController::class, 'getDivisionsByLeagueId']);
Route::get('/league_divisions/getDivisionById/{id}', [App\Http\Controllers\LeagueDivisionsController::class, 'getDivisionById']);
Route::put('/league_divisions/update/{id}', [App\Http\Controllers\LeagueDivisionsController::class, 'updateDivision']);
Route::delete('/league_divisions/delete/{id}', [App\Http\Controllers\LeagueDivisionsController::class, 'deleteDivision']);

// League Members routes
Route::post('/league_members/create', [App\Http\Controllers\LeagueMembersController::class, 'create']);
Route::get('/league_members/getLeagueMembersByUserId/{user_id}', [App\Http\Controllers\LeagueMembersController::class, 'getLeagueMembersByUserId']);
Route::get('/league_members/getLeagueMembersByLeagueId/{league_id}', [App\Http\Controllers\LeagueMembersController::class, 'getLeagueMembersByLeagueId']);
Route::get('/league_members/getLeagueMemberByLeagueAndUserId/{league_id}/{user_id}', [App\Http\Controllers\LeagueMembersController::class, 'getLeagueMemberByLeagueAndUserId']);
Route::put('/league_members/updateRole/{league_id}/{user_id}', [App\Http\Controllers\LeagueMembersController::class, 'updateRole']);
Route::put('/league_members/updateTeamName/{league_id}/{user_id}', [App\Http\Controllers\LeagueMembersController::class, 'updateTeamName']);
Route::put('/league_members/updateRemainingFaabBudget/{league_id}/{user_id}', [App\Http\Controllers\LeagueMembersController::class, 'updateRemainingFaabBudget']);
Route::delete('/league_members/delete/{league_id}/{user_id}', [App\Http\Controllers\LeagueMembersController::class, 'deleteLeagueMember']);

// NFL Schedule routes
Route::post('/nfl_schedules/create', [App\Http\Controllers\NFLScheduleController::class, 'create']);
Route::get('/nfl_schedules/getNFLSchedules', [App\Http\Controllers\NFLScheduleController::class, 'getNFLSchedules']);
Route::get('/nfl_schedules/getNFLScheduleById/{id}', [App\Http\Controllers\NFLScheduleController::class, 'getNFLScheduleById']);
Route::get('/nfl_schedules/getNFLSchedulesByYearAndWeek/{year}/{week}', [App\Http\Controllers\NFLScheduleController::class, 'getNFLSchedulesByYearAndWeek']);
Route::get('/nfl_schedules/getNFLScheduleByYearWeekAndTeam/{year}/{week}/{team}', [App\Http\Controllers\NFLScheduleController::class, 'getNFLScheduleByYearWeekAndTeam']);
Route::put('/nfl_schedules/update/{id}', [App\Http\Controllers\NFLScheduleController::class, 'update']);
Route::delete('/nfl_schedules/delete/{id}', [App\Http\Controllers\NFLScheduleController::class, 'delete']);

// Draft Picks routes
Route::post('/draft_picks/create', [App\Http\Controllers\DraftPicksController::class, 'create']);
Route::get('/draft_picks/getDraftPicks', [App\Http\Controllers\DraftPicksController::class, 'getDraftPicks']);
Route::get('/draft_picks/getDraftPickById/{id}', [App\Http\Controllers\DraftPicksController::class, 'getDraftPickById']);
Route::put('/draft_picks/update/{id}', [App\Http\Controllers\DraftPicksController::class, 'updateDraftPick']);
Route::delete('/draft_picks/delete/{id}', [App\Http\Controllers\DraftPicksController::class, 'deleteDraftPick']);

// Fantasy Weeks routes
Route::post('/fantasy_weeks/create', [App\Http\Controllers\FantasyWeeksController::class, 'create']);
Route::get('/fantasy_weeks/getFantasyWeeks', [App\Http\Controllers\FantasyWeeksController::class, 'getFantasyWeeks']);
Route::get('/fantasy_weeks/getFantasyWeekById/{id}', [App\Http\Controllers\FantasyWeeksController::class, 'getFantasyWeekById']);
Route::get('/fantasy_weeks/getFantasyWeeksByYear/{year}', [App\Http\Controllers\FantasyWeeksController::class, 'getFantasyWeeksByYear']);
Route::get('/fantasy_weeks/getFantasyWeekByYearAndWeek/{year}/{week}', [App\Http\Controllers\FantasyWeeksController::class, 'getFantasyWeekByYearAndWeek']);
Route::put('/fantasy_weeks/update/{id}', [App\Http\Controllers\FantasyWeeksController::class, 'update']);
Route::delete('/fantasy_weeks/delete/{id}', [App\Http\Controllers\FantasyWeeksController::class, 'delete']);

// League routes
Route::post('/leagues/create', [App\Http\Controllers\LeagueController::class, 'create']);
Route::get('/leagues/getLeagues', [App\Http\Controllers\LeagueController::class, 'getLeagues']);
Route::get('/leagues/getLeagueById/{id}', [App\Http\Controllers\LeagueController::class, 'getLeagueById']);
Route::put('/leagues/update/{id}', [App\Http\Controllers\LeagueController::class, 'updateLeague']);
Route::delete('/leagues/delete/{id}', [App\Http\Controllers\LeagueController::class, 'deleteLeague']);
Route::post('/leagues/activate/{league_id}/{league_member_id}', [App\Http\Controllers\LeagueController::class, 'activateLeague']);

// Players routes
Route::post('/players/create', [App\Http\Controllers\PlayersController::class, 'create']);
Route::get('/players/getPlayers', [App\Http\Controllers\PlayersController::class, 'getPlayers']);
Route::get('/players/getPlayerById/{id}', [App\Http\Controllers\PlayersController::class, 'getPlayerById']);
Route::get('/players/getPlayerByExternalPlayerId/{external_player_id}', [App\Http\Controllers\PlayersController::class, 'getPlayerByExternalPlayerId']);
Route::put('/players/update/{id}', [App\Http\Controllers\PlayersController::class, 'updatePlayer']);
Route::delete('/players/delete/{id}', [App\Http\Controllers\PlayersController::class, 'deletePlayer']);

// Rostered Players routes
Route::post('/rostered_players/create', [App\Http\Controllers\RosteredPlayersController::class, 'create']);
Route::get('/rostered_players/getRosteredPlayerById/{id}', [App\Http\Controllers\RosteredPlayersController::class, 'getRosteredPlayerById']);
Route::get('/rostered_players/getRosteredPlayersByLeagueId/{league_id}', [App\Http\Controllers\RosteredPlayersController::class, 'getRosteredPlayersByLeagueId']);
Route::get('/rostered_players/getRosteredPlayersByLeagueMemberId/{league_member_id}', [App\Http\Controllers\RosteredPlayersController::class, 'getRosteredPlayersByLeagueMemberId']);
Route::get('/rostered_players/getRosteredPlayerByLeagueAndPlayerId/{league_id}/{player_id}', [App\Http\Controllers\RosteredPlayersController::class, 'getRosteredPlayerByLeagueAndPlayerId']);
Route::put('/rostered_players/update/{id}', [App\Http\Controllers\RosteredPlayersController::class, 'updateRosteredPlayer']);
Route::delete('/rostered_players/delete/{id}', [App\Http\Controllers\RosteredPlayersController::class, 'deleteRosteredPlayer']);

// Rostered Player Weekly Scores routes
Route::post('/rostered_player_weekly_scores/create', [App\Http\Controllers\RosteredPlayerWeeklyScoresController::class, 'create']);
Route::get('/rostered_player_weekly_scores/getById/{id}', [App\Http\Controllers\RosteredPlayerWeeklyScoresController::class, 'getById']);
Route::get('/rostered_player_weekly_scores/getByLeagueAndWeek/{league_id}/{week}', [App\Http\Controllers\RosteredPlayerWeeklyScoresController::class, 'getByLeagueAndWeek']);
Route::get('/rostered_player_weekly_scores/getByMemberAndWeek/{league_member_id}/{week}', [App\Http\Controllers\RosteredPlayerWeeklyScoresController::class, 'getByMemberAndWeek']);
Route::get('/rostered_player_weekly_scores/getByLeagueMemberId/{league_member_id}', [App\Http\Controllers\RosteredPlayerWeeklyScoresController::class, 'getByLeagueMemberId']);
Route::get('/rostered_player_weekly_scores/getByLeagueId/{league_id}', [App\Http\Controllers\RosteredPlayerWeeklyScoresController::class, 'getByLeagueId']);
Route::get('/rostered_player_weekly_scores/getByRosteredPlayerId/{rostered_player_id}', [App\Http\Controllers\RosteredPlayerWeeklyScoresController::class, 'getByRosteredPlayerId']);
Route::put('/rostered_player_weekly_scores/update/{id}', [App\Http\Controllers\RosteredPlayerWeeklyScoresController::class, 'update']);
Route::delete('/rostered_player_weekly_scores/delete/{id}', [App\Http\Controllers\RosteredPlayerWeeklyScoresController::class, 'delete']);

// Roster Rules routes
Route::post('/roster_rules/create', [App\Http\Controllers\RosterRulesController::class, 'create']);
Route::get('/roster_rules/getRosterRulesByLeagueId/{league_id}/{roster_type_id}', [App\Http\Controllers\RosterRulesController::class, 'getRosterRulesByLeagueId']);
Route::put('/roster_rules/update/{league_id}/{roster_type_id}', [App\Http\Controllers\RosterRulesController::class, 'updateRosterRules']);
Route::delete('/roster_rules/delete/{league_id}/{roster_type_id}', [App\Http\Controllers\RosterRulesController::class, 'deleteRosterRules']);

// Roster Types routes
Route::get('/roster_types/getRosterTypes', [App\Http\Controllers\RosterTypesController::class, 'getRosterTypes']);
Route::get('/roster_types/getRosterTypeById/{roster_type_id}', [App\Http\Controllers\RosterTypesController::class, 'getRosterTypeById']);

// Schedules routes
Route::post('/schedules/create', [App\Http\Controllers\SchedulesController::class, 'create']);
Route::get('/schedules/getSchedulesByLeagueId/{league_id}', [App\Http\Controllers\SchedulesController::class, 'getSchedulesByLeagueId']);
Route::put('/schedules/update/{id}', [App\Http\Controllers\SchedulesController::class, 'updateSchedule']);

// Scoring Rules routes
Route::post('/scoring_rules/create', [App\Http\Controllers\ScoringRulesController::class, 'create']);
Route::get('/scoring_rules/getScoringRulesByLeagueId/{league_id}', [App\Http\Controllers\ScoringRulesController::class, 'getScoringRulesByLeagueId']);
Route::put('/scoring_rules/update/{league_id}', [App\Http\Controllers\ScoringRulesController::class, 'updateScoringRules']);
Route::delete('/scoring_rules/delete/{league_id}', [App\Http\Controllers\ScoringRulesController::class, 'deleteScoringRules']);

// Matchup Page routes
Route::get('/matchups/getMatchupPageData/{league_id}/{schedule_id}', [App\Http\Controllers\MatchupPageController::class, 'getMatchupPageData']);

// Waiver Page routes
Route::get('/waivers/getWaiverPageData/{league_id}/{user_id}', [App\Http\Controllers\WaiverPageController::class, 'getWaiverPageData']);

// Waiver Claims routes
Route::post('/waiver_claims/create', [App\Http\Controllers\WaiverClaimsController::class, 'create']);
Route::get('/waiver_claims/getWaiverClaims', [App\Http\Controllers\WaiverClaimsController::class, 'getWaiverClaims']);
Route::get('/waiver_claims/getWaiverClaimById/{id}', [App\Http\Controllers\WaiverClaimsController::class, 'getWaiverClaimById']);
Route::get('/waiver_claims/getWaiverClaimsByLeagueMemberId/{league_member_id}', [App\Http\Controllers\WaiverClaimsController::class, 'getWaiverClaimsByLeagueMemberId']);
Route::get('/waiver_claims/getWaiverClaimsByLeagueId/{league_id}', [App\Http\Controllers\WaiverClaimsController::class, 'getWaiverClaimsByLeagueId']);
Route::get('/waiver_claims/getWaiverClaimsByPlayerIdAndLeagueId/{player_id}/{league_id}', [App\Http\Controllers\WaiverClaimsController::class, 'getWaiverClaimsByPlayerIdAndLeagueId']);
Route::put('/waiver_claims/update/{id}', [App\Http\Controllers\WaiverClaimsController::class, 'updateWaiverClaim']);
Route::delete('/waiver_claims/delete/{id}', [App\Http\Controllers\WaiverClaimsController::class, 'deleteWaiverClaim']);

// Waiver Rules routes
Route::post('/waiver_rules/create', [App\Http\Controllers\WaiverRulesController::class, 'create']);
Route::get('/waiver_rules/getWaiverRules', [App\Http\Controllers\WaiverRulesController::class, 'getWaiverRules']);
Route::get('/waiver_rules/getWaiverRuleById/{id}', [App\Http\Controllers\WaiverRulesController::class, 'getWaiverRuleById']);
Route::get('/waiver_rules/getWaiverRulesByLeagueId/{league_id}', [App\Http\Controllers\WaiverRulesController::class, 'getWaiverRulesByLeagueId']);
Route::put('/waiver_rules/update/{id}', [App\Http\Controllers\WaiverRulesController::class, 'updateWaiverRule']);
Route::delete('/waiver_rules/delete/{id}', [App\Http\Controllers\WaiverRulesController::class, 'deleteWaiverRule']);

// Weekly Stats routes
Route::post('/weekly_stats/create', [App\Http\Controllers\WeeklyStatsController::class, 'create']);
Route::get('/weekly_stats/getWeeklyStatById/{id}', [App\Http\Controllers\WeeklyStatsController::class, 'getWeeklyStatById']);
Route::get('/weekly_stats/getWeeklyStatsByPlayerId/{player_id}', [App\Http\Controllers\WeeklyStatsController::class, 'getWeeklyStatsByPlayerId']);
Route::get('/weekly_stats/getWeeklyStatsBySeasonAndWeek/{season}/{week}', [App\Http\Controllers\WeeklyStatsController::class, 'getWeeklyStatsBySeasonAndWeek']);
Route::post('/weekly_stats/getWeeklyStatsByPlayerIdsSeasonAndWeek', [App\Http\Controllers\WeeklyStatsController::class, 'getWeeklyStatsByPlayerIdsSeasonAndWeek']);
Route::put('/weekly_stats/update/{id}', [App\Http\Controllers\WeeklyStatsController::class, 'updateWeeklyStat']);
Route::delete('/weekly_stats/delete/{id}', [App\Http\Controllers\WeeklyStatsController::class, 'deleteWeeklyStat']);

// Yearly Stats routes
Route::post('/yearly_stats/create', [App\Http\Controllers\YearlyStatsController::class, 'create']);
Route::get('/yearly_stats/getYearlyStats', [App\Http\Controllers\YearlyStatsController::class, 'getYearlyStats']);
Route::get('/yearly_stats/getYearlyStatById/{id}', [App\Http\Controllers\YearlyStatsController::class, 'getYearlyStatById']);
Route::get('/yearly_stats/getYearlyStatsByPlayerId/{player_id}', [App\Http\Controllers\YearlyStatsController::class, 'getYearlyStatsByPlayerId']);
Route::get('/yearly_stats/getYearlyStatsBySeason/{season}', [App\Http\Controllers\YearlyStatsController::class, 'getYearlyStatsBySeason']);
Route::put('/yearly_stats/update/{id}', [App\Http\Controllers\YearlyStatsController::class, 'updateYearlyStat']);
Route::delete('/yearly_stats/delete/{id}', [App\Http\Controllers\YearlyStatsController::class, 'deleteYearlyStat']);