package kobs;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 *
 * @author steffen
 */
import java.io.BufferedReader;
import java.io.*;
import java.io.InputStreamReader;
import java.net.Authenticator;
import java.net.PasswordAuthentication;
import java.net.URL;

public class KReadHTTPFile {

    static String kuser; // your account name
    static String kpass; // your password for the account

    static class MyAuthenticator extends Authenticator {

        public PasswordAuthentication getPasswordAuthentication() {
            // I haven't checked getRequestingScheme() here, since for NTLM
            // and Negotiate, the usrname and password are all the same.
            //System.err.println("Feeding username and password for " + getRequestingScheme());
            return (new PasswordAuthentication(kuser, kpass.toCharArray()));
        }
    }

    public static String readURL2File(String URL, String fileName, String userName, String passWd) {
        kuser = userName;
        kpass = passWd;
        Authenticator.setDefault(new MyAuthenticator());
        try {
            URL url = new URL(URL);
            InputStream fis = url.openConnection().getInputStream();
            try {
                FileOutputStream fos = new FileOutputStream(fileName);

                try {
                    byte[] buffer = new byte[0xFFFF];
                    int len;
                    while ((len = fis.read(buffer)) != -1) {
                        fos.write(buffer, 0, len);
                    }
                } catch (IOException e) {
                    return KobsApp.lang.getProperty("URLSaveText", "Couldn't save file to disk");
                } finally {
                    if (fis != null) {
                        try {
                            fis.close();
                        } catch (IOException e) {
                            e.printStackTrace();
                        }
                    }
                    if (fos != null) {
                        try {
                            fos.close();
                        } catch (IOException e) {
                            e.printStackTrace();
                        }
                    }
                }
            } catch (IOException e) {
                return KobsApp.lang.getProperty("URLSaveText", "Couldn't save file to disk");
            }

        } catch (java.net.MalformedURLException e) {
            return KobsApp.lang.getProperty("URLErrorText", "Couldn't connect to Server- is the URL correct?");
        } catch (java.io.IOException e) {
            return KobsApp.lang.getProperty("URLDownloadErrorText", "Couldn't download data- please check login data (case sensitive)");
        }
        return "";
    }
}
