/*
 * KobsView.java
 */
package kobs;
import org.jdesktop.application.Action;
import org.jdesktop.application.ResourceMap;
import org.jdesktop.application.SingleFrameApplication;
import org.jdesktop.application.FrameView;
import org.jdesktop.application.TaskMonitor;

import java.awt.*;
import java.awt.event.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.ComponentAdapter;
import javax.swing.Timer;
import javax.swing.Icon;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.*;
import javax.swing.table.*;
import javax.swing.table.TableModel;
import javax.swing.event.*;
import java.util.*;
import java.net.*;
import java.io.*;
/**
 * The application's main frame.
 */
public class KobsView extends FrameView implements TableModelListener {

    public KobsView(SingleFrameApplication app) {
        super(app);

        initComponents();

        // status bar initialization - message timeout, idle icon and busy animation, etc
        ResourceMap resourceMap = getResourceMap();
        int messageTimeout = resourceMap.getInteger("StatusBar.messageTimeout");
        messageTimer = new Timer(messageTimeout, new ActionListener() {

            public void actionPerformed(ActionEvent e) {
                statusMessageLabel.setText("");
            }
        });
        messageTimer.setRepeats(false);
        int busyAnimationRate = resourceMap.getInteger("StatusBar.busyAnimationRate");
        for (int i = 0; i < busyIcons.length; i++) {
            busyIcons[i] = resourceMap.getIcon("StatusBar.busyIcons[" + i + "]");
        }
        busyIconTimer = new Timer(busyAnimationRate, new ActionListener() {

            public void actionPerformed(ActionEvent e) {
                busyIconIndex = (busyIconIndex + 1) % busyIcons.length;
                statusAnimationLabel.setIcon(busyIcons[busyIconIndex]);
            }
        });
        idleIcon = resourceMap.getIcon("StatusBar.idleIcon");
        statusAnimationLabel.setIcon(idleIcon);
        progressBar.setVisible(false);

        // connecting action tasks to status bar via TaskMonitor
        TaskMonitor taskMonitor = new TaskMonitor(getApplication().getContext());
        taskMonitor.addPropertyChangeListener(new java.beans.PropertyChangeListener() {

            public void propertyChange(java.beans.PropertyChangeEvent evt) {
                String propertyName = evt.getPropertyName();
                if ("started".equals(propertyName)) {
                    if (!busyIconTimer.isRunning()) {
                        statusAnimationLabel.setIcon(busyIcons[0]);
                        busyIconIndex = 0;
                        busyIconTimer.start();
                    }
                    progressBar.setVisible(true);
                    progressBar.setIndeterminate(true);
                } else if ("done".equals(propertyName)) {
                    busyIconTimer.stop();
                    statusAnimationLabel.setIcon(idleIcon);
                    progressBar.setVisible(false);
                    progressBar.setValue(0);
                } else if ("message".equals(propertyName)) {
                    String text = (String) (evt.getNewValue());
                    statusMessageLabel.setText((text == null) ? "" : text);
                    messageTimer.restart();
                } else if ("progress".equals(propertyName)) {
                    int value = (Integer) (evt.getNewValue());
                    progressBar.setVisible(true);
                    progressBar.setIndeterminate(false);
                    progressBar.setValue(value);
                }
            }
        });
        createMemberTable(KobsApp.members, jTableMembers);
        setDateTitle();
        new StartThread();

        while (KobsApp.members.size() == 0) {
            Syncronize();
            KURLDialog.res=true;
            if (!KURLDialog.res) {
                System.exit(0);
            }
        }
    }

    @Action
    public void showAboutBox() {
        if (aboutBox == null) {
            JFrame mainFrame = KobsApp.getApplication().getMainFrame();
            aboutBox = new KobsAboutBox(mainFrame);
            aboutBox.setLocationRelativeTo(mainFrame);
        }
        KobsApp.getApplication().show(aboutBox);
    }

