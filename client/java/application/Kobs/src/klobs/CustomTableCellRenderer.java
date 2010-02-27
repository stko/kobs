package klobs;

import java.awt.*;
import javax.swing.*;
import javax.swing.table.*;


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 *
 * @author steffen
 */
public class CustomTableCellRenderer extends DefaultTableCellRenderer {

    private Color selectedColor = new Color(240, 240, 0);
    private Color normalColor = new Color(255, 255, 255);

    @Override
    public Component getTableCellRendererComponent(JTable table, Object value, boolean isSelected,
            boolean hasFocus, int row, int column) {
        Component cell = super.getTableCellRendererComponent(table, value, isSelected, hasFocus, row, column);
        try {
            if (value instanceof KHashLink) {
                KStringHash newRecord = (KStringHash) ((KHashLink) value).getHashMap();
                String onsideValue=newRecord.get(KConstants.MemOnside);
                if (onsideValue != null && onsideValue.compareTo(KConstants.TrueValue) == 0) {
                    cell.setBackground(selectedColor);
                    } else {
                    cell.setBackground(normalColor);
                }
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
        return cell;
    }
}
