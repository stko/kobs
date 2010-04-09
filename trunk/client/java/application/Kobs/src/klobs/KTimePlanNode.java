/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package klobs;

import javax.swing.tree.*;
import java.util.Enumeration;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.text.ParseException;
import java.util.Date;
import java.util.HashMap;

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
    HashMap<String, KStringHash>memberList;

    public KTimePlanNode(KTimePlanNode parent) {
        super();
        this.memberList=new HashMap<String, KStringHash>();
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
}
