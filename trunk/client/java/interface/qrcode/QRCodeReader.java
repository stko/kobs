import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.net.*;
import java.io.*;
import java.io.IOException;
import java.util.*;
import javax.swing.Timer;

public class QRCodeReader  {
	static JFrame  frame;
	static JPanel panel;
	static JLabel label;
	static InputStream inputStream;
	static String hostPort;
	static String hostName;
	static String outputString = "";
	static Properties lang;
	static Timer timer;
	static InputThread inputThread;
	
	public static void main(String[] args) {
		Properties props = new Properties();
		try {
			props.load(new FileInputStream("QRCodeReader.props"));
		}
		catch (IOException ignored) {}
		lang = new Properties();
		try {
			lang.load(new FileInputStream("QRCodeReader.lang"));
		}
		catch (IOException ignored) {}
		hostPort = props.getProperty("HostPort","3305");
		hostName = props.getProperty("HostName","127.0.0.1");
		//Schedule a job for the event-dispatching thread:
		//creating and showing this application's GUI.
		javax.swing.SwingUtilities.invokeLater(new Runnable() {
			public void run() {
				QRCodeReader u = new QRCodeReader();
			}
		});
	}

	public QRCodeReader(){

		/**
		* Create the GUI and show it.  For thread safety,
		* this method should be invoked from the
		* event-dispatching thread.
		*/
		frame = new JFrame(lang.getProperty("WindowTitle","Klobs QRCode Reader"));
		frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		frame.setUndecorated(true);
		frame.getRootPane().setWindowDecorationStyle(JRootPane.PLAIN_DIALOG);
		panel = new JPanel();
		panel.setLayout(new GridLayout(3,2));
		panel.add(new JLabel(lang.getProperty("HostName","Host Name")+": "));
		panel.add(new JLabel(hostName));
		panel.add(new JLabel(lang.getProperty("HostPort","Host Port")+": "));
		panel.add(new JLabel(hostPort));
		panel.add(new JLabel(lang.getProperty("lastCard","last Card")+": "));
		label = new JLabel("-");
		panel.add(label);
		frame.add(panel);
		frame.setSize(300, 100);
		frame.setVisible(true);
		timer = new Timer(1000,new ActionListener() {
			public void actionPerformed(ActionEvent evt) {
					  label.setText("-");
					  timer.stop();		
				  }
			  });
		timer.stop();
		inputThread = new InputThread();
		inputThread.start();
 	}

	public class SendRequest{
		SendRequest(String mess){
			if(!timer.isRunning()){//if a message was not just sent
				try{
					DatagramSocket socket;
					DatagramPacket packet;
					InetAddress address;
					socket = new DatagramSocket();
					address = InetAddress.getByName(hostName);
					int pnum = Integer.parseInt(hostPort);
					//For send the message by the client
					byte message[] = mess.getBytes();
					packet = new DatagramPacket(message, message.length, address, pnum);
					socket.send(packet);
					label.setText(mess);
					timer.restart();
					socket.close();
				}
				catch(IOException io){}
			}
		}
	}

	public class InputThread extends Thread {
		public void run() {
			try {
				BufferedReader stdin = new BufferedReader(new InputStreamReader(System.in));
				String thisLine;
				while ((thisLine = stdin.readLine()) != null) { // while loop begins here
					thisLine=thisLine.trim().toUpperCase().replace("QR-CODE:","");
					if (thisLine.matches("^KLOBS[a-fA-F0-9]+")){
						thisLine=thisLine.replace("KLOBS","");
						new SendRequest("QR:"+thisLine);
					}else{
						System.out.println("Error: Wrong formated QR-Code-String: "+thisLine);
					}
				} // end while 
		                 //Nothing more to read? finish program..
				System.exit(0);
			} // end try
			catch (IOException e) {
				 System.err.println("Error: " + e);
			}
		}
	}
}