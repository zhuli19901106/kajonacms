<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                  *
********************************************************************************************************/

namespace Kajona\System\System;


/**
 * List of possible http-responsecodes. Can be returned to the client.
 *
 * @package module_system
 */
class HttpResponsetypes
{

    /**
     * Default xml response type
     */
    const STR_TYPE_XML = "Content-Type: text/xml; charset=utf-8";

    /**
     * Default json response type
     */
    const STR_TYPE_JSON = "Content-Type: application/json; charset=utf-8";

    /**
     * Default html response type
     */
    const STR_TYPE_HTML = "Content-Type: text/html; charset=utf-8";

    /**
     * Default csv response type
     */
    const STR_TYPE_CSV = "Content-type: text/csv";

    const STR_TYPE_JPEG = "Content-type: image/jpeg";

    const STR_TYPE_PNG = "Content-type: image/png";

    const STR_TYPE_GIF = "Content-type: image/gif";

    const STR_TYPE_PHAR = "Content-type: application/phar";

}
