<?php

namespace AppBundle\Helper;

/**
 * Class WorklogHelper
 * @package AppBundle\Helper
 */
class WorklogHelper
{

    /**
     * Logs splitten per dag.
     *
     * @param array $worklogs
     * @return array
     */
    public function splitLogsPerDay(array $worklogs)
    {
        $data = [];
        if (empty($worklogs)) {
            return [];
        }

        foreach ($worklogs as $log) {
            if (!isset($data[$log['date']])) {
                $data[$log['date']] = [];
            }

            $data[$log['date']][] = $log;
        }

        return $data;
    }

    /**
     * De ruwe logs omzetten in logs per dag.
     *
     * @param array $worklogs
     * @return array
     */
    public function getDayOverview(array $worklogs)
    {
        $data = [];
        if (empty($worklogs)) {
            return [];
        }

        foreach ($worklogs as $issueKey => $logs) {
            if (empty($logs)) {
                continue;
            }

            foreach ($logs as $log) {
                if (isset($data[$log['date']])) {
                    $data[$log['date']] = [];
                }

                $log['issue'] = $issueKey;
                $data[strtotime($log['date'])][] = $log;
            }
        }

        ksort($data);
        return $data;
    }
}
