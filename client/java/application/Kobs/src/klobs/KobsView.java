/*
 * KobsView.java
 */
package klobs;

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
import javax.swing.tree.*;
import javax.swing.table.*;
import javax.swing.table.TableModel;
import javax.swing.event.*;
import java.util.*;
import java.net.*;
import java.io.*;
import java.io.File;

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
        createMemberTable(KlobsApp.members, jTableMembers);
        setDateTitle();
        new StartThread();

        while (KlobsApp.members.size() == 0) {
            Syncronize();
            if (!KURLDialog.res) {
                JOptionPane.showMessageDialog(null,
                        KlobsApp.lang.getProperty("ProgramQuitText", "Without first sync the program is useless and will quit now"),
                        KlobsApp.lang.getProperty("ProgramQuitTitle", "Sync cancelled"),
                        JOptionPane.ERROR_MESSAGE);
                System.exit(0);
            }
        }
        //Make textField get the focus whenever frame is activated.
        this.getFrame().addWindowFocusListener(new WindowAdapter() {

            public void windowGainedFocus(WindowEvent e) {
                jTableMembers.requestFocusInWindow();
            }
        });

        jTableMembers.requestFocusInWindow();
        // ((TableRowSorter) jTableMembers.getModel()).setSortingStatus(0, 1);
        ((javax.swing.DefaultRowSorter) jTableMembers.getRowSorter()).toggleSortOrder(0);

    }

    @Action
    public void showAboutBox() {
        if (aboutBox == null) {
            JFrame mainFrame = KlobsApp.getApplication().getMainFrame();
            aboutBox = new KobsAboutBox(mainFrame);
            aboutBox.setLocationRelativeTo(mainFrame);
        }
        KlobsApp.getApplication().show(aboutBox);
    }

    /** This method is called from within the constructor to
     * initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is
     * always regenerated by the Form Editor.
     */
    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {
        java.awt.GridBagConstraints gridBagConstraints;

        mainPanel = new javax.swing.JPanel();
        mainToolBar = new javax.swing.JToolBar();
        mainTabbedPane = new javax.swing.JTabbedPane();
        memberPanel = new javax.swing.JPanel();
        memberScrollPane = new javax.swing.JScrollPane();
        jTableMembers = new javax.swing.JTable();
        memberToolBar = new javax.swing.JToolBar();
        jButtonAdd = new javax.swing.JButton();
        timePanel = new javax.swing.JPanel();
        timeBottomToolBar = new javax.swing.JToolBar();
        jLabel1 = new javax.swing.JLabel();
        timeComboBox = new javax.swing.JComboBox();
        jLabel2 = new javax.swing.JLabel();
        taskComboBox = new javax.swing.JComboBox();
        subtaskComboBox = new javax.swing.JComboBox();
        jLabel3 = new javax.swing.JLabel();
        trainerComboBox = new javax.swing.JComboBox();
        timeSplitPane = new javax.swing.JSplitPane();
        timeCanvasPanel = new javax.swing.JPanel();
        timeTopToolBar = new javax.swing.JToolBar();
        addTimeButton = new javax.swing.JButton();
        splitTimeButton = new javax.swing.JButton();
        addTaskButton = new javax.swing.JButton();
        deleteNodeButton = new javax.swing.JButton();
        timeScrollPane = new javax.swing.JScrollPane();
        timeTreeView = new javax.swing.JTree();
        onsidePanel = new javax.swing.JPanel();
        onsideAllScrollPane = new javax.swing.JScrollPane();
        onsideAllTree = new javax.swing.JTree();
        onsideSelectedScrollPane = new javax.swing.JScrollPane();
        onsideSelectedTree = new javax.swing.JTree();
        moveInButton = new javax.swing.JButton();
        moveOutButton = new javax.swing.JButton();
        onsideLabel = new javax.swing.JLabel();
        attendieLabel = new javax.swing.JLabel();
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
        mainPanel.setLayout(new java.awt.BorderLayout());

        mainToolBar.setRollover(true);
        mainToolBar.setName("mainToolBar"); // NOI18N
        mainPanel.add(mainToolBar, java.awt.BorderLayout.NORTH);

        mainTabbedPane.setName("mainTabbedPane"); // NOI18N

        memberPanel.setName("memberPanel"); // NOI18N
        memberPanel.setLayout(new java.awt.BorderLayout());

        memberScrollPane.setHorizontalScrollBarPolicy(javax.swing.ScrollPaneConstants.HORIZONTAL_SCROLLBAR_ALWAYS);
        memberScrollPane.setName("memberScrollPane"); // NOI18N

        jTableMembers.setAutoCreateRowSorter(true);
        jTableMembers.setModel(new javax.swing.table.DefaultTableModel(
            new Object [][] {

            },
            new String [] {
                "Last Name", "First Name", "Birthday", "City", "Phone", "Address", "Zip Code", "Kartennummer", "Gurt"
            }
        ));
        org.jdesktop.application.ResourceMap resourceMap = org.jdesktop.application.Application.getInstance(klobs.KlobsApp.class).getContext().getResourceMap(KobsView.class);
        jTableMembers.setToolTipText(resourceMap.getString("jTableMembers.toolTipText")); // NOI18N
        jTableMembers.setName("jTableMembers"); // NOI18N
        jTableMembers.setRowSelectionAllowed(false);
        jTableMembers.getTableHeader().setReorderingAllowed(false);
        jTableMembers.addKeyListener(new java.awt.event.KeyAdapter() {
            public void keyTyped(java.awt.event.KeyEvent evt) {
                jTableMembersKeyTyped(evt);
            }
        });
        memberScrollPane.setViewportView(jTableMembers);
        jTableMembers.getModel().addTableModelListener(this);
        jTableMembers.getColumnModel().getColumn(0).setMinWidth(5);
        jTableMembers.getColumnModel().getColumn(0).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title0")); // NOI18N
        jTableMembers.getColumnModel().getColumn(1).setMinWidth(5);
        jTableMembers.getColumnModel().getColumn(1).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title1")); // NOI18N
        jTableMembers.getColumnModel().getColumn(2).setMinWidth(5);
        jTableMembers.getColumnModel().getColumn(2).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title2")); // NOI18N
        jTableMembers.getColumnModel().getColumn(3).setMinWidth(5);
        jTableMembers.getColumnModel().getColumn(3).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title3")); // NOI18N
        jTableMembers.getColumnModel().getColumn(4).setMinWidth(5);
        jTableMembers.getColumnModel().getColumn(4).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title4")); // NOI18N
        jTableMembers.getColumnModel().getColumn(5).setMinWidth(5);
        jTableMembers.getColumnModel().getColumn(5).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title5")); // NOI18N
        jTableMembers.getColumnModel().getColumn(6).setMinWidth(5);
        jTableMembers.getColumnModel().getColumn(6).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title6")); // NOI18N
        jTableMembers.getColumnModel().getColumn(7).setMinWidth(5);
        jTableMembers.getColumnModel().getColumn(7).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title7")); // NOI18N
        jTableMembers.getColumnModel().getColumn(8).setMinWidth(5);
        jTableMembers.getColumnModel().getColumn(8).setHeaderValue(resourceMap.getString("jTableMembers.columnModel.title8")); // NOI18N
        jTableMembers.setDefaultRenderer(Object.class, new CustomTableCellRenderer());

        memberPanel.add(memberScrollPane, java.awt.BorderLayout.CENTER);

        memberToolBar.setRollover(true);
        memberToolBar.setName("memberToolBar"); // NOI18N

        javax.swing.ActionMap actionMap = org.jdesktop.application.Application.getInstance(klobs.KlobsApp.class).getContext().getActionMap(KobsView.class, this);
        jButtonAdd.setAction(actionMap.get("toggleAttendie")); // NOI18N
        jButtonAdd.setText(resourceMap.getString("jButtonAdd.text")); // NOI18N
        jButtonAdd.setFocusable(false);
        jButtonAdd.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
        jButtonAdd.setName("jButtonAdd"); // NOI18N
        jButtonAdd.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
        memberToolBar.add(jButtonAdd);

        memberPanel.add(memberToolBar, java.awt.BorderLayout.NORTH);

        mainTabbedPane.addTab(resourceMap.getString("memberPanel.TabConstraints.tabTitle"), memberPanel); // NOI18N

        timePanel.setName("timePanel"); // NOI18N
        timePanel.setLayout(new java.awt.BorderLayout());

        timeBottomToolBar.setRollover(true);
        timeBottomToolBar.setName("timeBottomToolBar"); // NOI18N

        jLabel1.setText(resourceMap.getString("jLabel1.text")); // NOI18N
        jLabel1.setName("jLabel1"); // NOI18N
        timeBottomToolBar.add(jLabel1);

        timeComboBox.setModel(new javax.swing.DefaultComboBoxModel(new String[] { "Item 1", "Item 2", "Item 3", "Item 4" }));
        timeComboBox.setName("timeComboBox"); // NOI18N
        timeBottomToolBar.add(timeComboBox);

        jLabel2.setText(resourceMap.getString("jLabel2.text")); // NOI18N
        jLabel2.setName("jLabel2"); // NOI18N
        timeBottomToolBar.add(jLabel2);

        taskComboBox.setModel(new javax.swing.DefaultComboBoxModel(new String[] { "Item 1", "Item 2", "Item 3", "Item 4" }));
        taskComboBox.setName("taskComboBox"); // NOI18N
        timeBottomToolBar.add(taskComboBox);

        subtaskComboBox.setModel(new javax.swing.DefaultComboBoxModel(new String[] { "Item 1", "Item 2", "Item 3", "Item 4" }));
        subtaskComboBox.setName("subtaskComboBox"); // NOI18N
        timeBottomToolBar.add(subtaskComboBox);

        jLabel3.setText(resourceMap.getString("jLabel3.text")); // NOI18N
        jLabel3.setName("jLabel3"); // NOI18N
        timeBottomToolBar.add(jLabel3);

        trainerComboBox.setModel(new javax.swing.DefaultComboBoxModel(new String[] { "Item 1", "Item 2", "Item 3", "Item 4" }));
        trainerComboBox.setName("trainerComboBox"); // NOI18N
        timeBottomToolBar.add(trainerComboBox);

        timePanel.add(timeBottomToolBar, java.awt.BorderLayout.SOUTH);

        timeSplitPane.setDividerLocation(400);
        timeSplitPane.setResizeWeight(0.5);
        timeSplitPane.setName("timeSplitPane"); // NOI18N
        timeSplitPane.setPreferredSize(new java.awt.Dimension(0, 0));

        timeCanvasPanel.setName("timeCanvasPanel"); // NOI18N
        timeCanvasPanel.setPreferredSize(new java.awt.Dimension(0, 0));
        timeCanvasPanel.setLayout(new java.awt.BorderLayout());

        timeTopToolBar.setRollover(true);
        timeTopToolBar.setMaximumSize(new java.awt.Dimension(0, 0));
        timeTopToolBar.setName("timeTopToolBar"); // NOI18N
        timeTopToolBar.setPreferredSize(new java.awt.Dimension(100, 18));

        addTimeButton.setAction(actionMap.get("doTimeViewAction")); // NOI18N
        addTimeButton.setText(resourceMap.getString("addTimeButton.text")); // NOI18N
        addTimeButton.setActionCommand(resourceMap.getString("addTimeButton.actionCommand")); // NOI18N
        addTimeButton.setFocusable(false);
        addTimeButton.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
        addTimeButton.setName("addTimeButton"); // NOI18N
        addTimeButton.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
        timeTopToolBar.add(addTimeButton);

        splitTimeButton.setAction(actionMap.get("doTimeViewAction")); // NOI18N
        splitTimeButton.setText(resourceMap.getString("splitTimeButton.text")); // NOI18N
        splitTimeButton.setActionCommand(resourceMap.getString("splitTimeButton.actionCommand")); // NOI18N
        splitTimeButton.setFocusable(false);
        splitTimeButton.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
        splitTimeButton.setName("splitTimeButton"); // NOI18N
        splitTimeButton.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
        timeTopToolBar.add(splitTimeButton);
        splitTimeButton.getAccessibleContext().setAccessibleName(resourceMap.getString("jButton2.AccessibleContext.accessibleName")); // NOI18N

        addTaskButton.setAction(actionMap.get("doTimeViewAction")); // NOI18N
        addTaskButton.setText(resourceMap.getString("addTaskButton.text")); // NOI18N
        addTaskButton.setActionCommand(resourceMap.getString("addTaskButton.actionCommand")); // NOI18N
        addTaskButton.setFocusable(false);
        addTaskButton.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
        addTaskButton.setName("addTaskButton"); // NOI18N
        addTaskButton.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
        timeTopToolBar.add(addTaskButton);
        addTaskButton.getAccessibleContext().setAccessibleName(resourceMap.getString("jButton3.AccessibleContext.accessibleName")); // NOI18N

        deleteNodeButton.setAction(actionMap.get("doTimeViewAction")); // NOI18N
        deleteNodeButton.setText(resourceMap.getString("deleteNodeButton.text")); // NOI18N
        deleteNodeButton.setActionCommand(resourceMap.getString("deleteNodeButton.actionCommand")); // NOI18N
        deleteNodeButton.setFocusable(false);
        deleteNodeButton.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
        deleteNodeButton.setName("deleteNodeButton"); // NOI18N
        deleteNodeButton.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
        timeTopToolBar.add(deleteNodeButton);
        deleteNodeButton.getAccessibleContext().setAccessibleName(resourceMap.getString("jButton4.AccessibleContext.accessibleName")); // NOI18N

        timeCanvasPanel.add(timeTopToolBar, java.awt.BorderLayout.NORTH);

        timeScrollPane.setName("timeScrollPane"); // NOI18N

        javax.swing.tree.DefaultMutableTreeNode treeNode1 = new javax.swing.tree.DefaultMutableTreeNode("root");
        timeTreeView.setModel(new javax.swing.tree.DefaultTreeModel(treeNode1));
        timeTreeView.setEditable(true);
        timeTreeView.setName("timeTreeView"); // NOI18N
        timeScrollPane.setViewportView(timeTreeView);

        timeCanvasPanel.add(timeScrollPane, java.awt.BorderLayout.CENTER);

        timeSplitPane.setLeftComponent(timeCanvasPanel);

        onsidePanel.setName("onsidePanel"); // NOI18N
        onsidePanel.setLayout(new java.awt.GridBagLayout());

        onsideAllScrollPane.setName("onsideAllScrollPane"); // NOI18N

        onsideAllTree.setName("onsideAllTree"); // NOI18N
        onsideAllScrollPane.setViewportView(onsideAllTree);

        gridBagConstraints = new java.awt.GridBagConstraints();
        gridBagConstraints.gridx = 0;
        gridBagConstraints.gridy = 1;
        gridBagConstraints.gridheight = 3;
        gridBagConstraints.fill = java.awt.GridBagConstraints.BOTH;
        gridBagConstraints.weightx = 1.0;
        onsidePanel.add(onsideAllScrollPane, gridBagConstraints);

        onsideSelectedScrollPane.setName("onsideSelectedScrollPane"); // NOI18N

        onsideSelectedTree.setName("onsideSelectedTree"); // NOI18N
        onsideSelectedScrollPane.setViewportView(onsideSelectedTree);

        gridBagConstraints = new java.awt.GridBagConstraints();
        gridBagConstraints.gridx = 2;
        gridBagConstraints.gridy = 1;
        gridBagConstraints.gridheight = 3;
        gridBagConstraints.fill = java.awt.GridBagConstraints.BOTH;
        gridBagConstraints.weightx = 1.0;
        gridBagConstraints.weighty = 1.0;
        onsidePanel.add(onsideSelectedScrollPane, gridBagConstraints);

        moveInButton.setText(resourceMap.getString("moveInButton.text")); // NOI18N
        moveInButton.setName("moveInButton"); // NOI18N
        gridBagConstraints = new java.awt.GridBagConstraints();
        gridBagConstraints.gridx = 1;
        gridBagConstraints.gridy = 1;
        gridBagConstraints.anchor = java.awt.GridBagConstraints.SOUTH;
        onsidePanel.add(moveInButton, gridBagConstraints);

        moveOutButton.setText(resourceMap.getString("moveOutButton.text")); // NOI18N
        moveOutButton.setName("moveOutButton"); // NOI18N
        gridBagConstraints = new java.awt.GridBagConstraints();
        gridBagConstraints.gridx = 1;
        gridBagConstraints.gridy = 2;
        gridBagConstraints.anchor = java.awt.GridBagConstraints.NORTH;
        onsidePanel.add(moveOutButton, gridBagConstraints);

        onsideLabel.setText(resourceMap.getString("onsideLabel.text")); // NOI18N
        onsideLabel.setName("onsideLabel"); // NOI18N
        gridBagConstraints = new java.awt.GridBagConstraints();
        gridBagConstraints.gridx = 0;
        gridBagConstraints.gridy = 0;
        onsidePanel.add(onsideLabel, gridBagConstraints);

        attendieLabel.setText(resourceMap.getString("attendieLabel.text")); // NOI18N
        attendieLabel.setName("attendieLabel"); // NOI18N
        gridBagConstraints = new java.awt.GridBagConstraints();
        gridBagConstraints.gridx = 2;
        gridBagConstraints.gridy = 0;
        onsidePanel.add(attendieLabel, gridBagConstraints);

        timeSplitPane.setRightComponent(onsidePanel);

        timePanel.add(timeSplitPane, java.awt.BorderLayout.CENTER);

        mainTabbedPane.addTab(resourceMap.getString("timePanel.TabConstraints.tabTitle"), timePanel); // NOI18N

        mainPanel.add(mainTabbedPane, java.awt.BorderLayout.CENTER);

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
            .add(statusPanelSeparator, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, 846, Short.MAX_VALUE)
            .add(statusPanelLayout.createSequentialGroup()
                .addContainerGap()
                .add(statusMessageLabel)
                .addPreferredGap(org.jdesktop.layout.LayoutStyle.RELATED, 662, Short.MAX_VALUE)
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

    private void jTableMembersKeyTyped(java.awt.event.KeyEvent evt) {//GEN-FIRST:event_jTableMembersKeyTyped
        if (evt.getKeyChar() == ' ') {
            toggleAttendie();
        }
    }//GEN-LAST:event_jTableMembersKeyTyped

    @Action
    public void Syncronize() {
        String error;

        KURLDialog urlDialog = new KURLDialog(null, true, KlobsApp.props.getProperty("username", ""), "", KlobsApp.props.getProperty("URL", "http://mitglieder.shojikido.de/adm_program/modules/kobs/syncklobs.php"), false);


        urlDialog.setVisible(true);
        if (urlDialog.res) {
            if ((error = KReadHTTPFile.syncronize2URL(urlDialog.URL, KConstants.DBSessionFileName, KConstants.DBDataFileName, urlDialog.userName, urlDialog.userPw)).compareTo("") != 0) {
                JOptionPane.showMessageDialog(null, error, KlobsApp.lang.getProperty("URLErrorTitle", "Syncronisation Error"), JOptionPane.ERROR_MESSAGE);
            } else {
                JOptionPane.showMessageDialog(null, KlobsApp.lang.getProperty("URLDownloadOkText", "Syncronisation succesfull -actual Data received"), KlobsApp.lang.getProperty("URLDownloadOkTitle", "Syncronisation"), JOptionPane.INFORMATION_MESSAGE);
                KlobsApp.props.setProperty("username", urlDialog.userName);
                KlobsApp.props.setProperty("URL", urlDialog.URL);
                try {
                    KlobsApp.props.store(new java.io.FileOutputStream(KConstants.PrefsFileName), "Kobs Preferences");

                } catch (java.io.IOException ignored) {
                }
                ;
                File f = new File(KConstants.DBSessionFileName);
                if (f.exists() && !f.delete()) {
                    JOptionPane.showMessageDialog(null, KlobsApp.lang.getProperty("DeleteOldSession", "Can't delete old Session file"), KlobsApp.lang.getProperty("DeleteOldSessionTitle", "File problem"), JOptionPane.ERROR_MESSAGE);
                }
                ;
                KlobsApp.importUserDB();
                createMemberTable(KlobsApp.members, jTableMembers);
            }
        }
    }

    public void getNr(String nr) {
        Toolkit.getDefaultToolkit().beep();
        KStringHash thisRecord = KlobsApp.members.find("kartennummer", nr);
        if (jTableMembers.hasFocus() && jTableMembers.getSelectedColumn() == 7 && jTableMembers.getSelectedRowCount() == 1 && jTableMembers.getSelectedColumnCount() == 1) {
            KHashLink actHashLink = (KHashLink) jTableMembers.getModel().getValueAt(jTableMembers.getRowSorter().convertRowIndexToModel(jTableMembers.getSelectedRow()), jTableMembers.getSelectedColumn());
            //HashMap<String, String> newRecord = (KStringHash) actHashLink.getHashMap();
            KStringHash newRecord = (KStringHash) actHashLink.getHashMap();
            if (thisRecord != newRecord) {
                if (thisRecord != null) {
                    thisRecord.put("kartennummer", "");
                    thisRecord.put("modified", "true");
                }
                newRecord.put("kartennummer", nr);
                newRecord.put("modified", "true");
                thisRecord = newRecord;
                jTableMembers.invalidate();
                jTableMembers.validate();
                jTableMembers.repaint();
            }
        }
        //thisRecord = KlobsApp.members.find("kartennummer", nr);
        if (thisRecord != null) {
            thisRecord.put(KConstants.MemOnside, KConstants.TrueValue);
            statusMessageLabel.setText(KlobsApp.lang.getProperty("LastCardText", "Last Card: ") + thisRecord.get("first_name") + " " + thisRecord.get("last_name"));
            messageTimer.start();
        } else {
            statusMessageLabel.setText(KlobsApp.lang.getProperty("UnkonwnCard", "Unknown Card!"));
            messageTimer.start();

        }
    }

    public void createMemberTable(HashMap<String, KStringHash> map, JTable jTable) {
        ((DefaultTableModel) jTable.getModel()).getDataVector().removeAllElements();
        Iterator<String> all = map.keySet().iterator();
        while (all.hasNext()) {
            String currentall = all.next();
            KStringHash thisRecord = map.get(currentall);
            ((DefaultTableModel) jTable.getModel()).addRow(
                    new Object[]{
                        new KHashLink(thisRecord, KConstants.TableIndices[0]),
                        new KHashLink(thisRecord, KConstants.TableIndices[1]),
                        new KHashLink(thisRecord, KConstants.TableIndices[2]),
                        new KHashLink(thisRecord, KConstants.TableIndices[3]),
                        new KHashLink(thisRecord, KConstants.TableIndices[4]),
                        new KHashLink(thisRecord, KConstants.TableIndices[5]),
                        new KHashLink(thisRecord, KConstants.TableIndices[6]),
                        new KHashLink(thisRecord, KConstants.TableIndices[7]),
                        new KHashLink(thisRecord, KConstants.TableIndices[8])
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
                int port = Integer.valueOf(KlobsApp.lang.getProperty("UDPPort", "3305")).intValue();
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
        JFrame mainFrame = KlobsApp.getApplication().getMainFrame();
        KDateDialog dateDialog = new KDateDialog(
                mainFrame,
                KlobsApp.lang.getProperty("DateFormat", "MM/dd/yyyy"),
                KlobsApp.lang.getProperty("TimeFormat", "hh:mm"),
                KlobsApp.actDate,
                KlobsApp.actStartTime,
                KlobsApp.actEndTime);
        KlobsApp.actDate = dateDialog.date;
        KlobsApp.actDateString = dateDialog.dateString;
        KlobsApp.actStartTime = dateDialog.startTime;
        KlobsApp.actStartTimeString = dateDialog.startTimeString;
        KlobsApp.actEndTime = dateDialog.endTime;
        KlobsApp.actEndTimeString = dateDialog.endTimeString;
        KlobsApp.actLocation = dateDialog.location;
        KlobsApp.actLocationId = ((KStringHash) dateDialog.locationHash).get(KConstants.LocIdName);

        setDateTitle();
    }

    public void setDateTitle() {
        this.getFrame().setTitle(KlobsApp.actDateString + " - " + KlobsApp.actLocation + " - " + KlobsApp.actLocationId + " - " + KlobsApp.actStartTimeString + " - " + KlobsApp.actEndTimeString + " - " + KConstants.AppName);
    }

    public void tableChanged(TableModelEvent e) {
        /* now it becomes tricky: As changing of the cell destroys the previous reference to the KHashLink by a simple String,
         * I've to restore this information through the cell beside
         */

        int row = e.getFirstRow();
        int column = e.getColumn();
        if (row < 0 || column < 0) {
            return;
        }
        // I also need a valid colcount from left or right beside the changed cell
        int refreshCol = (column > 0) ? 0 : 1;
        // then we read the new value of the cell
        TableModel model = (TableModel) e.getSource();
        if (!(model.getValueAt(row, column) instanceof String)) {
            return;
        }
        String newValueString = "";
        try {
            newValueString = (String) model.getValueAt(row, column);
        } catch (java.lang.Error ignored) {
        }
        ;
        // now we read the original data from the cell beside
        KHashLink actHashLink = null;
        try {
            actHashLink = (KHashLink) jTableMembers.getModel().getValueAt(row, refreshCol);
        } catch (java.lang.Error ignored) {
        }
        ;
        if (actHashLink != null) {
            KStringHash thisRecord = actHashLink.getHashMap();
            if (thisRecord != null) {
                thisRecord.put(KConstants.TableIndices[column], newValueString);
                thisRecord.put(KConstants.MemModKey, KConstants.TrueValue);
                model.setValueAt(new KHashLink(thisRecord, KConstants.TableIndices[column]), row, column);

            }
        }
    }

    @Action
    public void toggleAttendie() {

        KHashLink actHashLink = null;
        int[] rowIndices = jTableMembers.getSelectedRows();
        for (int row : rowIndices) {
            // get actual Member
            try {
                actHashLink = (KHashLink) jTableMembers.getModel().getValueAt(jTableMembers.getRowSorter().convertRowIndexToModel(row), 0);
            } catch (java.lang.Error e) {
            }
            // valid selection?
            if (actHashLink != null) {
                KStringHash thisRecord = actHashLink.getHashMap();
                if (thisRecord != null) {
                    String onsideValue = thisRecord.get(KConstants.MemOnside);
                    if (onsideValue != null && onsideValue.compareTo(KConstants.TrueValue) == 0) {
                        thisRecord.put(KConstants.MemOnside, KConstants.FalseValue);
                    } else {
                        thisRecord.put(KConstants.MemOnside, KConstants.TrueValue);
                    }
                }
                jTableMembers.invalidate();
                jTableMembers.validate();
                jTableMembers.repaint();
            }
        }
    }

    @Action
    public void doTimeViewAction(ActionEvent evt) {
        String command = evt.getActionCommand();

        DefaultMutableTreeNode node = (DefaultMutableTreeNode) timeTreeView.getLastSelectedPathComponent();


        if (node != null) {




            // Compare the action command to the known actions.

            if (command.equals(addTimeButton.getActionCommand())) {
                System.out.println("addTime");
                DefaultMutableTreeNode childNode = new DefaultMutableTreeNode(new KTimePlanNode(1));
                System.out.println(node.getChildCount());
                node.add(childNode);
                ((DefaultTreeModel) timeTreeView.getModel()).reload(node);

                //timeTreeView.scrollPathToVisible(new TreePath(childNode.getPath()));
            }

            if (command.equals(splitTimeButton.getActionCommand())) {
                System.out.println("splitTime");

            }

            if (command.equals(addTaskButton.getActionCommand())) {
                System.out.println("addTask");

            }

            if (command.equals(deleteNodeButton.getActionCommand())) {
                System.out.println("deleteNode");
                TreePath currentSelection = timeTreeView.getSelectionPath();
                if (currentSelection != null) {
                    DefaultMutableTreeNode currentNode = (DefaultMutableTreeNode) (currentSelection.getLastPathComponent());
                    MutableTreeNode parent = (MutableTreeNode) (currentNode.getParent());
                    if (parent != null) {
                        ((DefaultTreeModel) timeTreeView.getModel()).removeNodeFromParent(currentNode);
                        return;
                    }
                }


            }
        }


    }
    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton addTaskButton;
    private javax.swing.JButton addTimeButton;
    private javax.swing.JLabel attendieLabel;
    private javax.swing.JButton deleteNodeButton;
    private javax.swing.JButton jButtonAdd;
    private javax.swing.JLabel jLabel1;
    private javax.swing.JLabel jLabel2;
    private javax.swing.JLabel jLabel3;
    private javax.swing.JMenuItem jMenuItem1;
    private javax.swing.JMenuItem jMenuItemDate;
    private javax.swing.JSeparator jSeparator1;
    private javax.swing.JTable jTableMembers;
    private javax.swing.JPanel mainPanel;
    private javax.swing.JTabbedPane mainTabbedPane;
    private javax.swing.JToolBar mainToolBar;
    private javax.swing.JPanel memberPanel;
    private javax.swing.JScrollPane memberScrollPane;
    private javax.swing.JToolBar memberToolBar;
    private javax.swing.JMenuBar menuBar;
    private javax.swing.JButton moveInButton;
    private javax.swing.JButton moveOutButton;
    private javax.swing.JScrollPane onsideAllScrollPane;
    private javax.swing.JTree onsideAllTree;
    private javax.swing.JLabel onsideLabel;
    private javax.swing.JPanel onsidePanel;
    private javax.swing.JScrollPane onsideSelectedScrollPane;
    private javax.swing.JTree onsideSelectedTree;
    private javax.swing.JProgressBar progressBar;
    private javax.swing.JButton splitTimeButton;
    private javax.swing.JLabel statusAnimationLabel;
    private javax.swing.JLabel statusMessageLabel;
    private javax.swing.JPanel statusPanel;
    private javax.swing.JComboBox subtaskComboBox;
    private javax.swing.JComboBox taskComboBox;
    private javax.swing.JToolBar timeBottomToolBar;
    private javax.swing.JPanel timeCanvasPanel;
    private javax.swing.JComboBox timeComboBox;
    private javax.swing.JPanel timePanel;
    private javax.swing.JScrollPane timeScrollPane;
    private javax.swing.JSplitPane timeSplitPane;
    private javax.swing.JToolBar timeTopToolBar;
    private javax.swing.JTree timeTreeView;
    private javax.swing.JComboBox trainerComboBox;
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
