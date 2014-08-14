/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
    public static final String LOGIN_ERROR_BOX = "//*[@id=\"loginError\"]";
    
    
    //TopMenu
    public static final String TOPMENU_INPUT_SEARCHBOX = "//*[@id='globalSearchInput']";
    public static final String TOPMENU_INPUT_SEARCHBOX_SHOW_ALL_SEARCHRESULTS = "//*[@class='detailedResults ui-menu-item']/a";
    public static final String TOPMENU_SELECTBOX_ASPECT = "html/body/div[1]/div[1]/div/div/div[2]/select";
    public static final String TOPMENU_USER_DROPDOWN = "html/body/div[1]/div[1]/div/div/div[1]/div/a";
    public static final String TOPMENU_USER_DROPDOWN_MESSAGES = "html/body/div[1]/div[1]/div/div/div[1]/div/ul/li[1]/a";
    public static final String TOPMENU_USER_DROPDOWN_MESSAGES_LNK_SHOWALLMESAGES= "//*[@id='messagingShortlist']/li[last()]/a";
    public static final String TOPMENU_USER_DROPDOWN_LOGOUT_LNK= "html/body/div[1]/div[1]/div/div/div[1]/div/ul/li[last()]/a";
    
    
    
    //LeftMenu
    public static final String LEFTMENU_MODULE_XX = "";
    
}
