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
    
    @Override
    public String toString(){
        return this.get((String)this.keySet().toArray()[0]);
    }

}
