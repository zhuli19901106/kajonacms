/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package selenium.pages;

import selenium.pages.base.Page;

/**
 *
 * @author smy
 */
public class LoginPage extends Page {
    
    
    public void login() {
        input("username", "somename");
        input("password", "somename");
        click("submitbutton");
    }
    
}
