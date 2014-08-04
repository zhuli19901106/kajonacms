/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package selenium.pages.base;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import selenium.webdriver.WebDriverFactory;
import selenium.webdriver.WebDriverType;

/**
 *
 * @author smy
 */
public class Page {
    
    protected static WebDriver driver = null;
    
    
    public Page() {
        
        if(driver == null) {
            driver = WebDriverFactory.createWebDriver(WebDriverType.FIREFOX);
        }
    }
    
    public void click(String xPathKey) { 
        try {
            driver.findElement(By.xpath(xPathKey)).click();
        }
        catch (Exception e) {
            throw e;
        }
    }
    
    public void input(String xPathKey, String value) {
        try {
            driver.findElement(By.xpath(xPathKey)).sendKeys(value);
        }
        catch (Exception e) {
            throw e;
        }
    }
    
    
}
