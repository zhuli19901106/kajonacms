//   (c) 2004-2006 by MulchProductions, www.mulchprod.de
//   (c) 2007-2016 by Kajona, www.kajona.de
//       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt
//       $Id$

if (typeof KAJONA == "undefined") {
	var KAJONA = {
		util: {},
		portal: {
			lang: {}
		},
		admin: {
			lang: {},
            forms: {}
		}
	};
}


/*
 * -------------------------------------------------------------------------
 * Global functions
 * -------------------------------------------------------------------------
 */


/**
 * Function to get the element from the current opener.
 *
 * @param strElementId
 * @returns {*}
 */
KAJONA.util.getElementFromOpener = function(strElementId) {
    if (window.opener) {
        return $('#' + strElementId, window.opener.document);
    } else if (parent){
        return $('#' + strElementId, parent.document);
    }
    else {
        return $('#' + strElementId);
    }
};

/**
 * Function to evaluate the script-tags in a passed string, e.g. loaded by an ajax-request
 *
 * @param {String} scripts
 * @see http://wiki.ajax-community.de/know-how:nachladen-von-javascript
 **/
KAJONA.util.evalScript = function (scripts) {
	try {
        if(scripts != '')	{
            var script = "";
			scripts = scripts.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function() {
                 if (scripts !== null)
                         script += arguments[1] + '\n';
                return '';
            });
			if(script)
                (window.execScript) ? window.execScript(script) : window.setTimeout(script, 0);
		}
		return false;
	}
	catch(e) {
        alert(e);
	}
};

KAJONA.util.isTouchDevice = function() {
    return !!('ontouchstart' in window) ? 1 : 0;
};


/**
 * Checks if the given array contains the given string
 *
 * @param {String} strNeedle
 * @param {String[]} arrHaystack
 */
KAJONA.util.inArray = function (strNeedle, arrHaystack) {
    for (var i = 0; i < arrHaystack.length; i++) {
        if (arrHaystack[i] == strNeedle) {
            return true;
        }
    }
    return false;
};

/**
 * Used to show/hide an html element
 *
 * @param {String} strElementId
 * @param {Function} objCallbackVisible
 * @param {Function} objCallbackInvisible
 */
KAJONA.util.fold = function (strElementId, objCallbackVisible, objCallbackInvisible) {
	var $element = $('#'+strElementId);
	if ($element.hasClass("folderHidden")) 	{
        $element.removeClass("folderHidden");
        $element.addClass("folderVisible");
		if ($.isFunction(objCallbackVisible)) {
			objCallbackVisible(strElementId);
		}
    }
    else {
        $element.removeClass("folderVisible");
        $element.addClass("folderHidden");
		if ($.isFunction(objCallbackInvisible)) {
			objCallbackInvisible(strElementId);
		}
    }
};

/**
 * Used to show/hide an html element and switch an image (e.g. a button)
 *
 * @param {String} strElementId
 * @param {String} strImageId
 * @param {String} strImageVisible
 * @param {String} strImageHidden
 */
KAJONA.util.foldImage = function (strElementId, strImageId, strImageVisible, strImageHidden) {
	var element = document.getElementById(strElementId);
	var image = document.getElementById(strImageId);
	if (element.style.display == 'none') 	{
		element.style.display = 'block';
		image.src = strImageVisible;
    }
    else {
    	element.style.display = 'none';
    	image.src = strImageHidden;
    }
};

KAJONA.util.setBrowserFocus = function (strElementId) {
	$(function() {
		try {
		    focusElement = $("#"+strElementId);
		    if (focusElement.hasClass("inputWysiwyg")) {
		    	CKEDITOR.config.startupFocus = true;
		    } else {
		        focusElement.focus();
		    }
		} catch (e) {}
	});
};

/**
 * some functions to track the mouse position and move an element
 * @deprecated will be removed with Kajona 3.4 or 3.5, use YUI Panel instead
 */
KAJONA.util.mover = (function() {
	var currentMouseXPos;
	var currentMouseYPos;
	var objToMove = null;
	var objDiffX = 0;
	var objDiffY = 0;

	function checkMousePosition(e) {
		if (document.all) {
			currentMouseXPos = event.clientX + document.body.scrollLeft;
			currentMouseYPos = event.clientY + document.body.scrollTop;
		} else {
			currentMouseXPos = e.pageX;
			currentMouseYPos = e.pageY;
		}

		if (objToMove != null) {
			objToMove.style.left = currentMouseXPos - objDiffX + "px";
			objToMove.style.top = currentMouseYPos - objDiffY + "px";
		}
	}

	function setMousePressed(obj) {
		objToMove = obj;
		objDiffX = currentMouseXPos - objToMove.offsetLeft;
		objDiffY = currentMouseYPos - objToMove.offsetTop;
	}

	function unsetMousePressed() {
		objToMove = null;
	}


	//public variables and methods
	return {
		checkMousePosition : checkMousePosition,
		setMousePressed : setMousePressed,
		unsetMousePressed : unsetMousePressed
	}
}());

/**
 * Converts a string into an integer representation of the string regarding thousands and decimal separator.
 * e.g. "1.000.000" => "1000000"
 * e.g. "7.000" => "7000"
 *
 * @param objValue
 * @param strStyleThousand
 * @param strStyleDecimal
 * @returns {string}
 */
KAJONA.util.convertValueToInt = function(objValue, strStyleThousand, strStyleDecimal) {
    var strValue = objValue+"";

    var strRegExpThousand = new RegExp("\\"+strStyleThousand, 'g')
    strValue = strValue.replace(strRegExpThousand, "");//remove first thousand separator

    return parseInt(strValue);
};

