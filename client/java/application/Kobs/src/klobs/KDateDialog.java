/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package klobs;

/*
 * Copyright (c) 1995 - 2008 Sun Microsystems, Inc.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   - Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   - Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in the
 *     documentation and/or other materials provided with the distribution.
 *
 *   - Neither the name of Sun Microsystems nor the names of its
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
/**
 *
 * @author steffen
 */
import javax.swing.JOptionPane;
import javax.swing.JDialog;
import javax.swing.JTextField;
import javax.swing.JFormattedTextField;
import javax.swing.JComboBox;
import javax.swing.*;
//import javax.swing.text.*;
import java.beans.*; //property change stuff

import java.awt.*;
import java.awt.event.*;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.text.ParseException;
import java.util.Date;

/* 1.4 example used by DialogDemo.java. */
class KDateDialog extends JDialog
        implements ActionListener,
        PropertyChangeListener {

    private JTextField textField;
    private JTextField LocField;
    private JFormattedTextField startTimeField;
    private JFormattedTextField endTimeField;
    private JComboBox locationField;
    private String dateFormat;
    private String timeFormat;
    protected Date date;
    protected String dateString;
    protected String location;
    protected KStringHash locationHash;
    protected Date startTime;
    protected String startTimeString;
    protected Date endTime;
    protected String endTimeString;
    private JOptionPane optionPane;
    private String dummyString = "";

    /** Creates the reusable dialog. */
    public KDateDialog(Frame aFrame, String dateFormat, String timeFormat, Date startDate, Date startTime, Date endTime) {
        super(aFrame, true);
        this.date = startDate;
        this.startTime = startTime;
        this.endTime = endTime;
        this.dateFormat = dateFormat;
        this.timeFormat = timeFormat;
        setTitle(KlobsApp.lang.getProperty("Details", "Details"));

        textField = new JTextField(10);
        DateFormat df = new SimpleDateFormat(dateFormat);
        DateFormat tf = new SimpleDateFormat(timeFormat);

        textField.setText(df.format(startDate));
        dateString = textField.getText();

        startTimeField = new JFormattedTextField(new javax.swing.text.DateFormatter(tf));
        startTimeField.setText(tf.format(startTime));
        startTimeString = startTimeField.getText();

        endTimeField = new JFormattedTextField(new javax.swing.text.DateFormatter(tf));
        endTimeField.setText(tf.format(endTime));
        endTimeString = endTimeField.getText();
        KStringHash[] orte = {};
        orte = (KStringHash[]) KlobsApp.locations.values().toArray(orte);
        locationField = new JComboBox(orte);
        /*
        catch (ParseException e) {
        JOptionPane.showMessageDialog(
        KDateDialog.this, "corrupt dateformat in config. Please check");
        }
         */

        //  Object[] possibilities = {"ham", "spam", "yam"};
        //Create an array of the text and components to be displayed.
        Object[] array = {
            KlobsApp.lang.getProperty("DateMessage", "On which date the training takes place?") + " (" + dateFormat + ")",
            textField,
            KlobsApp.lang.getProperty("LocationMessage", "Where is the training?"),
            locationField,
            KlobsApp.lang.getProperty("StartTimeMessage", "When does it start?") + " (" + timeFormat + ")",
            startTimeField,
            KlobsApp.lang.getProperty("EndTimeMessage", "When does it stop?") + " (" + timeFormat + ")",
            endTimeField
        };


        //Create the JOptionPane.
        optionPane = new JOptionPane(array,
                JOptionPane.QUESTION_MESSAGE,
                JOptionPane.OK_CANCEL_OPTION,
                null,
                null,
                null);
        //Make this dialog display it.
        setContentPane(optionPane);

        //Handle window closing correctly.
        setDefaultCloseOperation(DO_NOTHING_ON_CLOSE);
        addWindowListener(new WindowAdapter() {

            public void windowClosing(WindowEvent we) {
                /*
                 * Instead of directly closing the window,
                 * we're going to change the JOptionPane's
                 * value property.
                 */
                optionPane.setValue(new Integer(
                        JOptionPane.CLOSED_OPTION));
            }
        });

        //Ensure the text field always gets the first focus.
        addComponentListener(new ComponentAdapter() {

            public void componentShown(ComponentEvent ce) {
                textField.requestFocusInWindow();
            }
        });

        //Register an event handler that puts the text into the option pane.
        textField.addActionListener(this);

        //Register an event handler that reacts to option pane state changes.
        optionPane.addPropertyChangeListener(this);
        this.pack();
        this.setVisible(true);

    }

    /** This method handles events for the text field. */
    public void actionPerformed(ActionEvent e) {
        optionPane.setValue(dummyString);
    }

    /** This method reacts to state changes in the option pane. */
    public void propertyChange(PropertyChangeEvent e) {
        String prop = e.getPropertyName();
        if (isVisible() && (e.getSource() == optionPane) && (JOptionPane.VALUE_PROPERTY.equals(prop) ||
                JOptionPane.INPUT_VALUE_PROPERTY.equals(prop))) {
            Object value = optionPane.getValue();

            if (optionPane.getValue().equals(JOptionPane.UNINITIALIZED_VALUE)) {
                //ignore reset
                return;
            }
            if (optionPane.getValue().equals( JOptionPane.CLOSED_OPTION)) {
                //ignore reset
                return;
            }

            if (optionPane.getValue().equals( JOptionPane.CANCEL_OPTION)) {
                //ignore reset
                return;
            }

            //Reset the JOptionPane's value.
            //If you don't do this, then if the user
            //presses the same button next time, no
            //property change event will be fired.
            optionPane.setValue(
                    JOptionPane.UNINITIALIZED_VALUE);
            // check the inputs for valitity
            boolean allOk = true;
            DateFormat df = new SimpleDateFormat(dateFormat);
            DateFormat tf = new SimpleDateFormat(timeFormat);
            if (allOk) {
                try {
                    date = df.parse(textField.getText());
                    dateString = df.format(date);
                } catch (ParseException stacktrace) {
                    //text was invalid
                    allOk = false;
                    textField.selectAll();
                    JOptionPane.showMessageDialog(
                            KDateDialog.this,
                            KlobsApp.lang.getProperty("DateInputError", "The given date is not valid. Please check"),
                            "Try again..",
                            JOptionPane.ERROR_MESSAGE);
                    textField.requestFocusInWindow();
                }
            }
            if (allOk) {
                try {
                    startTime = tf.parse(startTimeField.getText());
                    startTimeString = tf.format(startTime);
                } catch (ParseException stacktrace) {
                    //text was invalid
                    allOk = false;
                    startTimeField.selectAll();
                    JOptionPane.showMessageDialog(
                            KDateDialog.this,
                            KlobsApp.lang.getProperty("TimeInputError", "The given time is not valid. Please check"),
                            "Try again..",
                            JOptionPane.ERROR_MESSAGE);
                    textField.requestFocusInWindow();
                }
            }
            if (allOk) {
                try {
                    endTime = tf.parse(endTimeField.getText());
                    endTimeString = tf.format(endTime);
                } catch (ParseException stacktrace) {
                    //text was invalid
                    allOk = false;
                    endTimeField.selectAll();
                    JOptionPane.showMessageDialog(
                            KDateDialog.this,
                            KlobsApp.lang.getProperty("TimeInputError", "The given time is not valid. Please check"),
                            "Try again..",
                            JOptionPane.ERROR_MESSAGE);
                    textField.requestFocusInWindow();
                }
            }
            locationHash = (KStringHash) locationField.getSelectedItem();
            location = locationHash.toString();
            if (allOk) {
                textField.setText(null);
                setVisible(false);

            }
        }
    }
}
