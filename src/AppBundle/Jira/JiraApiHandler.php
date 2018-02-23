<?php

namespace AppBundle\Jira;

use AppBundle\Parser\JiraApiDataParser;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\JiraClient;

/**
 * Class JiraApiHandler
 * @package AppBundle\Jira\Service
 */
class JiraApiHandler extends JiraClient
{

    /**
     * @var JiraApiDataParser
     */
    private $jiraApiDataParser;

    /**
     * JiraApiHandler constructor.
     *
     * @param JiraApiDataParser $jiraApiDataParser
     * @param string $host
     * @param string $user
     * @param string $password
     */
    public function __construct(
        JiraApiDataParser $jiraApiDataParser,
        string $host,
        string $user,
        string $password
    )
    {
        $this->jiraApiDataParser = $jiraApiDataParser;

        parent::__construct(new ArrayConfiguration([
            'jiraHost' => $host,
            'jiraUser' => $user,
            'jiraPassword' => $password,
        ]));
    }

    /**
     * Sprints ophalen.
     *
     * @param int $board
     * @return array
     */
    public function getSprints(int $board)
    {
        $this->setAPIUri('/rest/agile/1.0/board/'.$board);
        $uri = 'sprint?state=active';

        $result = $this->exec($uri, null);

        return $this->jiraApiDataParser->parse($result);
    }

    /**
     * Issues uit een sprint ophalen.
     *
     * @param array $sprint
     * @return array
     */
    public function getSprintIssues(array $sprint)
    {
        $this->setAPIUri('/rest/greenhopper/latest/rapid/charts/');
        $uri = 'sprintreport?rapidViewId='.$sprint['board'].'&sprintId='.$sprint['id'];
        $result = $this->exec($uri, null);

        return $this->jiraApiDataParser->parse($result);
    }

    /**
     * Gedetailleerd issues ophalen.
     * TODO: er worden nu maximaal 100 issues opgehaald.
     *
     * @param $issues
     * @return array
     */
    public function getIssues(array $issues)
    {
        $fields = [
            'id',
            'worklog',
            'customfield_10005', // sp
            'customfield_10302', // waarde
            'timeoriginalestimate',
            'timespent',
            'status'
        ];

        $this->setAPIUri('/rest/api/2/search');
        $result = $this->exec('?jql=key%20in%20('.implode(',', array_keys($issues)).')&fields='.implode(',', $fields).'&maxResults=100', null);

        return $this->jiraApiDataParser->parse($result);
    }
}
