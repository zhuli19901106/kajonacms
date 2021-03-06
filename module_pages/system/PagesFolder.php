<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/


namespace Kajona\Pages\System;

use Kajona\System\System\AdminListableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\Link;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\OrmObjectlist;
use Kajona\System\System\SearchResultobjectInterface;
use Kajona\System\System\SystemChangelog;
use Kajona\System\System\SystemModule;
use Kajona\System\System\VersionableInterface;


/**
 * This class manages all stuff related with folders, used by pages. Folders just exist in the database,
 * not in the filesystem
 *
 * @author sidler@mulchprod.de
 * @targetTable page_folder.folder_id
 * @sortManager Kajona\Pages\System\PagesSortmanager
 *
 * @module pages
 * @moduleId _pages_folder_id_
 */
class PagesFolder extends \Kajona\System\System\Model implements \Kajona\System\System\ModelInterface, VersionableInterface, AdminListableInterface, SearchResultobjectInterface
{

    /**
     * @var string
     * @versionable
     * @addSearchIndex
     *
     * @fieldMandatory
     * @fieldType Kajona\System\Admin\Formentries\FormentryText
     * @fieldLabel ordner_name
     * @tableColumn page_folder.folder_name
     * @tableColumnDatatype char254
     */
    private $strName = "";


    /**
     * Return an on-lick link for the passed object.
     * This link is used by the backend-search for the autocomplete-field
     *
     * @see getLinkAdminHref()
     * @return mixed
     */
    public function getSearchAdminLinkForObject()
    {
        return Link::getLinkAdminHref("pages", "list", "&systemid=".$this->getSystemid());
    }


    protected function onInsertToDb()
    {

        //fix the initial sort-id
        $strQuery = "SELECT COUNT(*)
                       FROM "._dbprefix_."system
                      WHERE system_prev_id = ?
                        AND (system_module_nr = ? OR system_module_nr = ?)";
        $arrRow = $this->objDB->getPRow($strQuery, array($this->getPrevId(), _pages_modul_id_, _pages_folder_id_));
        $this->setIntSort($arrRow["COUNT(*)"]);

        return parent::onInsertToDb();
    }

    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName()
    {
        return $this->getStrName();
    }

    /**
     * Returns the icon the be used in lists.
     * Please be aware, that only the filename should be returned, the wrapping by getImageAdmin() is
     * done afterwards.
     *
     * @return string the name of the icon, not yet wrapped by getImageAdmin()
     */
    public function getStrIcon()
    {
        return "icon_folderClosed";
    }

    /**
     * In nearly all cases, the additional info is rendered left to the action-icons.
     *
     * @return string
     */
    public function getStrAdditionalInfo()
    {
        return "";
    }

    /**
     * If not empty, the returned string is rendered below the common title.
     *
     * @return string
     */
    public function getStrLongDescription()
    {
        return "";
    }


    /**
     * Returns a list of folders under the given systemid
     *
     * @param string $strSystemid
     *
     * @return PagesFolder[]
     * @static
     */
    public static function getFolderList($strSystemid = "")
    {
        if (!validateSystemid($strSystemid)) {
            $strSystemid = SystemModule::getModuleByName("pages")->getSystemid();
        }

        return self::getObjectListFiltered(null, $strSystemid);
    }

    /**
     * Returns all Pages listed in a given folder
     *
     * @param string $strFolderid
     *
     * @return PagesPage[]
     * @static
     */
    public static function getPagesInFolder($strFolderid = "")
    {
        if (!validateSystemid($strFolderid)) {
            $strFolderid = SystemModule::getModuleByName("pages")->getSystemid();
        }

        return PagesPage::getObjectListFiltered(null, $strFolderid);

    }

    /**
     * Returns the list of pages and folders, so containing both object types, being located
     * under a given systemid.
     *
     * @param string $strFolderid
     * @param bool $bitOnlyActive
     * @param null $intStart
     * @param null $intEnd
     *
     * @return PagesPage[] | PagesFolder[]
     */
    public static function getPagesAndFolderList($strFolderid = "", $bitOnlyActive = false, $intStart = null, $intEnd = null)
    {
        if (!validateSystemid($strFolderid)) {
            $strFolderid = SystemModule::getModuleByName("pages")->getSystemid();
        }

        $objORM = new OrmObjectlist();
        $strQuery = "SELECT system_id, system_module_nr
						FROM "._dbprefix_."system
						WHERE system_prev_id=?
                         AND (system_module_nr = ? OR system_module_nr = ? )
	                      ".($bitOnlyActive ? " AND system_status = 1 " : "")."
	                      ".$objORM->getDeletedWhereRestriction()."
                    ORDER BY system_sort ASC";

        $arrIds = Carrier::getInstance()->getObjDB()->getPArray($strQuery, array($strFolderid, _pages_modul_id_, _pages_folder_id_), $intStart, $intEnd);
        $arrReturn = array();
        foreach ($arrIds as $arrOneRecord) {
            $objRecord = Objectfactory::getInstance()->getObject($arrOneRecord["system_id"]);
            if ($objRecord instanceof PagesFolder || $objRecord instanceof PagesPage) {
                $arrReturn[] = $objRecord;
            }

        }

        return $arrReturn;
    }

    /**
     * Returns the list of pages and folders, so containing both object types, being located
     * under a given systemid.
     *
     * @param string $strFolderid
     * @param bool $bitOnlyActive
     *
     * @return int
     */
    public static function getPagesAndFolderListCount($strFolderid = "", $bitOnlyActive = false)
    {
        if (!validateSystemid($strFolderid)) {
            $strFolderid = SystemModule::getModuleByName("pages")->getSystemid();
        }
        $objORM = new OrmObjectlist();
        $strQuery = "SELECT COUNT(*)
						FROM "._dbprefix_."system
						WHERE system_prev_id=?
                         AND (system_module_nr = ? OR system_module_nr = ? )
                         ".$objORM->getDeletedWhereRestriction()."
	                      ".($bitOnlyActive ? " AND system_status = 1 " : "");

        $arrRow = Carrier::getInstance()->getObjDB()->getPRow($strQuery, array($strFolderid, _pages_modul_id_, _pages_folder_id_));
        return $arrRow["COUNT(*)"];
    }


    public function getVersionActionName($strAction)
    {
        if ($strAction == SystemChangelog::$STR_ACTION_EDIT) {
            return $this->getLang("pages_ordner_edit", "pages");
        }
        elseif ($strAction == SystemChangelog::$STR_ACTION_DELETE) {
            return $this->getLang("pages_ordner_delete", "pages");
        }

        return $strAction;
    }

    public function renderVersionValue($strProperty, $strValue)
    {
        return $strValue;
    }

    public function getVersionPropertyName($strProperty)
    {
        return $strProperty;
    }

    public function getVersionRecordName()
    {
        return Carrier::getInstance()->getObjLang()->getLang("change_object_folder", "pages");
    }

    /**
     * @return string
     *
     */
    public function getStrName()
    {
        return $this->strName;
    }

    public function setStrName($strName)
    {
        $this->strName = $strName;
    }

}
