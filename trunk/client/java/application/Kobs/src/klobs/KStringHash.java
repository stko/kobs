/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package klobs;

/**
 *
 * @author steffen
 */
import java.util.HashMap;

public class KStringHash extends HashMap<String, String> {

    @Override
    public String toString() {
        if (this.containsKey("last_name")) { // ok, this is not good programming technique, but at least it works for this...
            return this.get("last_name") + " , " + this.get("first_name");

        } else { // this is to make t
            return this.get((String) this.keySet().toArray()[0]);
        }
    }
}
