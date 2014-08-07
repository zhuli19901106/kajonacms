package selenium.pages;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

/**
 *
 * @author stefan.meyer1@yahoo.de
 */
public class LoginPage {
    
    private WebDriver driver = null;
    
    @FindBy(xpath = "//*[@id='name']")
    WebElement userName;
    
    @FindBy(xpath = "//*[@id='passwort']")
    WebElement password;
    
    @FindBy(xpath = "html/body/div[1]/div/div/div/div[2]/div/form/div[3]/button")
    WebElement loginBtn;
    
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
        
        return PageFactory.initElements(driver, LandingPage.class);
    }
    
}
