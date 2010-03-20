/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package klobs;

import javax.swing.tree.*;
import java.util.Enumeration;

/**
 *
 * @author steffen
 */
public class KTimePlanNode extends DefaultMutableTreeNode {
//public class KTimePlanNode  {

    public long duration = 5;
    public long startTime = 0;
    String location = "";

    @Override
    public String toString() {


        MutableTreeNode parentNode = this.parent;
        if (parentNode != null && parentNode instanceof KTimePlanNode) {
            startTime = ((KTimePlanNode) parentNode).startTime;
        }
        KTimePlanNode preNode = (KTimePlanNode) this.getPreviousSibling();
        while (preNode != null) {
            if (!preNode.isLeaf()) {
                startTime += preNode.duration;
            }
            preNode = (KTimePlanNode) preNode.getPreviousSibling();
        }
        getDuration();
        if (!this.isLeaf() || parentNode == null) {
            return location + " " + int2Time(startTime) + "-" + int2Time(startTime + duration) + " (" + Long.toString(duration) + "min)";
        } else {
            return "Action";
        }
    }
    /**
     * calculates the duration times for all subnodes, but also sets that time for all subnodes at the same time
     * @return duration times for all subnodes
     */
    public long getDuration() {
        if (this.isLeaf()) {
            return 0; //an actin itself does not have a endTime startTime
        } else {
            long durationTime = 0;
            for (Enumeration<KTimePlanNode> childrens = this.children(); childrens.hasMoreElements();) {
                KTimePlanNode thisChild = (KTimePlanNode) childrens.nextElement();
                if (!thisChild.isLeaf()) {
                    durationTime += thisChild.getDuration();
                }
            }
        if (durationTime>0){
            duration=durationTime;
        }
            return duration;

        }
    }

    public String int2Time(long min) {
        long secs = 60;

        return String.format("%02d:%02d", min / 60, min % secs);
    }

    public void setInitalData(String location, long startTime, long endTime) {
        startTime =(startTime % (1000*3600*24))/(1000*60);//calculating minutes out of a date
        endTime =(endTime % (1000*3600*24))/(1000*60);//calculating minutes out of a date
        this.location = location;
        this.startTime = startTime;
        this.duration = endTime - startTime; 
    }
}