/**
 * Converts a string into a float representation of the string regarding thousands and decimal separator.
 * e.g. "1.000.000,23" => "1000000.23"
 *
 * @param objValue
 * @param strStyleThousand
 * @param strStyleDecimal
 * @returns {string}
 */
KAJONA.util.convertValueToFloat = function(objValue, strStyleThousand, strStyleDecimal) {
    var strValue = objValue+"";

    var strRegExpThousand = new RegExp("\\"+strStyleThousand, 'g')
    var strRegExpDecimal = new RegExp("\\"+strStyleDecimal, 'g')
    var strRegExpComma = new RegExp("\\,", 'g')

    strValue = strValue.replace(strRegExpThousand, "");//remove first thousand separator
    strValue = strValue.replace(strRegExpComma, ".");//replace decimal with decimal point for db
    strValue = strValue.replace(strRegExpDecimal, ".");//replace decimal with decimal point for db

    return parseFloat(strValue);
};

/**
 * Formats a number into a formatted string
 * @see http://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-money-in-javascript
 *
 * .format(12345678.9, 2, 3, '.', ',');  // "12.345.678,90"
 * .format(123456.789, 4, 4, ' ', ':');  // "12 3456:7890"
 * .format(12345678.9, 0, 3, '-');       // "12-345-679"
 *
 * @param floatValue mixed: number to be formatted
 * @param intDecimalLength integer: length of decimal
 * @param intLengthWholePart integer: length of whole part
 * @param strDelimiterSections mixed: sections delimiter
 * @param strDelimiterDecimal mixed: decimal delimiter
 */
KAJONA.util.formatNumber = function(floatValue, intDecimalLength, intLengthWholePart, strDelimiterSections, strDelimiterDecimal) {
    var re = '\\d(?=(\\d{' + (intLengthWholePart || 3) + '})+' + (intDecimalLength > 0 ? '\\D' : '$') + ')',
        num = floatValue.toFixed(Math.max(0, ~~intDecimalLength));

    return (strDelimiterDecimal ? num.replace('.', strDelimiterDecimal) : num).replace(new RegExp(re, 'g'), '$&' + (strDelimiterSections || ','));
};

/*
 * -------------------------------------------------------------------------
 * Admin-specific functions
 * -------------------------------------------------------------------------
 */

/**
 * Loader for dynamically loading additional js and css files after the onDOMReady event
 */
KAJONA.admin.loader = new KAJONA.util.Loader();


/**
 * Folderview functions
 */
