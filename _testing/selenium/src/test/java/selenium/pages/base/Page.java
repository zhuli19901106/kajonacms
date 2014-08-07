package selenium.pages.base;

import java.util.concurrent.TimeUnit;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.PageFactory;
import selenium.pages.TopMenu;
import selenium.properties.MessagesEnum;
import selenium.webdriver.WebDriverFactory;
import selenium.webdriver.WebDriverType;

/**
 *
 * @author stefan.meyer1@yahoo.de
 */
public class Page {
    
    private WebDriver driver = null;
    private TopMenu topMenu = null;
    
    
    public Page(WebDriver driver) {
        this.driver = driver;
        this.driver.manage().timeouts().implicitlyWait(10, TimeUnit.SECONDS);
        this.topMenu = PageFactory.initElements(this.driver, TopMenu.class);
    }

    public void mouseOver(String xPathKey) {
        WebElement element = driver.findElement(By.xpath(MessagesEnum.SELENIUM.getString(xPathKey)));
    }

    public WebDriver getDriver() {
        return driver;
    }

    public TopMenu getTopMenu() {
        return topMenu;
    }
    
    
    
}
