<?php
# Lifter010: TODO
/*
 * SearchNavigation.php - navigation for search page
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @author      Michael Riehemann <michael.riehemann@uni-oldenburg.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       2.0
 */

/**
 * This navigation includes all search pages depending on the
 * activated modules.
 */
class SearchNavigation extends Navigation
{
    /**
     * Initialize a new Navigation instance.
     */
    public function __construct()
    {
        parent::__construct(_('Suche'));

        $this->setImage(Icon::create('search', 'navigation', ['title' => _('Suche')]));
    }

    /**
     * Initialize the subnavigation of this item. This method
     * is called once before the first item is added or removed.
     */
    public function initSubNavigation()
    {
        parent::initSubNavigation();

        // browse courses
        // get first search option
        $navigation_option = SemBrowse::getSearchOptionNavigation('sidebar');

        if ($navigation_option) {
            $navigation = new Navigation(_('Veranstaltungen'),
                    $navigation_option->getURL());
            foreach (array_keys(Config::get()->COURSE_SEARCH_NAVIGATION_OPTIONS) as $name) {
                $navigation_option = SemBrowse::getSearchOptionNavigation('sidebar', $name);
                if ($navigation_option) {
                    $navigation->addSubNavigation($name, $navigation_option);
                }
            }

            $this->addSubNavigation('courses', $navigation);
        }


        if ($GLOBALS['user']->id != 'nobody') {
            // search archive
            $navigation = new Navigation(_('Archiv'), 'dispatch.php/search/archive');
            $this->addSubNavigation('archive', $navigation);

            // search users
            $navigation = new Navigation(_('Personen'), 'browse.php');
            $this->addSubNavigation('users', $navigation);

            // browse institutes
            $navigation = new Navigation(_('Einrichtungen'), 'institut_browse.php');
            $this->addSubNavigation('institutes', $navigation);

            // browse resources
            if (get_config('RESOURCES_ENABLE')) {
                $navigation = new Navigation(_('Ressourcen'), 'resources.php', array('view' => 'search', 'reset' => 'TRUE'));
                $this->addSubNavigation('resources', $navigation);
            }
        }
    }
}
