/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package selenium.properties;

import java.io.FileInputStream;
import java.io.IOException;
import java.text.MessageFormat;
import java.util.Properties;

/**
 * 
 * @author stefan.meyer1@yahoo.de
 */
public enum MessagesEnum {
    
    SELENIUM("/src/test/resources/selenium/properties/selenium.properties"),
    TESTNG("/src/test/resources/testng/properties/testng.properties");
    
    private String path = null;
    private Properties properties = null;
    
    MessagesEnum(String path) {
        this.path = path;
        this.properties = this.loadPropertiesFiles(path);
    }
    
    /**
     * 
     * 
     * @param key
     * @return 
     */
    public String getString(String key) {
        String value =  this.properties.getProperty(key);
        if(value == null) {
            return '!' + key + '!';
        }
        return value;
    }
    
    /**
     * 
     * 
     * @param key
     * @param params
     * @return 
     */
    public String getString(String key, Object... params) {
        return MessageFormat.format(this.getString(key), params);
    }
    
    /**
     * 
     * @return 
     */
    public Properties getProperties() {
        return this.properties;
    }
    
    /**
     * 
     * @param relativePath
     * @return 
     */
    private Properties loadPropertiesFiles(String relativePath) {
        String currentUserDir = System.getProperty("user.dir");
        Properties p = null;
        try {
            FileInputStream f = new FileInputStream(currentUserDir + this.path);
            p = new Properties();
            p.load(f);
            f.close();
        } catch (IOException iOException) {
            iOException.printStackTrace();
        }
        
        return p;
    }
    
}
