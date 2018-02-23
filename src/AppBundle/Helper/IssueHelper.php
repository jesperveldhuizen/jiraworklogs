<?php

namespace AppBundle\Helper;

/**
 * Class IssueHelper
 * @package AppBundle\Helper
 */
class IssueHelper
{

    /**
     * De logs van een issue ophalen.
     *
     * @param string $issueKey
     * @param array $workLogs
     * @return array
     */
    public function getLogsForIssue(string $issueKey, array $workLogs)
    {
        $logs = [];
        if (empty($workLogs)) {
            return $logs;
        }

        $timeSpent = 0;
        foreach ($workLogs as $key => $rows) {
            if ($issueKey != $key) {
                continue;
            }

            if (empty($rows)) {
                continue;
            }

            foreach ($rows as $row) {
                $row['issue'] = $issueKey;
                $logs[] = $row;
                $timeSpent += $row['timeSpentSeconds'];
            }
        }

        return [
            'timeSpent' => $timeSpent,
            'logs' => $logs
        ];
    }

    /**
     * Aantal uren voor een issue ophalen.
     *
     * @param string $issueKey
     * @param array $workLogs
     * @return int
     */
    public function getTimeSpentForIssue(string $issueKey, array $workLogs)
    {
        $logs = $this->getLogsForIssue($issueKey, $workLogs);
        if (empty($logs)) {
            return 0;
        }

        return (int)$logs['timeSpent'];
    }

    /**
     * Totaal aantal uren per logs berekenen.
     *
     * @param array $workLogs
     * @return int
     */
    public function getTimeSpentForLogs(array $workLogs)
    {
        $timeSpent = 0;
        if (empty($workLogs)) {
            return $timeSpent;
        }

        foreach ($workLogs as $log) {
            $timeSpent += (int)$log['timeSpentSeconds'];
        }

        return $timeSpent;
    }

    /**
     * Totalen voor de issues ophalen.
     *
     * @param $issues
     * @return array
     */
    public function getTotalsForIssues(array $issues)
    {
        $totals = [
            'sp' => 0,
            'estimate' => 0,
            'timespent' => 0,
            'value' => 0,
            'exceeded' => 0
        ];

        if (empty($issues)) {
            return $totals;
        }

        foreach ($issues as $issue) {
            foreach (array_keys($totals) as $key) {
                $totals[$key] += $issue[$key];
            }
        }

        return $totals;
    }

    /**
     * Issues ophalen die over de uren zijn gegaan.
     *
     * @param array $issues
     * @return array
     */
    public function getExceededIssues(array $issues)
    {
        $data = [];
        if (empty($issues)) {
            return $data;
        }

        foreach ($issues as $issue) {
            if ($issue['timespent'] <= $issue['estimate']) {
                continue;
            }

            $data[] = $issue;
        }

        if (empty($data)) {
            return [];
        }

        usort($data, function($a, $b) {
            $a = strtotime($a['exceeded']);
            $b = strtotime($b['exceeded']);

            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });

        return $data;
    }
}
