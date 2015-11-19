
global.include = function(path){
    var data = require('fs').readFileSync(path, 'utf8');
    return require("vm").runInNewContext(data, global, path);
};

beforeEach(function () {

    var jsdom = require('../../node_modules/jsdom/lib/jsdom.js');
    var fs = require('fs');

    var html = fs.readFileSync('./spec/helpers/template.htm', 'utf8');

    var document = jsdom.jsdom(html);
    var window = document.defaultView;

    global.document = document;
    global.window = window;

    jQuery = require('../../node_modules/jquery/dist/jquery.min.js');
    global.jQuery = global.$ = jQuery;

    global.jQuery(global.document);

});

// JUnit reporting
(function() {

    var env = global.jasmine.getEnv();

    var reporters = require('jasmine-reporters');
    var junitReporter = new reporters.JUnitXmlReporter({
        savePath: './reporting',
        consolidateAll: false
    });

    env.addReporter(junitReporter);

}());


