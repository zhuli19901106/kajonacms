package selenium.pages.util;

/**
 *
 * @author stefan.meyer@artemeon.de
 */
public class Constants {
    public static final String IE_SSL_OVERRIDELINK = "//*[@id='overridelink']";
    
    
    //Login Page
    public static final String LOGIN_INPUT_USERNAME = "//*[@id='name']";
    public static final String LOGIN_INPUT_PASSWORD = "//*[@id='passwort']";
    public static final String LOGIN_BUTTON = "//*[@id='loginContainer_content']/form[1]/div[last()]/button";
    public static final String LOGIN_ERROR_BOX = "//*[@id='loginError']";
    
    
    //TopMenu
    public static final String TOPMENU_SEARCHBOX_INPUT = "//*[@id='globalSearchInput']";
    public static final String TOPMENU_SEARCHBOX_LNK_SEARCHRESULTS = "//*[@class='detailedResults ui-menu-item']/a";
    public static final String TOPMENU_USERMENU = "html/body/div[1]/div[1]/div/div/div[1]/div/a";
    public static final String TOPMENU_USERMENU_MESSAGES = "html/body/div[1]/div[1]/div/div/div[1]/div/ul/li[1]/a";
    public static final String TOPMENU_USERMENU_MESSAGES_LNK_SHOWALLMESAGES= "//*[@id='messagingShortlist']/li[last()]/a";
    public static final String TOPMENU_USERMENU_LOGOUT_LNK= "html/body/div[1]/div[1]/div/div/div[1]/div/ul/li[last()]/a";
    public static final String TOPMENU_ASPECT_SELECTBOX = "//*[@class='navbar navbar-fixed-top']/div[1]/div/div/div[2]/select";
    
    
    
    //LeftMenu
    public static final String LEFTMENU_MODULE_XX = "";
    
}
