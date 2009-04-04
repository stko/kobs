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
import java.net.*;

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

    public static String syncronize2URL(String URL, String sessionFileName, String userDataFileName, String userName, String passWd) {
        kuser = userName;
        kpass = passWd;
        Authenticator.setDefault(new MyAuthenticator());
        try {
            URL url = new URL(URL);
            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod("POST");
            //conn.setUseCaches(false);
            conn.setRequestProperty("Content-Type", "text/xml; charset=\"utf-8\"");
            conn.setDoOutput(true);
            //           conn.setDoInput(true);
            // wenn man zeichensatz-konform schreiben möchte, sollte man einen passenden Stream nehmen:
            //OutputStreamWriter out = new OutputStreamWriter(conn.getOutputStream(), "UTF8");
            // wir lesen aber binär, und darum:
            try { //gibts überhaupt eine Sessionfile?
                DataInputStream fs = new DataInputStream(new FileInputStream(sessionFileName));
                BufferedOutputStream out = new BufferedOutputStream(conn.getOutputStream());
                chain(fs, out);
                out.close();
            } catch (java.io.IOException e) {
            }
            InputStream fis = conn.getInputStream();
            try {
                FileOutputStream fos = new FileOutputStream(userDataFileName);

                try {
                    chain(fis, fos);
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
            if (conn.getResponseCode() != HttpURLConnection.HTTP_OK) {
                return (conn.getResponseMessage());
            }

            conn.disconnect();



        } catch (java.net.MalformedURLException e) {
            return KobsApp.lang.getProperty("URLErrorText", "Couldn't connect to Server- is the URL correct?");
        } catch (java.io.IOException e) {
            return KobsApp.lang.getProperty("URLDownloadErrorText", "Couldn't upload data- please check login data (case sensitive)");
        }
        return "";
    }

    public static void chain(InputStream input, OutputStream out) throws IOException {
        byte[] buf = new byte[10000];
        int len = 0;
        while ((len = input.read(buf, 0, 1000)) > 0) {
            out.write(buf, 0, len);
        }
    }
}
