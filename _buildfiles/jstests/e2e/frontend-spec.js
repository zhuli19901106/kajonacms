
describe('frontend', function() {

    beforeEach(function() {
        browser.ignoreSynchronization = true;
    });

    it('test front page', function() {
        browser.get('http://127.0.0.1:8080');

        expect(browser.driver.findElement(by.css('.contentRight')).getText()).toMatch(/This installation of Kajona was successful/);
    });

});
