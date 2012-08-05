all: QRCodeReader.jar

QRCodeReader.jar: QRCodeReader.class
	jar -cvmf MANIFEST.MF QRCodeReader.jar *.class 

QRCodeReader.class: QRCodeReader.java
	javac  QRCodeReader.java


clean:
	rm QRCodeReader.jar QRCodeReader*.class

run:
	java -jar QRCodeReader.jar

