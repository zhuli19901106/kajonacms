
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;
import selenium.pages.LandingPage;
import selenium.pages.LoginPage;
import selenium.pages.util.Constants;
import selenium.pages.util.SeleniumUtil;
import selenium.properties.MessagesEnum;
import selenium.webdriver.WebDriverFactory;
import selenium.webdriver.WebDriverType;
import testng.base.SeleniumTestBase;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author smy
 */
public class RunnerClass {
    
    static final Logger logger = LogManager.getLogger(RunnerClass.class.getName());
    
    public static void main(String[] args) {
        
        logger.error("Something to tlog");
        
        WebDriver driver = WebDriverFactory.createWebDriver(WebDriverType.FIREFOX);
        driver.get(MessagesEnum.SELENIUM.getString("selenium.defaultUrl"));
        //IE Hack for https
        if (SeleniumUtil.isElementPresentAndDisplayed(driver, By.xpath(Constants.IE_SSL_OVERRIDELINK))) {
            driver.findElement(By.xpath(Constants.IE_SSL_OVERRIDELINK)).click();
        }
        
        
        LoginPage loginPage = PageFactory.initElements(driver, LoginPage.class);
        LandingPage p = loginPage.login(MessagesEnum.SELENIUM.getString("selenium.defaultUserName"), 
                MessagesEnum.SELENIUM.getString("selenium.defaultPassword"));
        p.getTopMenu().search("pro");
        p.getTopMenu().showAllUserMessages();
        p.getTopMenu().selectAspect("IMS");
        p.getTopMenu().selectAspect("Verwaltung");
        p.getTopMenu().selectAspect("Riskmanager");
        p.getTopMenu().logOut();
        
        driver.quit();
    }
    
}
