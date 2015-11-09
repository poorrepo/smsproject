/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SmsServer;

import com.google.gson.Gson;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author Tomek
 */

public class BuforZadan
{
    static int taskNumber;
    static boolean taskBusy;
    static int iloscTransakcji;
    static ArrayList<String> buforZad = new ArrayList<String>();
    static int rozmiarBuforuZadan = 5;
    static String nextTask = "";
    static DataBaseAction dba = new DataBaseAction();
    
    public BuforZadan() {
        taskNumber = 0;
        iloscTransakcji = 0;
    }
    
    //synchronized 
    public static void getZad() {
        if(buforZad.size()>0)
        {        
            nextTask=buforZad.get(0);
        }
        else
        {
            //ustawnie nastepnego zad jako brak
            nextTask="";
            
            //jak juz nic nie ma w buforze to sprobuj pobrac z bazy danych
            GetTasksFromDB();
        }
        
        if(buforZad.size()>0)
        {   
            //pobieranie z listy zadania z poczatku kolejki
            nextTask=buforZad.get(0);
        }
    }
    
    public static void GetTasksFromDB() {
        try 
        {
            //zrobic zeby zwrocil to albo dodal do listy bezposrednio i oznaczyl ze odczytane. moze usunac bezposrednio zeby nie wadzily?
            //System.out.println("# Bufor pusty. Odpytywanie bazy danych...");
            
            String selectedBy=Konfiguracja.getDeviceComPort();
            
            //markowanie zadan w bazie. onzacza ze zadania beda wykonywane przez dana instancje programu(urzadzenie)
            String updateQuery = "update sms.tasks set selected_by = '"+selectedBy+"' where id_task in (select id_task from(select id_task, send_time FROM sms.tasks where (selected_by is null OR selected_by = '') AND send_time <= now() order by send_time asc limit 5)as t);";
            dba.updateDataBase(updateQuery);
            
            //String selectQuery = "SELECT id_task, phone_number, message, send_time FROM sms.tasks where selected_by = '"+selectedBy+"' order by send_time";
            String selectQuery = "SELECT id_task, phone_number, message, send_time, selected_by FROM sms.tasks where selected_by = '"+selectedBy+"' AND send_time <= now() order by send_time";
            //dba.readDataBase("SELECT phone_number, message, sender, send_time FROM sms.tasks order by send_time asc limit 5");

            dba.readDataBase(selectQuery);
        } 
        catch (Exception ex) 
        {
            Logger.getLogger(BuforZadan.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    
    public static void RemoveOldestTask() {
        
        JSONToQuery parsedJson = new Gson().fromJson(BuforZadan.nextTask, JSONToQuery.class);

        buforZad.remove(0);  
        nextTask="";
        
        //i informacja do bazy ze wykonano
        String execQuery="DELETE FROM sms.tasks WHERE tasks.id_task='"+parsedJson.getId()+"';";
        //System.out.println(execQuery);
        try 
        {
            dba.removeDataBase(execQuery);
        } 
        catch (Exception ex) 
        {
            ex.printStackTrace();
        }
    }
    
    public static void setTask(String id_task, String msg, String number) {
        System.out.println("\t-> Zad do bufora:"+"{\"id_task\":\""+id_task+"\",\"message\":\""+msg+"\",\"number\":\""+number+"\"}");
        buforZad.add("{\"id_task\":\""+id_task+"\",\"message\":\""+msg+"\",\"number\":\""+number+"\"}");
    }
     
    public static void sendTaskToDB(JSONToQuery incomingJSON) {   
        try 
        {
            Date dt = new java.util.Date();
            SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            String currentTime = sdf.format(dt);    
            String czas_wyslania="";
            if(incomingJSON.getSend_time().isEmpty())
            {
                czas_wyslania=currentTime;
            }
            else
            {
                czas_wyslania=incomingJSON.getSend_time();
            }

            String query="INSERT INTO `sms`.`tasks` (`phone_number`, `message`, `sender`, `send_time`) VALUES ('"+incomingJSON.getNumber()+"', '"+incomingJSON.getMessage()+"', '"+incomingJSON.getUser()+"', '"+czas_wyslania+"')";
            System.out.println("-> Do bazy: "+incomingJSON.toString());
            dba.writeDataBase(query);
            //notify();

        } 
        catch (ParseException ex) 
        {
            Logger.getLogger(BuforZadan.class.getName()).log(Level.SEVERE, null, ex);
        }
        catch(Exception ex)
        {
            Logger.getLogger(BuforZadan.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    
    synchronized public static void resetTasksInDB() {
        String updateQuery = "update sms.tasks set selected_by = null";
        try 
        {
            System.out.println("-> Reset rezerwacji zadan w bazie danych...");
            dba.updateDataBase(updateQuery);
        } 
        catch (Exception ex) 
        {
            Logger.getLogger(BuforZadan.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
}