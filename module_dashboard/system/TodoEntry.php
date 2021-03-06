<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                        *
********************************************************************************************************/

namespace Kajona\Dashboard\System;

/**
 * Object which represents a todo entry
 *
 * @package module_dashboard
 * @author christoph.kappestein@gmail.com
 */
class TodoEntry extends EntryAbstract
{
    /**
     * @var array
     */
    protected $arrModuleNavi;

    /**
     * @return array
     */
    public function getArrModuleNavi()
    {
        return $this->arrModuleNavi;
    }

    /**
     * @param array $arrModuleNavi
     */
    public function setArrModuleNavi(array $arrModuleNavi)
    {
        $this->arrModuleNavi = $arrModuleNavi;
    }
}
