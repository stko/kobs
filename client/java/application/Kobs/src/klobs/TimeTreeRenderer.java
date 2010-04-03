/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package klobs;

import javax.swing.ImageIcon;
import javax.swing.Icon;
import java.awt.Component;
import java.util.ArrayList;
import javax.swing.tree.*;
import javax.swing.JTree;

/**
 *
 * @author steffen
 */
public class TimeTreeRenderer extends DefaultTreeCellRenderer {

    ArrayList myIcons;

    public TimeTreeRenderer() {
        myIcons = loadIconList(new String[]{
                    "resources/home_16.png",
                    "resources/clock_16.png",
                    "resources/flag_16.png"
                });
    }

    @Override
    public Component getTreeCellRendererComponent(
            JTree tree,
            Object value,
            boolean sel,
            boolean expanded,
            boolean leaf,
            int row,
            boolean hasFocus) {

        super.getTreeCellRendererComponent(
                tree, value, sel,
                expanded, leaf, row,
                hasFocus);
        if (row == 0) { //root node
            setIcon((Icon) myIcons.get(0));
        } else {
            if (leaf) {
                setIcon((Icon) myIcons.get(2));
                //setToolTipText("This book is in the Tutorial series.");
            } else {
                setIcon((Icon) myIcons.get(1));
            }
        }

        return this;
    }

    /** Loads all given icons into an Arraylist
     *
     */
    ArrayList loadIconList(String[] iconList) {
        myIcons = new ArrayList(iconList.length);
        for (int i = 0; i < iconList.length; i++) {
            /** Returns an ImageIcon, or null if the path was invalid. */
            java.net.URL imgURL = KlobsApp.class.getResource(iconList[i]);
            if (imgURL != null) {
                myIcons.add(new ImageIcon(imgURL));
            } else {
                System.err.println("Couldn't find icon file: " + iconList[i]);
            }
        }
        return myIcons;
    }
}

    
    

