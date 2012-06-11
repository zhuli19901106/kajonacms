<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_modul_postacomment_portal_xml.php 3962 2011-07-03 12:10:54Z sidler $						*
********************************************************************************************************/

/**
 * Portal-class of the postacomment-module
 * Serves xml-requests, e.g. saves a sent comment
 *
 * @package modul_postacomment
 * @author sidler@mulchprod.de
 */
class class_module_postacomment_portal_xml extends class_portal implements interface_xml_portal {

    private $strErrors;

	/**
	 * Constructor
	 */
	public function __construct() {
        $this->setArrModuleEntry("moduleId", _postacomment_modul_id_);
        $this->setArrModuleEntry("modul", "postacomment");
        parent::__construct();
	}


	/**
	 * saves a post in the database and returns the post as html.
	 * In case of missing fields, the form is returned again
	 *
	 * @return string
     * @permissons right1
	 */
	protected function actionSavePost() {
	    $strReturn = "";

	    $strXMLContent = "";

        //validate needed fields
        if(!$this->validateForm()) {
            //Create form to reenter values
            $strTemplateID = $this->objTemplate->readTemplate("/module_postacomment/".$this->getParam("comment_template"), "postacomment_form");
            $arrForm = array();
            $arrForm["formaction"] = getLinkPortalHref($this->getPagename(), "", "postComment", "", $this->getSystemid());
            $arrForm["comment_name"] = $this->getParam("comment_name");
            $arrForm["comment_subject"] = $this->getParam("comment_subject");
            $arrForm["comment_message"] = $this->getParam("comment_message");
            $arrForm["comment_template"] = $this->getParam("comment_template");
            $arrForm["comment_systemid"] = $this->getParam("comment_systemid");
            $arrForm["comment_page"] = $this->getParam("comment_page");
            $arrForm["validation_errors"] = $this->strErrors;

            //texts
            $arrForm["postacomment_write_new"] = $this->getLang("postacomment_write_new");
            $arrForm["form_name_label"] = $this->getLang("form_name_label");
            $arrForm["form_subject_label"] = $this->getLang("form_subject_label");
            $arrForm["form_message_label"] = $this->getLang("form_message_label");
            $arrForm["form_captcha_label"] = $this->getLang("commons_captcha");
            $arrForm["form_captcha_reload_label"] = $this->getLang("commons_captcha_reload");
            $arrForm["form_submit_label"] = $this->getLang("form_submit_label");

            $strXMLContent .= $this->fillTemplate($arrForm, $strTemplateID);
        }
        else {
            //save the post to the db
            //pageid or systemid to filter?
            $strSystemidfilter = $this->getParam("comment_systemid");
            $strPagefilter = class_module_pages_page::getPageByName($this->getParam("comment_page"))->getSystemid();

            $objPost = new class_module_postacomment_post();
            $objPost->setStrUsername($this->getParam("comment_name"));
            $objPost->setStrTitle($this->getParam("comment_subject"));
            $objPost->setStrComment($this->getParam("comment_message"));

            $objPost->setStrAssignedPage($strPagefilter);
            $objPost->setStrAssignedSystemid($strSystemidfilter);
            $objPost->setStrAssignedLanguage($this->getStrPortalLanguage());

            $objPost->updateObjectToDb();
            $this->flushPageFromPagesCache($this->getPagename());

            $strMailtext = $this->getLang("new_comment_mail")."\r\n\r\n".$objPost->getStrComment()."\r\n";
            $strMailtext .= getLinkAdminHref("postacomment", "edit", "&systemid=".$objPost->getSystemid(), false);
            $objMessageHandler = new class_module_messaging_messagehandler();
            $arrGroups = array();
            $allGroups = class_module_user_group::getAllGroups();
            foreach($allGroups as $objOneGroup) {
                if(class_rights::getInstance()->checkPermissionForGroup($objOneGroup->getSystemid(), class_rights::$STR_RIGHT_EDIT, $this->getObjModule()->getSystemid()))
                    $arrGroups[] = $objOneGroup;
            }
            $objMessageHandler->sendMessage($strMailtext, $arrGroups, new class_messageprovider_postacomment());


            //reinit post -> encoded entities
            $objPost->initObject();


            //load the post as a new post to add it at top of the list
            $arrOnePost = array();
            $arrOnePost["postacomment_post_name"] = $objPost->getStrUsername();
            $arrOnePost["postacomment_post_subject"] = $objPost->getStrTitle();
            $arrOnePost["postacomment_post_message"] = $objPost->getStrComment();
            $arrOnePost["postacomment_post_systemid"] = $objPost->getSystemid();
            $arrOnePost["postacomment_post_date"] = timeToString($objPost->getIntDate(), true);

            $strTemplateID = $this->objTemplate->readTemplate("/module_postacomment/".$this->getParam("comment_template"), "postacomment_post");
            $strXMLContent .= $this->fillTemplate($arrOnePost, $strTemplateID);
        }

        class_xml::setBitSuppressXmlHeader(true);
	    $strReturn .= $this->createPostCommentXML($strXMLContent);

        return $strReturn;
	}


	private function createPostCommentXML($strContent) {
        $strReturn = "";

        //$strReturn .= "<postacomment>\n";
        //placing a html-part, so no xmlSafeString needed
        //$strReturn .= xmlSafeString($strContent);
        $strReturn .= $strContent;

	    //$strReturn .= "</postacomment>";
        return $strReturn;
	}


    /**
	 * Validates the form data provided by the user
	 *
	 * @return bool
	 */
	public function validateForm() {
	    $bitReturn = true;

	    $strTemplateId = $this->objTemplate->readTemplate("/module_postacomment/".$this->getParam("comment_template"), "validation_error_row");
	    if(uniStrlen($this->getParam("comment_name")) < 2) {
	        $bitReturn = false;
	        $this->strErrors .= $this->fillTemplate(array("error" => $this->getLang("validation_name")), $strTemplateId);
	    }
	    if(uniStrlen($this->getParam("comment_message")) < 2) {
	        $bitReturn = false;
	        $this->strErrors .= $this->fillTemplate(array("error" => $this->getLang("validation_message")), $strTemplateId);
	    }
	    if($this->objSession->getCaptchaCode() != $this->getParam("form_captcha") || $this->getParam("form_captcha") == "") {
	        $bitReturn = false;
	        $this->strErrors .= $this->fillTemplate(array("error" => $this->getLang("validation_code")), $strTemplateId);
	    }
	    return $bitReturn;
	}
}
