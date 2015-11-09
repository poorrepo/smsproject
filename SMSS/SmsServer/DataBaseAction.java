/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SmsServer;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

/**
 *
 * @author Tomek
 */

public class DataBaseAction 
{
    private Connection connect = null;
    private Statement statement = null;
    private PreparedStatement preparedStatement = null;
    private ResultSet resultSet = null;
    
    public DataBaseAction() {}
    
    //Odczytywanie zadan z bazy danych
    public void readDataBase(String query) throws Exception {
        try 
        {
            //ladowanei sterownika bazy danych
            try 
            {
                Class.forName("com.mysql.jdbc.Driver");
            } 
            catch (ClassNotFoundException e) 
            {
                throw e;
            }
            // Konfiguracja polaczenia z baza danych
            connect = DriverManager.getConnection(
                    "jdbc:mysql://"+Konfiguracja.getDbHost()+
                    "/"+Konfiguracja.getDbName()+"?"+
                    "user="+Konfiguracja.getDbUser()+
                    "&password="+Konfiguracja.getDbPwds()
                );

            // Nawiazaywanie polaczenia z baza
            statement = connect.createStatement();
            
            // Pobieranie wynikow operacji
            resultSet = statement.executeQuery(query);
            
            // Przetwarzanie wynikow

            writeResultSet(resultSet);
        } 
        catch (Exception e) 
        {
            throw e;
        } 
        finally 
        {
            close();
        }
    }
    
    // Metoda zapisu do bazy danych
    public void writeDataBase(String query) throws Exception {
      try 
      {
        Class.forName("com.mysql.jdbc.Driver");
        
        connect = DriverManager.getConnection("jdbc:mysql://"+Konfiguracja.getDbHost()+"/"+Konfiguracja.getDbName()+"?"+"user="+Konfiguracja.getDbUser()+"&password="+Konfiguracja.getDbPwds());
        preparedStatement = connect.prepareStatement(query);
        preparedStatement.executeUpdate();
      } 
      catch (Exception e) 
      {
        throw e;
      } 
      finally 
      {
        close();
      }
    }
    
    // Aktualizacja rekordow bazy danych
    public void updateDataBase(String query) throws Exception {
     try 
      {        
        Class.forName("com.mysql.jdbc.Driver");
        
        connect = DriverManager.getConnection("jdbc:mysql://"+Konfiguracja.getDbHost()+"/"+Konfiguracja.getDbName()+"?"+"user="+Konfiguracja.getDbUser()+"&password="+Konfiguracja.getDbPwds());
        preparedStatement = connect.prepareStatement(query);
        preparedStatement.executeUpdate();
      } 
      catch (Exception e) 
      {
        throw e;
      } 
      finally 
      {
        close();
      }
    }
    
    // Usuwanie zadan z bazy danych ( po wykonianu zadania)
    // W bazie znajduje sie triggger ktory przed usunieciem utworzy wpis w tabeli wiadomosci archiwalnych
    public void removeDataBase(String query) throws Exception {
      try 
      {
        Class.forName("com.mysql.jdbc.Driver");

        connect = DriverManager.getConnection("jdbc:mysql://"+Konfiguracja.getDbHost()+"/"+Konfiguracja.getDbName()+"?"+"user="+Konfiguracja.getDbUser()+"&password="+Konfiguracja.getDbPwds());
        Statement preparedStatement = connect.createStatement();
        preparedStatement.executeUpdate(query);
       } 
      catch (Exception e) 
      {
        
      } 
      finally 
      {
        close();
      }
    }       

    // Przetwarzanie wykinow z bazy danych
    // w tym wypadu zapis zadan do bufora
    private void writeResultSet(ResultSet resultSet) throws SQLException {
        //dla kazdego zwroconego wiersza, dodaj do bufora
        while(resultSet.next()) 
        {         
            BuforZadan.setTask
            (
                resultSet.getString("id_task"),
                resultSet.getString("message"), 
                resultSet.getString("phone_number")
            );
        }
    }

    // Zamykanie polaczenia z baza danych
    private void close() {
      try {
        if (resultSet != null) {
          resultSet.close();
        }

        if (statement != null) {
          statement.close();
        }

        if (connect != null) {
          connect.close();
        }
      } catch (Exception e) {

      }
    }

    // Metoda testowania bazy danych
    public void testDataBase() throws Exception {
        this.readDataBase("SELECT COUNT(tasks.id_task) as kolejka from sms.tasks;");
    }
} 