package selenium.webdriver;

import java.util.concurrent.TimeUnit;
import org.apache.commons.lang3.SystemUtils;
import org.openqa.selenium.Platform;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebDriverException;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.ie.InternetExplorerDriver;
import org.openqa.selenium.remote.CapabilityType;
import org.openqa.selenium.remote.DesiredCapabilities;
import org.openqa.selenium.safari.SafariDriver;
import selenium.properties.MessagesEnum;

/**
 *
 * @author stefan.meyer1@yahoo.de
 */
public class WebDriverFactory {
    public static final long DEFAULT_IMPLICIT_WAIT_TIME = 15;
    
    
    
    private static final String CHROME_DRIVER_EXECUTABLE;
    private static final String IE_DRIVER_EXECUTABLE;
    
    static {
        //initialize pathes to the driver servers
        String currentUserDir = System.getProperty("user.dir");
        String pathToDrivers = currentUserDir + MessagesEnum.SELENIUM.getString("selenium.dirvers.basepath");
        
        if(SystemUtils.IS_OS_WINDOWS) {
            pathToDrivers += "/windows";
            CHROME_DRIVER_EXECUTABLE = pathToDrivers + "/chromedriver.exe";
            IE_DRIVER_EXECUTABLE = pathToDrivers + "/IEDriverServer_x86.exe";
        }
        else if(SystemUtils.IS_OS_LINUX) {
            pathToDrivers += "/unix";
            CHROME_DRIVER_EXECUTABLE = pathToDrivers + "/chromedriver";
            IE_DRIVER_EXECUTABLE = null;
        }
        else if (SystemUtils.IS_OS_MAC) {
            CHROME_DRIVER_EXECUTABLE = pathToDrivers + "/chromedriver";
            IE_DRIVER_EXECUTABLE = null;
        }
        else {
            CHROME_DRIVER_EXECUTABLE = null;
            IE_DRIVER_EXECUTABLE = null;
        }
    }
    
    public static WebDriver createWebDriver(WebDriverType type) {
        WebDriver driver = null;

        switch (type) {
            case FIREFOX:
                driver = createFirefoxDriver();
                break;
            case CHROME:
                driver = createChromeDriver();
                break;
            case INTERNETEXPLORER:
                driver = createIEDriver();
                break;
            case SAFARI:
                driver= createSafariDriver();
                break;
            default:
                throw new WebDriverException("Could not initialze WebDrive instance. Available webdrivers are Firefox, Chrome, Internet Explorer and Safari");
        }

        driver.manage().timeouts().implicitlyWait(DEFAULT_IMPLICIT_WAIT_TIME, TimeUnit.SECONDS);
        
        return driver;
    }
    
    private static WebDriver createFirefoxDriver() {
        DesiredCapabilities dc = createDesiredCapabilities(WebDriverType.FIREFOX);
        WebDriver driver = new FirefoxDriver(dc);
        
        return driver;
    }
    
    private static WebDriver createChromeDriver() {
        System.out.println(CHROME_DRIVER_EXECUTABLE);
        System.setProperty("webdriver.chrome.driver", CHROME_DRIVER_EXECUTABLE);
        
        DesiredCapabilities dc = createDesiredCapabilities(WebDriverType.CHROME);
        WebDriver driver = new ChromeDriver(dc);
        
        return driver;
    }
    
    private static WebDriver createIEDriver() {
        System.out.println(IE_DRIVER_EXECUTABLE);
        System.setProperty("webdriver.ie.driver", IE_DRIVER_EXECUTABLE);
        
        DesiredCapabilities dc = createDesiredCapabilities(WebDriverType.INTERNETEXPLORER);
        WebDriver driver = new InternetExplorerDriver(dc);
        
        return driver;
    }
    
    private static WebDriver createSafariDriver() {
        DesiredCapabilities dc = createDesiredCapabilities(WebDriverType.SAFARI);
        WebDriver driver = new SafariDriver(dc);

        return driver;
    }
    
    private static DesiredCapabilities createDesiredCapabilities(WebDriverType type) 
    {
        DesiredCapabilities dc = null;    

        switch (type) {
            case FIREFOX:
                dc = DesiredCapabilities.firefox();
                break;

            case CHROME:
                dc = DesiredCapabilities.chrome();
                break;

            case INTERNETEXPLORER:
                dc = DesiredCapabilities.internetExplorer();
                break;
                
            case SAFARI:
                dc = DesiredCapabilities.safari();
        }
        
        dc.setCapability(CapabilityType.ACCEPT_SSL_CERTS, true);
        dc.setCapability(CapabilityType.SUPPORTS_JAVASCRIPT, true);
        return dc;
    }
}
