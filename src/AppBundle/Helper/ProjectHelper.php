<?php

namespace AppBundle\Helper;

class ProjectHelper
{
    use HelperTrait;

    /**
     * Issues per project sorteren.
     *
     * @param array $issues
     * @return array
     */
    public function getProjects(array $issues)
    {
        $projects = [];
        if (empty($issues)) {
            return $projects;
        }

        foreach ($issues as $key => $issue) {
            list($projectId) = explode('-', $key);
            if (!isset($projects[$projectId])) {
                $projects[$projectId] = [];
            }

            $projects[$projectId][] = $issue;
        }

        return $projects;
    }

    /**
     * Per project het aantal gelogde uren ophalen.
     *
     * @param array $workLogs
     * @return array
     */
    public function getTimeSpentByProject(array $workLogs)
    {
        $data = [];
        if (empty($workLogs)) {
            return $data;
        }

        foreach ($workLogs as $issueKey => $logs) {
            if (empty($logs)) {
                continue;
            }

            list($project) = explode('-', $issueKey);
            if (!isset($data[$project])) {
                $data[$project] = 0;
            }

            foreach ($logs as $log) {
                $data[$project] += (int)$log['timeSpentSeconds'];
            }
        }

        arsort($data, SORT_NUMERIC);
        return $data;
    }

    /**
     * De logs van een project ophalen.
     *
     * @param array $issues
     * @param array $workLogs
     * @param string $projectKey
     * @return array
     */
    public function getLogsForProject(array $issues, array $workLogs, string $projectKey)
    {
        $data = [];
        if (empty($issues) || empty($workLogs)) {
            return $data;
        }

        foreach ($workLogs as $issueKey => $logs) {
            if (empty($logs)) {
                continue;
            }

            list($project) = explode('-', $issueKey);
            if ($project != $projectKey) {
                continue;
            }

            foreach ($logs as $log) {
                $log['issue'] = $issueKey;
                $data[] = $log;
            }
        }

        return $this->sortDataByDate($data);
    }

    /**
     * De issues van een project ophalen.
     *
     * @param string $projectKey
     * @param array $issues
     * @return array
     */
    public function getIssuesForProject(string $projectKey, array $issues)
    {
        $data = [];
        if (empty($issues)) {
            return $data;
        }

        foreach ($issues as $issueKey => $issue) {
            list($key) = explode('-', $issueKey);
            if ($key != $projectKey) {
                continue;
            }

            $data[] = $issue;
        }

        return $data;
    }
}
