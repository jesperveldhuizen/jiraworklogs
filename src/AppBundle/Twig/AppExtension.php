<?php

namespace AppBundle\Twig;

use AppBundle\Helper\EmployeeHelper;
use AppBundle\Helper\IssueHelper;
use AppBundle\Helper\ProjectHelper;
use AppBundle\Helper\WorklogHelper;
use AppBundle\Jira\JiraDataManager;

/**
 * Class AppExtension
 * @package AppBundle\Twig
 */
class AppExtension extends \Twig_Extension
{

    /**
     * @var JiraDataManager
     */
    private $jiraDataManager;

    /**
     * @var EmployeeHelper
     */
    private $employeeHelper;

    /**
     * @var IssueHelper
     */
    private $issueHelper;

    /**
     * @var ProjectHelper
     */
    private $projectHelper;

    /**
     * @var WorklogHelper
     */
    private $worklogHelper;

    /**
     * AppExtension constructor.
     *
     * @param JiraDataManager $jiraDataManager
     * @param EmployeeHelper $employeeHelper
     * @param IssueHelper $issueHelper
     * @param ProjectHelper $projectHelper
     * @param WorklogHelper $worklogHelper
     */
    public function __construct(
        JiraDataManager $jiraDataManager,
        EmployeeHelper $employeeHelper,
        IssueHelper $issueHelper,
        ProjectHelper $projectHelper,
        WorklogHelper $worklogHelper
    ) {
        $this->jiraDataManager = $jiraDataManager;
        $this->employeeHelper = $employeeHelper;
        $this->issueHelper = $issueHelper;
        $this->projectHelper = $projectHelper;
        $this->worklogHelper = $worklogHelper;
    }

    /**
     * Twig functions registreren.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('app_getProjects', [$this->projectHelper, 'getProjects']),
            new \Twig_SimpleFunction('app_getLogsForIssue', [$this->issueHelper, 'getLogsForIssue']),
            new \Twig_SimpleFunction('app_getTimeSpentPerUser', [$this->employeeHelper, 'getTimeSpentPerUser']),
            new \Twig_SimpleFunction('app_getTimeSpentByProject', [$this->projectHelper, 'getTimeSpentByProject']),
            new \Twig_SimpleFunction('app_getTimeSpentForIssue', [$this->issueHelper, 'getTimeSpentForIssue']),
            new \Twig_SimpleFunction('app_splitLogsPerDay', [$this->worklogHelper, 'splitLogsPerDay']),
            new \Twig_SimpleFunction('app_getDateDayByString', [$this, 'getDateDayString']),
            new \Twig_SimpleFunction('app_getTimeSpentForLogs', [$this->issueHelper, 'getTimeSpentForLogs']),
            new \Twig_SimpleFunction('app_calculateHoursbySeconds', [$this, 'calculateHoursbySeconds']),
            new \Twig_SimpleFunction('app_getTotalsForIssues', [$this->issueHelper, 'getTotalsForIssues']),
            new \Twig_SimpleFunction('app_getIssuesForProject', [$this->projectHelper, 'getIssuesForProject']),
            new \Twig_SimpleFunction('app_isDateInSprint', [$this, 'isDateInSprint']),
            new \Twig_SimpleFunction('app_getDateByTs', [$this, 'getDateByTs'])
        ];
    }

    /**
     * Datum omzetten naar tekstuele variant.
     *
     * @param string $date
     * @param bool $showDay
     * @return string
     */
    public function getDateDayString(string $date, bool $showDay = true)
    {
        $format = '%A %e %B';
        if (!$showDay) {
            $format = '%e %B';
        }

        $ts = strtotime($date);
        return strftime($format, $ts);
    }

    /**
     * Timestamp omzetten naar datum.
     *
     * @param int $ts
     * @return false|string
     */
    public function getDateByTs(int $ts)
    {
        return date('d-m-Y', $ts);
    }

    /**
     * Aantal uren berekenen aan de hand van de secondes.
     *
     * @param int $seconds
     * @return float
     */
    public function calculateHoursbySeconds(?int $seconds)
    {
        if (empty($seconds)) {
            return (float)0;
        }

        return round($seconds / 3600, 2);
    }

    /**
     * Controleren of een datum binnen een sprint valt.
     *
     * @param string $date
     * @param array $sprint
     * @param bool $isTs
     * @return bool
     */
    public function isDateInSprint(string $date, array $sprint, bool $isTs = false)
    {
        if (!$isTs) {
            $date = strtotime($date);
        }

        return $date >= strtotime($sprint['start']) && $date <= strtotime($sprint['end']);
    }
}
