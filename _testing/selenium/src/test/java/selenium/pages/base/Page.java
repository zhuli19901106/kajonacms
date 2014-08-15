package selenium.pages.base;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;
import selenium.pages.LeftMenu;
import selenium.pages.TopMenu;

/**
 *
 * @author stefan.meyer1@yahoo.de
 */
public class Page {
    
    private static WebDriver driver = null;
    private static TopMenu topMenu = null;
    private static LeftMenu leftMenu = null;
    
    public Page(WebDriver driver) {
        Page.driver = driver;
        Page.topMenu = PageFactory.initElements(Page.driver, TopMenu.class);
        Page.leftMenu = PageFactory.initElements(Page.driver, LeftMenu.class);
    }

    public WebDriver getDriver() {
        return driver;
    }

    public TopMenu getTopMenu() {
        return topMenu;
    }
    
    public LeftMenu getLeftMenu() {
        return leftMenu;
    }
}
