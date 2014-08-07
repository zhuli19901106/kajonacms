
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;
import selenium.pages.LandingPage;
import selenium.pages.LoginPage;
import selenium.properties.MessagesEnum;
import selenium.webdriver.WebDriverFactory;
import selenium.webdriver.WebDriverType;

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
    
    public static void main(String[] args) {
        
        WebDriver driver = WebDriverFactory.createWebDriver(WebDriverType.FIREFOX);
        driver.get(MessagesEnum.SELENIUM.getString("selenium.defaultUrl"));
        
        LoginPage loginPage = PageFactory.initElements(driver, LoginPage.class);
        LandingPage p = loginPage.login(MessagesEnum.SELENIUM.getString("selenium.defaultUserName"), 
                MessagesEnum.SELENIUM.getString("selenium.defaultPassword"));
        p.getTopMenu().search("pro");
        //p.showAllUserMessages();
        p.getTopMenu().logOut();
    }
    
}
