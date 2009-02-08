<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2009 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                          *
********************************************************************************************************/


include_once(_systempath_."/interface_sc_installer.php");

/**
 * Installer of the filemanager samplecontent
 *
 * @package modul_filemanager
 */
class class_installer_sc_filemanager implements interface_sc_installer  {

    private $objDB;
    private $strContentLanguage;

    /**
     * Does the hard work: installs the module and registers needed constants
     *
     */
    public function install() {
		$strReturn = "";

        $strReturn .= "Creating picture upload folder\n";
            if(!is_dir(_portalpath_."/pics/upload"))
                mkdir(_realpath_."/portal/pics/upload");

            $strReturn .= "Creating new picture repository\n";
            include_once(_systempath_."/class_modul_filemanager_repo.php");
            $objRepo = new class_modul_filemanager_repo();

            if($this->strContentLanguage == "de")
                $objRepo->setStrName("Hochgeladene Bilder");
            else
                $objRepo->setStrName("Picture uploads");

            $objRepo->setStrPath("/portal/pics/upload");
            $objRepo->setStrUploadFilter(".jpg,.png,.gif,.jpeg");
            $objRepo->setStrViewFilter(".jpg,.png,.gif,.jpeg");
            $objRepo->saveObjectToDb();
            $strReturn .= "ID of new repo: ".$objRepo->getSystemid()."\n";
            
        $strReturn .= "Creating file upload folder\n";
            if(!is_dir(_portalpath_."/downloads"))
                mkdir(_realpath_."/portal/downloads");

            $strReturn .= "Creating new file repository\n";
            include_once(_systempath_."/class_modul_filemanager_repo.php");
            $objRepo = new class_modul_filemanager_repo();

            if($this->strContentLanguage == "de")
                $objRepo->setStrName("Hochgeladene Dateien");
            else
                $objRepo->setStrName("File uploads");

            $objRepo->setStrPath("/portal/downloads");
            $objRepo->setStrUploadFilter(".zip,.pdf,.txt");
            $objRepo->setStrViewFilter(".zip,.pdf,.txt");
            $objRepo->saveObjectToDb();
            $strReturn .= "ID of new repo: ".$objRepo->getSystemid()."\n";
		return $strReturn;
    }
    
    public function setObjDb($objDb) {
        $this->objDB = $objDb;
    }
    
    public function setStrContentlanguage($strContentlanguage) {
        $this->strContentLanguage = $strContentlanguage;
    }

    public function getCorrespondingModule() {
        return "filemanager";
    }
    
}
?>