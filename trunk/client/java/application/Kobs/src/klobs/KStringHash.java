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
import java.util.Iterator;

public class KStringHash extends HashMap<String, String> {

    @Override
    public String toString() {
        if (this.containsKey("last_name")) { // ok, this is not good programming technique, but at least it works for this...
            return this.get("last_name") + " , " + this.get("first_name");

        } else { // this is to make t
            return this.get((String) this.keySet().toArray()[0]);
        }
    }

    public void addElement(String key, String Element) {
        String content = this.get(key);
        String[] newElement = splitKeyValue(Element);
        HashMap<String, String> oldElements = new HashMap<String, String>();
        if (content != null) {
            oldElements = splitString2Elements(content);
        }
        if (newElement.length == 2) {
            oldElements.put(newElement[0], newElement[1]);
        }
        this.put(key, concatElements2String(oldElements));

    }

    public void removeElement(String key, String Element) {
        HashMap<String, String> oldElements = new HashMap<String, String>();
        String content = this.get(key);
        String[] newElement = splitKeyValue(Element);
        if (content != null && newElement.length == 2) {
            oldElements = splitString2Elements(content);
        }
        oldElements.remove(newElement[0]);
        this.put(key, concatElements2String(oldElements));
    }

    HashMap<String, String> splitString2Elements(String input) {
        HashMap<String, String> res = new HashMap<String, String>();
        String[] elements;
        if (input.contains("|")) {
            elements = input.split("\\|");
        } else {
            elements = new String[1];
            elements[0] = input;
        }
        for (int i = 0; i < elements.length; i++) {
            String[] element = splitKeyValue(elements[i]);
            if (element != null) {
                res.put(element[0].trim().toUpperCase(), element[1].trim().toUpperCase());
            }
        }
        return res;
    }

    String concatElements2String(HashMap<String, String> elements) {
        String res = "";
        Iterator<String> all = elements.keySet().iterator();
        while (all.hasNext()) {
            String key = all.next();
            if (!res.equals("")) {
                res += " | ";
            }
            res = res + key + ":" + elements.get(key);
        }
        return res;
    }

    String[] splitKeyValue(String input) {
        String[] res = new String[2];
        String[] element = input.split(":", 2);
        if (element.length == 2 && !element[0].trim().isEmpty() && !element[1].trim().isEmpty()) {
            res[0] = element[0].trim().toUpperCase();
            res[1] = element[1].trim().toUpperCase();
            return res;
        } else {
            return null;
        }
    }
}