    /** This method is called from within the constructor to
     * initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is
     * always regenerated by the Form Editor.
     */
    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        mainPanel = new javax.swing.JPanel();
        jToolBar1 = new javax.swing.JToolBar();
        jSplitPane1 = new javax.swing.JSplitPane();
        jPanel1 = new javax.swing.JPanel();
        jToolBar2 = new javax.swing.JToolBar();
        jPanel2 = new javax.swing.JPanel();
        jToolBar3 = new javax.swing.JToolBar();
        jButtonLock = new javax.swing.JButton();
        jButtonAdd = new javax.swing.JButton();
        jButtonDelete = new javax.swing.JButton();
        jTabbedPane1 = new javax.swing.JTabbedPane();
        jScrollPane1 = new javax.swing.JScrollPane();
        jTableAttendies = new javax.swing.JTable();
        jScrollPane2 = new javax.swing.JScrollPane();
        jTableMembers = new javax.swing.JTable();
        menuBar = new javax.swing.JMenuBar();
        javax.swing.JMenu fileMenu = new javax.swing.JMenu();
        jMenuItem1 = new javax.swing.JMenuItem();
        jSeparator1 = new javax.swing.JSeparator();
        jMenuItemDate = new javax.swing.JMenuItem();
        javax.swing.JMenuItem exitMenuItem = new javax.swing.JMenuItem();
        javax.swing.JMenu helpMenu = new javax.swing.JMenu();
        javax.swing.JMenuItem aboutMenuItem = new javax.swing.JMenuItem();
        statusPanel = new javax.swing.JPanel();
        javax.swing.JSeparator statusPanelSeparator = new javax.swing.JSeparator();
        statusMessageLabel = new javax.swing.JLabel();
        statusAnimationLabel = new javax.swing.JLabel();
        progressBar = new javax.swing.JProgressBar();

        mainPanel.setName("mainPanel"); // NOI18N

        jToolBar1.setRollover(true);
        jToolBar1.setName("jToolBar1"); // NOI18N

        jSplitPane1.setDividerLocation(400);
        jSplitPane1.setName("jSplitPane1"); // NOI18N

        jPanel1.setName("jPanel1"); // NOI18N

        jToolBar2.setRollover(true);
        jToolBar2.setName("jToolBar2"); // NOI18N

        org.jdesktop.layout.GroupLayout jPanel1Layout = new org.jdesktop.layout.GroupLayout(jPanel1);
        jPanel1.setLayout(jPanel1Layout);
        jPanel1Layout.setHorizontalGroup(
            jPanel1Layout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(jToolBar2, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, 399, Short.MAX_VALUE)
        );
        jPanel1Layout.setVerticalGroup(
            jPanel1Layout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(jPanel1Layout.createSequentialGroup()
                .add(jToolBar2, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, 25, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE)
                .addContainerGap(649, Short.MAX_VALUE))
        );

        jSplitPane1.setLeftComponent(jPanel1);

        jPanel2.setName("jPanel2"); // NOI18N

        jToolBar3.setRollover(true);
        jToolBar3.setName("jToolBar3"); // NOI18N

        org.jdesktop.application.ResourceMap resourceMap = org.jdesktop.application.Application.getInstance(kobs.KobsApp.class).getContext().getResourceMap(KobsView.class);
        jButtonLock.setText(resourceMap.getString("jButtonLock.text")); // NOI18N
        jButtonLock.setFocusable(false);
        jButtonLock.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
        jButtonLock.setName("jButtonLock"); // NOI18N
        jButtonLock.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
        jToolBar3.add(jButtonLock);

        javax.swing.ActionMap actionMap = org.jdesktop.application.Application.getInstance(kobs.KobsApp.class).getContext().getActionMap(KobsView.class, this);
        jButtonAdd.setAction(actionMap.get("AddAttendie")); // NOI18N
        jButtonAdd.setFocusable(false);
        jButtonAdd.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
        jButtonAdd.setName("jButtonAdd"); // NOI18N
        jButtonAdd.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
        jToolBar3.add(jButtonAdd);

        jButtonDelete.setAction(actionMap.get("RemoveAttendie")); // NOI18N
        jButtonDelete.setFocusable(false);
        jButtonDelete.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
        jButtonDelete.setName("jButtonDelete"); // NOI18N
        jButtonDelete.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
        jToolBar3.add(jButtonDelete);

        jTabbedPane1.setName("jTabbedPane1"); // NOI18N

        jScrollPane1.setHorizontalScrollBarPolicy(javax.swing.ScrollPaneConstants.HORIZONTAL_SCROLLBAR_ALWAYS);
        jScrollPane1.setName("jScrollPane1"); // NOI18N

