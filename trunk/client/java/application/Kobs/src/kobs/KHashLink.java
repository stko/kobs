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

    private KStringHash thisRecord;
    private String identifier;

    public KHashLink(KStringHash thisRec, String ident) {
        thisRecord = thisRec;
        identifier = ident;
    }

    public String toString() {
        return thisRecord.get(identifier);
    }
    
    public KStringHash getHashMap(){
        return thisRecord;
    }
}

