/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package kobs;

/**
 *
 * @author steffen
 */
import java.util.HashMap;

public class KHashLink {

    private HashMap<String, String> thisRecord;
    private String identifier;

    public KHashLink(HashMap<String, String> thisRec, String ident) {
        thisRecord = thisRec;
        identifier = ident;
    }

    public String toString() {
        return thisRecord.get(identifier);
    }
    
    public HashMap<String, String> getHashMap(){
        return thisRecord;
    }
}

