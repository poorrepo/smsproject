/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SmsServer;

import java.io.IOException;

/**
 *
 * @author Tomek
 
**/

public class ServerMain {
    
    public static DeviceCommunicator dvc = new DeviceCommunicator();

    public static void main(String[] arg) throws IOException
    {
        String ConfigFile = "config.xml";
        
        System.out.println("URUCHAMIANIE PROGRAMU...");
    
        //ladowanie konfiguracji
        System.out.println("#####################################################");
        System.out.println("ŁADOWANIE KONFIGURACJI...");
        
        Konfiguracja.WczytajKonfiguracjeZpliku(ConfigFile);
        
        //wyswietlanei zmeinnych konfiguracyjnych
        
        System.out.println("\t DB HOST: "+Konfiguracja.getDbHost());
        System.out.println("\t DB USER: "+Konfiguracja.getDbUser());
        System.out.println("\t DB PASS: "+Konfiguracja.getDbPwds());
        System.out.println("\t DB NAME: "+Konfiguracja.getDbName());
        System.out.println("\t Socket Port: "+Konfiguracja.getSocketPort());
        System.out.println("\t Device COM Port: "+Konfiguracja.getDeviceComPort());
        
        //testowanie konfiguracji
        System.out.println("#####################################################");
        System.out.println("TESTOWANIE KONFIGURACJI");
        
        //testowanie komunikacji z urzadzeniem
        
        
        System.out.println("-> Wyszukiwanie portów COM w systemie... ");
        dvc.searchForPorts(); //wyszukiwanie portow COM w systemie
        System.out.println("#####################################################");
        
        System.out.println("-> Testowanie komunikacji z urządzeniem... ");
        //jezeli wybrany port istnieje i dziala (actionDeviceTest) to ok
        if(!dvc.testComPort()){ 
            System.exit(1);
        }
        
        //testowanie komunikacji z urzadzeniem
        System.out.println("\t Nazwa urządzenia: ");
        dvc.executeAction(DeviceCommunicator.actionDeviceName);
        
        //inicjacja watku taskmanagera
        TaskManager kp1 = new TaskManager();
        
        //zresetowanie zajetosci zadan w bazie danych
        BuforZadan.resetTasksInDB();
                
        // inicjacja socketu i uruchomienie jako watku
        SocketHandler socketServer = new SocketHandler(Konfiguracja.getSocketPort()); 
       
        //info ze tutaj konczy sie main
        System.out.println("Main END");           
    }  
}
