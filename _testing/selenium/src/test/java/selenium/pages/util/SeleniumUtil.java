package selenium.pages.util;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;

/**
 *
 * @author smy
 */
public class SeleniumUtil {
    
    /**
     * 
     * @param driver
     * @param element - The Element to which should be moved to
     */
    public static void moveToElement(WebDriver driver, WebElement element) {
        Actions builder = new Actions(driver);
        builder.moveToElement(element).perform();
    }
    
    
    /**
     * Checks if an element is present in the DOM and is displayed in the page.
     * 
     * @param driver
     * @param locator - The locator to find the element
     * 
     * @return true if the elemtent is present in the DOM and is displayed in
     * the page, else false
     */
    public static boolean isElementPresentAndDisplayed(WebDriver driver, By locator) {
        try {
            driver.findElement(locator).isDisplayed();
            return true;
        } catch (NoSuchElementException ex) {
            return false;
        }
    }
    
    /**
     * Checks if an element is present in the DOM regardless if is displayed or not.
     *
     * @param driver
     * @param locator - The locator to find the element
     *
     * @return true if the element is present in the DOM, else false
     */
    public static boolean isElementPresent(WebDriver driver, By locator) {
        try {
            driver.findElement(locator);
            return true;
        } catch (NoSuchElementException ex) {
            return false;
        }
    }
}
