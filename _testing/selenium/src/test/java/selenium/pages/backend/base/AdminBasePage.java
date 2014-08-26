package selenium.pages.backend.base;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;
import selenium.pages.backend.LeftMenu;
import selenium.pages.backend.TopMenu;

/**
 *
 * @author stefan.meyer1@yahoo.de
 */
public class AdminBasePage {
    
    private static WebDriver driver = null;
    private static TopMenu topMenu = null;
    private static LeftMenu leftMenu = null;
    
    public AdminBasePage(WebDriver driver) {
        AdminBasePage.driver = driver;
        AdminBasePage.topMenu = PageFactory.initElements(AdminBasePage.driver, TopMenu.class);
        AdminBasePage.leftMenu = PageFactory.initElements(AdminBasePage.driver, LeftMenu.class);
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
