<?php

namespace AppBundle\Helper;

/**
 * Class EmployeeHelper
 * @package AppBundle\Helper
 */
class EmployeeHelper
{
    use HelperTrait;

    /**
     * Tijden per persoon ophalen.
     *
     * @param $workLogs
     * @return array
     */
    public function getTimeSpentPerUser(array $workLogs)
    {
        $data = [];
        if (empty($workLogs)) {
            return $data;
        }

        foreach ($workLogs as $issueKey => $logs) {
            if (empty($logs)) {
                continue;
            }

            foreach ($logs as $log) {
                $key = $log['user']['id'];
                if (!isset($data[$key])) {
                    $data[$key] = [
                        'logs' => [],
                        'timeSpent' => 0
                    ];
                }

                $data[$key]['user'] = $log['user'];
                $data[$key]['logs'][] = $log;
                $data[$key]['timeSpent'] += $log['timeSpentSeconds'];
            }
        }

        return $data;
    }

    /**
     * Alle logs van een user ophalen.
     *
     * @param array $workLogs
     * @param string $employeeId
     * @return array
     */
    public function getWorklogsByUser(array $workLogs, string $employeeId)
    {
        $data = [];
        if (empty($workLogs)) {
            return $data;
        }

        foreach ($workLogs as $issueKey => $logs) {
            if (empty($logs)) {
                continue;
            }

            foreach ($logs as $log) {
                if ($log['user']['id'] != $employeeId) {
                    continue;
                }

                $log['issue'] = $issueKey;
                $data[] = $log;
            }
        }

        return $this->sortDataByDate($data);
    }
}
