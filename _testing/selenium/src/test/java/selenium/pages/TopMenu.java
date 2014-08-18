package selenium.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.Select;
import selenium.pages.util.Constants;
import selenium.pages.util.SeleniumUtil;
import selenium.pages.util.SeleniumWaitHelper;

/**
 *
 * @author smy
 */
public class TopMenu {

    private WebDriver driver = null;

    //SearchBox
    @FindBy(xpath = Constants.TOPMENU_SEARCHBOX_INPUT)
    WebElement searchBox;
    @FindBy(xpath = Constants.TOPMENU_SEARCHBOX_LNK_SEARCHRESULTS)
    WebElement lnkShowAllSearchResults;

    //UserMenu
    @FindBy(xpath = Constants.TOPMENU_USERMENU)
    WebElement lnkUserMenu;
    @FindBy(xpath = Constants.TOPMENU_USERMENU_MESSAGES)
    WebElement lnkUserMenuMessages;
    @FindBy(xpath = Constants.TOPMENU_USERMENU_MESSAGES_LNK_SHOWALLMESAGES)
    WebElement lnkUserMenuShowAllMessages;
    @FindBy(xpath = Constants.TOPMENU_USERMENU_LOGOUT_LNK)
    WebElement lnkUserMenuLogOut;

    //Aspect chooser
    @FindBy(xpath = Constants.TOPMENU_ASPECT_SELECTBOX)
    WebElement selectAspect;

    public TopMenu(WebDriver driver) {
        this.driver = driver;
    }

    public LoginPage logOut() {
        SeleniumUtil.moveToElement(driver, lnkUserMenu);
        lnkUserMenuLogOut.click();

        boolean allLoginElementsPresent = SeleniumUtil.isElementPresentAndDisplayed(driver, By.xpath(Constants.LOGIN_INPUT_USERNAME))
                && SeleniumUtil.isElementPresentAndDisplayed(driver, By.xpath(Constants.LOGIN_INPUT_PASSWORD))
                && SeleniumUtil.isElementPresentAndDisplayed(driver, By.xpath(Constants.LOGIN_BUTTON))
                && !SeleniumUtil.isElementPresentAndDisplayed(driver, By.xpath(Constants.LOGIN_ERROR_BOX));

        if (!allLoginElementsPresent) {
            return null;
        }

        return PageFactory.initElements(driver, LoginPage.class);
    }

    public void search(String searchTerm) {
        searchBox.sendKeys(searchTerm);

        SeleniumWaitHelper.waitForElementUntilPresent(driver, By.xpath(Constants.TOPMENU_SEARCHBOX_LNK_SEARCHRESULTS), 10);

        lnkShowAllSearchResults.click();
    }

    public TopMenu showUserMenu() {
        SeleniumUtil.moveToElement(driver, lnkUserMenu);
        SeleniumWaitHelper.waitForElementUntilPresent(driver, By.xpath(Constants.TOPMENU_USERMENU), 10);
        return this;
    }

    public void showAllUserMessages() {
        this.showUserMenu();
        SeleniumUtil.moveToElement(driver, lnkUserMenuMessages);
        SeleniumWaitHelper.waitForElementUntilPresent(driver, By.xpath(Constants.TOPMENU_USERMENU_MESSAGES_LNK_SHOWALLMESAGES), 10);

        lnkUserMenuShowAllMessages.click();
    }

    public void selectAspect(String aspect) {
        Select select = new Select(selectAspect);
        select.selectByVisibleText(aspect);
        driver.navigate().refresh();
        SeleniumWaitHelper.waitForElementUntilPresent(driver, By.xpath(Constants.TOPMENU_ASPECT_SELECTBOX), 10);
    }

}
