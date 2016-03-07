<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2015 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                            *
********************************************************************************************************/

namespace Kajona\Dashboard\Admin;

use Kajona\Dashboard\Admin\Widgets\Adminwidget;
use Kajona\Dashboard\Admin\Widgets\AdminwidgetInterface;
use Kajona\Dashboard\System\DashboardWidget;
use Kajona\Dashboard\System\TodoRepository;
use Kajona\System\Admin\AdminController;
use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\AdminInterface;
use Kajona\System\Admin\Formentries\FormentryDate;
use Kajona\System\Admin\Formentries\FormentryDropdown;
use Kajona\System\Admin\Formentries\FormentryText;
use Kajona\System\System\Exception;
use Kajona\System\System\Link;
use Kajona\System\System\Session;
use Kajona\System\System\SystemAspect;

/**
 * The dashboard admin class
 *
 * @package module_dashboard
 * @author sidler@mulchprod.de
 *
 * @module dashboard
 * @moduleId _dashboard_module_id_
 */
class DashboardAdmin extends AdminController implements AdminInterface {

    protected $arrColumnsOnDashboard = array("column1", "column2", "column3");

    /**
     * @return array
     */
    public function getOutputModuleNavi() {
        $arrReturn = array();
        $arrReturn[] = array("view", Link::getLinkAdmin($this->getArrModule("modul"), "list", "", $this->getLang("modul_titel"), "", "", true, "adminnavi"));
        $arrReturn[] = array("view", Link::getLinkAdmin($this->getArrModule("modul"), "calendar", "", $this->getLang("action_calendar"), "", "", true, "adminnavi"));
        $arrReturn[] = array("view", Link::getLinkAdmin($this->getArrModule("modul"), "todo", "", $this->getLang("action_todo"), "", "", true, "adminnavi"));
        $arrReturn[] = array("", "");
        $arrReturn[] = array("edit", Link::getLinkAdmin($this->getArrModule("modul"), "addWidgetToDashboard", "", $this->getLang("action_add_widget_to_dashboard"), "", "", true, "adminnavi"));
        return $arrReturn;
    }

    /**
     * @return array
     */
    protected function getArrOutputNaviEntries() {
        $arrReturn = parent::getArrOutputNaviEntries();
        if(isset($arrReturn[count($arrReturn)-2]))
            unset($arrReturn[count($arrReturn)-2]);
        return $arrReturn;
    }


    /**
     * Generates the dashboard itself.
     * Loads all widgets placed on the dashboard
     *
     * @return string
     * @autoTestable
     * @permissions view
     */
    protected function actionList() {
        $strReturn = "";
        //load the widgets for each column. currently supporting 3 columns on the dashboard.
        $objDashboardmodel = new DashboardWidget();
        $arrColumns = array();
        //build each row
        foreach($this->arrColumnsOnDashboard as $strColumnName) {
            $strColumnContent = $this->objToolkit->getDashboardColumnHeader($strColumnName);
            $strWidgetContent = "";
            foreach($objDashboardmodel->getWidgetsForColumn($strColumnName, SystemAspect::getCurrentAspectId()) as $objOneSystemmodel) {
                $strWidgetContent .= $this->layoutAdminWidget($objOneSystemmodel);
            }

            $strColumnContent .= $strWidgetContent;
            $strColumnContent .= $this->objToolkit->getDashboardColumnFooter();
            $arrColumns[] = $strColumnContent;
        }
        $strReturn .= $this->objToolkit->getMainDashboard($arrColumns);

        return $strReturn;
    }