KAJONA.admin.folderview = {
	/**
	 * holds a reference to the ModalDialog
	 */
	dialog: undefined,

	/**
	 * holds CKEditors CKEditorFuncNum parameter to read it again in KAJONA.admin.folderview.fillFormFields()
	 * so we don't have to pass through the param with all requests
	 */
	selectCallbackCKEditorFuncNum: 0,

	/**
	 * To be called when the user selects an page/folder/file out of a folderview dialog/popup
	 * Detects if the folderview is embedded in a dialog or popup to find the right context
     *
     * @param {Array} arrTargetsValues
     * @param {function} objCallback
	 */
	selectCallback: function (arrTargetsValues, objCallback) {
		if (window.opener) {
			window.opener.KAJONA.admin.folderview.fillFormFields(arrTargetsValues);
		} else if (parent) {
			parent.KAJONA.admin.folderview.fillFormFields(arrTargetsValues);
		}

        if ($.isFunction(objCallback)) {
			objCallback();
		}

        this.close();

	},

	/**
	 * fills the form fields with the selected values
	 */
	fillFormFields: function (arrTargetsValues) {
		for (var i in arrTargetsValues) {
	    	if (arrTargetsValues[i][0] == "ckeditor") {
	    		CKEDITOR.tools.callFunction(this.selectCallbackCKEditorFuncNum, arrTargetsValues[i][1]);
	    	} else {
	    		var formField = $("#"+arrTargetsValues[i][0]).get(0);

                if (formField != null) {
                	formField.value = arrTargetsValues[i][1];

                	//fire the onchange event on the form field
                    if (document.createEvent) { //Firefox
                        var evt = document.createEvent("Events");
                        evt.initEvent('change', true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                        formField.dispatchEvent(evt);
                    } else if (document.createEventObject) { //IE
                        var evt = document.createEventObject();
                        formField.fireEvent('onchange', evt);
                    }

                }
	    	}
		}
	},

	/**
	 * fills the form fields with the selected values
	 */
	close: function () {
		if (window.opener) {
			window.close();
		} else if (parent) {
			var context = parent.KAJONA.admin.folderview;
			context.dialog.hide();
			context.dialog.setContentRaw("");
		}
	}
};


KAJONA.admin.tooltip = {
    initTooltip : function() {
        KAJONA.admin.loader.loadFile(['/core/module_system/admin/scripts/qtip2/jquery.qtip.min.js', '/core/module_system/admin/scripts/qtip2/jquery.qtip.min.css'], function() {

            //common tooltips

            $('*[rel=tooltip][title!=""]').qtip({
                position: {
                    viewport: $(window)
                },
                style: {
                    classes: 'qtip-bootstrap'
                }
            });

            //tag tooltips
            $('*[rel=tagtooltip][title!=""]').each( function() {
                $(this).qtip({
                    position: {
                        viewport: $(window)
                    },
                    style: {
                        classes: 'qtip-bootstrap'
                    },
                    content: {
                        text: $(this).attr("title")+"<div id='tags_"+$(this).data('systemid')+"' data-systemid='"+$(this).data('systemid')+"'></div>"
                    },
                    events: {
                        render: function(event, api) {
                            KAJONA.admin.loader.loadFile('/core/module_tags/admin/scripts/tags.js', function() {
                                KAJONA.admin.tags.loadTagTooltipContent($(api.elements.content).find('div').data('systemid'), "", $(api.elements.content).find('div').attr('id'));
                            })
                        }
                    }
                });
            })

        });
    },

    addTooltip : function(objElement, strText) {
        KAJONA.admin.loader.loadFile(['/core/module_system/admin/scripts/qtip2/jquery.qtip.min.js', '/core/module_system/admin/scripts/qtip2/jquery.qtip.min.css'], function() {

            if(strText) {
                $(objElement).qtip({
                    position: {
                        viewport: $(window)
                    },
                    style: {
                        classes: 'qtip-bootstrap'
                    },
                    content : {
                        text: strText
                    }
                });
            }
            else {
                $(objElement).qtip({
                    position: {
                        viewport: $(window)
                    },
                    style: {
                        classes: 'qtip-bootstrap'
                    }
                });
            }
        });
    },

    removeTooltip : function(objElement) {
        $(objElement).qtip('hide');
    }

};



/**
 * switches the edited language in admin
 */
KAJONA.admin.switchLanguage = function(strLanguageToLoad) {
	var url = window.location.href;
	url = url.replace(/(\?|&)language=([a-z]+)/, "");
	if (url.indexOf('?') == -1) {
		window.location.replace(url + '?language=' + strLanguageToLoad);
	} else {
		window.location.replace(url + '&language=' + strLanguageToLoad);
	}
};

/**
 * little helper function for the system right matrix
 */
KAJONA.admin.permissions = {
    checkRightMatrix : function () {
        // mode 1: inheritance
        if (document.getElementById('inherit').checked) {
            // loop over all checkboxes to disable them
            for (var intI = 0; intI < document.forms['rightsForm'].elements.length; intI++) {
                var objCurElement = document.forms['rightsForm'].elements[intI];
                if (objCurElement.type == 'checkbox') {
                    if (objCurElement.id != 'inherit') {
                        objCurElement.disabled = true;
                        objCurElement.checked = false;
                        var strCurId = "inherit," + objCurElement.id;
                        if (document.getElementById(strCurId) != null) {
                            if (document.getElementById(strCurId).value == '1') {
                                objCurElement.checked = true;
                            }
                        }
                    }
                }
            }
        } else {
            // mode 2: no inheritance, make all checkboxes editable
            for (intI = 0; intI < document.forms['rightsForm'].elements.length; intI++) {
                var objCurElement = document.forms['rightsForm'].elements[intI];
                if (objCurElement.type == 'checkbox') {
                    if (objCurElement.id != 'inherit') {
                        objCurElement.disabled = false;
                    }
                }
            }
        }
    },

    toggleMode : null,
    toggleEmtpyRows : function (strVisibleName, strHiddenName, parentSelector) {

        $(parentSelector).each(function() {

            if($(this).find("input:checked").length == 0 && $(this).find("th").length == 0) {
                if(KAJONA.admin.permissions.toggleMode == null) {
                    KAJONA.admin.permissions.toggleMode = $(this).hasClass("hidden") ? "show" : "hide";
                }

                if(KAJONA.admin.permissions.toggleMode == "show") {
                    $(this).removeClass("hidden");
                }
                else {
                    $(this).addClass("hidden");
                }
            }
        });

        KAJONA.admin.permissions.toggleMode = null;

        if($('#rowToggleLink').hasClass("rowsVisible")) {
            $('#rowToggleLink').html(strVisibleName);
            $('#rowToggleLink').removeClass("rowsVisible");
        }
        else {
            $('#rowToggleLink').html(strHiddenName);
            $('#rowToggleLink').addClass("rowsVisible")
        }
    },

    submitForm : function() {
        var objResponse = {
            bitInherited : $("#inherit").is(":checked"),
            arrConfigs : []
        };

        $('#rightsForm table tr input:checked').each(function(){
            if($(this).find("input:checked").length == 0) {
                objResponse.arrConfigs.push($(this).attr('id'));
            }
        });

        $("#responseContainer").html('').addClass("loadingContainer");

        $.ajax({
            url: KAJONA_WEBPATH + '/xml.php?admin=1&module=right&action=saveRights&systemid='+ $('#systemid').val(),
            type: 'POST',
            data: {json: JSON.stringify(objResponse)},
            dataType: 'json'
        }).done(function(data) {
            $("#responseContainer").removeClass("loadingContainer").html(data.message);
        });


        return false;
    }
};

/**
 * General way to display a status message.
 * Therefore, the html-page should provide the following elements as noted as instance-vars:
 * - div,   id: jsStatusBox    				the box to be animated
 * 		 class: jsStatusBoxMessage			class in case of an informal message
 * 		 class: jsStatusBoxError		    class in case of an error message
 * - div,   id: jsStatusBoxContent			the box to place the message-content into
 *
 * Pass a xml-response from a Kajona server to displayXMLMessage() to start the logic
 * or use messageOK() / messageError() passing a regular string
 */
KAJONA.admin.statusDisplay = {
	idOfMessageBox : "jsStatusBox",
	idOfContentBox : "jsStatusBoxContent",
	classOfMessageBox : "jsStatusBoxMessage",
	classOfErrorBox : "jsStatusBoxError",
	timeToFadeOutMessage : 3000,
	timeToFadeOutError   : 5000,
	timeToFadeOut : null,

	/**
	 * General entrance point. Use this method to pass an xml-response from the kajona server.
	 * Tries to find a message- or an error-tag an invokes the corresponding methods
	 *
	 * @param {String} message
	 */
	displayXMLMessage : function(message) {
		//decide, whether to show an error or a message, message only in debug mode
		if(message.indexOf("<message>") != -1 && message.indexOf("<error>") == -1) {
			var intStart = message.indexOf("<message>")+9;
			var responseText = message.substr(intStart, message.indexOf("</message>")-intStart);
			this.messageOK(responseText);
		}

		if(message.indexOf("<error>") != -1) {
			var intStart = message.indexOf("<error>")+7;
			var responseText = message.substr(intStart, message.indexOf("</error>")-intStart);
			this.messageError(responseText);
		}
	},

	/**
	 * Creates a informal message box contaning the passed content
	 *
	 * @param {String} strMessage
	 */
    messageOK : function(strMessage) {
		$("#"+this.idOfMessageBox).removeClass(this.classOfMessageBox).removeClass(this.classOfErrorBox).addClass(this.classOfMessageBox);
		this.timeToFadeOut = this.timeToFadeOutMessage;
		this.startFadeIn(strMessage);
    },

	/**
	 * Creates an error message box containg the passed content
	 *
	 * @param {String} strMessage
	 */
    messageError : function(strMessage) {
        $("#"+this.idOfMessageBox).removeClass(this.classOfMessageBox).removeClass(this.classOfErrorBox).addClass(this.classOfErrorBox);
		this.timeToFadeOut = this.timeToFadeOutError;
		this.startFadeIn(strMessage);
    },

	startFadeIn : function(strMessage) {
		var statusBox = $("#"+this.idOfMessageBox);
		var contentBox = $("#"+this.idOfContentBox);
		contentBox.html(strMessage);
		statusBox.css("display", "").css("opacity", 0.0);

		//place the element at the top of the page
		var screenWidth = $(window).width();
		var divWidth = statusBox.width();
		var newX = screenWidth/2 - divWidth/2;
		var newY = $(window).scrollTop() -2;
        statusBox.css('top', newY);
        statusBox.css('left', newX);

		//start fade-in handler

        KAJONA.admin.statusDisplay.fadeIn();

	},

	fadeIn : function () {
        $("#"+this.idOfMessageBox).animate({opacity: 0.8}, 1000, function() {
            window.setTimeout("KAJONA.admin.statusDisplay.startFadeOut()", KAJONA.admin.statusDisplay.timeToFadeOut);
        });
	},

	startFadeOut : function() {
        $("#"+this.idOfMessageBox).animate(
            { top: -200 },
            1000,
            function() {
                $("#"+this.idOfMessageBox).css("display", "none");
            }
        );

	}
};


/**
 * Functions to execute system tasks
 */
KAJONA.admin.systemtask = {
    executeTask : function(strTaskname, strAdditionalParam, bitNoContentReset) {
        if(bitNoContentReset == null || bitNoContentReset == undefined) {

            if(document.getElementById('taskParamForm') != null) {
                document.getElementById('taskParamForm').style.display = "none";
            }

            jsDialog_0.setTitle(KAJONA_SYSTEMTASK_TITLE);
            jsDialog_0.setContentRaw(kajonaSystemtaskDialogContent);
            $("#"+jsDialog_0.containerId).find("div.modal-dialog").removeClass("modal-lg");
            document.getElementById('systemtaskCancelButton').onclick = this.cancelExecution;
            jsDialog_0.init();
        }

        KAJONA.admin.ajax.genericAjaxCall("system", "executeSystemTask", "&task="+strTaskname+strAdditionalParam, function(data, status, jqXHR) {
            if(status == 'success') {
                var strResponseText = data;

                //parse the response and check if it's valid
                if(strResponseText.indexOf("<error>") != -1) {
                    KAJONA.admin.statusDisplay.displayXMLMessage(strResponseText);
                }
                else if(strResponseText.indexOf("<statusinfo>") == -1) {
                	KAJONA.admin.statusDisplay.messageError("<b>Request failed!</b><br />"+strResponseText);
                }
                else {
                    var intStart = strResponseText.indexOf("<statusinfo>")+12;
                    var strStatusInfo = strResponseText.substr(intStart, strResponseText.indexOf("</statusinfo>")-intStart);

                    //parse text to decide if a reload is necessary
                    var strReload = "";
                    if(strResponseText.indexOf("<reloadurl>") != -1) {
                        intStart = strResponseText.indexOf("<reloadurl>")+11;
                        strReload = strResponseText.substr(intStart, strResponseText.indexOf("</reloadurl>")-intStart);
                    }

                    //show status info
                    document.getElementById('systemtaskStatusDiv').innerHTML = strStatusInfo;
                    KAJONA.util.evalScript(strStatusInfo);

                    if(strReload == "") {
                    	jsDialog_0.setTitle(KAJONA_SYSTEMTASK_TITLE_DONE);
                    	document.getElementById('systemtaskLoadingDiv').style.display = "none";
                    	document.getElementById('systemtaskCancelButton').value = KAJONA_SYSTEMTASK_CLOSE;
                    }
                    else {
                    	KAJONA.admin.systemtask.executeTask(strTaskname, strReload, true);
                    }
                }
            }

            else {
                jsDialog_0.hide();
                KAJONA.admin.statusDisplay.messageError("<b>Request failed!</b><br />"+data);
            }
        });
    },

    cancelExecution : function() {
        jsDialog_0.hide();
    },

    setName : function(strName) {
    	document.getElementById('systemtaskNameDiv').innerHTML = strName;
    }
};

/**
 * AJAX functions for connecting to the server
 */
KAJONA.admin.ajax = {

    getDataObjectFromString: function(strData, bitFirstIsSystemid) {
        //strip other params, backwards compatibility
        var arrElements = strData.split("&");
        var data = { };

        if(bitFirstIsSystemid)
            data["systemid"] = arrElements[0];

        //first one is the systemid
        if(arrElements.length > 1) {
            $.each(arrElements, function(index, strValue) {
                if(!bitFirstIsSystemid || index > 0) {
                    var arrSingleParams = strValue.split("=");
                    data[arrSingleParams[0]] = arrSingleParams[1];
                }
            });
        }
        return data;
    },

    regularCallback: function(data, status, jqXHR) {
		if(status == 'success') {
			KAJONA.admin.statusDisplay.displayXMLMessage(data)
		}
		else {
			KAJONA.admin.statusDisplay.messageError("<b>Request failed!</b>")
		}
	},


	genericAjaxCall : function(module, action, systemid, objCallback) {
		var postTarget = KAJONA_WEBPATH + '/xml.php?admin=1&module='+module+'&action='+action;
        var data;
        if(systemid) {
            data = this.getDataObjectFromString(systemid, true);
        }

        KAJONA.admin.WorkingIndicator.getInstance().start();
        $.ajax({
            type: 'POST',
            url: postTarget,
            data: data,
            error: objCallback,
            success: objCallback,
            dataType: 'text'
        }).always(
            function(response) {
                KAJONA.admin.WorkingIndicator.getInstance().stop();
            }
        );

	},

    setAbsolutePosition : function(systemIdToMove, intNewPos, strIdOfList, objCallback, strTargetModule) {
        if(strTargetModule == null || strTargetModule == "")
            strTargetModule = "system";

        if(typeof objCallback == 'undefined' || objCallback == null)
            objCallback = KAJONA.admin.ajax.regularCallback;


        KAJONA.admin.ajax.genericAjaxCall(strTargetModule, "setAbsolutePosition", systemIdToMove + "&listPos=" + intNewPos, objCallback);
	},

	setSystemStatus : function(strSystemIdToSet, bitReload) {
        var objCallback = function(data, status, jqXHR) {
            if(status == 'success') {
				KAJONA.admin.statusDisplay.displayXMLMessage(data);

                if(bitReload !== null && bitReload === true)
                    location.reload();

                if (data.indexOf('<error>') == -1 && data.indexOf('<html>') == -1) {
                    var newStatus = $($.parseXML(data)).find("newstatus").text();
                    var link = $('#statusLink_' + strSystemIdToSet);

                    var adminListRow = link.parents('.admintable > tbody').first();
                    if (!adminListRow.length) {
                        adminListRow = link.parents('.grid > ul > li').first();
                    }

                    if (newStatus == 0) {
                        link.html(KAJONA.admin.ajax.setSystemStatusMessages.strInActiveIcon);
                        adminListRow.addClass('disabled');
                    } else {
                        link.html(KAJONA.admin.ajax.setSystemStatusMessages.strActiveIcon);
                        adminListRow.removeClass('disabled');
                    }

                    KAJONA.admin.tooltip.addTooltip($('#statusLink_' + strSystemIdToSet).find("[rel='tooltip']"));
				}
        	}
            else
        		KAJONA.admin.statusDisplay.messageError(data);
        };

        KAJONA.admin.tooltip.removeTooltip($('#statusLink_' + strSystemIdToSet).find("[rel='tooltip']"));
        KAJONA.admin.ajax.genericAjaxCall("system", "setStatus", strSystemIdToSet, objCallback);
	},

    setSystemStatusMessages : {
        strInActiveIcon : '',
        strActiveIcon : ''
    }

};


/**
 * Form management
 */
KAJONA.admin.forms = {};

KAJONA.admin.forms.initForm = function(strFormid) {
    $('#'+strFormid+' input , #'+strFormid+' select , #'+strFormid+' textarea ').each(function() {
        $(this).attr("data-kajona-initval", $(this).val());
    });
};

KAJONA.admin.forms.animateSubmit = function(objForm) {
    //try to get the button currently clicked

    if($(document.activeElement).prop('tagName') == "BUTTON") {
        $(document.activeElement).addClass('processing');
    }
    else {
        $(objForm).find('.savechanges[name=submitbtn]').addClass('processing');
    }
};

KAJONA.admin.forms.changeLabel = '';
KAJONA.admin.forms.changeConfirmation = '';

/**
 * Adds an onchange listener to the formentry with the passed ID. If the value is changed, a warning is rendered below the field.
 * In addition, a special confirmation may be required to change the field to the new value.
 *
 * @param strElementId
 * @param bitConfirmChange
 */
KAJONA.admin.forms.addChangelistener = function(strElementId, bitConfirmChange) {

    $('#'+strElementId).on('change', function(objEvent) {
        if($(this).val() != $(this).attr("data-kajona-initval")) {
            if($(this).closest(".form-group").find("div.changeHint").length == 0) {

                if(bitConfirmChange && bitConfirmChange == true) {
                    var bitResponse = confirm(KAJONA.admin.forms.changeConfirmation);
                    if(!bitResponse) {
                        $(this).val($(this).attr("data-kajona-initval"));
                        objEvent.preventDefault();
                        return;
                    }
                }

                $(this).closest(".form-group").addClass("has-warning");
                $(this).closest(".form-group").children("div:first").append($('<div class="changeHint text-warning"><span class="glyphicon glyphicon-warning-sign"></span> ' + KAJONA.admin.forms.changeLabel + '</div>'));
            }
        }
        else {
            if($(this).closest(".form-group").find("div.changeHint"))
                $(this).closest(".form-group").find("div.changeHint").remove();

            $(this).closest(".form-group").removeClass("has-warning");
        }
    });

};


KAJONA.admin.forms.renderMandatoryFields = function(arrFields) {

    for(var i=0; i<arrFields.length; i++) {
        var arrElement = arrFields[i];
        if(arrElement.length == 2) {
            if(arrElement[1] == 'date' || arrElement[1] == '\Kajona\System\System\Date_validator') {

                var $objElementDay = $("#"+arrElement[0]+"_day");
                if($objElementDay) {
                    $objElementDay.addClass("mandatoryFormElement");
                }

                var $objElementMonth = $("#"+arrElement[0]+"_month");
                if($objElementMonth) {
                    $objElementMonth.addClass("mandatoryFormElement");
                }

                var $objElementYear = $("#"+arrElement[0]+"_year");
                if($objElementYear) {
                    $objElementYear.addClass("mandatoryFormElement");
               }
            }

            var $objElement = $("#" + arrElement[0]);
            if($objElement)
                $objElement.addClass("mandatoryFormElement");

        }

    }
};

KAJONA.admin.forms.renderMissingMandatoryFields = function(arrFields) {
    $(arrFields).each(function(intIndex, strField) {
        var strFieldName = strField[0];
        if($("#"+strFieldName) && !$("#"+strFieldName).hasClass('inputWysiwyg')) {
            $("#"+strFieldName).closest(".form-group").addClass("has-error has-feedback");
			var objNode = $('<span class="glyphicon glyphicon-warning-sign form-control-feedback" aria-hidden="true"></span>');
            $("#"+strFieldName).closest("div:not(.input-group)").append(objNode);
        }
    });
};

KAJONA.admin.forms.loadTab = function(strEl, strHref) {
    if (strHref && $("#" + strEl).length > 0) {
        $("#" + strEl).html("");
        $("#" + strEl).addClass("loadingContainer");
        $.get(strHref, function(data) {
            $("#" + strEl).removeClass("loadingContainer");
            $("#" + strEl).html(data);
            KAJONA.admin.tooltip.initTooltip();
        });
    }
};

KAJONA.admin.lists = {
    arrSystemids : [],
    strConfirm : '',
    strCurrentUrl : '',
    strCurrentTitle : '',
    strDialogTitle : '',
    strDialogStart : '',
    intTotal : 0,

    /**
     * Toggles all fields
     */
    toggleAllFields : function() {
        //batchActionSwitch
        $("table.admintable input[type='checkbox']").each(function() {
            if($(this).attr('id').substr(0, 6) == "kj_cb_" && $(this).attr('id') != 'kj_cb_batchActionSwitch') {
                $(this)[0].checked = $('#kj_cb_batchActionSwitch')[0].checked;
            }
        });
    },

    /**
     * Toggles all fields with the given system id's
     *
     * @param arrSystemIds
     */
    toggleFields : function(arrSystemIds) {
        //batchActionSwitch
        $("table.admintable input[type='checkbox']").each(function() {
            if($(this).attr('id').substr(0, 6) == "kj_cb_" && $(this).attr('id') != 'kj_cb_batchActionSwitch') {
                var strSysid = $(this).closest("tr").data('systemid');
                if($.inArray(strSysid, arrSystemIds) !== -1) {//if strId in array
                    if($(this)[0].checked) {
                        $(this)[0].checked = false;
                    }
                    else {
                        $(this)[0].checked = true;
                    };
                }
            }
        });
        KAJONA.admin.lists.updateToolbar();
    },

    updateToolbar : function() {
        if($("table.admintable  input:checked").length == 0) {
            $('.batchActionsWrapper').removeClass("visible");
        }
        else {
            $('.batchActionsWrapper').addClass("visible");
        }
    },

    triggerAction : function(strTitle, strUrl, bitRenderInfo) {
        KAJONA.admin.lists.arrSystemids = [];
        KAJONA.admin.lists.strCurrentUrl = strUrl;
        KAJONA.admin.lists.strCurrentTitle = strTitle;
        KAJONA.admin.lists.bitRenderInfo = bitRenderInfo;

        //get the selected elements
        KAJONA.admin.lists.arrSystemids = KAJONA.admin.lists.getSelectedElements();

        if(KAJONA.admin.lists.arrSystemids.length == 0)
            return;

        var curConfirm = KAJONA.admin.lists.strConfirm.replace('%amount%', KAJONA.admin.lists.arrSystemids.length);
        curConfirm = curConfirm.replace('%title%', strTitle);

        jsDialog_1.setTitle(KAJONA.admin.lists.strDialogTitle);
        jsDialog_1.setContent(curConfirm, KAJONA.admin.lists.strDialogStart,  'javascript:KAJONA.admin.lists.executeActions();');
        jsDialog_1.init();

        //reset pending list on hide
        $('#'+jsDialog_1.containerId).on('hidden.bs.modal', function () {
            KAJONA.admin.lists.arrSystemids = [];
        });

        // reset messages
        if (KAJONA.admin.lists.bitRenderInfo) {
            $('.batchaction_messages_list').html("");
        }

        return false;
    },

    executeActions : function() {
        KAJONA.admin.lists.intTotal = KAJONA.admin.lists.arrSystemids.length;

        $('.batchActionsProgress > .progresstitle').text(KAJONA.admin.lists.strCurrentTitle);
        $('.batchActionsProgress > .total').text(KAJONA.admin.lists.intTotal);
        jsDialog_1.setContentRaw($('.batchActionsProgress').html());

        KAJONA.admin.lists.triggerSingleAction();
    },

    triggerSingleAction : function() {
        if(KAJONA.admin.lists.arrSystemids.length > 0 && KAJONA.admin.lists.intTotal > 0) {
            $('.batch_progressed').text((KAJONA.admin.lists.intTotal - KAJONA.admin.lists.arrSystemids.length +1));
			var intPercentage = ( (KAJONA.admin.lists.intTotal - KAJONA.admin.lists.arrSystemids.length) / KAJONA.admin.lists.intTotal * 100);
            $('.progress > .progress-bar').css('width', intPercentage+'%');
            $('.progress > .progress-bar').html(Math.round(intPercentage)+'%');

            var strUrl = KAJONA.admin.lists.strCurrentUrl.replace("%systemid%", KAJONA.admin.lists.arrSystemids[0]);
            KAJONA.admin.lists.arrSystemids.shift();

            $.ajax({
                type: 'POST',
                url: strUrl,
                success: function(resp) {
                    KAJONA.admin.lists.triggerSingleAction();
                    if (KAJONA.admin.lists.bitRenderInfo) {
                        var data = JSON.parse(resp);
                        if (data && data.message) {
                            $('.batchaction_messages_list').append("<li>" + data.message + "</li>");
                        }
                    }
                },
                dataType: 'text'
            });
        }
        else {
            $('.batch_progressed').text((KAJONA.admin.lists.intTotal));
            $('.progress > .progress-bar').css('width', 100+'%');
			$('.progress > .progress-bar').html('100%');

            if (!KAJONA.admin.lists.bitRenderInfo) {
                document.location.reload();
            }
            else {
                $('#jsDialog_1_cancelButton').css('display', 'none');
                $('#jsDialog_1_confirmButton').remove('click').on('click', function() {document.location.reload();}).html('<span data-lang-property="system:systemtask_close_dialog"></span>');
                KAJONA.admin.loader.loadFile('/core/module_system/system/scripts/lang.js', function() {
                    KAJONA.util.lang.initializeProperties($('#jsDialog_1_confirmButton'));
                });
            }
        }
    },

    /**
     * Creates an array which contains the selected system id's.
     *
     * @returns {Array}
     */
    getSelectedElements : function () {
        var selectedElements = [];

        //get the selected elements
        $("table.admintable  input:checked").each(function() {
            if($(this).attr('id').substr(0, 6) == "kj_cb_" && $(this).attr('id') != 'kj_cb_batchActionSwitch') {
                var sysid = $(this).closest("tr").data('systemid');
                if(sysid != "")
                    selectedElements.push(sysid);
            }
        });

        return selectedElements;
    },

    /**
     * Creates an array which contains all system id's.
     *
     * @returns {Array}
     */
    getAllElements : function () {
        var selectedElements = [];

        //get the selected elements
        $("table.admintable  input[type='checkbox']").each(function() {
            if($(this).attr('id').substr(0, 6) == "kj_cb_" && $(this).attr('id') != 'kj_cb_batchActionSwitch') {
                var sysid = $(this).closest("tr").data('systemid');
                if(sysid != "")
                    selectedElements.push(sysid);
            }
        });

        return selectedElements;
    }
};

/**
 * Dashboard calendar functions
 */
KAJONA.admin.dashboardCalendar = {};
KAJONA.admin.dashboardCalendar.eventMouseOver = function(strSourceId) {
    if(strSourceId == "")
        return;

    var sourceArray = eval("kj_cal_"+strSourceId);
    if(typeof sourceArray != undefined) {
        for(var i=0; i< sourceArray.length; i++) {
            $("#event_"+sourceArray[i]).addClass("mouseOver");
        }
    }
};

KAJONA.admin.dashboardCalendar.eventMouseOut = function(strSourceId) {
    if(strSourceId == "")
        return;

    var sourceArray = eval("kj_cal_"+strSourceId);
    if(typeof sourceArray != undefined) {
        for(var i=0; i< sourceArray.length; i++) {
            $("#event_"+sourceArray[i]).removeClass("mouseOver");
        }
    }
};


/**
 * Subsystem for all messaging related tasks. Queries the backend for the number of unread messages, ...
 * @type {Object}
 */
KAJONA.admin.messaging = {


    bitFirstLoad : true,

    /**
     * Gets the number of unread messages for the current user.
     * Expects a callback-function whereas the number is passed as a param.
     *
     * @param objCallback
     * @deprecated replaced by getRecentMessages
     */
    getUnreadCount : function(objCallback) {

        KAJONA.admin.ajax.genericAjaxCall("messaging", "getUnreadMessagesCount", "", function(data, status, jqXHR) {
            if(status == 'success') {
                var objResponse = $($.parseXML(data));
                KAJONA.admin.messaging.intCount = objResponse.find("messageCount").text();
                objCallback(objResponse.find("messageCount").text());

            }
        });
    },

    /**
     * Loads the list of recent messages for the current user.
     * The callback is passed the json-object as a param.
     * @param objCallback
     */
    getRecentMessages : function(objCallback) {
        KAJONA.admin.ajax.genericAjaxCall("messaging", "getRecentMessages", "", function(data, status, jqXHR) {
            if(status == 'success') {
                var objResponse = $.parseJSON(data);
                objCallback(objResponse);
            }
        });
    }
};

/**
 * Appends an table of contents navigation under the main navigation sidebar. The index contains all elements which
 * match the given selector. The text of the element gets used as link in the navigation. Sets also the fitting id to
 * each element.
 */
KAJONA.admin.renderTocNavigation = function (selector) {

    if(!$('.sidebar-nav').length) {
         return;
    }

    //handled before?
    if($('#toc-navigation ul').length > 0) {
        return;
    }

    // create the navigation
    var html = '';
    var arrIdMap = Array();
    $(selector).each(function () {
        if($(this).attr('id')) {
            var id = $(this).attr('id');
        }
        else {
            var id = $(this).text().replace(/(?!\w)[\x00-\xC0]/g, "-");
            var newId = id;
            var intI = 0;
            while(KAJONA.util.inArray(newId, arrIdMap)) {
                newId = id+"_"+(intI++);
            }

            id = newId;
            arrIdMap.push(id);
            $(this).attr('id', id);
        }
        html += '<li><a href="#' + id + '">' + $(this).text() + '</a></li>';
    });

    // append the element only if it is not already appended
    $('.sidebar-nav').append($('<div id="toc-navigation" class="toc-navigation-panel" role="navigation">').append($('<ul class="nav">').html(html)));

    // affix toc navigation
    $('#toc-navigation').affix({
        offset: {
            top: $('#toc-navigation').position().top + 30
        }
    });

    // scroll spy
    $('body').scrollspy({
        target: '#toc-navigation',
        offset: 60
    });

    // resize toc navigation to main navigation
    $(window).resize(function() {
        $('#toc-navigation').css('width', $('#moduleNavigation').width()+15);
        $('#toc-navigation').css('max-height', $(window).height()-60);
    });
    $('#toc-navigation').css('width', $('#moduleNavigation').width()+15);
    $('#toc-navigation').css('max-height', $(window).height()-60);
};




KAJONA.admin.WorkingIndicator = function() {

    if (KAJONA.admin.WorkingIndicator.prototype._singletonInstance) {
        return KAJONA.admin.WorkingIndicator.prototype._singletonInstance;
    }

    KAJONA.admin.WorkingIndicator.prototype._singletonInstance = this;

    var intWorkingCount = 0;

    this.start = function () {

        if(intWorkingCount == 0) {
            $('#status-indicator').addClass("active");
        }

        intWorkingCount++;
    };

    this.stop = function() {
        intWorkingCount--;

        if(intWorkingCount == 0) {
            $('#status-indicator').removeClass("active");
        }
    };



};
KAJONA.admin.WorkingIndicator.getInstance = function() {
    return new KAJONA.admin.WorkingIndicator();
};


/**
 * Wrapper for desktop notifications.
 *
 * @see https://developer.mozilla.org/en-US/docs/WebAPI/Using_Web_Notifications
 * @type {Object}
 */
KAJONA.util.desktopNotification = {

    bitGranted : false,

    /**
     * Sends a message to the client. Asks for permissions if not yet given.
     *
     * @param strTitle
     * @param strBody
     * @param {callback} onClick
     */
    showMessage : function (strTitle, strBody, onClick) {

        KAJONA.util.desktopNotification.grantPermissions();

        //for fucking IE
        if(typeof Notification == "undefined")
            return;

        if (Notification && Notification.permission === "granted") {
            KAJONA.util.desktopNotification.bitGranted = true;
        }
        else if (Notification && Notification.permission !== "denied") {
            Notification.requestPermission(function (status) {
                if (Notification.permission !== status) {
                    Notification.permission = status;
                }

                // If the user said okay
                if (status === "granted") {
                    KAJONA.util.desktopNotification.bitGranted = true;
                }
            });
        }


        if(KAJONA.util.desktopNotification.bitGranted) {
            var n = new Notification(strTitle, {body: strBody});

            if(onClick)
                n.onclick = onClick;
        }
    },




    grantPermissions: function() {

        //for fucking IE
        if(typeof Notification == "undefined")
            return;

        if (Notification && Notification.permission !== "granted") {
            Notification.requestPermission(function (status) {
                if (Notification.permission !== status) {
                    Notification.permission = status;
                }
            });
        }
    }

};


/*
 * -------------------------------------------------------------------------
 * Admin form functions
 * -------------------------------------------------------------------------
 */

/**
 * Hides a field in the form
 *
 * @param objField - my be a jquery field or a id selector
 */
KAJONA.admin.forms.hideField = function(objField) {
    objField = KAJONA.admin.forms.getObjField(objField);

    var objFormGroup = objField.closest('.form-group');

    //1. Hide field
    objFormGroup.slideUp(0);

    //2. Hide hint -> check if previous element has 'form-group' and if <span> with .help-block exists
    var objHintFormGroup = objFormGroup.prev('.form-group');
    if(objHintFormGroup.find('div > span.help-block').length > 0) {
        objHintFormGroup.slideUp(0);
    }
};

/**
 * Shows a field in the form
 *
 * @param objField - my be a jquery object or an id selector
 */
KAJONA.admin.forms.showField = function(objField) {
    objField = KAJONA.admin.forms.getObjField(objField);

    var objFormGroup = objField.closest('.form-group');

    //1. Show field
    objFormGroup.slideDown(0);

    //2. Show hint -> check if previous element has 'form-group' and if <span> with .help-block exists
    var objHintFormGroup = objFormGroup.prev('.form-group');
    if(objHintFormGroup.find('div > span.help-block').length > 0) {
        objHintFormGroup.slideDown(0);
    }
};

/**
 * Disables a field
 *
 * @param objField - my be a jquery object or an id selector
 */
KAJONA.admin.forms.setFieldReadOnly = function(objField) {
    objField = KAJONA.admin.forms.getObjField(objField);

    if (objField.is('input:checkbox') || objField.is('select')) {
        objField.prop("disabled", "disabled");
    }
    else {
        objField.attr("readonly", "readonly");
    }
};

/**
 * Enables a field
 *
 * @param objField - my be a jquery object or an id selector
 */
KAJONA.admin.forms.setFieldEditable = function(objField) {
    objField = KAJONA.admin.forms.getObjField(objField);

    if (objField.is('input:checkbox') || objField.is('select')) {
        objField.removeProp("disabled");
    }
    else {
        objField.removeProp("readonly");
    }
};

/**
 * Gets the jQuery object
 *
 * @param objField - my be a jquery object or an id selector
 */
KAJONA.admin.forms.getObjField = function (objField) {
    // If objField is already a jQuery object
    if(objField instanceof jQuery) {
        return objField
    } else {
        // Convert to jQuery object
        return $(objField);
    }
};
