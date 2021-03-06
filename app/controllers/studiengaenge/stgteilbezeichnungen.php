<?php
/**
 * stgteilbezeichnungen.php - Studiengaenge_StgteilbezeichnungenController
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Peter Thienel <thienel@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       3.5
 */



class Studiengaenge_StgteilbezeichnungenController extends MVVController
{

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        // set navigation
        Navigation::activateItem($this->me . '/studiengaenge/stgteilbezeichnungen');
        $this->action = $action;

        if (Request::isXhr()) {
            $this->set_layout(null);
        }
    }

    public function index_action()
    {
        $this->stgteilbezeichnungen = StgteilBezeichnung::getAllEnriched();
        PageLayout::setTitle(_('Alle Studiengangteil-Bezeichnungen'));
        $this->setSidebar();
    }

    /**
     * Creates a new Studiengangteil-Bezeichnung
     */
    function stgteilbezeichnung_action($bezeichnung_id = null)
    {
        $this->stgteilbezeichnung = StgteilBezeichnung::get($bezeichnung_id);
        if ($this->stgteilbezeichnung->isNew()) {
            PageLayout::setTitle(_('Neue Studiengangteil-Bezeichnung anlegen'));
        } else {
            $this->bezeichnung_id = $this->stgteilbezeichnung->getId();
            PageLayout::setTitle(_('Studiengangteil-Bezeichnung bearbeiten'));
        }
       
        $this->setSidebar();
        if (!$this->stgteilbezeichnung->isNew()) {
            $sidebar = Sidebar::get();
            $action_widget = $sidebar->getWidget('actions');
            $action_widget->addLink(
                _('Log-Einträge dieser Studiengangteil-Bezeichnung'),
                $this->url_for('shared/log_event/show/' . $this->stgteilbezeichnung->id),
                Icon::create('log', 'clickable'), array('data-dialog' => '')
            );
        }

    }
    
    /**
     * Store a Studiengangteil-Bezeichnung
     * @param null $bezeichnung_id
     */
    public function store_action($bezeichnung_id = null) {
        CSRFProtection::verifyUnsafeRequest();
        $stgteilbezeichnung = StgteilBezeichnung::get($bezeichnung_id);
        if ($stgteilbezeichnung->isNew()) {
            $success_message = ('Die Studiengangteil-Bezeichnung "%s" wurde angelegt.');
        } else {
            $success_message = _('Die Studiengangteil-Bezeichnung "%s" wurde geändert.');
        }
        $stored = false;
        $stgteilbezeichnung->name = Request::i18n('name')->trim();
        $stgteilbezeichnung->name_kurz = Request::i18n('name_kurz')->trim();
        
        $stgteilbezeichnung->verifyPermission();
        
        try {
            $stored = $stgteilbezeichnung->store();
        } catch (InvalidValuesException $e) {
            PageLayout::postError(htmlReady($e->getMessage()));
        }
    
        if ($stored !== false) {
            PageLayout::postSuccess(sprintf($success_message, htmlReady($stgteilbezeichnung->name)));
        }
        $this->relocate('studiengaenge/stgteilbezeichnungen');
    }

    /**
     * Deletes a Abschluss-Kategorie
     */
    function delete_action($stgteilbezeichnung_id)
    {
        CSRFProtection::verifyUnsafeRequest();
        $stgteilbezeichnung = StgteilBezeichnung::get($stgteilbezeichnung_id);
        if ($stgteilbezeichnung->count_studiengaenge) {
            PageLayout::postError( sprintf(_('Löschen nicht möglich! Die Studiengangteil-Bezeichnung "%s" wird bereits verwendet!'),
                htmlReady($stgteilbezeichnung->name)));
        } else {
            $perm = MvvPerm::get($stgteilbezeichnung);
            if (!$perm->havePerm(MvvPerm::PERM_CREATE)) {
                throw new Trails_Exception(403, _('Keine Berechtigung'));
            }
            PageLayout::postSuccess(sprintf(
                _('Studiengangteil-Bezeichnung "%s" gelöscht!'),
                htmlReady($stgteilbezeichnung->name)
            ));
            $stgteilbezeichnung->delete();
        }
        $this->redirect('studiengaenge/stgteilbezeichnungen');
    }

    /**
     * sorts Teilstudiengaenge
     */
    public function sort_action()
    {
        $orderedIds = Request::getArray('newOrder');
        $stgteilbezeichnungen = SimpleORMapCollection::createFromArray(
            StgteilBezeichnung::getAll()
        );
        if (is_array($orderedIds)) {
            $i = 1;
            foreach ($orderedIds as $id) {
                $stgteilbezeichnung = $stgteilbezeichnungen->find($id);
                if ($stgteilbezeichnung) {
                    if ($stgteilbezeichnung->position != $i) {
                        $stgteilbezeichnung->position = $i;
                        $stgteilbezeichnung->store();
                    }
                    $i++;
                }
            }
        }
        $this->set_status(200);
        $this->render_nothing();
    }
    
    /**
     * Display details
     * @param $bezeichnung_id
     * @return bool
     */
    public function details_action($bezeichnung_id)
    {
        $this->stgteilbezeichnung = StgteilBezeichnung::get($bezeichnung_id);
        $this->bezeichnung_id = $this->stgteilbezeichnung->getId();
        if (!Request::isXhr()) {
            $this->perform_relayed('stgteilbezeichnungen');
            return true;
        }
    }

    /**
     * Creates the sidebar widgets
     */
    protected function setSidebar()
    {
        $sidebar = Sidebar::get();
        $sidebar->setImage(Assets::image_path("sidebar/learnmodule-sidebar.png"));

        if (MvvPerm::havePermCreate('StgteilBezeichnung')) {
            $widget  = new ActionsWidget();
            $widget->addLink(
                _('Neue Studiengangteil-Bezeichnung'),
                $this->url_for('/stgteilbezeichnung'),
                Icon::create('add', 'clickable')
            )->asDialog();
            $sidebar->addWidget($widget);
        }
        $helpbar = Helpbar::get();
        $widget = new HelpbarWidget();
        $widget->addElement(new WidgetElement(_("Sie können die Reihenfolge der Studiengangteil-Bezeichnungen durch Ziehen der Zeilen ändern.").'</br>'));
        $widget->addElement(new WidgetElement(_("Eine Studiengangteil-Bezeichnung kann aufgeklappt werden, um Details anzuzeigen.")));
        $helpbar->addWidget($widget);

        $this->sidebar_rendered = true;
    }

}
