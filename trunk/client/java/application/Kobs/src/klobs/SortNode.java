/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package klobs;

import javax.swing.tree.*;
import java.util.Collections;

/**
 * Help Class for sorted Jtree
 * @author steffen
 */
public class SortNode extends DefaultMutableTreeNode implements Comparable {
    public SortNode(Object name) {
        super(name);
    }
    @Override
    public void insert(final MutableTreeNode newChild, final int childIndex) {
        super.insert(newChild, childIndex);
        Collections.sort(this.children);
    }
    public int compareTo(final Object o) {
        return this.toString().compareToIgnoreCase(o.toString());
    }
}
