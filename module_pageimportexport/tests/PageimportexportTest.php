<?php
namespace Kajona\Pageimportexport\Tests;

use Kajona\Pageimportexport\Admin\Systemtasks\SystemtaskPageexport;
use Kajona\Pageimportexport\Admin\Systemtasks\SystemtaskPageimport;
use Kajona\Pages\Admin\Elements\ElementrichtextAdmin;
use Kajona\Pages\System\PagesPage;
use Kajona\Pages\System\PagesPageelement;
use Kajona\System\System\Carrier;
use Kajona\System\System\Classloader;
use Kajona\System\System\Database;
use Kajona\System\System\OrmRowcache;
use Kajona\System\System\Resourceloader;
use Kajona\System\Tests\Testbase;

class PageimportexportTest extends Testbase
{
    protected function tearDown()
    {
        parent::tearDown();
        Carrier::getInstance()->getObjRights()->setBitTestMode(false);
    }


    public function testImportExport()
    {

        Carrier::getInstance()->getObjRights()->setBitTestMode(true);
        $strName = generateSystemid();
        $strBrowsername = generateSystemid();
        $strSeoString = generateSystemid();
        $strDesc = generateSystemid();


        $objPage = new PagesPage();
        $objPage->setStrName($strName);
        $objPage->setStrBrowsername($strBrowsername);
        $objPage->setStrSeostring($strSeoString);
        $objPage->setStrDesc($strDesc);
        $objPage->setStrTemplate("standard.tpl");
        $objPage->updateObjectToDb();

        $strPagesystemid = $objPage->getSystemid();


        $objPagelement = new PagesPageelement();
        $objPagelement->setStrPlaceholder("text_richtext");
        $objPagelement->setStrName("text");
        $objPagelement->setStrElement("richtext");
        $objPagelement->updateObjectToDb($objPage->getSystemid());

        $objElement = new ElementRichtextAdmin($objPagelement->getSystemid());
        $objElement->setSystemid($objPagelement->getSystemid());
        $objElement->loadElementData();
        $objElement->setStrText("para_title");
        $objElement->updateForeignElement();


        Carrier::getInstance()->setParam("pageExport", $strName);
        $objPageExport = new SystemtaskPageexport();
        $objPageExport->executeTask();

        $objPage->deleteObjectFromDatabase();
        OrmRowcache::flushCache();
        Database::getInstance()->flushQueryCache();

        $this->assertNull(PagesPage::getPageByName($strName));

        $this->assertFileExists(_realpath_._projectpath_."/temp/".$strPagesystemid.".xml");

        Carrier::getInstance()->setParam("pageimport_file", _projectpath_."/temp/".$strPagesystemid.".xml");
        $objImport = new SystemtaskPageimport();
        $objImport->executeTask();

        $objPage = PagesPage::getPageByName($strName);
        $this->assertNotNull($objPage);

        $this->assertEquals($objPage->getStrName(), $strName);
        $this->assertEquals($objPage->getStrDesc(), $strDesc);
        $this->assertEquals($objPage->getStrSeostring(), $strSeoString);
        $this->assertEquals($objPage->getStrBrowsername(), $strBrowsername);

        $objElements = PagesPageelement::getAllElementsOnPage($objPage->getSystemid());

        $this->assertEquals(1, count($objElements));
        $objElements = $objElements[0];

        $strPath = Resourceloader::getInstance()->getPathForFile("/admin/elements/".$objElements->getStrClassAdmin());
        $strClass = Classloader::getInstance()->getClassnameFromFilename($strPath);

        $this->assertEquals($strClass, "Kajona\\Pages\\Admin\\Elements\\ElementRichtextAdmin");


        /** @var ElementRichtextAdmin $objElement */
        $objElement = $objElements->getConcreteAdminInstance();
        $objElement->setSystemid($objElements->getSystemid());
        $objElement->loadElementData();

        $this->assertEquals("para_title", $objElement->getStrText());


        $objPage->deleteObjectFromDatabase();
    }
}

