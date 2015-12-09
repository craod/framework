#!/bin/bash

echo ""
echo "Configuring environment"

if [[ ! -z $(grep "#force_color_prompt=yes" /home/vagrant/.bashrc) ]];
then
echo "Enabling color prompt"
perl -pi -e 's/\#force_color_prompt\=yes/force_color_prompt\=yes/g' /home/vagrant/.bashrc
fi

if [[ -z $(grep "cd /var/www" /home/vagrant/.bashrc) ]];
then
echo "Changing default directory"
echo "cd /var/www" >> /home/vagrant/.bashrc
fi