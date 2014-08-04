package testng.base;

import selenium.webdriver.WebDriverFactory;
import selenium.webdriver.WebDriverType;
import org.openqa.selenium.WebDriver;
import org.testng.annotations.AfterClass;
import org.testng.annotations.BeforeTest;
import org.testng.annotations.Parameters;

public class SeleniumTestBase
{
    
    private final static String BROWSER_IE = "ie";
    private final static String BROWSER_FF = "firefox";
    private final static String BROWSER_CHROME = "chrome";
    
    protected WebDriverType type = null;
    protected WebDriver driver = null;
    
    @BeforeTest
    @Parameters("browser")
    public void setUp(String browser) {
        switch(browser) {
            case BROWSER_FF:
                driver = WebDriverFactory.createWebDriver(WebDriverType.FIREFOX);
                break;
                
            case BROWSER_IE:
                driver = WebDriverFactory.createWebDriver(WebDriverType.INTERNETEXPLORER);
                break;
                
            case BROWSER_CHROME:
                driver = WebDriverFactory.createWebDriver(WebDriverType.CHROME);
                break;
        }
    }
    
    
    @AfterClass
    public void tearDown() {
        driver.quit();
    }
}