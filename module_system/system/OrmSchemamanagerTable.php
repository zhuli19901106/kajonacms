<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                        *
********************************************************************************************************/

namespace Kajona\System\System;


/**
 * Data-object used by the schema-manager internally.
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 4.6
 */
class OrmSchemamanagerTable
{

    /**
     * @var OrmSchemamanagerRow[]
     */
    private $arrRows = array();

    private $bitTxSafe = true;

    private $strName = "";

    /**
     * @param string $strName
     * @param bool $bitTxSafe
     */
    public function __construct($strName, $bitTxSafe = true)
    {
        $this->strName = $strName;
        $this->bitTxSafe = $bitTxSafe;
    }

    /**
     * @param OrmSchemamanagerRow[] $arrRows
     */
    public function setArrRows($arrRows)
    {
        $this->arrRows = $arrRows;
    }

    /**
     * @return OrmSchemamanagerRow[]
     */
    public function getArrRows()
    {
        return $this->arrRows;
    }

    /**
     * @param boolean $bitTxSafe
     */
    public function setBitTxSafe($bitTxSafe)
    {
        $this->bitTxSafe = $bitTxSafe;
    }

    /**
     * @return boolean
     */
    public function getBitTxSafe()
    {
        return $this->bitTxSafe;
    }

    public function addRow(OrmSchemamanagerRow $objRow)
    {
        $this->arrRows[] = $objRow;
    }

    /**
     * @param string $strName
     */
    public function setStrName($strName)
    {
        $this->strName = $strName;
    }

    /**
     * @return string
     */
    public function getStrName()
    {
        return $this->strName;
    }


}
