<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2011 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                 *
********************************************************************************************************/

/**
 * This class manages all stuff related with folders, used by pages. Folders just exist in the database,
 * not in the filesystem
 *
 * @package modul_pages
 * @author sidler@mulchprod.de
 */
class class_modul_pages_folder extends class_model implements interface_model, interface_versionable  {

    private $strActionEdit = "editFolder";

    private $strName = "";

    private $strOldName = "";
    /**
     * Constructor to create a valid object
     *
     * @param string $strSystemid (use "" on new objects)
     */
    public function __construct($strSystemid = "") {
        $arrModul = array();
        $arrModul["name"] 				= "modul_pages";
		$arrModul["moduleId"] 			= _pages_folder_id_;
		$arrModul["modul"]				= "pages";

		//base class
		parent::__construct($arrModul, $strSystemid);

		//init current object
		if($strSystemid != "")
		    $this->initObject();
    }


     /**
     * @see class_model::getObjectTables();
     * @return array
     */
    protected function getObjectTables() {
        return array();
    }

    /**
     * @see class_model::getObjectDescription();
     * @return string
     */
    protected function getObjectDescription() {
        return $this->getStrName();
    }

    /**
     * Initalises the current object, if a systemid was given
     *
     */
    public function initObject() {
        $strQuery = "SELECT * FROM "._dbprefix_."system WHERE system_id='".$this->objDB->dbsafeString($this->getSystemid())."'";
        $arrRow = $this->objDB->getRow($strQuery);
        if(count($arrRow) > 0) {
            $this->setStrName($arrRow["system_comment"]);
            $this->strOldName = $this->getStrName();
        }
    }

    /**
     * Updates the current object to the database
     *
     * @return bool
     */
    protected function updateStateToDb() {
        //create change-logs
        $objChanges = new class_modul_system_changelog();
        $objChanges->createLogEntry($this, $this->strActionEdit);
        
        class_logger::getInstance()->addLogRow("updated folder ".$this->getStrName(), class_logger::$levelInfo);
        return true;
    }

    /**
	 * Returns a list of folders under the given systemid
	 *
	 * @param string $strSystemid
	 * @return mixed
	 * @static
	 */
	public static function getFolderList($strSystemid = "") {
		if(!validateSystemid($strSystemid))
			$strSystemid = class_modul_system_module::getModuleByName("pages")->getSystemid();
            
		//Get all folders
		$strQuery = "SELECT system_id FROM "._dbprefix_."system
		              WHERE system_module_nr="._pages_folder_id_."
		                AND system_prev_id='".dbsafeString($strSystemid)."'
		             ORDER BY system_comment ASC";

		$arrIds = class_carrier::getInstance()->getObjDB()->getArray($strQuery);
		$arrReturn = array();
		foreach($arrIds as $arrOneId)
		    $arrReturn[] = new class_modul_pages_folder($arrOneId["system_id"]);

		return $arrReturn;
	}


	/**
	 * Changes Position of a folder in the system-tree
	 *
	 * @param string $strFolderID
	 * @param string $strNewPrevID
	 * @return bool
	 * @static
	 */
	public static function moveFolder($strFolderID, $strNewPrevID) {

        if(!validateSystemid($strNewPrevID))
            $strNewPrevID = class_modul_system_module::getModuleByName("pages")->getSystemid();

		$strQuery = "UPDATE "._dbprefix_."system
		              SET  system_prev_id='".dbsafeString($strNewPrevID)."'
		              WHERE system_id='".dbsafeString($strFolderID)."'
		                AND system_module_nr="._pages_folder_id_;
		return class_carrier::getInstance()->getObjDB()->_query($strQuery);
	}


	/**
	 * Changes Position of a site in the system-tree
	 *
	 * @param string $strSiteID
	 * @param string $strNewPrevID
	 * @return bool
	 * @static
	 */
	public static function moveSite($strSiteID, $strNewPrevID) {

        if(!validateSystemid($strNewPrevID))
            $strNewPrevID = class_modul_system_module::getModuleByName("pages")->getSystemid();


		$strQuery = "UPDATE "._dbprefix_."system
		              SET system_prev_id='".dbsafeString($strNewPrevID)."'
		              WHERE system_id='".dbsafeString($strSiteID)."'
		              AND system_module_nr="._pages_modul_id_;
		return class_carrier::getInstance()->getObjDB()->_query($strQuery);
	}


	/**
	 * Returns all Pages listed in a given folder
	 *
	 * @param string $strFolderid
	 * @return string
	 * @static
	 */
	public static function getPagesInFolder($strFolderid = "") {
		if(!validateSystemid($strFolderid))
			$strFolderid = class_modul_system_module::getModuleByName("pages")->getSystemid();
            
		$strQuery = "SELECT system_id
						FROM "._dbprefix_."page as page,
							 "._dbprefix_."system as system
						WHERE system.system_prev_id='".dbsafeString($strFolderid)."'
							AND system.system_module_nr="._pages_modul_id_."
							AND system.system_id = page.page_id
							ORDER BY page_name";

		$arrIds = class_carrier::getInstance()->getObjDB()->getArray($strQuery);
		$arrReturn = array();
		foreach($arrIds as $arrOneId)
		    $arrReturn[] = new class_modul_pages_page($arrOneId["system_id"]);

		return $arrReturn;
	}

