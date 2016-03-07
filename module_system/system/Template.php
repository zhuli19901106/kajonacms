<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2015 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                           *
********************************************************************************************************/

namespace Kajona\System\System;

use Kajona\System\System\Scriptlets\ScriptletXConstants;


/**
 * This class does all the template stuff as loading, parsing, etc..
 *
 * @package module_system
 * @author sidler@mulchprod.de
 *
 */
class Template
{

    const INT_ELEMENT_MODE_MASTER = 1;
    const INT_ELEMENT_MODE_REGULAR = 0;


    private $strTempTemplate = "";

    private static $objTemplate = null;


    /** @var array for backwards compatibility */
    private $arrTemplateIdMap = array();


    /** @var  TemplateFileParser */
    private $objFileParser;
    /** @var  TemplateSectionParser */
    private $objSectionParser;

    /** @var  TemplatePlaceholderParser */
    private $objPlaceholderParser;

    /** @var  TemplateBlocksParser */
    private $objBlocksParser;

    /**
     * @inheritDoc
     */
    private function __construct()
    {
        $this->objFileParser = new TemplateFileParser();
        $this->objSectionParser = new TemplateSectionParser();
        $this->objPlaceholderParser = new TemplatePlaceholderParser();
        $this->objBlocksParser = new TemplateBlocksParser();
    }


    /**
     * Returns one instance of the template object, using a singleton pattern
     *
     * @return Template The template object
     */
    public static function getInstance()
    {
        if (self::$objTemplate == null) {
            self::$objTemplate = new Template();
        }

        return self::$objTemplate;
    }

    /**
     * Reads a template from the filesystem
     *
     * @param string $strName
     * @param string $strSection
     * @param bool $bitForce Force the passed template name, not adding the current area
     * @param bool $bitThrowErrors If set true, the method throws exceptions in case of errors
     *
     * @return string The identifier for further actions
     * @throws Exception
     *
     * @deprecated use the direct  fill / parse methods instead
     */
    public function readTemplate($strName, $strSection = "", $bitForce = false, $bitThrowErrors = false)
    {

        $strTemplate = $this->objFileParser->readTemplate($strName);

        if ($strSection != "") {
            $strTemplate = $this->objSectionParser->readSection($strTemplate, $strSection);
        }


        $strHash = md5($strName.$strSection);
        $this->arrTemplateIdMap[$strHash] = $strTemplate;

        return $strHash;
    }



    public function parsePageTemplateString($strContent, $intMode = Template::INT_ELEMENT_MODE_REGULAR)
    {


        //read top level placeholder
        $arrPlaceholder = $this->objPlaceholderParser->getElements($this->removeSection($strContent, TemplateKajonaSections::BLOCKS), $intMode);
        $objRoot = new TemplateBlockContainer(TemplateKajonaSections::ROOT, "", "", "", "");
        $objRoot->setArrPlaceholder($arrPlaceholder);

        //fetch blocks sections
        $arrBlocksSections = $this->objBlocksParser->readBlocks($strContent, TemplateKajonaSections::BLOCKS);
        $objRoot->setArrBlocks($arrBlocksSections);

        //fetch block sections
        foreach($arrBlocksSections as $objOneBlock) {
            $arrBlockSections = $this->objBlocksParser->readBlocks($objOneBlock->getStrContent(), TemplateKajonaSections::BLOCK);

            $objOneBlock->setArrBlocks($arrBlockSections);

            //fetch elements per block section
            foreach($arrBlockSections as $objOneBlockSection) {
                $arrElements = $this->objPlaceholderParser->getElements($objOneBlockSection->getStrContent(), $intMode);
                $objOneBlockSection->setArrPlaceholder($arrElements);
            }
        }

        return $objRoot;
    }

    public function parsePageTemplate($strName, $intMode = Template::INT_ELEMENT_MODE_REGULAR)
    {
        $strTemplate = $this->objFileParser->readTemplate($strName);
        return $this->parsePageTemplateString($strTemplate, $intMode);
    }


    public function getBlocksElementsFromTemplate($strTemplateFile, $strSection = "")
    {
        $strTemplate = $this->objFileParser->readTemplate($strTemplateFile);

        if ($strSection != "") {
            $strTemplate = $this->objSectionParser->readSection($strTemplate, $strSection);
        }

        return $this->objBlocksParser->readBlocks($strTemplate, TemplateKajonaSections::BLOCKS);
    }

