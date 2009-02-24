
all: ttyReader.jar

ttyReader.jar: ttyReader.java lib/comm.jar lib/javax.comm.properties lib/libLinuxSerialParallel.so
	javac -extdirs lib/ ttyReader.java
	jar cmf mainClass.txt  ttyReader.jar lib *.class -J"cp lib/comm.jar:. -Djava.library.path=lib/"