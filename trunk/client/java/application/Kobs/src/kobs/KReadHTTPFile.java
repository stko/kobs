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

    public static String writeFile2URL(String URL, String fileName, String userName, String passWd) {
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
           // wenn man zeichensatz-konform schreiben möchte, sollte man einen passenden Stream nehmen:
            //OutputStreamWriter out = new OutputStreamWriter(conn.getOutputStream(), "UTF8");
            // wir lesen aber binär, und darum:
            BufferedOutputStream out = new BufferedOutputStream(conn.getOutputStream());
            DataInputStream fs = new DataInputStream(new FileInputStream(fileName));
            chain(fs,out);
            out.close();

            if (conn.getResponseCode() != HttpURLConnection.HTTP_OK) {
                return (conn.getResponseMessage());
            }
        conn.disconnect();

        /* 
        
         *  
        byte b[] = {};
        try {
        File f = new File(<Path2File>);
        long len = f.length();
        if (len == 0) throw new IOException("No file to write!");
        DataInputStream fs = new DataInputStream(new FileInputStream(<Path2File>));
        b = new byte[(int)len];
        int bytes = fs.read(b);
        fs.close();
        if (bytes != len) throw new IOException("Error reading file, bytes read = "+bytes);
        } catch (IOException e1) {
        //log.error("File error!");
        e1.printStackTrace();
        return;
        }
        try {
        URL url = "" URL(<UrlString>);
        HttpURLConnection conn = (HttpURLConnection) url.openConnection();
        conn.setDoOutput(true);
        BufferedOutputStream output = new BufferedOutputStream(conn.getOutputStream());
        output.write(b);
        output.close();
        int responseCode = conn.getResponseCode();
        if (responseCode < 200 || responseCode > 299) {
        throw new IOException("Error uploading file!" +  responseCode);
        }
        conn.disconnect();
  
        
         */

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