        jTableAttendies.setModel(new javax.swing.table.DefaultTableModel(
            new Object [][] {

            },
            new String [] {
                "Name", "Gurt"
            }
        ) {
            Class[] types = new Class [] {
                java.lang.String.class, java.lang.String.class
            };
            boolean[] canEdit = new boolean [] {
                false, false
            };

            public Class getColumnClass(int columnIndex) {
                return types [columnIndex];
            }

            public boolean isCellEditable(int rowIndex, int columnIndex) {
                return canEdit [columnIndex];
            }
        });
        jTableAttendies.setDragEnabled(true);
        jTableAttendies.setName("jTableAttendies"); // NOI18N
        jTableAttendies.getTableHeader().setReorderingAllowed(false);
        jTableAttendies.addFocusListener(new java.awt.event.FocusAdapter() {
            public void focusGained(java.awt.event.FocusEvent evt) {
                jTableAttendiesFocusGained(evt);
            }
            public void focusLost(java.awt.event.FocusEvent evt) {
                jTableAttendiesFocusLost(evt);
            }
        });
        jScrollPane1.setViewportView(jTableAttendies);
        jTableAttendies.getColumnModel().getColumn(0).setHeaderValue(resourceMap.getString("jTableAttendies.columnModel.title0")); // NOI18N
        jTableAttendies.getColumnModel().getColumn(1).setHeaderValue(resourceMap.getString("jTableAttendies.columnModel.title1")); // NOI18N

        jTabbedPane1.addTab(resourceMap.getString("jScrollPane1.TabConstraints.tabTitle"), jScrollPane1); // NOI18N

        jScrollPane2.setHorizontalScrollBarPolicy(javax.swing.ScrollPaneConstants.HORIZONTAL_SCROLLBAR_ALWAYS);
        jScrollPane2.setName("jScrollPane2"); // NOI18N

        jTableMembers.setModel(new javax.swing.table.DefaultTableModel(
            new Object [][] {

            },
            new String [] {
                "Last Name", "First Name", "Birthday", "City", "Phone", "Address", "Zip Code", "Kartennummer", "Gurt"
            }
        ));
        jTableMembers.setName("jTableMembers"); // NOI18N
        jTableMembers.addFocusListener(new java.awt.event.FocusAdapter() {
            public void focusGained(java.awt.event.FocusEvent evt) {
                jTableMembersFocusGained(evt);
            }
            public void focusLost(java.awt.event.FocusEvent evt) {
                jTableMembersFocusLost(evt);
            }
        });
        jScrollPane2.setViewportView(jTableMembers);
        jTableMembers.getModel().addTableModelListener(this);
        jTableMembers.getColumnModel().getColumn(0).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title0")); // NOI18N
        jTableMembers.getColumnModel().getColumn(1).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title1")); // NOI18N
        jTableMembers.getColumnModel().getColumn(2).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title2")); // NOI18N
        jTableMembers.getColumnModel().getColumn(3).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title3")); // NOI18N
        jTableMembers.getColumnModel().getColumn(4).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title4")); // NOI18N
        jTableMembers.getColumnModel().getColumn(5).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title5")); // NOI18N
        jTableMembers.getColumnModel().getColumn(6).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title6")); // NOI18N
        jTableMembers.getColumnModel().getColumn(7).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title7")); // NOI18N
        jTableMembers.getColumnModel().getColumn(8).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title8")); // NOI18N

        jTabbedPane1.addTab(resourceMap.getString("jScrollPane2.TabConstraints.tabTitle"), jScrollPane2); // NOI18N

