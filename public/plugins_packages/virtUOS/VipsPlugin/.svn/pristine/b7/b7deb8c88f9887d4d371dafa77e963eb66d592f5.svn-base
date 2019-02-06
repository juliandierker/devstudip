<?php
/*
 * lti_exercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2018  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

Exercise::addExerciseType(_vips('Externe Aufgabe (LTI-Tool)'), 'lti_exercise', 'lti-tool');

class lti_exercise extends Exercise
{
    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request)
    {
        parent::initFromRequest($request);

        $this->task['launch_url'] = trim($request['launch_url']);
        $this->task['consumer_key'] = trim($request['consumer_key']);
        $this->task['consumer_secret'] = trim($request['consumer_secret']);
        $this->task['custom_parameters'] = trim($request['custom_parameters']);
        $this->task['send_lis_person'] = (int) $request['send_lis_person'];
    }

    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise)
    {
        parent::initXML($exercise);

        $external_link = $exercise->items->item->{'external-link'};
        $this->task['launch_url'] = studip_utf8decode(trim($external_link['uri']));

        foreach ($external_link->param as $param) {
            $param_name = (string) $param['name'];
            if (in_array($param_name, ['consumer_key', 'consumer_secret', 'custom_parameters', 'send_lis_person'])) {
                $this->task[$param_name] = studip_utf8decode(trim($param));
            }
        }
    }

    /**
     * Evaluates a student's solution for the individual items in this
     * exercise. Returns an array of ('points' => float, 'safe' => boolean).
     *
     * @param solution The solution XML string as returned by responseFromRequest().
     */
    public function evaluateItems($solution)
    {
        return [['points' => $solution->response['score'], 'safe' => true]];
    }

    /**
     * Compute the default maximum points which can be reached in this
     * exercise, dependent on the number of answers (defaults to 1).
     */
    public function itemCount()
    {
        return 0;
    }

    /**
     * Get the link object for the LTI launch request.
     */
    public function getLTILink($assignment, $user_id)
    {
        $lti_link = new VipsLTILink($this->task['launch_url'], $this->task['consumer_key'], $this->task['consumer_secret']);
        $lti_link->setResource($this->id, $this->title, $this->description);
        $lti_link->setCourse($assignment->course_id);

        $group = $assignment->getUserGroup($user_id);

        if ($group) {
            $lti_link->setUser('group:' . $group->id);
            $lti_link->addVariable('Person.name.full', $group->name);
            $lti_link->addLaunchParameter('lis_person_name_full', $group->name);
        } else {
            $lti_link->setUser($user_id, 'Learner', $this->task['send_lis_person']);
        }

        $lti_link->addLaunchParameters([
            'launch_presentation_locale' => str_replace('_', '-', getUserLanguage($user_id)),
            'launch_presentation_document_target' => 'iframe'
        ]);

        foreach (explode("\n", $this->task['custom_parameters']) as $param) {
            list($key, $value) = explode('=', $param);
            if (isset($value)) {
                $lti_link->addCustomParameter(trim($key), trim($value));
            }
        }

        return $lti_link;
    }

    /**
     * Create a template for editing an exercise.
     *
     * @return The template
     */
    function getEditTemplate($assignment)
    {
        if ($this->task['launch_url'] && $this->task['consumer_key'] && $this->task['consumer_secret']) {
            $widget = new LinksWidget();
            $widget->setTitle(_vips('Links'));
            $widget->addLink(_vips('Aufgabe im LTI-Tool bearbeiten'),
                vips_url('sheets/relay/lti_dialog', ['assignment_id' => $assignment->id, 'exercise_id' => $this->id]),
                Icon::create('link-extern'), ['data-dialog' => 'size=1200x780']);
            Sidebar::get()->addWidget($widget);
        }

        return parent::getEditTemplate($assignment);
    }

    /**
     * Create a template for solving an lti_exercise.
     *
     * @return The template
     */
    public function getSolveTemplate($solution, $assignment, $user_id)
    {
        URLHelper::setBaseUrl($GLOBALS['ABSOLUTE_URI_STUDIP']);

        $lti_link = $this->getLTILink($assignment, $user_id);
        $lti_link->addLaunchParameters([
            'lis_outcome_service_url' => vips_url('sheets/relay/outcome', ['exercise_id' => $this->id, 'cid' => NULL]),
            'lis_result_sourcedid' => $assignment->id . ':' . $this->id . ':' . $user_id
        ]);

        $template = parent::getSolveTemplate($solution, $assignment, $user_id);
        $template->exercise_id = $this->id;
        $template->lti_link    = $lti_link;

        return $template;
    }

    /**
     * Create a template for correcting a pl_exercise.
     *
     * @return The template
     */
    public function getCorrectionTemplate($solution)
    {
        return $this->getSolveTemplate($solution, $solution->assignment, $solution->user_id);
    }

    /**
     * Create a template for printing an exercise.
     *
     * @return The template
     */
    public function getPrintTemplate($solution, $assignment, $user_id)
    {
        $template = $GLOBALS['template_factory']->open('shared/string');
        $template->content = _vips('Diese Aufgabe wird aus einem externen System eingebunden und kann hier nicht angezeigt werden.');

        return $template;
    }

    public function lti_dialog_action($controller)
    {
        global $vipsPlugin, $vipsTemplateFactory;

        $assignment_id = Request::int('assignment_id');
        $assignment = VipsAssignment::find($assignment_id);

        vips_require_status('tutor');
        check_exercise_assignment($this->id, $assignment);
        check_assignment_access($assignment);

        $lti_link = $this->getLTILink($assignment, $vipsPlugin->userID);
        $lti_link->addLaunchParameter('roles', 'Instructor');

        $template = $vipsTemplateFactory->open('exercises/solve_lti_exercise');
        $template->exercise_id = $this->id;
        $template->lti_link    = $lti_link;

        $controller->render_template($template);
    }

    public function iframe_action($controller)
    {
        global $vipsTemplateFactory;

        $template = $vipsTemplateFactory->open('exercises/iframe_lti_exercise');
        $template->launch_url  = Request::get('launch_url');
        $template->launch_data = Request::getArray('launch_data');
        $template->signature   = Request::get('signature');

        $controller->render_template($template);
    }

    public function outcome_action($controller)
    {
        require_once 'vendor/oauth-php/library/OAuthRequestVerifier.php';

        global $vipsPlugin, $vipsTemplateFactory;

        $message = file_get_contents('php://input');

        $envelope = new SimpleXMLElement($message);
        $header = current($envelope->imsx_POXHeader->children());
        $body = current($envelope->imsx_POXBody->children());

        $message_id = studip_utf8decode(trim($header->imsx_messageIdentifier));
        $operation = studip_utf8decode($body->getName());
        $source_id = trim($body->resultRecord->sourcedGUID->sourcedId);
        $score = (float) $body->resultRecord->result->resultScore->textString;
        list($assignment_id, $exercise_id, $user_id) = explode(':', $source_id);

        $exercise = Exercise::find($exercise_id);
        $assignment = VipsAssignment::find($assignment_id);
        $vipsPlugin->userID = $user_id;
        $vipsPlugin->courseID = $assignment->course_id;

        OAuthStore::instance('PDO', [
            'dsn' => 'mysql:host=' . $GLOBALS['DB_STUDIP_HOST'] . ';dbname=' . $GLOBALS['DB_STUDIP_DATABASE'],
            'username' => $GLOBALS['DB_STUDIP_USER'],
            'password' => $GLOBALS['DB_STUDIP_PASSWORD']
        ]);

        $oarv = new OAuthRequestVerifier();
        $oarv->verifySignature($exercise->task['consumer_secret'], false, false);

        check_exercise_assignment($exercise_id, $assignment);
        check_assignment_access($assignment);

        $template = $vipsTemplateFactory->open('exercises/xml_lti_response');
        $template->message_id = uniqid();
        $template->message_ref = $message_id;
        $template->status_severity = 'status';
        $template->status_code = 'success';
        $template->operation = $operation;

        if ($operation == 'readResultRequest') {
            $solution = $assignment->getSolution($user_id, $exercise_id);

            if ($solution) {
                $template->score = $solution->response['score'];
                $template->description = 'score has been read';
            } else {
                $template->status_severity = 'error';
                $template->status_code = 'failure';
                $template->description = 'no score found for: ' . $source_id;
            }
        } else if ($operation == 'replaceResultRequest') {
            $solution = $exercise->getSolutionFromRequest(null);
            $solution->response = compact('score');
            $assignment->correctSolution($solution);
            $assignment->storeSolution($solution);
            $template->description = 'score has been updated';
        } else if ($operation == 'deleteResultRequest') {
            $assignment->deleteSolution($user_id, $exercise_id);
            $template->description = 'score has been deleted';
        } else {
            $template->status_severity = 'error';
            $template->status_code = 'unsupported';
            $template->description = 'operation not supported: ' . $operation;
        }

        $controller->set_content_type('text/xml; charset=UTF-8');
        $controller->render_template($template);
    }
}
