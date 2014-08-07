/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package selenium.pages;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;

/**
 *
 * @author smy
 */
public class TopMenu {
    
    private WebDriver driver = null;
    
    //SearchBox
    @FindBy(xpath = "//*[@id='globalSearchInput']")
    WebElement searchBox;
    @FindBy(xpath = "//*[@class='detailedResults ui-menu-item']/a")
    WebElement lnkShowAllSearchResults;
    
    //UserMenu
    @FindBy(xpath = "html/body/div[1]/div[1]/div/div/div[1]/div/a")
    WebElement linkUserMenu;
    @FindBy(xpath = "html/body/div[1]/div[1]/div/div/div[1]/div/ul/li[1]/a")
    WebElement lnkUserMenuMessages;
    @FindBy(xpath = "//*[@id='messagingShortlist']/li[last()]/a")
    WebElement lnkUserMenuShowAllMessages;
    
    @FindBy(xpath = "html/body/div[1]/div[1]/div/div/div[1]/div/ul/li[last()]/a")
    WebElement lnkLogOut;

    public TopMenu(WebDriver driver) {
        this.driver = driver;
    }
    
    public void logOut() {
        Actions builder = new Actions(driver);
        builder.moveToElement(linkUserMenu).perform();
        lnkLogOut.click();
    }

    public void search(String searchTerm) {
        searchBox.sendKeys(searchTerm);
        lnkShowAllSearchResults.click();
    }

    public void showAllUseRMessages() {
        //mouseOver("selenium.topmenu.usermenu.dropdown");
        //mouseOver("selenium.topmenu.usermenu.dropdown.messages");
        //click("selenium.topmenu.usermenu.dropdown.messages.link.showallmessages");
    }
    
}
