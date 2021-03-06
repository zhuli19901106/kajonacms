<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Facebooklikebox\Installer;

use Kajona\Pages\System\PagesElement;
use Kajona\System\System\InstallerBase;
use Kajona\System\System\InstallerRemovableInterface;
use Kajona\System\System\SystemModule;

/**
 * Installer to install a facebooklikebox-element to use in the portal
 *
 * @author sidler@mulchprod.de
 * @moduleId _facebooklikebox_module_id_
 */
class InstallerFacebooklikebox extends InstallerBase implements InstallerRemovableInterface
{

    /**
     * @inheritdoc
     */
    public function install()
    {
        $strReturn = "";

        //register the module
        $this->registerModule($this->objMetadata->getStrTitle(), _facebooklikebox_module_id_, "", "", $this->objMetadata->getStrVersion(), false);


        //Register the element
        $strReturn .= "Registering facebooklikebox-element...\n";
        //check, if not already existing
        $objElement = PagesElement::getElement($this->objMetadata->getStrTitle());
        if ($objElement == null) {
            $objElement = new PagesElement();
            $objElement->setStrName($this->objMetadata->getStrTitle());
            $objElement->setStrClassAdmin("ElementFacebooklikeboxAdmin.php");
            $objElement->setStrClassPortal("ElementFacebooklikeboxPortal.php");
            $objElement->setIntCachetime(3600 * 24 * 30);
            $objElement->setIntRepeat(0);
            $objElement->setStrVersion($this->objMetadata->getStrVersion());
            $objElement->updateObjectToDb();
            $strReturn .= "Element registered...\n";
        }
        else {
            $strReturn .= "Element already installed!...\n";

            if ($objElement->getStrVersion() < 5) {
                $strReturn .= "Updating element version!...\n";
                $objElement->setStrVersion("5.0");
                $objElement->updateObjectToDb();
            }
        }
        return $strReturn;
    }


    /**
     * @return string
     */
    public function update()
    {
        $strReturn = "";

        $arrModule = SystemModule::getPlainModuleData($this->objMetadata->getStrTitle(), false);
        if ($arrModule["module_version"] == "4.7") {
            $strReturn .= "Updating 4.7 to 5.0...\n";
            $this->updateElementAndModule("5.0");
        }

        return $strReturn;
    }

    /**
     * @inheritdoc
     */
    public function isRemovable()
    {
        return true;
    }


    /**
     * @inheritdoc
     */
    public function remove(&$strReturn)
    {
        return $this->removeModuleAndElement($strReturn);
    }
}
