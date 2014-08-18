package selenium.pages.util;

/**
 *
 * @author stefan.meyer@artemeon.de
 */
public class Constants {

    public static final String IE_SSL_OVERRIDELINK = "//*[@id='overridelink']";

    //Login Page
    public static final String LOGIN_CONTAINER                              = "//*[@id='loginContainer_content']";
    public static final String LOGIN_INPUT_USERNAME                         = LOGIN_CONTAINER + "//*[@id='name']";
    public static final String LOGIN_INPUT_PASSWORD                         = LOGIN_CONTAINER + "//*[@id='passwort']";
    public static final String LOGIN_BUTTON                                 = LOGIN_CONTAINER + "/form[1]/div[last()]/button";
    public static final String LOGIN_ERROR_BOX                              = LOGIN_CONTAINER + "/div[@id='loginError']";

    //TopMenu
    public static final String TOPMENU_SEARCHBOX_INPUT                      = "//*[@id='globalSearchInput']";
    public static final String TOPMENU_SEARCHBOX_LNK_SEARCHRESULTS          = "//*[@class='detailedResults ui-menu-item']/a";
    public static final String TOPMENU_USERMENU                             = "//*[@class='dropdown userNotificationsDropdown']";
    public static final String TOPMENU_USERMENU_MESSAGES                    = TOPMENU_USERMENU + "/ul/li[1]/a";
    public static final String TOPMENU_USERMENU_TAGS                        = TOPMENU_USERMENU + "/ul/li[2]/a";
    public static final String TOPMENU_USERMENU_HELP                        = TOPMENU_USERMENU + "/ul/li[3]/a";
    public static final String TOPMENU_USERMENU_MESSAGES_SUBMENU            = TOPMENU_USERMENU + "//*[@id='messagingShortlist']";
    public static final String TOPMENU_USERMENU_MESSAGES_LNK_SHOWALLMESAGES = TOPMENU_USERMENU_MESSAGES_SUBMENU + "/li[last()]/a";
    public static final String TOPMENU_USERMENU_LOGOUT_LNK                  = TOPMENU_USERMENU + "/ul/li[last()]/a";
    public static final String TOPMENU_ASPECT_SELECTBOX                     = "//*[@class='navbar navbar-fixed-top']/div[1]/div/div/div[2]/select";

    //LeftMenu
    public static final String LEFTMENU_MODULE_XX                           = "";
}
