#!/bin/sh
cd $(dirname $0)
#java -cp lib/RXTXcomm.jar:. -Djava.library.path=lib/ ttyReader $1 &
java -cp lib/comm.jar:. -Djava.library.path=lib/ ttyReader $1 &
export pid=$!
java -jar Klobs.jar
kill $pid
