/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package kobs;

/**
 *
 * @author steffen
 */
import java.io.File;
import org.w3c.dom.Document;
import org.w3c.dom.*;

import java.util.HashMap;
import java.util.Iterator;

import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.DocumentBuilder;
import org.xml.sax.SAXException;
import org.xml.sax.SAXParseException;

import java.io.*;
import org.xml.sax.*;
import org.w3c.dom.*;
import javax.xml.parsers.*;
import javax.xml.transform.*;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

public class KSessionSave {

    Element author = null;
    Element lines = null;
    Element title = null;

    public KSessionSave(String filename) {
        Document doc;
        Element root;
        try {
            DocumentBuilderFactory docBuilderFactory = DocumentBuilderFactory.newInstance();
            DocumentBuilder docBuilder = docBuilderFactory.newDocumentBuilder();
            try {
                doc = docBuilder.parse(new File(filename));
            } catch (java.io.IOException ignore) {
                doc = docBuilder.newDocument();
                root = doc.createElement("klobsdata");
                System.out.println("Datei erzeugt");
                doc.appendChild(root);
            }
            if (doc != null) {
                doc.getDocumentElement().normalize();
                root = doc.getDocumentElement();
                //root.setAttribute("type", "Shakespearean");
                // ----Abspeichern geänderter Mitgliederdaten ---
                if (KobsApp.members.size() > 0) {
                    Element update = doc.createElement("updates");
                    KReadInfoXML data = KobsApp.members;
                    Iterator<String> all = data.keySet().iterator();
                    while (all.hasNext()) {
                        String currentall = all.next();
                        System.out.println("Record: " + currentall);
                        HashMap<String, String> thisRecord = data.get(currentall);
                        if (thisRecord.containsKey(KConstants.MemModKey)) {
                            Element member = doc.createElement("member");
                            Iterator<String> records = thisRecord.keySet().iterator();
                            while (records.hasNext()) {
                                String currentKey = records.next();
                                Element entry = doc.createElement(currentKey);
                                entry.appendChild(doc.createTextNode(thisRecord.get(currentKey)));
                                member.appendChild(entry);

                                System.out.println(currentKey + ":" + thisRecord.get(currentKey));
                            }
                            update.appendChild(member);
                        }
                    }
                    root.appendChild(update);
                }
                // ----Ende Abspeichern geänderter Mitgliederdaten ---
                // ----Abspeichern von Trainingszeiten ---
                if (KobsApp.attendies.size() > 0) {
                    Element update = doc.createElement("trainings");
                    Element entry = doc.createElement("location");
                    entry.appendChild(doc.createTextNode(KobsApp.actLocation));
                    update.appendChild(entry);
                    entry = doc.createElement("locationid");
                    entry.appendChild(doc.createTextNode(KobsApp.actLocationId));
                    update.appendChild(entry);
                    entry = doc.createElement("date");
                    entry.appendChild(doc.createTextNode(KobsApp.actDateString));
                    update.appendChild(entry);
                    HashMap<String, KStringHash> trainingData = KobsApp.attendies;
                    Iterator<String> all = trainingData.keySet().iterator();
                    while (all.hasNext()) {
                        String currentall = all.next();
                        Element training = doc.createElement("training");
                        System.out.println("attendie: " + currentall);
                        HashMap<String, String> thisRecord = trainingData.get(currentall);
                        entry = doc.createElement(KConstants.UsrIdName);
                        entry.appendChild(doc.createTextNode(thisRecord.get(KConstants.UsrIdName)));
                        training.appendChild(entry);
                        entry = doc.createElement("typ");
                        entry.appendChild(doc.createTextNode("1"));
                        training.appendChild(entry);
                        entry = doc.createElement("subtyp");
                        entry.appendChild(doc.createTextNode("1"));
                        training.appendChild(entry);
                        entry = doc.createElement("trainerid");
                        entry.appendChild(doc.createTextNode("1"));
                        training.appendChild(entry);
                        entry = doc.createElement("starttime");
                        entry.appendChild(doc.createTextNode(KobsApp.actStartTimeString));
                        training.appendChild(entry);
                        entry = doc.createElement("duration");
                        entry.appendChild(doc.createTextNode(Long.toString((KobsApp.actEndTime.getTime() - KobsApp.actStartTime.getTime()) / 60000))); //in minutes
                        training.appendChild(entry);

                        update.appendChild(training);
                    }

                    root.appendChild(update);
                }
            // ----Ende Abspeichern Trainingszeiten ---

            /*                author = doc.createElement("author");
            
            Element lastName = doc.createElement("last-name");
            lastName.appendChild(doc.createTextNode("Shakespeare"));
            author.appendChild(lastName);
            
            Element firstName = doc.createElement("first-name");
            firstName.appendChild(doc.createTextNode("William"));
            author.appendChild(firstName);
            
            Element nationality = doc.createElement("nationality");
            nationality.appendChild(doc.createTextNode("British"));
            author.appendChild(nationality);
            
            Element yearOfBirth = doc.createElement("year-of-birth");
            yearOfBirth.appendChild(doc.createTextNode("1564"));
            author.appendChild(yearOfBirth);
            
            Element yearOfDeath = doc.createElement("year-of-death");
            yearOfDeath.appendChild(doc.createTextNode("1616"));
            author.appendChild(yearOfDeath);
            
            root.appendChild(author);
            
            title = doc.createElement("title");
            title.appendChild(doc.createTextNode("Sonnet 130"));
            root.appendChild(title);
            
            lines = doc.createElement("lines");
            
            Element line01 = doc.createElement("line");
            line01.appendChild(doc.createTextNode("My mistress' eyes are nothing like the sun,"));
            lines.appendChild(line01);
            
            Element line02 = doc.createElement("line");
            line02.appendChild(doc.createTextNode("Coral is far more red than her lips red."));
            lines.appendChild(line02);
            
            Element line03 = doc.createElement("line");
            line03.appendChild(doc.createTextNode("If snow be white, why then her breasts are dun,"));
            lines.appendChild(line03);
            
            Element line04 = doc.createElement("line");
            line04.appendChild(doc.createTextNode("If hairs be wires, black wires grow on her head."));
            lines.appendChild(line04);
            
            Element line05 = doc.createElement("line");
            line05.appendChild(doc.createTextNode("I have seen roses damasked, red and white,"));
            lines.appendChild(line05);
            
            Element line06 = doc.createElement("line");
            line06.appendChild(doc.createTextNode("But no such roses see I in her cheeks."));
            lines.appendChild(line06);
            
            Element line07 = doc.createElement("line");
            line07.appendChild(doc.createTextNode("And in some perfumes is there more delight"));
            lines.appendChild(line07);
            
            Element line08 = doc.createElement("line");
            line08.appendChild(doc.createTextNode("Than in the breath that from my mistress reeks."));
            lines.appendChild(line08);
            
            Element line09 = doc.createElement("line");
            line09.appendChild(doc.createTextNode("I love to hear her speak, yet well I know"));
            lines.appendChild(line09);
            
            Element line10 = doc.createElement("line");
            line10.appendChild(doc.createTextNode("That music hath a far more pleasing sound."));
            lines.appendChild(line10);
            
            Element line11 = doc.createElement("line");
            line11.appendChild(doc.createTextNode("I grant I never saw a goddess go,"));
            lines.appendChild(line11);
            
            Element line12 = doc.createElement("line");
            line12.appendChild(doc.createTextNode("My mistress when she walks, treads on the ground."));
            lines.appendChild(line12);
            
            Element line13 = doc.createElement("line");
            line13.appendChild(doc.createTextNode("And yet, by Heaven, I think my love as rare"));
            lines.appendChild(line13);
            
            Element line14 = doc.createElement("line");
            line14.appendChild(doc.createTextNode("As any she belied with false compare."));
            lines.appendChild(line14);
            
            root.appendChild(lines);
             */
            }
            // ---- Use a XSLT transformer for writing the new XML file ----
            Transformer transformer = TransformerFactory.newInstance().newTransformer();
            DOMSource source = new DOMSource(doc);
            FileOutputStream os = new FileOutputStream(new File(filename));
            StreamResult result = new StreamResult(os);
            transformer.transform(source, result);
        } catch (SAXParseException err) {
            System.out.println("** Parsing error" + ", line " + err.getLineNumber() + ", uri " + err.getSystemId());
            System.out.println(" " + err.getMessage());
        } catch (SAXException e) {
            Exception x = e.getException();
            ((x == null) ? e : x).printStackTrace();

        } catch (java.io.IOException ignore) {
            System.out.println("Datei nicht gefunden?");
        } catch (Throwable t) {
            t.printStackTrace();
        }

    }
}