        org.jdesktop.layout.GroupLayout jPanel2Layout = new org.jdesktop.layout.GroupLayout(jPanel2);
        jPanel2.setLayout(jPanel2Layout);
        jPanel2Layout.setHorizontalGroup(
            jPanel2Layout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(jToolBar3, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, 488, Short.MAX_VALUE)
            .add(jTabbedPane1, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, 488, Short.MAX_VALUE)
        );
        jPanel2Layout.setVerticalGroup(
            jPanel2Layout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(jPanel2Layout.createSequentialGroup()
                .add(jToolBar3, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, 25, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(org.jdesktop.layout.LayoutStyle.RELATED)
                .add(jTabbedPane1, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, 643, Short.MAX_VALUE))
        );

        jSplitPane1.setRightComponent(jPanel2);

        org.jdesktop.layout.GroupLayout mainPanelLayout = new org.jdesktop.layout.GroupLayout(mainPanel);
        mainPanel.setLayout(mainPanelLayout);
        mainPanelLayout.setHorizontalGroup(
            mainPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(jToolBar1, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, 899, Short.MAX_VALUE)
            .add(org.jdesktop.layout.GroupLayout.TRAILING, jSplitPane1, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, 899, Short.MAX_VALUE)
        );
        mainPanelLayout.setVerticalGroup(
            mainPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(mainPanelLayout.createSequentialGroup()
                .add(jToolBar1, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, 25, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(org.jdesktop.layout.LayoutStyle.RELATED)
                .add(jSplitPane1, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, 676, Short.MAX_VALUE))
        );

        menuBar.setName("menuBar"); // NOI18N

        fileMenu.setText(resourceMap.getString("fileMenu.text")); // NOI18N
        fileMenu.setName("fileMenu"); // NOI18N

        jMenuItem1.setAction(actionMap.get("Syncronize")); // NOI18N
        jMenuItem1.setText(resourceMap.getString("SyncronizeMenuItem.text")); // NOI18N
        jMenuItem1.setName("SyncronizeMenuItem"); // NOI18N
        fileMenu.add(jMenuItem1);
        jMenuItem1.getAccessibleContext().setAccessibleName(resourceMap.getString("jMenuItem1.AccessibleContext.accessibleName")); // NOI18N

        jSeparator1.setName("jSeparator1"); // NOI18N
        fileMenu.add(jSeparator1);

        jMenuItemDate.setAction(actionMap.get("setDate")); // NOI18N
        jMenuItemDate.setText(resourceMap.getString("jMenuItemDate.text")); // NOI18N
        jMenuItemDate.setName("jMenuItemDate"); // NOI18N
        fileMenu.add(jMenuItemDate);

        exitMenuItem.setAction(actionMap.get("quit")); // NOI18N
        exitMenuItem.setText(resourceMap.getString("exitMenuItem.text")); // NOI18N
        exitMenuItem.setName("exitMenuItem"); // NOI18N
        fileMenu.add(exitMenuItem);

        menuBar.add(fileMenu);

        helpMenu.setText(resourceMap.getString("helpMenu.text")); // NOI18N
        helpMenu.setName("helpMenu"); // NOI18N

        aboutMenuItem.setAction(actionMap.get("showAboutBox")); // NOI18N
        aboutMenuItem.setName("aboutMenuItem"); // NOI18N
        helpMenu.add(aboutMenuItem);

        menuBar.add(helpMenu);

        statusPanel.setName("statusPanel"); // NOI18N

        statusPanelSeparator.setName("statusPanelSeparator"); // NOI18N

        statusMessageLabel.setName("statusMessageLabel"); // NOI18N

        statusAnimationLabel.setHorizontalAlignment(javax.swing.SwingConstants.LEFT);
        statusAnimationLabel.setName("statusAnimationLabel"); // NOI18N

        progressBar.setName("progressBar"); // NOI18N

        org.jdesktop.layout.GroupLayout statusPanelLayout = new org.jdesktop.layout.GroupLayout(statusPanel);
        statusPanel.setLayout(statusPanelLayout);
        statusPanelLayout.setHorizontalGroup(
            statusPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(statusPanelSeparator, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, 899, Short.MAX_VALUE)
            .add(statusPanelLayout.createSequentialGroup()
                .addContainerGap()
                .add(statusMessageLabel)
                .addPreferredGap(org.jdesktop.layout.LayoutStyle.RELATED, 715, Short.MAX_VALUE)
                .add(progressBar, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(org.jdesktop.layout.LayoutStyle.RELATED)
                .add(statusAnimationLabel)
                .addContainerGap())
        );
        statusPanelLayout.setVerticalGroup(
            statusPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(statusPanelLayout.createSequentialGroup()
                .add(statusPanelSeparator, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, 2, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(org.jdesktop.layout.LayoutStyle.RELATED, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                .add(statusPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.BASELINE)
                    .add(statusMessageLabel)
                    .add(statusAnimationLabel)
                    .add(progressBar, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE))
                .add(3, 3, 3))
        );

        setComponent(mainPanel);
        setMenuBar(menuBar);
        setStatusBar(statusPanel);
    }// </editor-fold>//GEN-END:initComponents

private void jTableAttendiesFocusGained(java.awt.event.FocusEvent evt) {//GEN-FIRST:event_jTableAttendiesFocusGained
    jButtonDelete.setEnabled(KobsApp.attendies.size() > 0);
}//GEN-LAST:event_jTableAttendiesFocusGained

private void jTableAttendiesFocusLost(java.awt.event.FocusEvent evt) {//GEN-FIRST:event_jTableAttendiesFocusLost
    jButtonDelete.setEnabled(false);
}//GEN-LAST:event_jTableAttendiesFocusLost

private void jTableMembersFocusGained(java.awt.event.FocusEvent evt) {//GEN-FIRST:event_jTableMembersFocusGained
    jButtonAdd.setEnabled(KobsApp.members.size() > 0);
}//GEN-LAST:event_jTableMembersFocusGained

private void jTableMembersFocusLost(java.awt.event.FocusEvent evt) {//GEN-FIRST:event_jTableMembersFocusLost
    jButtonAdd.setEnabled(false);
}//GEN-LAST:event_jTableMembersFocusLost

    @Action
    public void Syncronize() {
        String error;
        
            KURLDialog urlDialog= new KURLDialog(null, true,KobsApp.props.getProperty("username",""),"",KobsApp.props.getProperty("URL","http://localhost/admidio/adm_program/modules/kobs/getkobsdata.php"),false);


        urlDialog.setVisible(true);
        if (urlDialog.res){
            if ( (error=KReadHTTPFile.writeFile2URL(urlDialog.URL, KConstants.DBSessionFileName, urlDialog.userName, urlDialog.userPw)).compareTo("")!=0){
              JOptionPane.showMessageDialog(null,error,KobsApp.lang.getProperty("URLErrorTitle","Syncronisation Error"),JOptionPane.ERROR_MESSAGE); 
           }
           else {
                JOptionPane.showMessageDialog(null,"Sessiondata übertragen??");
           }           
           if ( (error=KReadHTTPFile.readURL2File(urlDialog.URL, KConstants.DBDataFileName, urlDialog.userName, urlDialog.userPw)).compareTo("")!=0){
              JOptionPane.showMessageDialog(null,error,KobsApp.lang.getProperty("URLErrorTitle","Syncronisation Error"),JOptionPane.ERROR_MESSAGE); 
           }
           else {
                JOptionPane.showMessageDialog(null,KobsApp.lang.getProperty("URLDownloadOkText","Syncronisation succesfull -actual Data received"),KobsApp.lang.getProperty("URLDownloadOkTitle","Syncronisation"),JOptionPane.INFORMATION_MESSAGE); 
                KobsApp.props.setProperty("username",urlDialog.userName);
                KobsApp.props.setProperty("URL",urlDialog.URL);
                try{
                    KobsApp.props.store(new java.io.FileOutputStream(KConstants.PrefsFileName), "Kobs Preferences");
                } catch(java.io.IOException ignored){};
                KobsApp.importUserDB();
                createMemberTable(KobsApp.members,jTableMembers);
           }
        }
    }
    
    public void getNr(String nr) {
            System.out.println(nr);
            Toolkit.getDefaultToolkit().beep();
            KStringHash thisRecord= KobsApp.members.find("kartennummer", nr);
            if (jTableMembers.hasFocus() && jTableMembers.getSelectedColumn()==7 && jTableMembers.getSelectedRowCount() ==1 && jTableMembers.getSelectedColumnCount() ==1){
                System.out.println("Insert Kartennummer");
                KHashLink actHashLink= (KHashLink)jTableMembers.getModel().getValueAt( jTableMembers.getSelectedRow(),jTableMembers.getSelectedColumn());
                HashMap<String,String> newRecord= actHashLink.getHashMap();
                if (thisRecord != newRecord){
                    newRecord.put("kartennummer", nr);
                    newRecord.put("modified", "true");
                    if (thisRecord!=null){
                        thisRecord.put("kartennummer", "");
                        thisRecord.put("modified", "true");
                    }
                    jTableMembers.updateUI();
                }
            }
            thisRecord= KobsApp.members.find("kartennummer", nr);
            if (( thisRecord!=null) && !KobsApp.attendies.containsValue(thisRecord)){
                String usrId=thisRecord.get("usr_id");
                KobsApp.attendies.put(usrId, thisRecord);
                createAttendiesTable(KobsApp.attendies,jTableAttendies);
            }
        }
    
    public void createAttendiesTable(HashMap<String, KStringHash> map, JTable jTable){
        ((DefaultTableModel) jTable.getModel()).getDataVector().removeAllElements();
        Iterator<String> all = map.keySet().iterator();
        while (all.hasNext()) {
            String currentall = all.next();
            KStringHash thisRecord = map.get(currentall);
            Iterator<String> records = thisRecord.keySet().iterator();

            while (records.hasNext()) {
                String currentKey = records.next();
                System.out.println(currentKey + ":" + thisRecord.get(currentKey));
            }
            ((DefaultTableModel) jTable.getModel()).addRow(new Object[]{new KHashLink(thisRecord,"last_name"), new KHashLink(thisRecord,"gurt")});


        }
    }
    
    public void createMemberTable(HashMap<String, KStringHash> map, JTable jTable){
        ((DefaultTableModel) jTable.getModel()).getDataVector().removeAllElements();
         Iterator<String> all = map.keySet().iterator();
        while (all.hasNext()) {
            String currentall = all.next();
            KStringHash thisRecord = map.get(currentall);
             ((DefaultTableModel) jTable.getModel()).addRow(
                     new Object[]{
                     new KHashLink(thisRecord, KConstants.TableIndices[0]),
                     new KHashLink(thisRecord,KConstants.TableIndices[1]),
                     new KHashLink(thisRecord,KConstants.TableIndices[2]),
                     new KHashLink(thisRecord,KConstants.TableIndices[3]),
                     new KHashLink(thisRecord,KConstants.TableIndices[4]),
                     new KHashLink(thisRecord,KConstants.TableIndices[5]),
                     new KHashLink(thisRecord,KConstants.TableIndices[6]),
                     new KHashLink(thisRecord,KConstants.TableIndices[7]),
                     new KHashLink(thisRecord,KConstants.TableIndices[8])
             });


        }
    }
    
    public class StartThread implements Runnable {

        StartThread() {
            thread = new Thread(this);
            thread.start();
        }

        public void run() {
            try {
                byte[] buffer = new byte[1024];
                int port = Integer.valueOf(KobsApp.lang.getProperty("UDPPort", "3305")).intValue();
                try {
                    socket = new DatagramSocket(port);
                    while (true) {
                        try {
                            //Receive request from client
                            DatagramPacket packet = new DatagramPacket(buffer, buffer.length);
                            socket.receive(packet);
                            InetAddress client = packet.getAddress();
                            int client_port = packet.getPort();
                            getNr(new String(buffer).trim());

                        } catch (UnknownHostException ue) {
                        }
                    }
                } catch (java.net.BindException b) {
                }
            } catch (IOException e) {
                System.err.println(e);
            }
        }
    }

    @Action
    public void setDate() {
        JFrame mainFrame = KobsApp.getApplication().getMainFrame();
        KDateDialog dateDialog = new KDateDialog(
               mainFrame,
                KobsApp.lang.getProperty("DateFormat","MM/dd/yyyy"),
                KobsApp.lang.getProperty("TimeFormat","hh:mm"),
                KobsApp.actDate,
                KobsApp.actStartTime,
                KobsApp.actEndTime);
                KobsApp.actDate=dateDialog.date;
                KobsApp.actDateString=dateDialog.dateString;
                KobsApp.actStartTime=dateDialog.startTime;
                KobsApp.actStartTimeString=dateDialog.startTimeString;
                KobsApp.actEndTime=dateDialog.endTime;
                KobsApp.actEndTimeString=dateDialog.endTimeString;
                KobsApp.actLocation=dateDialog.location;
                KobsApp.actLocationId=((KStringHash)dateDialog.locationHash).get(KConstants.LocIdName);
                
                setDateTitle();
    }

    public void setDateTitle(){
        this.getFrame().setTitle(KobsApp.actDateString+" - "+KobsApp.actLocation+" - "+KobsApp.actLocationId+" - "+KobsApp.actStartTimeString+" - "+KobsApp.actEndTimeString+" - "+KConstants.AppName);
    }
    
    public void tableChanged(TableModelEvent e) {
        /* now it becomes tricky: As changing of the cell destroys the previous reference to the KHashLink by a simple String,
         * I've to restore this information through the cell beside
         */
        
        int row = e.getFirstRow();
        int column = e.getColumn();
        if (row<0 || column<0){
            return;
        }
        // I also need a valid colcount from left or right beside the changed cell
        int refreshCol= (column>0)? 0: 1;
        // then we read the new value of the cell
        TableModel model = (TableModel)e.getSource();
        if (!(model.getValueAt(row, column) instanceof String)){
            return;
        }
        String newValueString="";
        try{
            newValueString= (String) model.getValueAt(row, column);
        }
        catch (java.lang.Error ignored){};
        // now we read the original data from the cell beside
        KHashLink actHashLink=null;
       try{
            actHashLink= (KHashLink)jTableMembers.getModel().getValueAt( row,refreshCol);
        }

        catch (java.lang.Error ignored){};
        if (actHashLink!=null){        
            KStringHash thisRecord= actHashLink.getHashMap();
            if (thisRecord!=null){
                thisRecord.put(KConstants.TableIndices[column], newValueString);
                thisRecord.put(KConstants.MemModKey, KConstants.MemModValue);
                model.setValueAt(new KHashLink(thisRecord,KConstants.TableIndices[column]), row, column);

            }
        }
    }
      
    @Action
    public void AddAttendie() {

        KHashLink actHashLink=null;
        // get actual Member
        try{
            actHashLink= (KHashLink)jTableMembers.getModel().getValueAt( jTableMembers.getSelectedRow(),jTableMembers.getSelectedColumn());
        }
        catch (java.lang.Error e){};
        // valid selection?
        if (actHashLink!=null){        
            KStringHash thisRecord= actHashLink.getHashMap();
            if (( thisRecord!=null) && !KobsApp.attendies.containsValue(thisRecord)){
                String usrId=thisRecord.get("usr_id");
                KobsApp.attendies.put(usrId, thisRecord);
                createAttendiesTable(KobsApp.attendies,jTableAttendies);
                jTableAttendies.updateUI();
            }
        }
    }

    @Action
    public void RemoveAttendie() {

        KHashLink actHashLink=null;
                // get actualAttendie
       try{
           actHashLink= (KHashLink)jTableAttendies.getModel().getValueAt( jTableAttendies.getSelectedRow(),jTableAttendies.getSelectedColumn());
       }
       catch (java.lang.Error e){};
        // valid selection?
        if (actHashLink!=null){        
            HashMap<String,String> thisRecord= actHashLink.getHashMap();
            if (( thisRecord!=null) && KobsApp.attendies.containsValue(thisRecord)){
               String usrId=thisRecord.get("usr_id");
                KobsApp.attendies.remove(usrId);
                createAttendiesTable(KobsApp.attendies,jTableAttendies);
                jButtonDelete.setEnabled(KobsApp.attendies.size()>0);
                jTableAttendies.updateUI();
            }
         }
    }
    
    
    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton jButtonAdd;
    private javax.swing.JButton jButtonDelete;
    private javax.swing.JButton jButtonLock;
    private javax.swing.JMenuItem jMenuItem1;
    private javax.swing.JMenuItem jMenuItemDate;
    private javax.swing.JPanel jPanel1;
    private javax.swing.JPanel jPanel2;
    private javax.swing.JScrollPane jScrollPane1;
    private javax.swing.JScrollPane jScrollPane2;
    private javax.swing.JSeparator jSeparator1;
    private javax.swing.JSplitPane jSplitPane1;
    private javax.swing.JTabbedPane jTabbedPane1;
    private javax.swing.JTable jTableAttendies;
    private javax.swing.JTable jTableMembers;
    private javax.swing.JToolBar jToolBar1;
    private javax.swing.JToolBar jToolBar2;
    private javax.swing.JToolBar jToolBar3;
    private javax.swing.JPanel mainPanel;
    private javax.swing.JMenuBar menuBar;
    private javax.swing.JProgressBar progressBar;
    private javax.swing.JLabel statusAnimationLabel;
    private javax.swing.JLabel statusMessageLabel;
    private javax.swing.JPanel statusPanel;
    // End of variables declaration//GEN-END:variables

    private final Timer messageTimer;
    private final Timer busyIconTimer;
    private final Icon idleIcon;
    private final Icon[] busyIcons = new Icon[15];
    private int busyIconIndex = 0;

    private JDialog aboutBox;
    private KURLDialog urlDialog;
    
    Thread thread;
    DatagramSocket socket;

}
