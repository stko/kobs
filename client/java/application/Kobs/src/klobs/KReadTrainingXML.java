/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package klobs;

import java.io.File;
import org.w3c.dom.*;

import java.util.HashMap;
import java.util.ArrayList;
import java.util.List;
import java.util.Collections;
import java.util.Iterator;

import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.DocumentBuilder;
import org.xml.sax.SAXException;
import org.xml.sax.SAXParseException;

/**
 *
 * @author steffen
 */
public class KReadTrainingXML {

    HashMap<String, Object> mainTrainings;

    public KReadTrainingXML(String filename, String primaryKey, String tag) {
        mainTrainings = new HashMap<String, Object>();
        try {
            DocumentBuilderFactory docBuilderFactory = DocumentBuilderFactory.newInstance();
            DocumentBuilder docBuilder = docBuilderFactory.newDocumentBuilder();
            Document doc = docBuilder.parse(new File(filename));

            // normalize text representation
            doc.getDocumentElement().normalize();
            //System.out.println ("Root element of the doc is " + doc.getDocumentElement().getNodeName());
            // Get a nodelist of all nodes matching to tag
            NodeList listOfTags = doc.getElementsByTagName(tag);
            // taking the first occurence of a tag node
            Element firstTagElement = (Element) listOfTags.item(0);
            // getting the childnodes of the tag node
            NodeList listOfPersons = firstTagElement.getChildNodes();
            // run through all childs
            for (Integer s = 0; s < listOfPersons.getLength(); s++) {
                String thisPrimaryKeyValue = "";
                //get a single child node of a tag npde
                Node firstPersonNode = listOfPersons.item(s);
                // if the node has the right type
                if (firstPersonNode.getNodeType() == Node.ELEMENT_NODE) {
                    HashMap<String, Object> thisRecord = new HashMap<String, Object>();
                    // getting a list of all child of a single tag node
                    Element firstPersonElement = (Element) firstPersonNode;
                    NodeList allChildNodes = firstPersonElement.getChildNodes();
                    // run through the list of all child nodes of a single tag child node
                    for (Integer s2 = 0; s2 < allChildNodes.getLength(); s2++) {
                        // get a single node of the childs of a tag node
                        Node thisNode = (Node) allChildNodes.item(s2);
                        String thisValue;
                        String thisKey;
                        // if the node has the right type
                        if (thisNode.getNodeType() == Node.ELEMENT_NODE) {
                            try {
                                NodeList childList = firstPersonElement.getElementsByTagName(thisNode.getNodeName());
                                Element firstChildElement = (Element) childList.item(0);
                                NodeList textFNList = firstChildElement.getChildNodes();
                                thisKey = thisNode.getNodeName().trim();
                                if (!thisKey.matches(KConstants.DBTrainingSubTypName)) {
                                    thisValue = ((Node) textFNList.item(0)).getNodeValue().trim();
                                    thisRecord.put(thisKey, thisValue);
                                    if (thisKey.matches(primaryKey)) {
                                        thisPrimaryKeyValue = thisValue;
                                    }
                                } else { //read in all data from trainingsubtype

                                    // that node is a subtyp-node, so it becomes even more tricky
                                    //first check, if the subtyp -hashtable already exists,
                                    HashMap<String, KStringHash> thisSubTypHashmap = (HashMap<String, KStringHash>) thisRecord.get(KConstants.DBTrainingSubTypName);
                                    if (thisSubTypHashmap == null) {
                                        thisSubTypHashmap = new HashMap<String, KStringHash>();
                                        thisRecord.put(KConstants.DBTrainingSubTypName, thisSubTypHashmap);
                                    }
                                    // getting the childnodes of the subtyp node
                                    NodeList listOfSubTypeNodes = thisNode.getChildNodes();
                                    //dumpLoop(thisNode, "");
                                    // run through all childs
                                    KStringHash thisSubTypRecord = new KStringHash();
                                    for (Integer s3 = 0; s3 < listOfSubTypeNodes.getLength(); s3++) {
                                        //get a single child node of a tag npde
                                        Node actualSubTypeNode = listOfSubTypeNodes.item(s3);
                                        // if the node has the right type
                                        if (actualSubTypeNode.getNodeType() == Node.ELEMENT_NODE) {
                                            try {
                                                String thisSubTypKey = actualSubTypeNode.getNodeName().trim();
                                                String thisSubTypValue = actualSubTypeNode.getFirstChild().getNodeValue().trim();
                                                String thisPrimarySubKeyValue = "";
                                                thisSubTypRecord.put(thisSubTypKey, thisSubTypValue);
                                                if (thisSubTypKey.matches(primaryKey)) {
                                                    thisPrimarySubKeyValue = thisSubTypValue;
                                                }
                                                if (!thisPrimarySubKeyValue.matches("")) {
                                                    thisSubTypHashmap.put(thisPrimarySubKeyValue, thisSubTypRecord);

                                                }
                                            } catch (java.lang.NullPointerException e) {
                                            }
                                        }
                                    }
                                }
                            } catch (java.lang.NullPointerException e) {
                            }
                        }
                    }
                    if (!thisPrimaryKeyValue.matches("")) {
                        mainTrainings.put(thisPrimaryKeyValue, thisRecord);
                    }
                }//end of if clause
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
        /*
        Iterator<String> all = mainTrainings.keySet().iterator();
        while (all.hasNext()) {
        String currentall = all.next();
        KStringHash thisRecord = mainTrainings.get(currentall);
        String thisline = thisRecord.get(primaryKey);
        if (thisline != null && thisline.equals(type)) {
        return thisRecord;
        }
        }
         */
        return null;
    }

    /**
     * gives a string[] of all available training- types
     * @return
     */
    public String[] getTypValues() {
        ArrayList<String> myList = new ArrayList();
        // Hier werden die keys sortiert
        List<String> sortedList = new ArrayList<String>();
        sortedList.addAll(mainTrainings.keySet());
        Collections.sort(sortedList);

        // Und hier kommt der Iterator
        Iterator<String> iter = sortedList.iterator();
        while (iter.hasNext()) {
            String key = iter.next();
            HashMap<String, Object> thisTyp = (HashMap<String, Object>) mainTrainings.get(key);
            myList.add((String) thisTyp.get("name"));
        }
        return myList.toArray(new String[1]);
    }

    /**
     * returns the ID for a given type
     * @param type of a training
     * @return the type ID
     */
    public String getTypeValueID(String type) {
        if (type != null) {
// Hier wird die keys sortiert
            List<String> sortedList = new ArrayList<String>();
            sortedList.addAll(mainTrainings.keySet());
            Collections.sort(sortedList);

// Und hier kommt der Iterator
            Iterator<String> iter = sortedList.iterator();
            while (iter.hasNext()) {
                String key = iter.next();
                HashMap<String, Object> thisTyp = (HashMap<String, Object>) mainTrainings.get(key);
                if (type.matches((String) thisTyp.get("name"))) {
                    return key;
                }
            }
        }
        return null;
    }

    /**
     * gives a string[] of all available training- types
     * @return
     */
    public String[] getSubTypValues(String type) {
        ArrayList<String> myList = new ArrayList();
        if (type != null) {
            HashMap<String, Object> subTypeHashMap = (HashMap<String, Object>) mainTrainings.get(getTypeValueID(type));
            if (subTypeHashMap != null) {
                HashMap<String, Object> subTypeData = (HashMap<String, Object>) subTypeHashMap.get(KConstants.DBTrainingSubTypName);
                if (subTypeData != null) {

                    // Hier werden die keys sortiert
                    List<String> sortedList = new ArrayList<String>();
                    sortedList.addAll(subTypeData.keySet());
                    Collections.sort(sortedList);

                    // Und hier kommt der Iterator
                    Iterator<String> iter = sortedList.iterator();

                    while (iter.hasNext()) {
                        String key = iter.next();
                        KStringHash thisTyp = (KStringHash) subTypeData.get(key);
                        myList.add((String) thisTyp.get("name"));
                    }
                }
            }
        }
        return myList.toArray(new String[1]);
    }

    /**
     * returns the ID for a given type
     * @param type of a training
     * @return the type ID
     */
    public String getSubTypeValueID(String type, String subType) {
        if (type != null && subType != null) {
            HashMap<String, Object> subTypeHashMap = (HashMap<String, Object>) mainTrainings.get(getTypeValueID(type));
            if (subTypeHashMap != null) {
                HashMap<String, Object> subTypeData = (HashMap<String, Object>) subTypeHashMap.get(KConstants.DBTrainingSubTypName);
                if (subTypeData != null) {
                    Object[] myKeys = subTypeData.keySet().toArray();
                    for (Integer i = 0; i < myKeys.length; i++) {
                        HashMap<String, Object> thisTyp = (HashMap<String, Object>) subTypeData.get((String) myKeys[i]);
                        if (((String) thisTyp.get("name")).matches(subType)) {
                            return (String) myKeys[i];
                        }
                    }
                }
            }
        }
        return null;
    }

    private void dumpLoop(Node node, String indent) {
        switch (node.getNodeType()) {
            case Node.CDATA_SECTION_NODE:
                System.out.println(indent + "CDATA_SECTION_NODE");
                break;
            case Node.COMMENT_NODE:
                System.out.println(indent + "COMMENT_NODE");
                break;
            case Node.DOCUMENT_FRAGMENT_NODE:
                System.out.println(indent + "DOCUMENT_FRAGMENT_NODE");
                break;
            case Node.DOCUMENT_NODE:
                System.out.println(indent + "DOCUMENT_NODE");
                break;
            case Node.DOCUMENT_TYPE_NODE:
                System.out.println(indent + "DOCUMENT_TYPE_NODE");
                break;
            case Node.ELEMENT_NODE:
                System.out.println(indent + "ELEMENT_NODE");
                System.out.println(indent + node.getNodeName());
                break;
            case Node.ENTITY_NODE:
                System.out.println(indent + "ENTITY_NODE");
                break;
            case Node.ENTITY_REFERENCE_NODE:
                System.out.println(indent + "ENTITY_REFERENCE_NODE");
                break;
            case Node.NOTATION_NODE:
                System.out.println(indent + "NOTATION_NODE");
                break;
            case Node.PROCESSING_INSTRUCTION_NODE:
                System.out.println(indent + "PROCESSING_INSTRUCTION_NODE");
                break;
            case Node.TEXT_NODE:
                System.out.println(indent + "TEXT_NODE");
                System.out.println(indent + node.getNodeValue());
                break;
            default:
                System.out.println(indent + "Unknown node");
                break;
        }
        NodeList list = node.getChildNodes();
        for (int i = 0; i < list.getLength(); i++) {
            dumpLoop(list.item(i), indent + "   ");
        }
    }
}
