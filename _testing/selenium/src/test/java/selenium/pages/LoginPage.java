package selenium.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import selenium.pages.util.Constants;
import selenium.pages.util.SeleniumUtil;

/**
 *
 * @author stefan.meyer1@yahoo.de
 */
public class LoginPage {
    
    private WebDriver driver = null;
    
    @FindBy(xpath = Constants.LOGIN_INPUT_USERNAME)
    private WebElement userName;
    
    @FindBy(xpath = Constants.LOGIN_INPUT_PASSWORD)
    private WebElement password;
    
    @FindBy(xpath = Constants.LOGIN_BUTTON)
    private WebElement loginBtn;
    
    @FindBy(xpath = Constants.LOGIN_ERROR_BOX)
    private WebElement loginErrorBox;
    
    public LoginPage(WebDriver driver) {
        this.driver = driver;
    }
    
    /**
     * 
     * @param username
     * @param password
     * @return 
     */
    public LandingPage login(String username, String password) {
        this.userName.sendKeys(username);
        this.password.sendKeys(password);
        this.loginBtn.click();
        
        if(SeleniumUtil.isElementPresent(driver, By.xpath(Constants.LOGIN_ERROR_BOX))) {
            return null;
        }
        
        return PageFactory.initElements(driver, LandingPage.class);
    }
}
