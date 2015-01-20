//   (c) 2013-2014 by Kajona, www.kajona.de
//       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt
//       $Id$

if (!KAJONA) {
    alert('load kajona.js before!');
}


KAJONA.admin.dashboard = {

    removeWidget : function(strSystemid) {
        KAJONA.admin.ajax.genericAjaxCall('dashboard', 'deleteWidget', strSystemid, function(data, status, jqXHR) {
            if (status == 'success') {

                $("div[data-systemid="+strSystemid+"]").remove();
                KAJONA.admin.statusDisplay.displayXMLMessage(data);
                jsDialog_1.hide();

            } else {
                KAJONA.admin.statusDisplay.messageError('<b>Request failed!</b><br />' + data);
            }
        });
    },

    init : function() {

        $('div.dbEntry').each(function () {
            var systemId = $(this).data('systemid');
            KAJONA.admin.ajax.genericAjaxCall('dashboard', 'getWidgetContent', systemId, function(data, status, jqXHR) {

                content = $("div.dbEntry[data-systemid='"+systemId+"'] .content");

                if (status == 'success') {
                    var $parent = content.parent();
                    content.remove();

                    var $newNode = $("<div class='content'></div>").append($.parseJSON(data));
                    $parent.append($newNode);

                    //TODO use jquerys eval?
                    KAJONA.util.evalScript(data);
                    KAJONA.admin.tooltip.initTooltip();

                } else {
                    //KAJONA.admin.statusDisplay.messageError('<b>Request failed!</b><br />' + data);
                }
            });
        });

        $("#dashboard").find(".column").each(function(index) {

            //$("#dashboard").sortable({
            $(this).sortable({
                items: 'div.dbEntry',
                handle: 'h2',
                dropOnEmpty : true,
                forcePlaceholderSize: true,
                cursor: 'move',
                connectWith: '.column',
                placeholder: 'dashboardPlaceholder col-sm-12',
                stop: function(event, ui) {
                    //search list for new pos
                    var intPos = 0;
                    $(".dbEntry").each(function(index) {
                        intPos++;
                        debugger;
                        if($(this).data("systemid") == ui.item.data("systemid")) {
                            console.log("set to pos "+intPos);



                            //KAJONA.admin.ajax.genericAjaxCall("dashboard", "setDashboardPosition", ui.item.data("systemid") + "&listPos=" + intPos+"&listId="+ui.item.closest('ul').attr('id'), KAJONA.admin.ajax.regularCallback)
                            //return false;
                        }
                    });
                },
                delay: KAJONA.util.isTouchDevice() ? 2000 : 0
            }).find("h2").css("cursor", "move");
        });

    }
};
