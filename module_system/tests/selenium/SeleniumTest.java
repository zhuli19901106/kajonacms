package core.module_system.tests.selenium;

import core._testing.selenium.src.test.base.SeleniumTestBase;
import org.testng.annotations.Test;

/**
 * Hello world!
 *
 */
public class SeleniumTest extends SeleniumTestBase
{
    @Test
    public void testABC1() {
        driver.get("https://localhost/agpV4/agp-core-project");
        
        //Used if the site is restricted and you continue to it
        //driver.navigate().to("javascript:document.getElementById('overridelink').click()");
    }
}