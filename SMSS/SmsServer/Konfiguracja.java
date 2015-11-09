/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



package SmsServer;

/**
 *
 * @author Tomek
 */

import java.io.File;
import java.io.IOException;
import java.util.Properties;

public class Konfiguracja 
{
    static Properties configFile;
    private static Boolean ConfStatus = false; //mowi czy konfiguracja jest wczytana
    private static Boolean ConfTest = false; // mowi czy konfiguracja bazy jest poprawna
    
    public static Boolean getConfStatus() {
        return ConfStatus;
    }

    private static String dbUser;
    private static String dbPwds;
    private static String dbHost;
    private static String dbName;
    private static String socketPort;
    private static String deviceComPort;
    private static String deviceName;

    public static String getDeviceName() {
        return deviceName;
    }

    public static void setDeviceName(String deviceName) {
        Konfiguracja.deviceName = deviceName;
    }
    
    public static void WczytajKonfiguracjeZpliku(String ConfigFilePath)
    {
        dbUser = "";
        dbPwds = "";
        dbHost = "";
        dbName = "";
        socketPort = "";
        deviceComPort = "";
        
        File f = new File(ConfigFilePath);
        configFile = new java.util.Properties();
        //jezeli plik konfiguracyjny istnieje
        if(f.exists() && f.canRead()) 
        {
            System.out.println("Plik konfiguracyjny istnieje... "+f.getAbsolutePath());    
            try 
            {
                configFile.loadFromXML( f.toURI().toURL().openStream() );
            } 
            catch (IOException ex) 
            {
                System.out.println(ex.getMessage());
            }
            ConfStatus = true;

            //ladowanie zmiennych
            dbUser = getProperty("dbUser");
            dbPwds = getProperty("dbPwds");
            dbHost = getProperty("dbHost");
            dbName = getProperty("dbName");
            socketPort = getProperty("socketPort");
            deviceComPort = getProperty("deviceComPort");
        }
        else
        {
            System.out.println("Plik konfiguracyjny "+f.getAbsolutePath()+" nie istnieje...");
            ConfStatus = false;
            System.exit(1);
        }
    }

    
    private static String getProperty(String key) 
    {     
        String value = configFile.getProperty(key);
        return value;
    }
    
    public static String getDbUser() {
        return dbUser;
    }

    public static String getDbPwds() {
        return dbPwds;
    }

    public static String getDbHost() {
        return dbHost;
    }

    public static String getDbName() {
        return dbName;
    }

    public static Boolean getConfTest() {
        return ConfTest;
    }
    
    public static int getSocketPort() {
        return Integer.parseInt(socketPort);
    }    
    
    public static String getDeviceComPort() {
        return deviceComPort;
    }     
        
}