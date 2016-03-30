<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                *
********************************************************************************************************/


namespace Kajona\News\System;

use Kajona\System\System\FilterBase;

/**
 * Class NewsCategoryFilter
 *
 * @package Kajona\News\System
 * @author stefan.meyer1@yahoo.de
 * @module news
 */
class NewsCategoryFilter extends FilterBase
{
    /**
     * @tableColumn news_category.news_cat_title
     * @fieldType Kajona\System\Admin\Formentries\FormentryText
     */
    private $strTitle;

    /**
     * @return mixed
     */
    public function getStrTitle()
    {
        return $this->strTitle;
    }

    /**
     * @param mixed $strTitle
     */
    public function setStrTitle($strTitle)
    {
        $this->strTitle = $strTitle;
    }
}