/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package selenium.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import selenium.pages.util.Constants;
import selenium.pages.util.SeleniumUtil;

/**
 *
 * @author smy
 */
public class TopMenu {
    
    private WebDriver driver = null;
    
    //SearchBox
    @FindBy(xpath = Constants.TOPMENU_INPUT_SEARCHBOX)
    WebElement searchBox;
    @FindBy(xpath = Constants.TOPMENU_INPUT_SEARCHBOX_SHOW_ALL_SEARCHRESULTS)
    WebElement lnkShowAllSearchResults;
    
    //UserMenu
    @FindBy(xpath = Constants.TOPMENU_USER_DROPDOWN)
    WebElement lnkUserDropdown;
    @FindBy(xpath = Constants.TOPMENU_USER_DROPDOWN_MESSAGES)
    WebElement lnkUserMenuMessages;
    @FindBy(xpath = Constants.TOPMENU_USER_DROPDOWN_MESSAGES_LNK_SHOWALLMESAGES)
    WebElement lnkUserMenuShowAllMessages;
    
    @FindBy(xpath = Constants.TOPMENU_USER_DROPDOWN_LOGOUT_LNK)
    WebElement lnkLogOut;

    public TopMenu(WebDriver driver) {
        this.driver = driver;
    }
    
    public LoginPage logOut() {
        SeleniumUtil.moveToElement(driver, lnkUserDropdown);
        lnkLogOut.click();
        
        boolean allLoginElementsPresent = SeleniumUtil.isElementPresent(driver, By.xpath(Constants.LOGIN_INPUT_USERNAME))
                                            && SeleniumUtil.isElementPresent(driver, By.xpath(Constants.LOGIN_INPUT_PASSWORD))
                                            && SeleniumUtil.isElementPresent(driver, By.xpath(Constants.LOGIN_BUTTON))
                                            && !SeleniumUtil.isElementPresent(driver, By.xpath(Constants.LOGIN_ERROR_BOX));
        
        if(!allLoginElementsPresent) {
            return null;
        }
        
        return PageFactory.initElements(driver, LoginPage.class);
    }

    public void search(String searchTerm) {
        searchBox.sendKeys(searchTerm);
        lnkShowAllSearchResults.click();
    }

    public void showAllUserMessages() {
        SeleniumUtil.moveToElement(driver, lnkUserDropdown);
        SeleniumUtil.moveToElement(driver, lnkUserMenuMessages);
        lnkUserMenuShowAllMessages.click();
        
    }
    
}
