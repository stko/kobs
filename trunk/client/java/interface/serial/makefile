
all: ttyReader.jar

ttyReader.jar: ttyReader.java lib/comm.jar lib/javax.comm.properties lib/libLinuxSerialParallel.so
	javac -extdirs lib/ ttyReader.java
	jar cmf Manifest.txt  ttyReader.jar lib gnu librxtxSerial.so rxtxSerial.dll *.class *.props *.lang
