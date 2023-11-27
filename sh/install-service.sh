#!/bin/bash -x

#get path of this script
WD="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"

systemctl stop dispatcher
systemctl stop xampp

#copy service file from service folder to /etc/systemd/system folder
cp $WD/../service/dispatcher.service /etc/systemd/system/.
cp $WD/../service/xampp.service /etc/systemd/system/.

systemctl disable dispatcher
systemctl disable xampp

systemctl enable dispatcher
systemctl enable xampp

systemctl start dispatcher
systemctl start xampp

