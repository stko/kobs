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
import org.w3c.dom.*;

import java.util.HashMap;
import java.util.Iterator;

import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.DocumentBuilder;
import org.xml.sax.SAXException;
import org.xml.sax.SAXParseException;

public class KReadInfoXML extends HashMap<String, KStringHash> {


    public KReadInfoXML(String filename, String primaryKey, String tag) {
        super();
        try {
            DocumentBuilderFactory docBuilderFactory = DocumentBuilderFactory.newInstance();
            DocumentBuilder docBuilder = docBuilderFactory.newDocumentBuilder();
            Document doc = docBuilder.parse(new File(filename));

            // normalize text representation
            doc.getDocumentElement().normalize();
            //System.out.println ("Root element of the doc is " + doc.getDocumentElement().getNodeName());

            NodeList listOfTags = doc.getElementsByTagName(tag);
            Element firstTagElement = (Element) listOfTags.item(0);
            NodeList listOfPersons = firstTagElement.getChildNodes();
            for (Integer s = 0; s < listOfPersons.getLength(); s++) {
                String thisPrimaryKeyValue = "";
                KStringHash thisRecord = new KStringHash();
                Node firstPersonNode = listOfPersons.item(s);
                if (firstPersonNode.getNodeType() == Node.ELEMENT_NODE) {
                    Element firstPersonElement = (Element) firstPersonNode;
                    NodeList allChildNodes = firstPersonElement.getChildNodes();
                    for (Integer s2 = 0; s2 < allChildNodes.getLength(); s2++) {
                        Node thisNode = (Node) allChildNodes.item(s2);
                        String thisValue;
                        String thisKey;
                        if (thisNode.getNodeType() == Node.ELEMENT_NODE) {
                            try {
                                NodeList childList = firstPersonElement.getElementsByTagName(thisNode.getNodeName());
                                Element firstChildElement = (Element) childList.item(0);
                                NodeList textFNList = firstChildElement.getChildNodes();
                                thisValue = ((Node) textFNList.item(0)).getNodeValue().trim();
                                thisKey = ((Node) allChildNodes.item(s2)).getNodeName().trim();
                                thisRecord.put(thisKey, thisValue);
                                if (thisKey.matches(primaryKey)) {
                                    thisPrimaryKeyValue = thisValue;
                                }
                            } catch (java.lang.NullPointerException e) {
                            }
                        }
                    }
                }//end of if clause
                if (!thisPrimaryKeyValue.matches("")) {
                    this.put(thisPrimaryKeyValue, thisRecord);
                }
            }//end of for loop with s var

        } catch (SAXParseException err) {
            System.err.println("** Parsing error" + ", line " + err.getLineNumber() + ", uri " + err.getSystemId());
            System.err.println(" " + err.getMessage());
        } catch (SAXException e) {
            Exception x = e.getException();
            ((x == null) ? e : x).printStackTrace();

        } catch (java.io.IOException ignore) {

        } catch (Throwable t) {
            t.printStackTrace();
        }

    }

    public KStringHash find(String primaryKey, String value) {
        Iterator<String> all = this.keySet().iterator();
        while (all.hasNext()) {
            String currentall = all.next();
            KStringHash thisRecord = this.get(currentall);
            String thisline = thisRecord.get(primaryKey);
            if (thisline !=null && thisline.contains(value.toUpperCase())) {
                return thisRecord;
            }
        }
        return null;
    }
}