    /**
     * Creates the layout of a dashboard-entry. loads the widget to fetch the contents of the concrete widget.
     *
     * @param DashboardWidget $objDashboardWidget
     *
     * @return string
     */
    protected function layoutAdminWidget($objDashboardWidget) {
        $strWidgetContent = "";
        $objConcreteWidget = $objDashboardWidget->getConcreteAdminwidget();

        $strWidgetId = $objConcreteWidget->getSystemid();
        $strWidgetName = $objConcreteWidget->getWidgetName();
        $strWidgetNameAdditionalContent = $objConcreteWidget->getWidgetNameAdditionalContent();

        $strWidgetContent .= $this->objToolkit->getDashboardWidgetEncloser(
            $objDashboardWidget->getSystemid(),
            $this->objToolkit->getAdminwidget(
                $strWidgetId,
                $strWidgetName,
                $strWidgetNameAdditionalContent,
                ($objDashboardWidget->rightEdit() ? Link::getLinkAdminDialog("dashboard", "editWidget", "&systemid=".$objDashboardWidget->getSystemid(), "", $this->getLang("editWidget"), "icon_edit", $objDashboardWidget->getConcreteAdminwidget()->getWidgetName()) : ""),
                ($objDashboardWidget->rightDelete() ? $this->objToolkit->listDeleteButton(
                    $objDashboardWidget->getConcreteAdminwidget()->getWidgetName(),
                    $this->getLang("widgetDeleteQuestion"),
                    "javascript:KAJONA.admin.dashboard.removeWidget(\'".$objDashboardWidget->getSystemid()."\');"
//                    getLinkAdminHref($this->getArrModule("modul"), "deleteWidget", "&systemid=".$objDashboardWidget->getSystemid())
                )  : ""),
                $objDashboardWidget->getConcreteAdminwidget()->getLayoutSection()
            )
        );

        return $strWidgetContent;
    }

    /**
     * Create a calendar based on the jquery fullcalendar. Loads all events from the XML action actionGetCalendarEvents
     *
     * @return string
     * @since 3.4
     * @autoTestable
     * @permissions view
     */
    protected function actionCalendar() {
        $strReturn = "";

        $strContainerId = "calendar-" . generateSystemid();
        $strEventCallback = Link::getLinkAdminXml("dashboard", "getCalendarEvents");
        $strLang = Session::getInstance()->getAdminLanguage();

        $strReturn .= "<div id='" . $strContainerId . "' class='calendar'></div>";
        $strReturn .= "<script type=\"text/javascript\">";
        $strReturn .= <<<JS
            KAJONA.admin.loader.loadFile(['/core/module_dashboard/admin/scripts/fullcalendar/fullcalendar.min.css',
                '/core/module_dashboard/admin/scripts/fullcalendar/lib/moment.min.js'], function(){
                KAJONA.admin.loader.loadFile(['/core/module_dashboard/admin/scripts/fullcalendar/fullcalendar.min.js'], function(){
                    var loadCalendar = function(){
                        $('#{$strContainerId}').fullCalendar({
                            header: {
                                left: 'prev,next',
                                center: 'title',
                                right: ''
                            },
                            editable: false,
                            theme: false,
                            lang: '{$strLang}',
                            eventLimit: true,
                            events: '{$strEventCallback}',
                            eventRender: function(event, el){
                                KAJONA.admin.tooltip.addTooltip(el, event.tooltip);
                                if (event.icon) {
                                    el.find("span.fc-title").prepend(event.icon);
                                }
                            },
                            loading: function(isLoading){
                                if (isLoading) {
                                    $('#moduleTitle').parent().next().html('<div style="float:right;width:30px;" class="calendar-loading"><div class="loadingContainer"></div></div>');
                                } else {
                                    $('#moduleTitle').parent().next().html('');
                                }
                            }
                        });
                        $('.fc-button-group').removeClass().addClass('btn-group');
                        $('.fc-button').removeClass().addClass('btn btn-default');
                    };

                    if ('{$strLang}' != 'en') {
                        KAJONA.admin.loader.loadFile(['/core/module_dashboard/admin/scripts/fullcalendar/lang/{$strLang}.js'], loadCalendar);
                    } else {
                        loadCalendar();
                    }
                });
            });
JS;
        $strReturn .= "</script>";

        return $strReturn;
    }

    /**
     * @permissions view
     */
    protected function actionTodo() {
        $arrContent = array();

        // overall tab
        $strCategory = $this->getParam("listfilter_category");
        $strSearch = $this->getParam("listfilter_search");
        $strDate = $this->getParam("listfilter_date");
        if (!empty($strCategory)) {
            $strLink = Link::getLinkAdminXml("dashboard", "todoCategory", "&category=" . $strCategory . "&search=" . urlencode($strSearch) . "&date=" . $strDate);
        } else {
            $strLink = Link::getLinkAdminXml("dashboard", "todoCategory", "&search=" . urlencode($strSearch) . "&date=" . $strDate);
        }

        $arrContent[$this->getLang("todo_overall_tasks")] = $strLink;

        // each category
        $arrCategories = TodoRepository::getAllCategories();
        foreach ($arrCategories as $strProviderName => $arrTaskCategories) {
            foreach ($arrTaskCategories as $strKey => $strCategoryName) {
                $arrContent[$strCategoryName] = Link::getLinkAdminXml("dashboard", "todoCategory", "&category=" . $strKey);
            }
        }

        $strReturn = $this->getListTodoFilter();
        $strReturn .= $this->objToolkit->getTabbedContent($arrContent);

        return $strReturn;
    }


