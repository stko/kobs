import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.net.*;
import java.io.*;
import java.io.IOException;
import javax.comm.*;
import java.util.*;
import javax.swing.Timer;

public class ttyReader implements  SerialPortEventListener {
	JFrame 				frame;
	JPanel 				panel;
	JLabel 				label;
	public static CommPortIdentifier	portId;
	public static Enumeration		portList;
	static InputStream			inputStream;
	static SerialPort			serialPort;
	static String 				serPortName;
	static String				hostPort;
	static String				hostName;
	boolean 				portFound = false;
	String 				outputString = "";
	static Properties			lang;
	Timer					timer;

	public static void main(String[] args) {
		Properties props = new Properties();
		try {
			props.load(new FileInputStream("kobsserial.props"));
		}
		catch (IOException ignored) {}
		lang = new Properties();
		try {
			lang.load(new FileInputStream("kobsserial.lang"));
		}
		catch (IOException ignored) {}
		boolean portFound = false;
		serPortName = props.getProperty("SerialPort","/dev/ttyUSB0");
		hostPort = props.getProperty("HostPort","3305");
		hostName = props.getProperty("HostName","127.0.0.1");
		portList = CommPortIdentifier.getPortIdentifiers();
		while (portList.hasMoreElements()&& !portFound) {
			portId = (CommPortIdentifier) portList.nextElement();
			if (portId.getPortType() == CommPortIdentifier.PORT_SERIAL) {
				if (portId.getName().equals(serPortName)) {
					portFound = true;
				} 
			} 
		} 
		if (!portFound) {
			JOptionPane.showMessageDialog(null,lang.getProperty("PortErrorText","Couldn't open serial Port"),lang.getProperty("PortErrorTitle","Port Initalisation Error"),JOptionPane.ERROR_MESSAGE);
		} 
		else {
			try {
				ttyReader u = new ttyReader( (SerialPort) portId.open("KobsReader", 2000));
			} 
			catch (PortInUseException e) {} 
		}
	}

	public ttyReader(SerialPort serialPort){

		
		frame = new JFrame(lang.getProperty("WindowTitle","Kobs Card Reader"));
		frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		frame.setUndecorated(true);
		frame.getRootPane().setWindowDecorationStyle(JRootPane.PLAIN_DIALOG);
		panel = new JPanel();
		panel.setLayout(new GridLayout(4,2));
		panel.add(new JLabel(lang.getProperty("HostName","Host Name")+": "));
		panel.add(new JLabel(hostName));
		panel.add(new JLabel(lang.getProperty("HostPort","Host Port")+": "));
		panel.add(new JLabel(hostPort));
		panel.add(new JLabel(lang.getProperty("serPort","Serial Port")+": "));
		panel.add(new JLabel(serPortName));
		panel.add(new JLabel(lang.getProperty("lastCard","last Card")+": "));
		label = new JLabel("-");
		panel.add(label);
		frame.add(panel);
		frame.setSize(300, 100);
		frame.setVisible(true);
		try {
			inputStream = serialPort.getInputStream();
		} catch (IOException e) {}
		timer = new Timer(1000,taskPerformer);
		timer.stop();
		try {
			serialPort.addEventListener(this);
		} catch (TooManyListenersException e) {}
		serialPort.notifyOnDataAvailable(true);
		System.out.println("timer start.");
//		timer.timerStart();
		try {
			serialPort.setSerialPortParams(9600, SerialPort.DATABITS_8, 
						SerialPort.STOPBITS_1, 
						SerialPort.PARITY_NONE);
		}
		catch (UnsupportedCommOperationException e){}
		//catch (java.io.IOException e){}

	}

	  ActionListener taskPerformer = new ActionListener() {
      public void actionPerformed(ActionEvent evt) {
			label.setText("-");
			System.out.println("tick....");	
			timer.stop();		
		}
	};

	public class SendRequest{
		SendRequest(String mess){
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
				//For Received message
/*				packet = new DatagramPacket(message, message.length);
				socket.receive(packet);
				String recmessage = new String(packet.getData());
				area.append("Received from server: " + recmessage);
*/				socket.close();
			}
			catch(IOException io){}
		}
	}
   /**
     * Method declaration
     *
     *
     * @param event
     *
     * @see
     */
	public void serialEvent(SerialPortEvent event) {
		switch (event.getEventType()) {
		case SerialPortEvent.BI:
		case SerialPortEvent.OE:
		case SerialPortEvent.FE:
		case SerialPortEvent.PE:
		case SerialPortEvent.CD:
		case SerialPortEvent.CTS:
		case SerialPortEvent.DSR:
		case SerialPortEvent.RI:
		case SerialPortEvent.OUTPUT_BUFFER_EMPTY:
			break;
		case SerialPortEvent.DATA_AVAILABLE:
			try {
				while (inputStream.available() > 0) {
					int inChar = inputStream.read();
					if (inChar >31) {
						outputString +=(char)inChar;
					}
					if (inChar==10 && outputString.length()>0) {
						new SendRequest(outputString);
						outputString="";
					}
				} 
			} catch (IOException e) {}
		
			break;
		}
	} 
}