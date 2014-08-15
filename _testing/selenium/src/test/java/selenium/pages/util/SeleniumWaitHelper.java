package selenium.pages.util;

import java.util.concurrent.TimeUnit;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import selenium.webdriver.WebDriverFactory;

/**
 *
 * @author stefan.meyer1@yahoo.de
 */
public class SeleniumWaitHelper {
    /**
     * Watis for the element until it is in the DOM and visible on the page.
     *
     * @param driver
     * @param locator
     * @param timeOutInSeconds - number of secons to wait until an exception is
     * being thrown
     */
    public static void waitForElementUntilPresent(WebDriver driver, By locator, long timeOutInSeconds) {
        SeleniumWaitHelper.nullifyImplicitWait(driver);
        WebDriverWait wait = new WebDriverWait(driver, timeOutInSeconds);
        wait.until(ExpectedConditions.visibilityOfElementLocated(locator));
        SeleniumWaitHelper.resetImplicitWait(driver);
    }

    /**
     * Nullifies the implicit wait
     *
     * @param driver
     */
    public static void nullifyImplicitWait(WebDriver driver) {
        driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait() 
    }

    /**
     * resets the implicit wait to default values.
     *
     * @param driver
     */
    public static void resetImplicitWait(WebDriver driver) {
        SeleniumWaitHelper.nullifyImplicitWait(driver);//first nullify before seeting a new value
        driver.manage().timeouts().implicitlyWait(WebDriverFactory.DEFAULT_IMPLICIT_WAIT_TIME, TimeUnit.SECONDS); //nullify implicitlyWait() 
    }
}
