# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
config.vm.box = "debian/jessie64"
config.vm.network "private_network", ip: "192.168.33.10"

config.vm.provision "shell", inline: <<-SHELL
    sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password az'
    sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password az'    
    sudo apt-get update
    sudo apt-get install -y nginx mysql-server php5-fpm php5-mysql php5-curl
    sudo rm /etc/nginx/sites-enabled/*
    sudo ln -s /vagrant/config/rainbowmondays.co.nz /etc/nginx/sites-enabled/rainbowmondays.conf
    mysql -paz -u root < /vagrant/config/schema.sql
    sudo nginx -s reload
    php5 /vagrant/api/setup/downloadDistricts.php
    php5 /vagrant/api/cron/downloadJobs.php
SHELL
end