    /**
     * Returns the list of pages and folders, so containing both object types, being located
     * under a given systemid.
     *
     * @param string $strFolderid
     * @return class_modul_pages_page | class_modul_pages_folder
     */
    public static function getPagesAndFolderList($strFolderid = "") {
        if(!validateSystemid($strFolderid))
			$strFolderid = class_modul_system_module::getModuleByName("pages")->getSystemid();

		$strQuery = "SELECT system_id, system_module_nr
						FROM "._dbprefix_."system
						WHERE system_prev_id='".dbsafeString($strFolderid)."'
							AND (system_module_nr = "._pages_modul_id_." OR system_module_nr = "._pages_folder_id_." )
							ORDER BY system_sort ASC";

		$arrIds = class_carrier::getInstance()->getObjDB()->getArray($strQuery);
		$arrReturn = array();
		foreach($arrIds as $arrOneRecord) {
            if($arrOneRecord["system_module_nr"] == _pages_modul_id_)
                $arrReturn[] = new class_modul_pages_page($arrOneRecord["system_id"]);
            else if($arrOneRecord["system_module_nr"] == _pages_folder_id_)
                $arrReturn[] = new class_modul_pages_folder($arrOneRecord["system_id"]);
        }

		return $arrReturn;
    }

	/**
	 * Looks up all folders with the given name
	 *
	 * @param string $strName
	 * @return array
	 */
	private function getFoldersByName($strName) {
		//Get all folders
		$strQuery = "SELECT system_id FROM "._dbprefix_."system
		              WHERE system_module_nr="._pages_folder_id_."
		                AND system_comment ='".dbsafeString($strName)."'
		             ORDER BY system_comment ASC";

		$arrIds = class_carrier::getInstance()->getObjDB()->getArray($strQuery);
		$arrReturn = array();
		foreach($arrIds as $arrOneId)
		    $arrReturn[] = new class_modul_pages_folder($arrOneId["system_id"]);

		return $arrReturn;
	}

	/**
	 * Deletes a folder from the systems,
	 * currently just, if the folder is empty
	 *
	 * @return bool
	 */
	public function deleteFolder() {
	    class_logger::getInstance()->addLogRow("deleted folder ".$this->getSystemid(), class_logger::$levelInfo);
	    if(count(class_modul_pages_folder::getFolderList($this->getSystemid())) == 0 && count(class_modul_pages_folder::getPagesInFolder($this->getSystemid())) == 0)
	        return $this->deleteSystemRecord($this->getSystemid());
	    else
	        return false;
	}




    public function getActionName($strAction) {
        if($strAction == $this->strActionEdit)
            return $this->getText("pages_ordner_edit", "pages", "admin");

        return $strAction;
    }

    public function getChangedFields($strAction) {
        if($strAction == $this->strActionEdit) {
            return array(
                array("property" => "foldername",  "oldvalue" => $this->strOldName, "newvalue" => $this->getStrName())
            );
        }
    }

    public function renderValue($strProperty, $strValue) {
        return $strValue;
    }

    public function getClassname() {
        return __CLASS__;
    }

    public function getModuleName() {
        return $this->arrModule["modul"];
    }

    public function getPropertyName($strProperty) {
        return $strProperty;
    }

    public function getRecordName() {
        return class_carrier::getInstance()->getObjText()->getText("change_object_folder", "pages", "admin");
    }

// --- GETTERS / SETTERS --------------------------------------------------------------------------------
    public function getStrName() {
        return $this->strName;
    }

    public function setStrName($strName, $bitSecure = false) {
        //check, if theres already a folder with the same name at the same level
        if($bitSecure)
            $strName = $this->checkFolderName($strName);
        $this->strName = $strName;
    }

    /**
     * Checks, if a foldername already exits ob the current level.
     * Tries to find a valid foldername
     *
     * @param string $strName
     * @param int $intCounter
     * @return string
     */
    private function checkFolderName($strName, $intCounter = 0) {

        if($intCounter != 0)
            $strNameNew = $strName."_".$intCounter;
        else
            $strNameNew = $strName;

        $arrFolders = $this->getFoldersByName($strNameNew);
        if(count($arrFolders) != 0) {
            foreach ($arrFolders as $intKey => $objOneFolder) {
                //not the same folder as the current?
                if(($objOneFolder->getSystemid() != $this->getSystemid()) || $this->getSystemid() == "") {
                    //used on a different level?
                    if($objOneFolder->getPrevId() != $this->strPrevId) {
                        unset($arrFolders[$intKey]);
                    }
                }
                else
                    unset($arrFolders[$intKey]);
            }

            if(count($arrFolders) != 0)
                $strNameNew = $this->checkFolderName($strName, ++$intCounter);
        }
        return $strNameNew;
    }

}
?>