<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2015 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                        *
********************************************************************************************************/

/**
 * Interface
 *
 * @package module_dashboard
 * @author christoph.kappestein@gmail.com
 */
interface interface_todo_provider extends interface_generic_plugin {

    const EXTENSION_POINT = "core.dashboard.admin.todo_event";

    /**
     * Returns all todo entries which are available. This can be entries which are in the past but still needs actions
     * or entries which are in the future. In the end all events are sorted after the todo entry validdate
     *
     * @return class_todo_entry[]
     */
    public function getEvents();

    /**
     * Returns all todo entries for a specific category
     *
     * @param $strCategory
     * @return mixed
     */
    public function getEventsByCategory($strCategory);

    /**
     * Returns an array of all available categories
     *
     * @return array
     */
    public function getCategories();

    /**
     * Returns whether the currently logged in user can view this events
     *
     * @return boolean
     */
    public function rightView();

}
