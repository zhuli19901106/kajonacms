<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                               *
********************************************************************************************************/

namespace Kajona\System\System;

use Kajona\Rating\System\RatingRate;


/**
 * Top-level class for all model-classes.
 * Please be aware that all logic located in this class will be moved to Root. This means that this
 * class will become useless. It will remain for API-compatibility but without any logic.
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @deprectated this class will be removed from future releases, all logic will be moved to class root.
 *
 *
 */
abstract class Model extends Root
{


    // --- RATING -------------------------------------------------------------------------------------------
    /**
     * Rating of the current file, if module rating is installed.
     *
     * @param bool $bitRound Rounds the rating or disables rounding
     *
     * @see SortableRatingInterface
     * @return float
     *
     * @todo: with php5.4, ths could be moved to traits
     */
    public function getFloatRating($bitRound = true)
    {
        $floatRating = null;
        $objModule = SystemModule::getModuleByName("rating");
        if ($objModule != null) {
            $objRating = RatingRate::getRating($this->getSystemid());
            if ($objRating != null) {
                $floatRating = $objRating->getFloatRating();
                if ($bitRound) {
                    $floatRating = round($floatRating, 2);
                }
            }
            else {
                $floatRating = 0.0;
            }
        }

        return $floatRating;
    }

    /**
     * Checks if the current user is allowed to rate the file
     *
     * @return bool
     *
     * @todo: with php5.4, ths could be moved to traits
     */
    public function isRateableByUser()
    {
        $bitReturn = false;
        $objModule = SystemModule::getModuleByName("rating");
        if ($objModule != null) {
            $objRating = RatingRate::getRating($this->getSystemid());
            if ($objRating != null) {
                $bitReturn = $objRating->isRateableByCurrentUser();
            }
            else {
                $bitReturn = true;
            }
        }

        return $bitReturn;
    }

    /**
     * Number of rating for the current file
     *
     * @see SortableRatingInterface
     * @return int
     *
     * @todo: with php5.4, ths could be moved to traits
     */
    public function getIntRatingHits()
    {
        $intHits = 0;
        $objModule = SystemModule::getModuleByName("rating");
        if ($objModule != null) {
            $objRating = RatingRate::getRating($this->getSystemid());
            if ($objRating != null) {
                $intHits = $objRating->getIntHits();
            }
            else {
                return 0;
            }
        }

        return $intHits;
    }

}
