<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

namespace Kajona\System\System;

use Kajona\System\System\Usersources\UsersourcesUserInterface;


/**
 * Model for a user
 * Note: Users do not use the classical system-id relation, so no entry in the system-table
 *
 * @package module_user
 * @author sidler@mulchprod.de
 *
 * @module user
 * @moduleId _user_modul_id_
 *
 * @blockFromAutosave
 */
class UserUser extends Model implements ModelInterface, AdminListableInterface
{

    private $strSubsystem = "kajona";

    /**
     *
     * @var UsersourcesUserInterface
     */
    private $objSourceUser;

    private $strUsername = "";

    private $intLogins = 0;
    private $intLastlogin = 0;
    private $intActive = 0;
    private $intAdmin = 0;
    private $intPortal = 0;
    private $strAdminskin = "";
    private $strAdminlanguage = "";
    private $strAdminModule = "";
    private $strAuthcode = "";
    private $intDeleted = 0;
    private $intItemsPerPage = 0;


    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName()
    {
        $strReturn = $this->getStrUsername();
        if ($this->getStrName() != "") {
            $strReturn .= " (".$this->getStrName().", ".$this->getStrForename().")";
        }

        if ($this->intDeleted == 1) {
            $strReturn = $this->getStrUsername()." (".$this->getLang("user_deleted").")";
        }

        return $strReturn;
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
        return "icon_user";
    }

    /**
     * In nearly all cases, the additional info is rendered left to the action-icons.
     *
     * @return string
     */
    public function getStrAdditionalInfo()
    {
        if ($this->rightRight1()) {
            return $this->getLang("user_logins", "user")." ".$this->getIntLogins()." ".$this->getLang("user_lastlogin", "user")." ".timeToString($this->getIntLastLogin(), false);
        }
        return "";
    }

    /**
     * If not empty, the returned string is rendered below the common title.
     *
     * @return string
     */
    public function getStrLongDescription()
    {
        if ($this->objSession->isSuperAdmin()) {
            $objUsersources = new UserSourcefactory();
            if (count($objUsersources->getArrUsersources()) > 1) {
                $objSubsystem = new UserSourcefactory();
                return $this->getLang("user_list_source", "user")." ".$objSubsystem->getUsersource($this->getStrSubsystem())->getStrReadableName();
            }
        }
        return "";
    }


    /**
     * @return bool
     */
    public function rightView()
    {
        return SystemModule::getModuleByName("user")->rightView();
    }

    /**
     * @return bool
     */
    public function rightEdit()
    {
        return SystemModule::getModuleByName("user")->rightEdit();
    }

    /**
     * @return bool
     */
    public function rightDelete()
    {
        return SystemModule::getModuleByName("user")->rightDelete();
    }

    /**
     * @return bool
     */
    public function rightRight1()
    {
        return SystemModule::getModuleByName("user")->rightRight1();
    }


    /**
     * Initialises the current object, if a systemid was given
     *
     * @return void
     */
    protected function initObjectInternal()
    {
        $strQuery = "SELECT * FROM "._dbprefix_."user WHERE user_id=?";
        $arrRow = $this->objDB->getPRow($strQuery, array($this->getSystemid()));

        if (count($arrRow) > 0) {
            $this->setStrUsername($arrRow["user_username"]);
            $this->setStrSubsystem($arrRow["user_subsystem"]);
            $this->setIntLogins($arrRow["user_logins"]);
            $this->setIntLastLogin($arrRow["user_lastlogin"]);
            $this->setIntActive($arrRow["user_active"]);
            $this->setIntAdmin($arrRow["user_admin"]);
            $this->setIntPortal($arrRow["user_portal"]);
            $this->setStrAdminskin($arrRow["user_admin_skin"]);
            $this->setStrAdminlanguage($arrRow["user_admin_language"]);
            $this->setSystemid($arrRow["user_id"]);
            $this->setStrAuthcode($arrRow["user_authcode"]);

            if (isset($arrRow["user_items_per_page"])) {
                $this->setIntItemsPerPage($arrRow["user_items_per_page"]);
            }

            if (isset($arrRow["user_deleted"])) {
                $this->intDeleted = $arrRow["user_deleted"];
            }

            if (isset($arrRow["user_admin_module"])) {
                $this->setStrAdminModule($arrRow["user_admin_module"]);
            }

        }
    }

