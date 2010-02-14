import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.net.*;
import java.io.*;
import java.io.IOException;
import java.util.*;
import javax.swing.Timer;
import javax.comm.*;// for SUN's serial/parallel port libraries
//import gnu.io.*; // for rxtxSerial library

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
	boolean					rawMode = false;
	int					rawModeTrigger = 20; //Number of raw input chars allowed before program jumps into raw mode
	long					actTime;
	long					lastTime;
	long					actDelta;
	
	public static void main(String[] args) {
		Properties props = new Properties();
		try {
			props.load(new FileInputStream("klobsserial.props"));
		}
		catch (IOException ignored) {}
		lang = new Properties();
		try {
			lang.load(new FileInputStream("klobsserial.lang"));
		}
		catch (IOException ignored) {}
		String osname = System.getProperty("os.name","").toLowerCase();
		if ( osname.startsWith("windows") ) {
			// windows
			serPortName = "COM1";
		} else if (osname.startsWith("linux")) {
			// linux
		serPortName = "/dev/ttyUSB0";
		} else if ( osname.startsWith("mac") ) {
			// mac
			serPortName = "????";
		} else {
			System.out.println("Sorry, your operating system is not supported");
			return;
		}
			
		if (args.length > 0) {
			serPortName = args[0];
		} else {
			serPortName = props.getProperty("SerialPort",serPortName);
		}


		boolean portFound = false;
		hostPort = props.getProperty("HostPort","3305");
		hostName = props.getProperty("HostName","127.0.0.1");
		portList = CommPortIdentifier.getPortIdentifiers();
		while (portList.hasMoreElements()&& !portFound) {
			portId = (CommPortIdentifier) portList.nextElement();
			if (portId.getPortType() == CommPortIdentifier.PORT_SERIAL) {
				System.out.println("port found"+portId.getName()+"\n");
				if (portId.getName().equals(serPortName)) {
					portFound = true;
				} 
			} 
		} 
		if (!portFound) {
			JOptionPane.showMessageDialog(null,lang.getProperty("PortErrorText","Couldn't open serial Port")+"\n"+serPortName,lang.getProperty("PortErrorTitle","Port Initalisation Error"),JOptionPane.ERROR_MESSAGE);
		} 
		else {
			try {
				ttyReader u = new ttyReader( (SerialPort) portId.open("KlobsReader", 2000));
			} 
			catch (PortInUseException e) {} 
		}
	}

	public ttyReader(SerialPort serialPort){

		
		frame = new JFrame(lang.getProperty("WindowTitle","Klobs Card Reader"));
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
			timer.stop();		
		}
	};

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
					if(inChar > -1){
						actTime=System.currentTimeMillis();
						actDelta=actTime -lastTime;
						lastTime=actTime;
						if (actDelta>100){ //last byte > 100 ms ago, so clear input buffer
							outputString="";
						}
						if (rawMode){
							outputString+=String.format("%1$02X",inChar);
							if (outputString.length()>9){ //assuming a card-iD is 40bits = 10 Hex chars long
								new SendRequest(outputString);
								outputString="";
							}
						} else {
							if("ABCDEFabcdef01234567890\n\r".indexOf((char)inChar)>-1){
								if (inChar >31) {
									outputString +=(char)inChar;
								}
								if (inChar==10){
									if(outputString.length()>9) {
									new SendRequest(outputString);
									}
									outputString="";
								}
							} else { //looks like a rawmode byte
								if (rawModeTrigger > 0){
									rawModeTrigger--;
								} else {
									rawMode=true;
								}
							}
						}
					}
				} 
			} catch (IOException e) {}
		
			break;
		}
	} 
}