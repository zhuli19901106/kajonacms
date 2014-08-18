package testng.base;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.openqa.selenium.By;
import selenium.webdriver.WebDriverFactory;
import selenium.webdriver.WebDriverType;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;
import org.testng.annotations.AfterClass;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Parameters;
import selenium.pages.LandingPage;
import selenium.pages.LoginPage;
import selenium.pages.util.Constants;
import selenium.pages.util.SeleniumUtil;
import selenium.properties.MessagesEnum;

/**
 * 
 * @author stefan.meyer1@yahoo.de
 */
public class SeleniumTestBase
{
    
    static final Logger logger = LogManager.getLogger(SeleniumTestBase.class.getName());
    
    private final static String BROWSER_IE = "ie";
    private final static String BROWSER_FF = "firefox";
    private final static String BROWSER_CHROME = "chrome";
    private final static String BROWSER_SAFARI = "safari";
    
    protected WebDriverType type = null;
    protected WebDriver driver = null;
    
    @BeforeClass
    @Parameters("browser")
    public void setUp(String browser) {
        switch(browser) {
            case BROWSER_FF:
                driver = WebDriverFactory.createWebDriver(WebDriverType.FIREFOX);
                break;
                
            case BROWSER_IE:
                driver = WebDriverFactory.createWebDriver(WebDriverType.INTERNETEXPLORER);
                break;
                
            case BROWSER_SAFARI:
                driver = WebDriverFactory.createWebDriver(WebDriverType.SAFARI);
                break;
                
            case BROWSER_CHROME:
                driver = WebDriverFactory.createWebDriver(WebDriverType.CHROME);
                break;
        }
    }
    
    @AfterClass(alwaysRun = true)
    public void tearDown() {
        driver.quit();
    }
    
    
    public LandingPage login(String userName, String password) {
        driver.get(MessagesEnum.SELENIUM.getString("selenium.defaultUrl"));
        
        //IE Hack for https
        if(SeleniumUtil.isElementPresent(driver, By.xpath(Constants.IE_SSL_OVERRIDELINK))) {
            driver.findElement(By.xpath(Constants.IE_SSL_OVERRIDELINK)).click();
        }
        
        LoginPage p = PageFactory.initElements(driver, LoginPage.class);
        LandingPage page = p.login(userName, password);
        
        return page;
    }
}