    public function getBlockElementsFromBlock($strBlock)
    {
        return $this->objBlocksParser->readBlocks($strBlock, TemplateKajonaSections::BLOCK);
    }

    /**
     * Helper to parse a single section out of a given template
     *
     * @param $strTemplate
     * @param $strSection
     *
     * @return string|null
     */
    public function getSectionFromTemplate($strTemplate, $strSection, $bitKeepSectionTag = false)
    {
        return $this->objSectionParser->readSection($strTemplate, $strSection, $bitKeepSectionTag);
    }


    /**
     * Fills a template with values passed in an array.
     * As an optional parameter an instance of LangWrapper can be passed
     * to fill placeholders matching the schema %%lang_...%% automatically.
     *
     * @param mixed $arrContent
     * @param string $strIdentifier
     * @param bool $bitRemovePlaceholder
     *
     * @return string The filled template
     */
    public function fillTemplate($arrContent, $strIdentifier, $bitRemovePlaceholder = true)
    {
        if (array_key_exists($strIdentifier, $this->arrTemplateIdMap)) {
            $strTemplate = (string)$this->arrTemplateIdMap[$strIdentifier];
        }
        else {
            $strTemplate = "Load template first!";
        }

        if (count($arrContent) >= 1) {
            foreach ($arrContent as $strPlaceholder => $strContent) {
                $strTemplate = str_replace("%%".$strPlaceholder."%%", $strContent."%%".$strPlaceholder."%%", $strTemplate);
            }
        }

        if ($bitRemovePlaceholder) {
            $strTemplate = $this->objPlaceholderParser->deletePlaceholder($strTemplate);
        }
        return $strTemplate;
    }

    /**
     * Fills a template with values passed in an array.
     * As an optional parameter an instance of LangWrapper can be passed
     * to fill placeholders matching the schema %%lang_...%% automatically.
     *
     * @param $arrPlaceholderContent
     * @param $strTemplateFile
     * @param string $strSection
     * @param bool $bitRemovePlaceholder
     *
     * @return string The filled template
     */
    public function fillTemplateFile($arrPlaceholderContent, $strTemplateFile, $strSection = "", $bitRemovePlaceholder = true)
    {

        $strTemplate = $this->objFileParser->readTemplate($strTemplateFile);

        if ($strSection != "") {
            $strTemplate = $this->objSectionParser->readSection($strTemplate, $strSection);
        }

        foreach ($arrPlaceholderContent as $strPlaceholder => $strContent) {
            $strTemplate = str_replace("%%".$strPlaceholder."%%", $strContent."%%".$strPlaceholder."%%", $strTemplate);
        }

        if ($bitRemovePlaceholder) {
            $strTemplate = $this->objPlaceholderParser->deletePlaceholder($strTemplate);
        }
        return $strTemplate;
    }


    public function fillBlocksToTemplateFile($arrBlocks, $strTemplateFile, $strBlocksDefinition = TemplateKajonaSections::BLOCKS)
    {
        return $this->objBlocksParser->fillBlocks($strTemplateFile, $arrBlocks, $strBlocksDefinition);
    }


    public function deleteBlocksFromTemplate($strTemplate, $strBlockDefinition = TemplateKajonaSections::BLOCKS)
    {
        foreach($this->objBlocksParser->readBlocks($strTemplate, $strBlockDefinition) as $objOneContainer) {
            $strTemplate = uniStrReplace($objOneContainer->getStrFullSection(), "", $strTemplate);
        }
        return $strTemplate;
    }


    /**
     * Fills the current temp-template with the passed values.
     * <b>Make sure to have the wanted template loaded before by using setTemplate()</b>
     *
     * @param mixed $arrContent
     * @param bool $bitRemovePlaceholder
     *
     * @return string The filled template
     * @deprecated use setTemplate() and fillTemplate() instead
     */
    public function fillCurrentTemplate($arrContent, $bitRemovePlaceholder = true)
    {
        $strIdentifier = $this->setTemplate($this->strTempTemplate);
        return $this->fillTemplate($arrContent, $strIdentifier, $bitRemovePlaceholder);
    }