    /**
     * Updates the current object to the database
     * <b>ATTENTION</b> If you don't want to update the password, set it to "" before!
     *
     * @param bool $strPrevid
     *
     * @return bool
     */
    public function updateObjectToDb($strPrevid = false)
    {

        if ($this->getSystemid() == "") {
            $strUserid = generateSystemid();
            $this->setSystemid($strUserid);
            $strQuery = "INSERT INTO "._dbprefix_."user (
                        user_id, user_active,
                        user_admin, user_portal,
                        user_admin_skin, user_admin_language,
                        user_logins, user_lastlogin, user_authcode, user_subsystem, user_username, user_admin_module, user_deleted, user_items_per_page

                        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            Logger::getInstance(Logger::USERSOURCES)->addLogRow("new user for subsystem ".$this->getStrSubsystem()." / ".$this->getStrUsername(), Logger::$levelInfo);

            $bitReturn = $this->objDB->_pQuery(
                $strQuery,
                array(
                    $strUserid,
                    (int)$this->getIntActive(),
                    (int)$this->getIntAdmin(),
                    (int)$this->getIntPortal(),
                    $this->getStrAdminskin(),
                    $this->getStrAdminlanguage(),
                    0,
                    0,
                    $this->getStrAuthcode(),
                    $this->getStrSubsystem(),
                    $this->getStrUsername(),
                    $this->getStrAdminModule(),
                    0,
                    $this->getIntItemsPerPage(),
                )
            );

            //create the new instance on the remote-system
            $objSources = new UserSourcefactory();
            $objProvider = $objSources->getUsersource($this->getStrSubsystem());
            $objTargetUser = $objProvider->getNewUser();
            $objTargetUser->updateObjectToDb();
            $objTargetUser->setNewRecordId($this->getSystemid());
            $this->objDB->flushQueryCache();

            return $bitReturn;
        }
        else {

            if (version_compare(SystemModule::getModuleByName("user")->getStrVersion(), "4.6.5", ">=")) {
                $strQuery = "UPDATE "._dbprefix_."user SET
                        user_active=?, user_admin=?, user_portal=?, user_admin_skin=?, user_admin_language=?, user_logins = ?, user_lastlogin = ?, user_authcode = ?, user_subsystem = ?,
                        user_username =?, user_admin_module = ?, user_items_per_page = ?
                        WHERE user_id = ?";
            }
            else if (version_compare(SystemModule::getModuleByName("user")->getStrVersion(), "4.4", ">=")) {
                $strQuery = "UPDATE "._dbprefix_."user SET
                        user_active=?, user_admin=?, user_portal=?, user_admin_skin=?, user_admin_language=?, user_logins = ?, user_lastlogin = ?, user_authcode = ?, user_subsystem = ?,
                        user_username =?, user_admin_module = ?
                        WHERE user_id = ?";
            }
            else {
                $strQuery = "UPDATE "._dbprefix_."user SET
                        user_active=?, user_admin=?, user_portal=?, user_admin_skin=?, user_admin_language=?, user_logins = ?, user_lastlogin = ?, user_authcode = ?, user_subsystem = ?,
                        user_username =?
                        WHERE user_id = ?";
            }

            $arrParams = array(
                (int)$this->getIntActive(),
                (int)$this->getIntAdmin(), (int)$this->getIntPortal(), $this->getStrAdminskin(), $this->getStrAdminlanguage(),
                (int)$this->getIntLogins(), (int)$this->getIntLastLogin(), $this->getStrAuthcode(),
                $this->getStrSubsystem(), $this->getStrUsername()
            );

            if (version_compare(SystemModule::getModuleByName("user")->getStrVersion(), "4.4", ">=")) {
                $arrParams[] = $this->getStrAdminModule();
            }

            if (version_compare(SystemModule::getModuleByName("user")->getStrVersion(), "4.6.5", ">=")) {
                $arrParams[] = $this->getIntItemsPerPage();
            }

            $arrParams[] = $this->getSystemid();


            Logger::getInstance(Logger::USERSOURCES)->addLogRow("updated user for subsystem ".$this->getStrSubsystem()." / ".$this->getStrUsername(), Logger::$levelInfo);
            return $this->objDB->_pQuery($strQuery, $arrParams);
        }
    }

