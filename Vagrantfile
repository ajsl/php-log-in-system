# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    # /*=====================================
    # =            FREE VERSION!            =
    # =====================================*/
    # This is the free (still awesome) version of Scotch Box.
    # Please go Pro to support the project and get more features.
    # Check out https://box.scotch.io to learn more. Thanks

    config.vm.box = "scotch/box"
    config.vm.network "private_network", ip: "192.168.33.10"
    config.vm.hostname = "scotchbox"
    config.vm.synced_folder ".", "/var/www", :mount_options => ["dmode=777", "fmode=666"]

    # Optional NFS. Make sure to remove other synced_folder line too
    #config.vm.synced_folder ".", "/var/www", :nfs => { :mount_options => ["dmode=777","fmode=666"] }

    # run some tasks while setting up box for the first time
    config.vm.provision "once", type: "shell" do |once|
        once.inline = <<SCRIPT
echo Fixing some problems with scotchbox...

echo update php settings
echo 'upload_max_filesize = 100M' >> /etc/php/7.0/apache2/conf.d/user.ini
echo 'post_max_size = 100M' >> /etc/php/7.0/apache2/conf.d/user.ini
echo 'max_execution_time = 120' >> /etc/php/7.0/apache2/conf.d/user.ini
echo 'memory_limit = 256M' >> /etc/php/7.0/apache2/conf.d/user.ini

echo Restart apache
sudo service apache2 restart
SCRIPT
    end
end
