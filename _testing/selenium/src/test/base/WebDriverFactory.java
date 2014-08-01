/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package core._testing.selenium.src.test.base;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.ie.InternetExplorerDriver;
import org.openqa.selenium.remote.CapabilityType;
import org.openqa.selenium.remote.DesiredCapabilities;

/**
 *
 * @author smy
 */
public class WebDriverFactory {
    
    private final static String CHROME_DRIVER_EXECUTABLE = "C:/Dev/selenium/client/webdrivers/chromedriver.exe";
    private final static String IE_DRIVER_EXECUTABLE = "C:/Dev/selenium/client/webdrivers/IEDriverServer.exe";
    
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
                //Used if the site is restricted and you continue to it
                //driver.navigate().to("javascript:document.getElementById('overridelink').click()");
                break;
                
                default:
        }

        return driver;
    }
    
    private static WebDriver createFirefoxDriver() {
        DesiredCapabilities dc = createDesiredCapabilities(WebDriverType.FIREFOX);
        WebDriver driver = new FirefoxDriver(dc);
        
        return driver;
    }
    
    private static WebDriver createChromeDriver() {
        System.setProperty("webdriver.chrome.driver", CHROME_DRIVER_EXECUTABLE);
        
        DesiredCapabilities dc = createDesiredCapabilities(WebDriverType.CHROME);
        WebDriver driver = new ChromeDriver(dc);
        
        return driver;
    }
    
    private static WebDriver createIEDriver() {
        System.setProperty("webdriver.ie.driver", IE_DRIVER_EXECUTABLE);
        
        DesiredCapabilities dc = createDesiredCapabilities(WebDriverType.INTERNETEXPLORER);
        WebDriver driver = new InternetExplorerDriver(dc);
        
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
                dc.setCapability(InternetExplorerDriver.INTRODUCE_FLAKINESS_BY_IGNORING_SECURITY_DOMAINS, true);
                break;
        }
        
        dc.setCapability(CapabilityType.ACCEPT_SSL_CERTS, true);
        dc.setCapability(CapabilityType.SUPPORTS_JAVASCRIPT, true);
        return dc;
    }
}
