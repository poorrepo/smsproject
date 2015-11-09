/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SmsServer;

public class JSONToQuery {
    private String user;

    private String id_task;

    public String getId() {
        return id_task;
    }

    public void setId(String id) {
        this.id_task = id;
    }
    private String pass;
    private String query;
    private String message;
    private String number;
    private String send_time;

    public String getSend_time() {
        return send_time;
    }

    public void setSend_time(String send_time) {
        this.send_time = send_time;
    }
    
    public String getUser() { return user; }
    public void setUser(String user) { this.user = user; }
    
    public String getPass() {
        return pass;
    }

    public void setPass(String pass) {
        this.pass = pass;
    }

    public String getQuery() {
        return query;
    }

    public void setQuery(String query) {
        this.query = query;
    }  
    
    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }
    
     public String getNumber() {
        return number;
    }

    public void setNumber(String message) {
        this.number = number;
    }   
       
    public String toString() {
        return String.format("user:%s, pass:%s, query:%s, number:%s, message:%s, send_time:%s", user,pass,query,number,message,send_time);
    }
    
}