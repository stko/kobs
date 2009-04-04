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

public class KStringHash extends HashMap<String,String> {
    
    public String toString(){
        return this.get(this.keySet().toArray()[0]);
    }

}