    /**
     * Replaces constants in the template set by setTemplate()
     *
     * @deprecated use scriptlets instead
     */
    public function fillConstants()
    {
        $objConstantScriptlet = new ScriptletXConstants();
        $this->strTempTemplate = $objConstantScriptlet->processContent($this->strTempTemplate);
    }

    /**
     * Deletes placeholder in the template set by setTemplate()
     *
     * @deprecated
     */
    public function deletePlaceholder()
    {
        $this->strTempTemplate = $this->objPlaceholderParser->deletePlaceholder($this->strTempTemplate);
    }


    /**
     * Returns the template set by setTemplate() and sets its back to ""
     *
     * @return string
     * @deprecated
     */
    public function getTemplate()
    {
        $strTemp = $this->strTempTemplate;
        $this->strTempTemplate = "";
        return $strTemp;
    }

    /**
     * Checks if the template referenced by the identifier contains the placeholder provided
     * by the second param.
     *
     * @param string $strIdentifier
     * @param string $strPlaceholdername
     *
     * @return bool
     * @deprecated replaced by containsPlaceholder
     * @see Template::containsPlaceholder
     */
    public function containesPlaceholder($strIdentifier, $strPlaceholdername)
    {
        return $this->containsPlaceholder($strIdentifier, $strPlaceholdername);
    }


    /**
     * Checks if the template referenced by the identifier contains the placeholder provided
     * by the second param.
     *
     * @param string $strIdentifier
     * @param string $strPlaceholdername
     *
     * @return bool
     * @deprecated
     */
    public function containsPlaceholder($strIdentifier, $strPlaceholdername)
    {
        return (isset($this->arrTemplateIdMap[$strIdentifier])
            && $this->objPlaceholderParser->containsPlaceholder($this->arrTemplateIdMap[$strIdentifier], $strPlaceholdername));
    }

    public function providesPlaceholder($strTemplate, $strSection)
    {
        return $this->objSectionParser->containsSection($this->objFileParser->readTemplate($strTemplate), $strSection);
    }

    /**
     * Checks if the template referenced by the given identifier provides the section passed.
     *
     * @param $strIdentifier
     * @param $strSection
     *
     * @return bool
     */
    public function containsSection($strIdentifier, $strSection)
    {
        return (isset($this->arrTemplateIdMap[$strIdentifier])
            && $this->objSectionParser->containsSection($this->arrTemplateIdMap[$strIdentifier], $strSection));
    }

    /**
     * Removes a section with all contents from the given (template) string
     *
     * @param $strTemplate
     * @param $strSection
     *
     * @return string
     */
    public function removeSection($strTemplate, $strSection)
    {
        return $this->objSectionParser->removeSection($strTemplate, $strSection);
    }

    /**
     * Returns the elements in a given template
     *
     * @param string $strIdentifier
     * @param int $intMode 0 = regular page, 1 = master page
     *
     * @return mixed
     */
    public function getElements($strIdentifier, $intMode = 0)
    {

        if (isset($this->arrTemplateIdMap[$strIdentifier])) {
            $strTemplate = $this->arrTemplateIdMap[$strIdentifier];
        }
        else {
            return array();
        }

        $strTemplate = $this->removeSection($strTemplate, TemplateKajonaSections::BLOCKS);

        return $this->objPlaceholderParser->getElements($strTemplate, $intMode);
    }

    /**
     * Sets the passed template as the current temp-template
     *
     * @param string $strTemplate
     *
     * @return string
     * @deprecated
     */
    public function setTemplate($strTemplate)
    {
        $this->strTempTemplate = $strTemplate;
        $strIdentifier = generateSystemid();
        $this->arrTemplateIdMap[$strIdentifier] = $strTemplate;
        return $strIdentifier;
    }

    /**
     * @param $strTemplateId
     *
     * @return bool
     * @deprecated
     */
    public function isValidTemplate($strTemplateId)
    {
        return isset($this->arrTemplateIdMap[$strTemplateId]) && $this->arrTemplateIdMap[$strTemplateId] != "";
    }

    /**
     * Returns the number of cached template sections
     *
     * @return int
     * @deprecated
     */
    public function getNumberCacheSize()
    {
        return count($this->arrTemplateIdMap);
    }
}
