/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package klobs;

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
import javax.swing.JTree;

public class KSessionSave {

    Element author = null;
    Element lines = null;
    Element title = null;

    public KSessionSave(String filename, JTree trainingsdata, KReadTrainingXML activities) {
        KSessionSaveSession(filename, trainingsdata, activities);
        KSessionUpdateLocalUserDataFile(KConstants.DBDataFileName);
    }

    public void KSessionSaveSession(String filename, JTree trainingsdata, KReadTrainingXML activities) {
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
                doc.appendChild(root);
            }
            if (doc != null) {
                doc.getDocumentElement().normalize();
                root = doc.getDocumentElement();
                //root.setAttribute("type", "Shakespearean");
                // ----Abspeichern geänderter Mitgliederdaten ---
                if (KlobsApp.members.size() > 0) {
                    Element update = doc.createElement("updates");
                    KReadInfoXML data = KlobsApp.members;
                    Iterator<String> all = data.keySet().iterator();
                    while (all.hasNext()) {
                        String currentall = all.next();
                        HashMap<String, String> thisRecord = data.get(currentall);
                        if (thisRecord.containsKey(KConstants.MemModKey)) {
                            Element member = doc.createElement("member");
                            Iterator<String> records = thisRecord.keySet().iterator();
                            while (records.hasNext()) {
                                String currentKey = records.next();
                                Element entry = doc.createElement(currentKey);
                                entry.appendChild(doc.createTextNode(thisRecord.get(currentKey)));
                                member.appendChild(entry);
                            }
                            update.appendChild(member);
                        }
                    }
                    root.appendChild(update);
                }
                // ----Ende Abspeichern geänderter Mitgliederdaten ---
                // ----Abspeichern von Trainingszeiten ---
                // if (KlobsApp.attendies.size() > 0) {
                Element update = doc.createElement("trainings");
                Element entry = doc.createElement("location");
                entry.appendChild(doc.createTextNode(KlobsApp.actLocation));
                update.appendChild(entry);
                entry = doc.createElement("locationid");
                entry.appendChild(doc.createTextNode(KlobsApp.actLocationId));
                update.appendChild(entry);
                entry = doc.createElement("date");
                entry.appendChild(doc.createTextNode(KlobsApp.actDateString));
                update.appendChild(entry);
                HashMap<String, KStringHash> trainingData = KlobsApp.members;
                Iterator<String> all = trainingData.keySet().iterator();
                while (all.hasNext()) {
                    String currentall = all.next();
                    HashMap<String, String> thisRecord = trainingData.get(currentall);

                    //              KStringHash thisRecord = actHashLink.getHashMap();
                    String onsideValue = thisRecord.get(KConstants.MemOnside);
                    if (onsideValue != null && onsideValue.compareTo(KConstants.TrueValue) == 0) {
                        Element training = doc.createElement("training");
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
                        entry.appendChild(doc.createTextNode(KlobsApp.actStartTimeString));
                        training.appendChild(entry);
                        entry = doc.createElement("duration");
                        //entry.appendChild(doc.createTextNode(Long.toString((KlobsApp.actEndTime.getTime() - KlobsApp.actStartTime.getTime()) / 60000))); //in minutes
                        entry.appendChild(doc.createTextNode(Long.toString(((KTimePlanNode) trainingsdata.getModel().getRoot()).duration))); //in minutes
                        training.appendChild(entry);

                        update.appendChild(training);
                    }
                }

                //}
                for (int i = 0; i < trainingsdata.getRowCount(); i++) {
                    KTimePlanNode thisNode = (KTimePlanNode) trainingsdata.getPathForRow(i).getLastPathComponent();
                    if (thisNode.isLeaf()) {


                        HashMap<String, KStringHash> NodeTrainingData = thisNode.memberList;
                        all = NodeTrainingData.keySet().iterator();
                        while (all.hasNext()) {
                            String currentall = all.next();
                            HashMap<String, String> thisRecord = NodeTrainingData.get(currentall);



                            //              KStringHash thisRecord = actHashLink.getHashMap();
                            String onsideValue = thisRecord.get(KConstants.MemOnside);
                            if (onsideValue != null && onsideValue.compareTo(KConstants.TrueValue) == 0) {
                                Element training = doc.createElement("training");
                                entry = doc.createElement(KConstants.UsrIdName);
                                entry.appendChild(doc.createTextNode(thisRecord.get(KConstants.UsrIdName)));
                                training.appendChild(entry);
                                entry = doc.createElement("typ");
                                entry.appendChild(doc.createTextNode(thisNode.getActionTypeId(activities)));
                                training.appendChild(entry);
                                entry = doc.createElement("subtyp");
                                entry.appendChild(doc.createTextNode(thisNode.getActionSubTypeId(activities)));
                                training.appendChild(entry);
                                entry = doc.createElement("trainerid");
                                entry.appendChild(doc.createTextNode("1"));
                                training.appendChild(entry);
                                entry = doc.createElement("starttime");
                                entry.appendChild(doc.createTextNode(thisNode.getStartTime()));
                                training.appendChild(entry);
                                entry = doc.createElement("duration");
                                //entry.appendChild(doc.createTextNode(Long.toString((KlobsApp.actEndTime.getTime() - KlobsApp.actStartTime.getTime()) / 60000))); //in minutes
                                entry.appendChild(doc.createTextNode(Long.toString(thisNode.getActionDuration()))); //in minutes
                                training.appendChild(entry);

                                update.appendChild(training);
                            }
                        }
                    }
                }

                root.appendChild(update);
                // ----Ende Abspeichern Trainingszeiten ---

            }
            // ---- Use a XSLT transformer for writing the new XML file ----
            Transformer transformer = TransformerFactory.newInstance().newTransformer();
            DOMSource source = new DOMSource(doc);
            FileOutputStream os = new FileOutputStream(new File(filename));
            StreamResult result = new StreamResult(os);
            transformer.transform(source, result);
        } catch (SAXParseException err) {
            System.err.println("** Parsing error" + ", line " + err.getLineNumber() + ", uri " + err.getSystemId());
            System.err.println(" " + err.getMessage());
        } catch (SAXException e) {
            Exception x = e.getException();
            ((x == null) ? e : x).printStackTrace();

        } catch (java.io.IOException ignore) {
            System.err.println("Datei nicht gefunden?");
        } catch (Throwable t) {
            t.printStackTrace();
        }

    }

    public void KSessionUpdateLocalUserDataFile(String filename) {
        Document doc;
        Element root;
        try {
            DocumentBuilderFactory docBuilderFactory = DocumentBuilderFactory.newInstance();
            DocumentBuilder docBuilder = docBuilderFactory.newDocumentBuilder();
            doc = docBuilder.parse(new File(filename));
            if (doc != null) {
                doc.getDocumentElement().normalize();
                root = doc.getDocumentElement();
                if (KlobsApp.members.size() > 0) {
                    NodeList listOfTags = doc.getElementsByTagName("members");
                    Element firstTagElement = (Element) listOfTags.item(0);
                    firstTagElement.getParentNode().removeChild(firstTagElement);
                    Element members = doc.createElement("members");
                    //root.setAttribute("type", "Shakespearean");
                    // ----Abspeichern geänderter Mitgliederdaten ---
                    KReadInfoXML data = KlobsApp.members;
                    Iterator<String> all = data.keySet().iterator();
                    while (all.hasNext()) {
                        String currentall = all.next();
                        HashMap<String, String> thisRecord = data.get(currentall);
                        Element member = doc.createElement("member");
                        Iterator<String> records = thisRecord.keySet().iterator();
                        while (records.hasNext()) {
                            String currentKey = records.next();
                            if (!currentKey.contentEquals(KConstants.MemModKey) &&
                                    !currentKey.contentEquals(KConstants.MemOnside)) {
                                Element entry = doc.createElement(currentKey);
                                entry.appendChild(doc.createTextNode(thisRecord.get(currentKey)));
                                member.appendChild(entry);
                            }
                        }
                        members.appendChild(member);
                    }
                    root.appendChild(members);
                }
            }
            // ---- Use a XSLT transformer for writing the new XML file ----
            Transformer transformer = TransformerFactory.newInstance().newTransformer();
            DOMSource source = new DOMSource(doc);
            FileOutputStream os = new FileOutputStream(new File(filename));
            StreamResult result = new StreamResult(os);
            transformer.transform(source, result);
        } catch (SAXParseException err) {
            System.err.println("** Parsing error" + ", line " + err.getLineNumber() + ", uri " + err.getSystemId());
            System.err.println(" " + err.getMessage());
        } catch (SAXException e) {
            Exception x = e.getException();
            ((x == null) ? e : x).printStackTrace();

        } catch (java.io.IOException ignore) {
            System.err.println("Datei nicht gefunden?");
        } catch (Throwable t) {
            t.printStackTrace();
        }

    }
}