    protected function getListTodoFilter()
    {
        $strReturn = "";

        // create the form
        $objFormgenerator = new AdminFormgenerator("listfilter", null);

        // provider
        $arrProvider = array();
        $arrCategories = TodoRepository::getAllCategories();
        foreach ($arrCategories as $strProviderName => $arrTaskCategories) {
            foreach ($arrTaskCategories as $strKey => $strCategoryName) {
                $arrProvider[$strKey] = $strProviderName . " / " . $strCategoryName;
            }
        }

        $objFormgenerator->addField(new FormentryDropdown("listfilter", "category"))
            ->setStrLabel($this->getLang("filter_category"))
            ->setArrKeyValues(array_merge(array("" => $this->getLang("filter_all_categories")), $arrProvider));

        $objFormgenerator->addField(new FormentryText("listfilter", "search"))
            ->setStrLabel($this->getLang("filter_search"));

        $objFormgenerator->addField(new FormentryDate("listfilter", "date"))
            ->setStrLabel($this->getLang("filter_date"));

        //render filter
        $strReturn .= $objFormgenerator->renderForm(Link::getLinkAdminHref("dashboard", "todo"), AdminFormgenerator::BIT_BUTTON_SUBMIT);

        $bitFilterActive = $this->getParam("listfilter_category") != "" || $this->getParam("listfilter_search") != "" || $this->getParam("listfilter_date") != "";

        $arrFolder = $this->objToolkit->getLayoutFolderPic($strReturn, $this->getLang("filter_show_hide", "agp_commons").($bitFilterActive ? $this->getLang("commons_filter_active") : ""), "icon_folderOpen", "icon_folderClosed", false);
        $strReturn = $this->objToolkit->getFieldset($arrFolder[1], $arrFolder[0]);

        return $strReturn;
    }


    /**
     * Generates the forms to add a widget to the dashboard
     *
     * @return string, "" in case of success
     * @autoTestable
     * @permissions edit
     */
    protected function actionAddWidgetToDashboard() {
        $strReturn = "";
        //step 1: select a widget, plz
        if($this->getParam("step") == "") {
            $arrWidgetsAvailable = DashboardWidget::getListOfWidgetsAvailable();

            $arrDD = array();
            foreach ($arrWidgetsAvailable as $strOneWidget) {
                /** @var $objWidget AdminwidgetInterface|Adminwidget */
                $objWidget = new $strOneWidget();
                $arrDD[$strOneWidget] = $objWidget->getWidgetName();

            }

            $arrColumnsAvailable = array();
            foreach ($this->arrColumnsOnDashboard as $strOneColumn)
                $arrColumnsAvailable[$strOneColumn] = $this->getLang($strOneColumn);


            $strReturn .= $this->objToolkit->formHeader(Link::getLinkAdminHref("dashboard", "addWidgetToDashboard"));
            $strReturn .= $this->objToolkit->formInputDropdown("widget", $arrDD, $this->getLang("widget"));
            $strReturn .= $this->objToolkit->formInputDropdown("column", $arrColumnsAvailable, $this->getLang("column"));
            $strReturn .= $this->objToolkit->formInputHidden("step", "2");
            $strReturn .= $this->objToolkit->formInputSubmit($this->getLang("addWidgetNextStep"));
            $strReturn .= $this->objToolkit->formClose();

            $strReturn .= $this->objToolkit->setBrowserFocus("widget");
        }
        //step 2: loading the widget and allow it to show a view fields
        else if($this->getParam("step") == "2") {
            $strWidgetClass = $this->getParam("widget");
            /** @var Adminwidget|AdminwidgetInterface $objWidget */
            $objWidget = new $strWidgetClass();

            if($objWidget->getEditForm() == "") {
                $this->adminReload(Link::getLinkAdminHref("dashboard", "addWidgetToDashboard", "&step=3&widget=".$strWidgetClass."&column=".$this->getParam("column")));
            }
            else {
                //ask the widget to generate its form-parts and wrap our elements around
                $strReturn .= $this->objToolkit->formHeader(Link::getLinkAdminHref("dashboard", "addWidgetToDashboard"));
                $strReturn .= $objWidget->getEditForm();
                $strReturn .= $this->objToolkit->formInputHidden("step", "3");
                $strReturn .= $this->objToolkit->formInputHidden("widget", $strWidgetClass);
                $strReturn .= $this->objToolkit->formInputHidden("column", $this->getParam("column"));
                $strReturn .= $this->objToolkit->formInputSubmit($this->getLang("commons_save"));
                $strReturn .= $this->objToolkit->formClose();
            }
        }
        //step 3: save all to the database
        else if($this->getParam("step") == "3") {
            //instantiate the concrete widget
            $strWidgetClass = $this->getParam("widget");
            /** @var Adminwidget|AdminwidgetInterface $objWidget */
            $objWidget = new $strWidgetClass();

            //let it process its fields
            $objWidget->loadFieldsFromArray($this->getAllParams());

            //and save the dashboard-entry
            $objDashboard = new DashboardWidget();
            $objDashboard->setStrClass($strWidgetClass);
            $objDashboard->setStrContent($objWidget->getFieldsAsString());
            $objDashboard->setStrColumn($this->getParam("column"));
            $objDashboard->setStrUser($this->objSession->getUserID());
            $objDashboard->setStrAspect(SystemAspect::getCurrentAspectId());
            if($objDashboard->updateObjectToDb(DashboardWidget::getWidgetsRootNodeForUser($this->objSession->getUserID(), SystemAspect::getCurrentAspectId())))
                $this->adminReload(Link::getLinkAdminHref($this->getArrModule("modul")));
            else
                return $this->getLang("errorSavingWidget");
        }


        return $strReturn;
    }

