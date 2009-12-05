/*
 * 
 * KobsApp.java
 */
package klobs;

import org.jdesktop.application.Application;
import org.jdesktop.application.SingleFrameApplication;
import java.io.*;
import java.util.*;
import java.util.Date;
import java.text.*;
import javax.swing.*;

/**
 * The main class of the application.
 */
public class KlobsApp extends SingleFrameApplication {

    static Properties lang;
    static Properties props;
    static KReadInfoXML members;
    static HashMap<String, KStringHash> attendies;
    static KReadInfoXML locations;
    static KReadInfoXML activities;
    static Date actDate;
    static String actDateString;
    static Date actStartTime;
    static String actStartTimeString;
    static Date actEndTime;
    static String actEndTimeString;
    static String actLocation;
    static String actLocationId="";

    /**
     * At startup create and show the main frame of the application.
     */
    @Override
    protected void startup() {
        this.getApplication().addExitListener(new ExitListener() {

            public boolean canExit(EventObject eo) {
                Object[] options = {
                    KlobsApp.lang.getProperty("QuestionCloseSessionYes", "Close record and Quit"),
                    KlobsApp.lang.getProperty("QuestionCloseSessionNo", "Return to program")
                
                };
                if (JOptionPane.showOptionDialog(null,
                        KlobsApp.lang.getProperty("QuestionCloseSession", "Do you want to close your record and finish the program?"),
                        KlobsApp.lang.getProperty("QuestionCloseSessionTitle", "Close Record?"),
                        JOptionPane.YES_NO_OPTION,
                        JOptionPane.QUESTION_MESSAGE,
                        null, //do not use a custom Icon
                        options, //the titles of buttons
                        options[1]//default button title
                        //default button title
                        ) == 0) { // Bedingung des Herunterfahrens testen
                    KSessionSave saveSession= new KSessionSave(KConstants.DBSessionFileName);
                    return true;
                }
                return false;
            }

            public void willExit(EventObject eo) {
                // Aufräumen...
            }
        });

        KobsView kobsView=new KobsView(this);
        show(kobsView);
                        while (KlobsApp.actLocationId.contentEquals("")) {
                    kobsView.setDate();

                }

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
    public static KlobsApp getApplication() {
        return Application.getInstance(KlobsApp.class);
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
        actDate = new java.util.Date();
        DateFormat df = new SimpleDateFormat(KlobsApp.lang.getProperty("DateFormat", "MM/dd/yyyy"));
        actDateString = df.format(actDate);
        actStartTime = new java.util.Date();
        long now = actStartTime.getTime();
        now = ((now + 15 * 60 * 1000)/(15 * 60 * 1000)) * 15 * 60 * 1000;
        actStartTime.setTime(now);
        df = new SimpleDateFormat(KlobsApp.lang.getProperty("TimeFormat", "hh:mm"));
        actStartTimeString = df.format(actStartTime);
        actEndTime = new java.util.Date();
        actEndTime.setTime(actStartTime.getTime() + 1 * 3600 * 1000); //= starttime + 1 hour
        actEndTimeString = df.format(actEndTime);

        launch(KlobsApp.class, args);

    }

    public static void importUserDB() {
        members = new KReadInfoXML(KConstants.DBDataFileName, KConstants.UsrIdName, "members");
        locations = new KReadInfoXML(KConstants.DBDataFileName, KConstants.LocIdName, "orte");
        activities = new KReadInfoXML(KConstants.DBDataFileName, "usr_id", "trainings");
        attendies = new HashMap<String, KStringHash>();
    }
}
