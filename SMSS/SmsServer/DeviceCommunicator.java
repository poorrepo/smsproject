/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SmsServer;

import gnu.io.*;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.ArrayList;
import java.util.Enumeration;
import java.util.TooManyListenersException;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author Tomek
 */

public class DeviceCommunicator {
    
    //for containing the ports that will be found
    private Enumeration ports = null;
    //map the port names to CommPortIdentifiers

    private ArrayList<CommPortIdentifier> ListaPortow = new ArrayList<CommPortIdentifier>();
    
    //this is the object that contains the opened port

    private SerialPort serialPort = null;
    private CommPortIdentifier selectedPort = null;
    //input and output streams for sending and receiving data
    private InputStream inputStream = null;
    private OutputStream outputStream = null;

    //just a boolean flag that i use for enabling
    //and disabling buttons depending on whether the program
    //is connected to a serial port or not
    private boolean bConnected = false;
    private String deviceName = "";
    //the timeout value for connecting with the port
    final static int TIMEOUT = 2000;

    //some ascii values for for certain things
    final static int SPACE_ASCII = 32;
    final static int DASH_ASCII = 45;
    final static int NEW_LINE_ASCII = 10;

    //a string for recording what goes on in the program
    //this string is written to the GUI
    String logText = "";
        
    protected static String actionDeviceTest = "AT"; //w przypadku komunikacji zwraca OK
    protected static String actionDeviceTestResponse = "OK";
    protected static String actionDeviceName = "AT+GMM"; //zwraca nazwe modelu tel/modemu
    
    protected ArrayList<String> PrepareMessage(String number, String message) {
        //znak 23 End of Transmission Block ETB ctrl+z
        ArrayList<String> command = new ArrayList<String>();
        command.add(""+(char)27); //escape, czyszczenie terminala
        command.add("AT\r");
        command.add("AT+CMGF=1\r");
        command.add("AT+CMGS=\""+number+"\"\r");
        command.add(message+(char)26);
        return command;
    }
       
    protected void processInputFromDevice(String out) {
        String mod2 = out;
        mod2 = mod2.replace((char)13, (char)32).replaceAll("[\n\n]+", "\n");
        
        //wyszukiwanie koncowych znakow pustych i ich usuwanie
        int poczatekZer=mod2.length();
        //System.out.println(mod2.length());
        
        for(int i=mod2.length()-1;i>0;i--)
        {
            if((byte)mod2.charAt(i)!=0)
            {
                poczatekZer=i;
                break;
            }
        }
        mod2 = mod2.substring(0, poczatekZer-1).replaceAll("\n","");
      
        String[] outputLines = mod2.split("  ");
        
        //System.out.println("\t "+mod2 +"");
        
        TaskManager.msg_send_fail=false;
        for(String x : outputLines)
        {
            System.out.println("\t out: "+x);
            if(x.contains("ERROR"))
            {
                TaskManager.msg_send_fail=true;
            }
        }
    }
        
    public void executeAction(String act) {
        try 
        {
            this.connect(CommPortIdentifier.getPortIdentifier(selectedPort.getName()));
        } 
        catch (NoSuchPortException ex) 
        {
            Logger.getLogger(DeviceCommunicator.class.getName()).log(Level.SEVERE, null, ex);
        }
        
        this.initIOStreams();
        
        this.serialWriter(act);
        
        try 
        {
            Thread.sleep(1000);
        } 
        catch (InterruptedException ex) 
        {
            Logger.getLogger(DeviceCommunicator.class.getName()).log(Level.SEVERE, null, ex);
        }
        
        this.serialReader();
        this.disconnect();
    }
       
    //testowanie portu COM
    public Boolean testComPort() {
                
        for(CommPortIdentifier ListPort : ListaPortow)
        {
            if(Konfiguracja.getDeviceComPort().equals(ListPort.getName()))
            {
                selectedPort = ListPort;
                break;
            }
        }
        
        try 
        {          
            if(!(selectedPort.getName().isEmpty()))
            {            
                //System.out.println("Obslugiwany port: "+selectedPort.getName());

                this.executeAction(actionDeviceTest);
                
                return true;
            }
            else
            {
                return false;
            }
        }
        catch (NullPointerException ex)
        {
            System.out.println("Brak okreslonego portu w systemie: "+Konfiguracja.getDeviceComPort());
            return false;
        }
    }
    
    //wyszukiwanie portow COM w systemiem, zapis do listy
    public void searchForPorts() {
        ports = null;
        ports = CommPortIdentifier.getPortIdentifiers();

        while (ports.hasMoreElements())
        {
            CommPortIdentifier curPort = (CommPortIdentifier)ports.nextElement();

            //get only serial ports
            if (curPort.getPortType() == CommPortIdentifier.PORT_SERIAL)
            {
                ListaPortow.add(curPort);
            }
        }
    }
    
    //ustanawiane polaczenia
    public void connect(CommPortIdentifier selectedPort) {
        
        //String selectedPort = Konfiguracja.getDeviceComPort();
        CommPort commPort = null;
        
        try
        {
            commPort = selectedPort.open("TigerControlPanel", TIMEOUT);

            serialPort = (SerialPort)commPort;
        }
        catch (PortInUseException e)
        {
            logText = selectedPort.getName() + " w użyciu. (" + e.toString() + ")";
            System.out.println(logText);
        }
        catch (Exception e)
        {
            logText = "Nie udało się otworzyć " + selectedPort.getName() + "(" + e.toString() + ")";
            System.out.println(logText);
        }
    }
    
    //rozlaczanie, zamykanie streamow
    public void disconnect() {
        try
        {
            serialPort.removeEventListener();
            serialPort.close();
            inputStream.close();
            outputStream.close();

        }
        catch (Exception e)
        {
            logText = "Failed to close " + serialPort.getName()+ "(" + e.toString() + ")";
            System.out.println(logText);
        }
    }
    
    //inicjacja streamow
    public boolean initIOStreams() {

        boolean successful = false;

        try 
        {
            inputStream = serialPort.getInputStream();
            outputStream = serialPort.getOutputStream();
   
            successful = true;
            return successful;
        }
        catch (IOException e) 
        {
            logText = "I/O Streams failed to open. (" + e.toString() + ")";

            return successful;
        }
    }
       
    //odczyt danych z urzadzenia
    public void serialReader() {

        try
        {
            byte mBytesIn [] = new byte[50];
            inputStream.read(mBytesIn);
            String logText = new String(mBytesIn);
            
            processInputFromDevice(logText);
            
        }
        catch (Exception e)
        {
            logText = "Failed to read data. (" + e.toString() + ")";
            System.out.println(logText);
        }
    }
    
    //zapisywanie do output stream i wysylka do urzadzenia
    public void serialWriter(String command) {
        try
        {
            //System.out.print("Polecenie: ");
            outputStream.write((command+"\r").getBytes());
            outputStream.flush();
        }
        catch (Exception e)
        {
            logText = "Failed to write data. (" + e.toString() + ")";
            System.out.println(logText);
        }
    }
    
    //nieuzywana
    public void initListener() {
        try
        {
            serialPort.addEventListener(null);
            serialPort.notifyOnDataAvailable(true);
        }
        catch (TooManyListenersException e)
        {
            logText = "Too many listeners. (" + e.toString() + ")";
            System.out.println(logText);
        }
    }
    
}
