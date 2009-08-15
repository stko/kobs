all: ttyReader.class

ttyReader.class: ttyReader.java
	javac -extdirs lib/ ttyReader.java


run:
	java -cp lib/comm.jar:. -Djava.library.path=lib/ ttyReader

jar: ttyReader.jar

ttyReader.jar: ttyReader.class lib/comm.jar lib/javax.comm.properties lib/libLinuxSerialParallel.so
	jar cmf Manifest.txt  ttyReader.jar lib gnu librxtxSerial.so rxtxSerial.dll *.class *.props *.lang
