/*
 * KobsApp.java
 */
package kobs;

import org.jdesktop.application.Application;
import org.jdesktop.application.SingleFrameApplication;
import java.io.*;
import java.util.*;
import java.util.Date;
import java.text.*;

/**
 * The main class of the application.
 */
public class KobsApp extends SingleFrameApplication {

    static Properties lang;
    static Properties props;
    static KReadInfoXML members;
    static HashMap<String, HashMap<String,String>> attendies;
    static KReadInfoXML locations;
    static KReadInfoXML activities;
    static Date actDate;
    static String actDateString;

    /**
     * At startup create and show the main frame of the application.
     */
    @Override
    protected void startup() {
        show(new KobsView(this));
    }

    /**
     * This method is to initialize the specified window by injecting resources.
     * Windows shown in our application come fully initialized from the GUI
     * builder, so this additional configuration is not needed.
     */
    @Override
    protected void configureWindow(java.awt.Window root) {
    }

    /**
     * A convenient static getter for the application instance.
     * @return the instance of KobsApp
     */
    public static KobsApp getApplication() {
        return Application.getInstance(KobsApp.class);
    }

    /**
     * Main method launching the application.
     */
    public static void main(String[] args) {

        lang = new Properties();
        try {
            lang.load(new FileInputStream(KConstants.LangFileName));
        } catch (IOException ignored) {
        }
        props = new Properties();
        try {
            props.load(new FileInputStream(KConstants.PrefsFileName));
        } catch (IOException ignored) {
        }
        importUserDB();
        actDate=new java.util.Date();
         DateFormat df = new SimpleDateFormat(KobsApp.lang.getProperty("DateFormat","MM/dd/yyyy"));

        actDateString=df.format(actDate);
        launch(KobsApp.class, args);
    }

    public static void importUserDB() {
        members = new KReadInfoXML(KConstants.DBDataFileName, "usr_id", "members");
        locations = new KReadInfoXML(KConstants.DBDataFileName, "ort", "orte");
        activities = new KReadInfoXML(KConstants.DBDataFileName, "usr_id", "trainings");
        attendies =new HashMap<String, HashMap<String,String>>();
    }


}
