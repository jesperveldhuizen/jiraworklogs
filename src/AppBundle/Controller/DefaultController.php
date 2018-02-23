<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{

    /**
     * Worklog overzicht.
     *
     * @param Request $request
     * @return string
     */
    public function indexAction(Request $request)
    {
        $sprints = $this->get('app.jira.jira_data_manager')->findActiveSprints();

        return new Response($this->renderView('@App/default/index.html.twig', [
            'sprints' => $sprints
        ]));
    }

    /**
     * Sprint data legen uit de cache.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function clearAction(Request $request)
    {
        $this->get('app.jira.jira_data_manager')->clearSprintData();
        return new RedirectResponse('/');
    }
}
