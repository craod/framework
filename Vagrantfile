# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.network "private_network", ip: "192.168.100.100"
    config.vm.provider "virtualbox" do |v|
      v.memory = 2048
      v.name = "api.craod.com"
    end
  config.vm.provision :shell, path: "Configuration/Vagrant/bootstrap.sh"
end