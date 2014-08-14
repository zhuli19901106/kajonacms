/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package testng.login;

import org.testng.Assert;
import org.testng.annotations.Test;
import selenium.pages.LandingPage;
import selenium.pages.LoginPage;
import testng.base.SeleniumTestBase;

/**
 *
 * @author smy
 */
public class LoginTest extends SeleniumTestBase {
    
    LandingPage page = null;
    
    
    @Test
    public void login() {
        page = this.login("admin", "admin");
        Assert.assertNotNull(page);
    }
    
    @Test(dependsOnMethods = "login" )
    public void logout() {
        LoginPage loginPage = page.getTopMenu().logOut();
        Assert.assertNotNull(loginPage);
    }
    
}