    /**
     * Called whenever a update-request was fired.
     * Use this method to synchronize yourselves with the database.
     * Use only updates, inserts are not required to be implemented.
     *
     * @return bool
     */
    protected function updateStateToDb()
    {
        return false;
    }


    /**
     * @param FilterBase|null $objFilter
     * @param string $strUsernameFilter
     * @param null $intStart
     * @param null $intEnd
     *
     * @return UserUser[]
     */
    public static function getObjectListFiltered(FilterBase $objFilter = null, $strUsernameFilter = "", $intStart = null, $intEnd = null)
    {
        $strDbPrefix = _dbprefix_;
        $arrParams = array();

        if (version_compare(SystemModule::getModuleByName("user")->getStrVersion(), "4.5", ">=")) {

            $strQuery = "SELECT user_tbl.user_id
                          FROM {$strDbPrefix}user AS user_tbl
                          LEFT JOIN {$strDbPrefix}user_kajona AS user_kajona ON user_tbl.user_id = user_kajona.user_id
                          WHERE
                              (user_tbl.user_username LIKE ? OR user_kajona.user_forename LIKE ? OR user_kajona.user_name LIKE ?)

                              AND (user_tbl.user_deleted = 0 OR user_tbl.user_deleted IS NULL)
                          ORDER BY user_tbl.user_username, user_tbl.user_subsystem ASC";

            $arrParams = array("%".$strUsernameFilter."%", "%".$strUsernameFilter."%", "%".$strUsernameFilter."%");
        }
        else {
            $strQuery = "SELECT user_id FROM {$strDbPrefix}user
                            WHERE user_username LIKE ? ORDER BY user_username, user_subsystem ASC";

            $arrParams = array("%".$strUsernameFilter."%");
        }

        $arrIds = Carrier::getInstance()->getObjDB()->getPArray($strQuery, $arrParams, $intStart, $intEnd);

        $arrReturn = array();
        foreach ($arrIds as $arrOneId) {
            $arrReturn[] = new UserUser($arrOneId["user_id"]);
        }

        return $arrReturn;
    }

    /**
     * @param FilterBase|null $objFilter
     * @param string $strUsernameFilter
     *
     * @return int
     */
    public static function getObjectCountFiltered(FilterBase $objFilter = null, $strUsernameFilter = "")
    {
        $strDbPrefix = _dbprefix_;
        $arrParams = array();

        if (version_compare(SystemModule::getModuleByName("user")->getStrVersion(), "4.5", ">=")) {
            $strQuery = "SELECT COUNT(*)
                          FROM {$strDbPrefix}user AS user_tbl
                          LEFT JOIN {$strDbPrefix}user_kajona AS user_kajona ON user_tbl.user_id = user_kajona.user_id
                          WHERE
                              (user_tbl.user_username LIKE ? OR user_kajona.user_forename LIKE ? OR user_kajona.user_name LIKE ?)

                              AND (user_tbl.user_deleted = 0 OR user_tbl.user_deleted IS NULL)";

            $arrParams = array("%".$strUsernameFilter."%", "%".$strUsernameFilter."%", "%".$strUsernameFilter."%");
        }
        else {
            $strQuery = "SELECT COUNT(*) FROM {$strDbPrefix}user
                            WHERE user_username LIKE ? ";

            $arrParams = array("%".$strUsernameFilter."%");
        }

        $arrRow = Carrier::getInstance()->getObjDB()->getPRow($strQuery, $arrParams);
        return $arrRow["COUNT(*)"];
    }


