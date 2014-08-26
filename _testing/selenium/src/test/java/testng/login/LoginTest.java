package testng.login;

import org.testng.Assert;
import org.testng.annotations.Test;
import selenium.pages.backend.AdminLoginPage;
import selenium.pages.backend.LandingAdminBasePage;
import testng.base.SeleniumTestBase;

/**
 *
 * @author smy
 */
public class LoginTest extends SeleniumTestBase {
    
    LandingAdminBasePage page = null;
    
    
    @Test
    public void login() {
        page = this.login("admin", "admin");
        Assert.assertNotNull(page);
    }
    
    @Test(dependsOnMethods = "login")
    public void chooseAspect() {
        page.getTopMenu().selectAspect("IMS");
    }
    
    @Test(dependsOnMethods = {"login", "chooseAspect"} )
    public void logout() {
        AdminLoginPage adminLoginPage = page.getTopMenu().logOut();
        Assert.assertNotNull(adminLoginPage);
    }
    
}
