"use strict";

/**
 * require statements
 */
var BasePage = requireHelper('/pageobject/base/BasePage.js');
var Constants = requireHelper('/pageobject/Constants.js');

/**
 *
 */
class ContentTopBar extends BasePage {

    /**
     *
     */
    constructor() {
        super();
    }

    /**
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get elemContentTopBar() {
        return this.webDriver.findElement(By.css(Constants.CONTENTTOPBAR_CSS_CONTENTTOPBAR));
    }

    /**
     *
     * @returns {webdriver.promise.Promise<string>|*}
     */
    getTitle() {
        return this.elemContentTopBar.findElement(By.id(Constants.CONTENTTOPBAR_ID_TITLE)).getText();
    }


}

/** @type {ContentTopBar} */
module.exports = ContentTopBar;