    /**
     * Fetches all available active users with the given username an returns them in an array
     *
     * @param string $strName
     *
     * @return UserUser[]
     */
    public static function getAllUsersByName($strName)
    {
        $objSubsystem = new UserSourcefactory();
        $objUser = $objSubsystem->getUserByUsername($strName);
        if ($objUser != null) {
            return array($objUser);
        }
        else {
            return null;
        }
    }


    /**
     * Deletes a user from the systems
     *
     * @throws Exception
     * @return bool
     */
    public function deleteObject()
    {

        if ($this->objSession->getUserID() == $this->getSystemid()) {
            throw new Exception("You can't delete yourself", Exception::$level_FATALERROR);
        }

        Logger::getInstance(Logger::USERSOURCES)->addLogRow("deleted user with id ".$this->getSystemid()." (".$this->getStrUsername()." / ".$this->getStrName().",".$this->getStrForename().")", Logger::$levelWarning);
        $this->getObjSourceUser()->deleteUser();
        $strQuery = "UPDATE "._dbprefix_."user SET user_deleted = 1, user_active = 0 WHERE user_id = ?";
        $bitReturn = $this->objDB->_pQuery($strQuery, array($this->getSystemid()));
        //call other models that may be interested
        CoreEventdispatcher::getInstance()->notifyGenericListeners(SystemEventidentifier::EVENT_SYSTEM_RECORDDELETED, array($this->getSystemid(), get_class($this)));

        return $bitReturn;
    }

    public function deleteObjectFromDatabase()
    {
        return $this->deleteObject();
    }


    /**
     * Returns an array of group-ids the current user is assigned to
     *
     * @return array string
     */
    public function getArrGroupIds()
    {
        $this->loadSourceObject();
        return $this->objSourceUser->getGroupIdsForUser();
    }

    /**
     * @return string
     */
    public function getStrEmail()
    {
        $this->loadSourceObject();
        if ($this->objSourceUser != null) {
            return $this->objSourceUser->getStrEmail();
        }
        else {
            return "n.a.";
        }
    }

    /**
     * @return string
     */
    public function getStrForename()
    {
        $this->loadSourceObject();
        if ($this->objSourceUser != null) {
            return $this->objSourceUser->getStrForename();
        }
        else {
            return "n.a.";
        }
    }

    /**
     * @return string
     */
    public function getStrName()
    {
        $this->loadSourceObject();
        if ($this->objSourceUser != null) {
            return $this->objSourceUser->getStrName();
        }
        else {
            return "n.a.";
        }
    }

    /**
     * @return void
     */
    private function loadSourceObject()
    {
        if ($this->objSourceUser == null && $this->intDeleted != 1) {
            $objUsersources = new UserSourcefactory();
            $this->setObjSourceUser($objUsersources->getSourceUser($this));
        }
    }




    // --- GETTERS / SETTERS --------------------------------------------------------------------------------

    /**
     * @return int
     */
    public function getIntLogins()
    {
        return $this->intLogins;
    }

    /**
     * @return int
     */
    public function getIntLastLogin()
    {
        return $this->intLastlogin;
    }

    /**
     * @return int
     */
    public function getIntActive()
    {
        return $this->intActive;
    }

    /**
     * @return int
     */
    public function getIntAdmin()
    {
        return $this->intAdmin;
    }

    /**
     * @return int
     */
    public function getIntPortal()
    {
        return $this->intPortal;
    }

    /**
     * @return string
     */
    public function getStrAdminskin()
    {
        return $this->strAdminskin;
    }

