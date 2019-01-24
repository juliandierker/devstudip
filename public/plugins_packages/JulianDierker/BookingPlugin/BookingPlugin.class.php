<?php

class BookingPlugin extends StudIPPlugin implements StandardPlugin, SystemPlugin
{
    public function getInfoTemplate($course_id)
    {
        return NULL;
    }
    public function getIconNavigation($course_id, $last_visit, $user_id)
    {    
        return NULL;
    }
    public function getTabNavigation($course_id)
    {
        return NULL;

    }
    private function setupExamNavigation()
    {
        $navigation = new Navigation('');

    }

}