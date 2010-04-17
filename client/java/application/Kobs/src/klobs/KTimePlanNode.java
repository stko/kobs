/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package klobs;

import javax.swing.tree.*;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.text.ParseException;
import java.util.*;

/**
 *
 * @author steffen
 */
public class KTimePlanNode extends DefaultMutableTreeNode {
//public class KTimePlanNode  {

    public long duration = 5;
    public Date startTime;
    static DateFormat timeFormat = new SimpleDateFormat(KlobsApp.lang.getProperty("TimeFormat", "hh:mm"));
    String location = "";
    public String typ = "";
    public String subTyp = "";
    HashMap<String, KStringHash> memberList;
    HashMap<String, KStringHash> branchMemberList;
    HashMap<String, KStringHash> leafMemberList;
    HashMap<String, KStringHash> totalMemberList;

    public KTimePlanNode(KTimePlanNode parent) {
        super();
        this.memberList = new HashMap<String, KStringHash>();
        this.branchMemberList = new HashMap<String, KStringHash>();
        this.leafMemberList = new HashMap<String, KStringHash>();
        this.totalMemberList = new HashMap<String, KStringHash>();
        if (parent == null) {
            duration = 5;
            startTime = new Date(0);
            location = "";
            typ = "";
            subTyp = "";

        } else {
            duration = parent.duration;
            startTime = new Date(parent.startTime.getTime());
            location = "";
            typ = parent.typ;
            subTyp = parent.subTyp;

        }
    }

    @Override
    public String toString() {
        getDuration();
        KTimePlanNode parentNode = (KTimePlanNode) this.parent;
        if (parentNode != null) {
            startTime = new Date(parentNode.startTime.getTime());
        }
        KTimePlanNode preNode = (KTimePlanNode) this.getPreviousSibling();
        while (preNode != null) {
            if (!preNode.isLeaf()) {
                startTime.setTime(startTime.getTime() + preNode.duration * 60000);
            }
            preNode = (KTimePlanNode) preNode.getPreviousSibling();
        }
        if (!this.isLeaf() || parentNode == null) {
            return location + " " + timeFormat.format(startTime) + "-" + timeFormat.format(new Date(startTime.getTime() + duration * 60 * 1000)) + " (" + Long.toString(duration) + "min)";
        } else {
            return typ + ":" + subTyp;
        }
    }

    /**
     * calculates the duration times for all subnodes, but also sets that time for all subnodes at the same time
     * @return duration times for all subnodes
     */
    public long getDuration() {
        if (this.isLeaf()) {
            return 0; //an action itself does not have a endTime startTime
        } else {
            long durationTime = 0;
            for (Enumeration<KTimePlanNode> childrens = this.children(); childrens.hasMoreElements();) {
                KTimePlanNode thisChild = (KTimePlanNode) childrens.nextElement();
                if (!thisChild.isLeaf()) {
                    durationTime += thisChild.getDuration();
                }
            }
            if (durationTime > 0) {
                duration = durationTime;
            }
            return duration;

        }
    }

    public String int2Time(long min) {
        return String.format("%02d:%02d", min / 60, min % 60);
    }

    public void setInitalData(String location, Date startTime, Date endTime) {
        this.location = location;
        this.startTime = new Date(startTime.getTime());
        this.duration = (endTime.getTime() - startTime.getTime()) / (60 * 1000);
    }

    public boolean durationIsEditable() {
        boolean isEditable = true;
        for (Enumeration<KTimePlanNode> childrens = this.children(); childrens.hasMoreElements();) {
            KTimePlanNode thisChild = (KTimePlanNode) childrens.nextElement();
            if (!thisChild.isLeaf()) {
                isEditable = false;
            }
        }
        return isEditable;
    }

    public void createMemberTree() {

        // if not root, copy the parent branch member list
        if (!this.isRoot()) {
            this.totalMemberList = (HashMap<String, KStringHash>) ((KTimePlanNode) this.parent).branchMemberList.clone();
        }
        HashMap<String, KStringHash> actualHashMap = new HashMap<String, KStringHash>();
        HashMap<String, KStringHash> parentHashMap = new HashMap<String, KStringHash>();
        this.memberList.clear();
        Iterator<String> all;
        HashMap<String, KStringHash> newHashMap = new HashMap<String, KStringHash>();
        // now create the leaflist containing all members which are members of the child leafs
        // first we fill the leafmemberlist with all available members
        this.leafMemberList = (HashMap<String, KStringHash>) this.totalMemberList.clone();
        // and then we run through all child leafs
        for (Enumeration<KTimePlanNode> childrens = this.children(); childrens.hasMoreElements();) {
            KTimePlanNode thisChild = (KTimePlanNode) childrens.nextElement();

            if (thisChild.isLeaf()) { //this node is an action entry
                //first, remove all own entries which are not shown in the parent any more
                actualHashMap = thisChild.memberList;
                parentHashMap = (HashMap<String, KStringHash>) ((KTimePlanNode) thisChild.getParent()).totalMemberList;
                newHashMap = new HashMap<String, KStringHash>();
                all = actualHashMap.keySet().iterator();
                while (all.hasNext()) {
                    String currentall = all.next();
                    KStringHash thisRecord = actualHashMap.get(currentall);
                    if (parentHashMap.containsKey(currentall)) {//is that member still in the parent list
                        newHashMap.put(currentall, thisRecord); //then copy it into the new list
                        this.memberList.put(currentall, thisRecord); //also add it to the memberlist
                        this.leafMemberList.remove(currentall);// and remove it in the same moment from the leafmember list
                    }
                }
                thisChild.memberList = newHashMap;
            }
        }
        // as next we calculate the remaining available members by removing all already found (leaf) members from the total list into the branchlist
        newHashMap = new HashMap<String, KStringHash>();
        this.branchMemberList = (HashMap<String, KStringHash>) this.totalMemberList.clone();
        all = this.memberList.keySet().iterator();
        while (all.hasNext()) {
            String currentall = all.next();
            if (this.branchMemberList.containsKey(currentall)) {//is that member also in the leaf list?
                this.branchMemberList.remove(currentall); //then remove it from the branch list
            }
        }
        /** so we have now the leaflist, containing all remaining members available for leaves
         * a memberlist, containing all members which are already in a direct child leaf
         * and the branchlist, containing all remaining members which can be used by the child branches
         * with that branchlist we can now go into the child branches to calculate their members
         *
         **/
        for (Enumeration<KTimePlanNode> childrens = this.children(); childrens.hasMoreElements();) {
            KTimePlanNode thisChild = (KTimePlanNode) childrens.nextElement();

            if (!thisChild.isLeaf()) { //this node is a branch
                //first calculate the child branch
                thisChild.createMemberTree();
                //and finally we add the branch members to our own member list and remove them simultainiously from the leaf list
                actualHashMap = thisChild.memberList;
                all = actualHashMap.keySet().iterator();
                while (all.hasNext()) {
                    String currentall = all.next();
                    KStringHash thisRecord = actualHashMap.get(currentall);
                    if (!this.memberList.containsKey(currentall)) {//is that member still in the parent list
                        this.memberList.put(currentall, thisRecord); //then copy it into the new list
                    }
                    if (this.leafMemberList.containsKey(currentall)) {//is that member still in the parent list
                        this.leafMemberList.remove(currentall);// and remove it in the same moment from the leafmember list
                    }
                }
            }
        }
    }
}
