<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                         *
********************************************************************************************************/

namespace Kajona\System\System;

use ReflectionClass;


/**
 * The pluginmanager is a central object used to load implementers of GenericPluginInterface.
 * Plugins identify themselves using a plugin / extension point key.
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 4.5
 */
class Pluginmanager
{

    /**
     * @var string[][]
     */
    private static $arrPluginClasses = array();

    private $strSearchPath = "/system";
    private $strPluginPoint = "";

    /**
     * @param string $strPluginPoint
     * @param string $strSearchPath
     */
    public function __construct($strPluginPoint, $strSearchPath = "/system")
    {
        $this->strPluginPoint = $strPluginPoint;
        $this->strSearchPath = $strSearchPath;
    }


    /**
     * This method returns all plugins registered for the current extension point searching at the predefined path.
     * By default, new instances of the classes are returned. If the implementing
     * class requires specific constructor arguments, pass them as the second argument and they will be
     * used during instantiation.
     *
     * @param array $arrConstructorArguments
     *
     * @static
     * @return GenericPluginInterface[]
     */
    public function getPlugins($arrConstructorArguments = array())
    {
        //load classes in passed-folders
        $strKey = md5($this->strSearchPath.$this->strPluginPoint);
        if (!array_key_exists($strKey, self::$arrPluginClasses)) {
            $strPluginPoint = $this->strPluginPoint;
            $arrClasses = Resourceloader::getInstance()->getFolderContent($this->strSearchPath, array(".php"), false, null,
                function (&$strOneFile, $strPath) use ($strPluginPoint, $arrConstructorArguments) {

                    $strClassname = Classloader::getInstance()->getClassnameFromFilename($strPath);

                    if ($strClassname == null) {
                        $strOneFile = null;
                        return;
                    }

                    $objReflection = new ReflectionClass($strClassname);

                    if ($objReflection->isInstantiable() && $objReflection->implementsInterface("Kajona\\System\\System\\GenericPluginInterface")) {
                        if ($objReflection->hasMethod("getExtensionName") && $objReflection->getMethod("getExtensionName")->invoke(null) == $strPluginPoint) {
                            $strOneFile = $strClassname;
                            return;
                        }
                    }

                    $strOneFile = null;

                });


            $arrClasses = array_filter($arrClasses, function ($strClass) {
                return $strClass !== null;
            });

            self::$arrPluginClasses[$strKey] = $arrClasses;
        }

        $arrReturn = array();
        foreach (self::$arrPluginClasses[$strKey] as $strOneClass) {
            $objReflection = new ReflectionClass($strOneClass);
            if (count($arrConstructorArguments) > 0) {
                $arrReturn[] = $objReflection->newInstanceArgs($arrConstructorArguments);
            }
            else {
                $arrReturn[] = $objReflection->newInstance();
            }
        }

        return $arrReturn;
    }

}

