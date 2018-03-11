#!/bin/sh
cd $(dirname $0)
zbarcam | java -jar QRCodeReader.jar
