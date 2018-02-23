<?php

namespace AppBundle\Jira;

use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * Class JiraDataManager
 * @package AppBundle\Service
 */
class JiraDataManager
{

    /**
     * @var JiraSprintFormatter
     */
    private $jiraSprintFormatter;

    /**
     * @var FilesystemCache
     */
    private $cache;

    /**
     * JiraDataManager constructor.
     *
     * @param JiraSprintFormatter $jiraSprintFormatter
     */
    public function __construct(JiraSprintFormatter $jiraSprintFormatter)
    {
        $this->jiraSprintFormatter = $jiraSprintFormatter;
        $this->cache = new FilesystemCache();
    }

    /**
     * Alle sprint gegevens ophalen van een specifieke sprint.
     *
     * @param $sprintId
     * @return array
     */
    public function getSprintData($sprintId)
    {
        $data = [
            'sprint' => null,
            'issues' => [],
            'worklogs' => []
        ];

        $sprint = $this->findSprintById($sprintId);
        if (empty($sprint)) {
            return $data;
        }

        $data['sprint'] = $sprint;
        $data['issues'] = $this->findIssues($sprint);
        $data['worklogs'] = $this->findWorkLogs($data['issues']);

        return $data;
    }

    /**
     * Sprint data uit de cache verwijderen.
     */
    public function clearSprintData()
    {
        $this->cache->clear();
    }

    /**
     * Huidige actieve sprints ophalen.
     */
    public function findActiveSprints()
    {
        $cacheKey = 'sprints';
        if (!$this->cache->has($cacheKey)) {
            $sprints = $this->jiraSprintFormatter->getActiveSprints();
            $this->cache->set($cacheKey, $sprints);
        } else {
            $sprints = $this->cache->get($cacheKey);
        }

        return $sprints;
    }

    /**
     * Sprint ophalen via sprint id.
     *
     * @param int $sprintId
     * @return array|null
     */
    public function findSprintById(int $sprintId)
    {
        $sprints = $this->findActiveSprints();
        if (empty($sprints)) {
            return null;
        }

        foreach ($sprints as $sprint) {
            if ($sprint['id'] == $sprintId) {
                return $sprint;
            }
        }

        return null;
    }

    /**
     * Issues van de sprint ophalen.
     *
     * @param array $sprint
     * @return array|mixed|null
     */
    private function findIssues(array $sprint)
    {
        $cacheKey = 'sprint_issues_'.$sprint['id'];
        if (!$this->cache->has($cacheKey)) {
            $issues = $this->jiraSprintFormatter->getSprintIssues($sprint);
            $this->cache->set($cacheKey, $issues);
        } else {
            $issues = $this->cache->get($cacheKey);
        }

        return $issues;
    }

    /**
     * Work logs van de meegegeven issues ophalen.
     *
     * @param array $issues
     * @return array
     */
    private function findWorkLogs(array $issues)
    {
        $cacheKey = 'sprint_worklogs_'.md5(implode('', array_keys($issues)));
        if (!$this->cache->has($cacheKey)) {
            $worklogs = $this->jiraSprintFormatter->getWorklogs($issues);
            $this->cache->set($cacheKey, $worklogs);
        } else {
            $worklogs = $this->cache->get($cacheKey);
        }

        return $worklogs;
    }
}
