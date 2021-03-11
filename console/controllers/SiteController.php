<?php
namespace console\controllers;

use common\models\Units;
use yii\helpers\Console;
use yii\console\Controller;

class SiteController extends Controller
{
    public function actionPlay() {

        $teams = Units::find()->limit(2)->asArray()->all();

        if(empty($teams) || count($teams) == 1) {
            Console::output("To play you need to add at least two teams");
        }

        $teamsInfo = [];
        foreach ($teams as $team) {

            $unitData = [];
            $unitsArray = str_split($team['string']);

            foreach ($unitsArray as $unit) {
                $unitData[] = array_merge(["type" => $unit], Units::UNIT_INFO[$unit]);
            }
            $teamsInfo[$team["id"]] = $unitData;

        }

        $firstTeamKey = array_key_first($teamsInfo);
        $secondTeamKey = array_key_last($teamsInfo);
        $firstTeam = $teamsInfo[$firstTeamKey];
        $secondTeam = $teamsInfo[$secondTeamKey];

        $win = false;
        while (!$win) {
            $attack = $this->findStrongestAttacker($firstTeam);
            $secondTeamSorted = $this->sortByLessLife($secondTeam);
            var_dump("First team start attack ");
            foreach ($secondTeamSorted as $key => $secondTeamUnit) {
                if($attack > 0) {
                    Console::output("Attacked with $attack");
                    $realAttack = round($attack/100 * (100 - $secondTeamUnit["defense"]));
                    $secondTeamUnit["life"] = $secondTeamUnit["life"] - $realAttack;
                    if($secondTeamUnit["life"] > 0) {
                        Console::output("Second team unit still live and have life ".$secondTeamUnit["life"]);
                        $secondTeamSorted[$key] = $secondTeamUnit;
                        $attack = 0;
                    }
                    else {
                        $attack = $attack - ($secondTeamUnit["life"] + round($attack/100 * $secondTeamUnit["defense"]));
                        unset($secondTeamSorted[$key]);
                        Console::output("Second team unit died, attacker have still $attack attack");
                    }
                }
                else {
                    Console::output("First team finish attack");
                    break;
                }
            }

            $secondTeam = $secondTeamSorted;
            if(empty($secondTeam)) {
                $win = true;
                Console::output("win first team");
                Units::updateAll(["win" => 1], ["id" => $firstTeamKey]);
                break;
            }

            $attack = $this->findStrongestAttacker($secondTeam);
            $firstTeamSorted = $this->sortByLessLife($firstTeam);
            Console::output("Second team start attack ");
            foreach ($firstTeamSorted as $key => $firstTeamUnit) {
                if($attack > 0) {
                    Console::output("Attacked with $attack");
                    $realAttack = round($attack/100 * (100 - $firstTeamUnit["defense"]));
                    $firstTeamUnit["life"] = $firstTeamUnit["life"] - $realAttack;
                    if($firstTeamUnit["life"] > 0) {
                        Console::output("First team unit still live and have life ".$firstTeamUnit["life"]);
                        $firstTeamSorted[$key] = $firstTeamUnit;
                        $attack = 0;
                    }
                    else {
                        $attack = $attack - ($firstTeamUnit["life"] + round($attack/100 * $firstTeamUnit["defense"]));
                        unset($firstTeamSorted[$key]);
                        Console::output("First team unit died, attacker have still $attack attack");
                    }
                }
                else {
                    Console::output("Second team finish attack");
                    break;
                }
            }

            $firstTeam = $firstTeamSorted;
            if(empty($firstTeam)) {
                $win = true;
                Console::output("win second team");
                Units::updateAll(["win" => 1], ["id" => $secondTeamKey]);
                break;
            }

            Console::output("Teams start recover");
            $firstTeam = $this->recoverTeamUnits($firstTeam);
            $secondTeam = $this->recoverTeamUnits($secondTeam);

        }


        die;


    }


    private function findStrongestAttacker(array $team) {
        $maxAttack = 0;
        foreach ($team as $unit) {
            if($unit['attack'] > $maxAttack) {
                $maxAttack = $unit['attack'];
            }
        }
        return $maxAttack;
    }

    private function sortByLessLife(array $team) {

        $sortedArray = [];

        foreach ($team as $unit) {
            if(empty($sortedArray)) {
                $sortedArray[] = $unit;
            }
            else {
                if($unit["life"] < $sortedArray[0]["life"]) {
                    array_unshift($sortedArray, $unit);
                }
                else {
                    array_push($sortedArray, $unit);
                }
            }
        }

        return $sortedArray;

    }

    private function recoverTeamUnits(array $team) {

        foreach ($team as $key => $unit) {
            if($unit["life"] < Units::LIFE_COUNT) {
                $unit["life"] = $unit["life"] + $unit["recover"];
                if($unit["life"] > Units::LIFE_COUNT) {
                    $unit["life"] = Units::LIFE_COUNT;
                }

                $team[$key] = $unit;
            }
        }

        return $team;

    }
}