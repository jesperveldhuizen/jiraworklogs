<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SprintController extends Controller
{

    /**
     * Sprint overzicht.
     *
     * @param Request $request
     * @param $sprintId
     * @return Response
     */
    public function indexAction(Request $request, $sprintId)
    {
        $sprint = $this->get('app.jira.jira_data_manager')->findSprintById($sprintId);
        if (empty($sprint)) {
            return new RedirectResponse('/');
        }

        $data = $this->get('app.jira.jira_data_manager')->getSprintData($sprintId);
        $days = $this->get('app.helper.worklog_helper')->getDayOverview($data['worklogs']);
        $exceeded = $this->get('app.helper.issue_helper')->getExceededIssues($data['issues']);

        return new Response($this->renderView('@App/sprint/index.html.twig', [
            'sprint' => $data['sprint'],
            'issues' => $data['issues'],
            'worklogs' => $data['worklogs'],
            'days' => $days,
            'exceeded' => $exceeded
        ]));
    }

    /**
     * Overzicht van de issues binnen een sprint.
     *
     * @param Request $request
     * @param int $sprintId
     * @return Response
     */
    public function issuesAction(Request $request, int $sprintId)
    {
        $data = $this->get('app.jira.jira_data_manager')->getSprintData($sprintId);

        return new Response($this->renderView('@App/sprint/issues.html.twig', [
            'issues' => $data['issues'],
            'sprint' => $data['sprint']
        ]));
    }

    /**
     * Issue detail pagina.
     *
     * @param Request $request
     * @param int $sprintId
     * @param string $issueKey
     * @return RedirectResponse|Response
     */
    public function issueAction(Request $request, int $sprintId, string $issueKey)
    {
        $data = $this->get('app.jira.jira_data_manager')->getSprintData($sprintId);
        $logs = $this->get('app.helper.issue_helper')->getLogsForIssue($issueKey, $data['worklogs']);

        if (empty($logs) || !isset($data['issues'][$issueKey])) {
            return new RedirectResponse('/');
        }

        return new Response($this->renderView('@App/sprint/issue.html.twig', [
            'logs' => $logs,
            'issue' => $data['issues'][$issueKey],
            'sprint' => $data['sprint']
        ]));
    }

    /**
     * Werknemer detail pagina binnen een sprint.
     *
     * @param Request $request
     * @param int $sprintId
     * @param string $employeeId
     * @return RedirectResponse|Response
     */
    public function employeeAction(Request $request, int $sprintId, string $employeeId)
    {
        $data = $this->get('app.jira.jira_data_manager')->getSprintData($sprintId);
        $logs = $this->get('app.helper.employee_helper')->getWorklogsByUser($data['worklogs'], $employeeId);

        if (empty($logs)) {
            return new RedirectResponse('/');
        }

        $firstLog = current($logs);

        return new Response($this->renderView('@App/sprint/employee.html.twig', [
            'logs' => $logs,
            'employee' => $firstLog['user'],
            'sprint' => $data['sprint']
        ]));
    }

    /**
     * Project detail pagina.
     *
     * @param Request $request
     * @param int $sprintId
     * @param string $projectKey
     * @return RedirectResponse|Response
     */
    public function projectAction(Request $request, int $sprintId, string $projectKey)
    {
        $data = $this->get('app.jira.jira_data_manager')->getSprintData($sprintId);
        $issues = $this->get('app.helper.project_helper')->getIssuesForProject($projectKey, $data['issues']);

        $logs = $this->get('app.helper.project_helper')->getLogsForProject($data['issues'], $data['worklogs'], $projectKey);
        if (empty($logs)) {
            return new RedirectResponse('/');
        }

        return new Response($this->renderView('@App/sprint/project.html.twig', [
            'logs' => $logs,
            'projectKey' => $projectKey,
            'issues' => $issues,
            'sprint' => $data['sprint']
        ]));
    }

    /**
     * Overzicht van logs binnen een dag.
     *
     * @param Request $request
     * @param int $sprintId
     * @param string $day
     * @return RedirectResponse|Response
     */
    public function dayAction(Request $request, int $sprintId, string $day)
    {
        $data = $this->get('app.jira.jira_data_manager')->getSprintData($sprintId);
        $days = $this->get('app.helper.worklog_helper')->getDayOverview($data['worklogs']);

        if (!isset($days[$day])) {
            return new RedirectResponse('/');
        }

        return new Response($this->renderView('@App/sprint/day.html.twig', [
            'sprint' => $data['sprint'],
            'logs' => $days[$day]
        ]));
    }
}