    /**
     * Deletes a widget from the dashboard
     *
     * @throws Exception
     * @return string "" in case of success
     * @permissions delete
     */
    protected function actionDeleteWidget() {
        $strReturn = "";
        $objDashboardwidget = new DashboardWidget($this->getSystemid());
        if(!$objDashboardwidget->deleteObject())
            throw new Exception("Error deleting widget", Exception::$level_ERROR);

        $this->adminReload(Link::getLinkAdminHref($this->getArrModule("modul")));

        return $strReturn;
    }

    /**
     * Creates the form to edit a widget (NOT the dashboard entry!)
     *
     * @throws Exception
     * @return string "" in case of success
     * @permissions edit
     */
    protected function actionEditWidget() {
        $strReturn = "";
        $this->setArrModuleEntry("template", "/folderview.tpl");
        if($this->getParam("saveWidget") == "") {
            $objDashboardwidget = new DashboardWidget($this->getSystemid());
            $objWidget = $objDashboardwidget->getConcreteAdminwidget();

            //ask the widget to generate its form-parts and wrap our elements around
            $strReturn .= $this->objToolkit->formHeader(Link::getLinkAdminHref("dashboard", "editWidget"));
            $strReturn .= $objWidget->getEditForm();
            $strReturn .= $this->objToolkit->formInputHidden("systemid", $this->getSystemid());
            $strReturn .= $this->objToolkit->formInputHidden("saveWidget", "1");
            $strReturn .= $this->objToolkit->formInputSubmit($this->getLang("commons_save"));
            $strReturn .= $this->objToolkit->formClose();
        }
        elseif($this->getParam("saveWidget") == "1") {
            //the dashboard entry
            $objDashboardwidget = new DashboardWidget($this->getSystemid());
            //the concrete widget
            $objConcreteWidget = $objDashboardwidget->getConcreteAdminwidget();
            $objConcreteWidget->loadFieldsFromArray($this->getAllParams());

            $objDashboardwidget->setStrContent($objConcreteWidget->getFieldsAsString());
            if(!$objDashboardwidget->updateObjectToDb())
                throw new Exception("Error updating widget to db!", Exception::$level_ERROR);

            $this->adminReload(Link::getLinkAdminHref($this->getArrModule("modul"), "", "&peClose=1&blockAction=1"));
        }

        return $strReturn;
    }

}

