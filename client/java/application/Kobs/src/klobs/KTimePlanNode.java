/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package klobs;

import javax.swing.tree.*;


/**
 *
 * @author steffen
 */
//public class KTimePlanNode extends DefaultMutableTreeNode {
public class KTimePlanNode  {

    Integer nodeType;

    public KTimePlanNode(Integer type){
        nodeType=type;
    }

    @Override
    public String toString(){
        return nodeType.toString();
    }

}
