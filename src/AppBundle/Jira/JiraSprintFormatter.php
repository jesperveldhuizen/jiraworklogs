<?php

namespace AppBundle\Jira;

/**
 * Class JiraSprintFormatter
 * @package AppBundle\Jira
 */
class JiraSprintFormatter
{

    /**
     * @var JiraApiHandler
     */
    private $jiraApiHandler;

    /**
     * SprintHandler constructor.
     *
     * @param JiraApiHandler $jiraApiHandler
     */
    public function __construct(JiraApiHandler $jiraApiHandler)
    {
        $this->jiraApiHandler = $jiraApiHandler;
    }

    /**
     * Alle actieve huidige sprints ophalen.
     *
     * @return null|array
     */
    public function getActiveSprints()
    {
        $boards = [
            '10', // team 1
            '6', // team 2
            '11' // team 3
        ];

        $data = [];
        foreach ($boards as $boardId) {
            $sprints = $this->jiraApiHandler->getSprints($boardId);
            if (empty($sprints)) {
                continue;
            }

            foreach ($sprints as $sprint) {
                $data[$sprint['id']] = [
                    'id' => $sprint['id'],
                    'board' => $boardId,
                    'name' => $sprint['name'],
                    'start' => date('Y-m-d', strtotime($sprint['startDate'])),
                    'end' => date('Y-m-d', strtotime($sprint['endDate']))
                ];
            }
        }

        return $data;
    }

    /**
     * De issues van een sprint ophalen.
     *
     * @param array $sprint
     * @return array
     */
    public function getSprintIssues(array $sprint)
    {
        $issues = [];

        $data = $this->jiraApiHandler->getSprintIssues($sprint);
        if (empty($data)) {
            return $issues;
        }

        $rows = ['completedIssues', 'issuesNotCompletedInCurrentSprint'];
        foreach ($rows as $key) {
            if (empty($data['contents'][$key])) {
                continue;
            }

            foreach ($data['contents'][$key] as $issue) {
                $issues[$issue['key']] = [
                    'id' => $issue['id'],
                    'key' => $issue['key'],
                    'summary' => $issue['summary']
                ];
            }
        }

        $keys = [
            'customfield_10005',
            'customfield_10302',
            'timeoriginalestimate',
            'timespent'
        ];

        $data = $this->jiraApiHandler->getIssues($issues);
        foreach ($data['issues'] as $row) {
            foreach ($issues as $issueKey => &$issue) {
                if ($issueKey != $row['key']) {
                    continue;
                }

                foreach ($keys as $key) {
                    if (empty($row['fields'][$key])) {
                        $row['fields'][$key] = 0;
                    }
                }

                $issue['sp'] = $row['fields']['customfield_10005'];
                $issue['value'] = $row['fields']['customfield_10302'];
                $issue['estimate'] = (int)$row['fields']['timeoriginalestimate'];
                $issue['timespent'] = (int)$row['fields']['timespent'];

                $issue['exceeded'] = 0;
                if ($issue['timespent'] > 0 && $issue['exceeded'] <= $issue['timespent']) {
                    $issue['exceeded'] = $issue['timespent'] - $issue['estimate'];
                }

                $issue['status'] = [
                    'id' => $row['fields']['status']['id'],
                    'name' => $row['fields']['status']['name']
                ];
            }
        }

        ksort($issues);
        return $issues;
    }

    /**
     * Worklogs ophalen van een lijst van issues.
     *
     * @param array $issues
     * @return array
     */
    public function getWorklogs(array $issues)
    {
        $logs = [];

        $data = $this->jiraApiHandler->getIssues($issues);
        if (empty($data)) {
            return $logs;
        }

        foreach ($data['issues'] as $issue) {
            if (empty($issue['fields']['worklog']['worklogs'])) {
                continue;
            }

            foreach ($issue['fields']['worklog']['worklogs'] as $worklog) {
                $logs[$issue['key']][] = [
                    'date' => date('Y-m-d', strtotime($worklog['started'])),
                    'user' => [
                        'id' => $worklog['author']['accountId'],
                        'name' => $worklog['author']['displayName']
                    ],
                    'comment' => $worklog['comment'],
                    'timeSpent' => $worklog['timeSpent'],
                    'timeSpentSeconds' => $worklog['timeSpentSeconds']
                ];
            }
        }

        return $logs;
    }
}
