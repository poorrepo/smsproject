/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SmsServer;

import com.google.gson.Gson;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author Tomek
 */
class TaskManager implements Runnable
{
    public static boolean msg_send_fail = false;
    protected DataBaseAction dba;
    //protected static BuforZadan bufor;
    //HandleConnection sk;
    protected Thread t;
    
    public void Pause(){
        try 
        {
            System.out.println("-> Brak zadan w bazie danych... sleep(5s)");
            t.sleep(5000);
        } 
        catch (InterruptedException ex) 
        {
            Logger.getLogger(TaskManager.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    
    public TaskManager(){
        t = new Thread(this);
        t.start();
    }
    
    public void run(){   
        System.out.println("-> Uruchamianie watku przydzielania zadan...");
        while(true)
        {
            try 
            {
                this.checkTask();
            } 
            catch (InterruptedException ex) 
            {
                Logger.getLogger(TaskManager.class.getName()).log(Level.SEVERE, null, ex);
            }
        } 
    } 
    
    public void checkTask() throws InterruptedException{
        
        if(msg_send_fail==false)
        {
            BuforZadan.getZad();
        }
        if(!BuforZadan.nextTask.isEmpty())
        {
            //System.out.println("-> PRZETWARZANIE zadania: "+BuforZadan.nextTask);
            
            if(ServerMain.dvc.testComPort())
            {
                JSONToQuery parsedJson = new Gson().fromJson(BuforZadan.nextTask, JSONToQuery.class);
                for(String cmd : ServerMain.dvc.PrepareMessage(parsedJson.getNumber(), parsedJson.getMessage()))
                {
                    ServerMain.dvc.executeAction(cmd);
                }
                
                if(msg_send_fail==false)
                {
                    BuforZadan.RemoveOldestTask();
                }
                else
                {
                    System.out.println("Blad wysylania wiadomosci. Pauza...");
                    this.Pause();
                }
            }
        }
        else
        {
            this.Pause();
        }
    }
}
