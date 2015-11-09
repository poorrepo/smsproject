package SmsServer;

import com.google.gson.Gson;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.ServerSocket;
import java.net.Socket;
import java.net.SocketException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;
/**
* A simple socket server
* @author faheem
*
*/

public class SocketHandler implements Runnable
{
    private ServerSocket serverSocket;
    private int port;
    JSONToQuery parsedJson;
    private String localUser = "admin";
    private String localPassword = "passwd";
    
    Thread t;
    public SocketHandler(int port) {
        this.port = port;
        t = new Thread(this);
        t.start();
    }
    
    public void run(){
        try 
        {
            System.out.println("-> Uruchamianie watku nasluchowania polaczeni przychodzacych...");
            this.createSocket();
            while(true) 
            {
                this.listenPort();
            }
        } 
        catch (Exception e) 
        {
            System.out.println("hej ho");
            e.printStackTrace();
        }
        finally
        {
            try 
            {
                this.zamknijSocket();
            } 
            catch (IOException ex) 
            {
                Logger.getLogger(SocketHandler.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
    }
    
    public void createSocket() throws IOException {
        System.out.println("\tUruchamianie socketu na porcie:" + port);
        serverSocket = new ServerSocket(port);
    }
    
    public void startListeningPort() throws IOException{
        while(true) 
        {
            this.listenPort();
        }
    }
    
    public void listenPort() throws IOException{
        //oczekiwanie na klienta
        System.out.println("-> Oczekiwanie na klienta...");
        Socket client = serverSocket.accept();

        //jezeli klient podlaczony to obsluguj
        if(client.isConnected())
        {
            Date czasPodlaczenia = new Date();

            System.out.println("=> Polaczenie przychodzace... " + czasPodlaczenia.toString() + client.getInetAddress());
   
            try
            {
                ObslugaStrumienSocket(client, czasPodlaczenia);
            }
            catch(SocketException e)
            {
                System.out.println("Blad komunikacji poprzez socket");
            }
            
        }
    }
    
    private void ObslugaStrumienSocket(Socket client, Date czasPodlaczenia) throws IOException, SocketException {
        //ttuaj przetwarzanie jest to co przyjdzie na socket (w formacie json)
        
        BufferedReader br = new BufferedReader(new InputStreamReader(client.getInputStream()));
        BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(client.getOutputStream()));
               
        String line = "";
        String authorizedUser="admin";
        String authorizedUserPass="tajnehaslo";
        while ((line = br.readLine()) != null) 
        {
        //System.out.println("RX: " + line);
            try
            {
                 parsedJson = new Gson().fromJson(line, JSONToQuery.class);

                 //jak user i pass ok
                 //dodaj query do kolejki
                 if((parsedJson.getUser().equals(authorizedUser))&&(parsedJson.getPass().equals(authorizedUserPass)))
                 {
                    //autoryzacja ok, identyfikacja zadania
                    bw.write(queryExecution(parsedJson));
                 }
                 else
                 {
                     System.out.println("Próba nieautoryzowanego dostepu! " + czasPodlaczenia.toString() + client.getInetAddress());

                 }
                 //bw.write("ANSW: odpowiedz\n");
            }
            catch (Exception e)
            {
                System.out.println("Odebrano niepoprawne żądanie. Błąd parsowania.");
            }
             finally
            {
                bw.write("\n");
                bw.flush();
            }        
        }
        
        //zamykanie polaczenia
        bw.close();
        br.close();
        zamknijPolaczenie(client);
    }  
    
    private void zamknijPolaczenie(Socket client) throws IOException{
        client.close();
    }
    
    public void zamknijSocket() throws IOException{
        if(!serverSocket.isClosed())
        {
            serverSocket.close();
            System.out.println("Zamknieto socket");
        }
    }
    
    //weryfikacja uzytkownika
    private boolean credentialsValidate(){
        //weryfikacja usera i hasla na podstawie danych z bazy danych
        //tymczasow na podstawie statycznych zmiennych
        if(localUser.equals(parsedJson.getUser()) &&
           localPassword.equals(parsedJson.getPass())) {
            return true;
        }
        else {
            return false;
        } 
    }
    
    //obsluga zapytania i zwracanie odpowiedzi do klienta
    private String queryExecution(JSONToQuery incomingJSON)
    {
        String ret = null;
        try
        {  
            switch(incomingJSON.getQuery())
            {
                case "getAppConfig":
                {
                    ret="KONFIGURACJA PROGRAMU";
                    break;
                }
                case "newTask":
                {
                    //System.out.println("wyk:" +incomingJSON.toString());
                    BuforZadan.sendTaskToDB(incomingJSON);
                    ret="0";
                    break;
                }  
                case "hello":
                {
                    //System.out.println("wyk:" +incomingJSON.toString());
                    ret="hi";
                    break;
                }                       
                default:
                {
                    ret="NULL";
                }
            }
        }
        catch (Exception e)
        {
            System.out.println("Odebrano niepoprawne żądanie. Błąd parsowania.");
        }
        finally
        {
            return ret;
        }
    }
    /**
    * Creates a SocketServer object and starts the server.
    *
    * @param args
    */
}