#!/bin/bash -x

filePath="$(dirname "$0")"
filePath="$(realpath "$filePath")"

cd $filePath
java -jar dispatcher.jar dispatcher.conf
