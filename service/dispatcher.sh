#!/bin/bash

filePath="$(dirname "$0")"
filePath="$(realpath "$filePath")"

java -jar "$filePath/dispatcher.jar" "$filePath/dispatcher.conf"