    /**
     * @return string
     */
    public function getStrAdminlanguage()
    {
        return $this->strAdminlanguage;
    }

    /**
     * @return string
     */
    public function getStrUsername()
    {
        return $this->strUsername;
    }

    /**
     * @param string $strUsername
     *
     * @return void
     */
    public function setStrUsername($strUsername)
    {
        $this->strUsername = $strUsername;
    }

    /**
     * @param int $intLogins
     *
     * @return void
     */
    public function setIntLogins($intLogins)
    {
        if ($intLogins == "") {
            $intLogins = 0;
        }
        $this->intLogins = $intLogins;
    }

    /**
     * @param int $intLastLogin
     *
     * @return void
     */
    public function setIntLastLogin($intLastLogin)
    {
        if ($intLastLogin == "") {
            $intLastLogin = 0;
        }
        $this->intLastlogin = $intLastLogin;
    }

    /**
     * @param int $intActive
     *
     * @return void
     */
    public function setIntActive($intActive)
    {
        if ($intActive == "") {
            $intActive = 0;
        }
        $this->intActive = $intActive;
    }

    /**
     * @param int $intAdmin
     *
     * @return void
     */
    public function setIntAdmin($intAdmin)
    {
        if ($intAdmin == "") {
            $intAdmin = 0;
        }
        $this->intAdmin = $intAdmin;
    }

    /**
     * @param int $intPortal
     *
     * @return void
     */
    public function setIntPortal($intPortal)
    {
        if ($intPortal == "") {
            $intPortal = 0;
        }
        $this->intPortal = $intPortal;
    }

    /**
     * @param string $strAdminskin
     *
     * @return void
     */
    public function setStrAdminskin($strAdminskin)
    {
        $this->strAdminskin = $strAdminskin;
    }

    /**
     * @param string $strAdminlanguage
     *
     * @return void
     */
    public function setStrAdminlanguage($strAdminlanguage)
    {
        $this->strAdminlanguage = $strAdminlanguage;
    }

    /**
     * @return string
     */
    public function getStrAuthcode()
    {
        return $this->strAuthcode;
    }

    /**
     * @param string $strAuthcode
     *
     * @return void
     */
    public function setStrAuthcode($strAuthcode)
    {
        $this->strAuthcode = $strAuthcode;
    }

    /**
     * @return string
     */
    public function getStrSubsystem()
    {
        return $this->strSubsystem;
    }

    /**
     * @param string $strSubsystem
     *
     * @return void
     */
    public function setStrSubsystem($strSubsystem)
    {
        $this->strSubsystem = $strSubsystem;
    }

    /**
     * @return UsersourcesUserInterface
     */
    public function getObjSourceUser()
    {
        $this->loadSourceObject();
        return $this->objSourceUser;
    }

    /**
     * @param UsersourcesUserInterface $objSourceUser
     *
     * @return void
     */
    public function setObjSourceUser($objSourceUser)
    {
        $this->objSourceUser = $objSourceUser;
    }

    /**
     * @return int
     */
    public function getIntRecordStatus()
    {
        return $this->intActive;
    }

    /**
     * @param string $strAdminModule
     *
     * @return void
     */
    public function setStrAdminModule($strAdminModule)
    {
        $this->strAdminModule = $strAdminModule;
    }

    /**
     * @return string
     */
    public function getStrAdminModule()
    {
        return $this->strAdminModule;
    }

    /**
     * @return int
     */
    public function getIntDeleted()
    {
        return $this->intDeleted;
    }

    /**
     * @param integer $intItemsPerPage
     */
    public function setIntItemsPerPage($intItemsPerPage)
    {
        $this->intItemsPerPage = (int)$intItemsPerPage;
    }

    /**
     * @return int
     */
    public function getIntItemsPerPage()
    {
        if ($this->intItemsPerPage > 0) {
            return $this->intItemsPerPage;
        }
        else {
            return SystemSetting::getConfigValue("_admin_nr_of_rows_");
        }
    }

}
