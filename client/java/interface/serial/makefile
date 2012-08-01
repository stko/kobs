all: ttyReader.jar

ttyReader.jar: ttyReader.class
	jar -cvmf MANIFEST.MF ttyReader.jar *.class lib

ttyReader.class: ttyReader.java
	javac -extdirs lib/ ttyReader.java




run:
	java -jar ttyReader.jar

