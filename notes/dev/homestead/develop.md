```
# 下载box
http://download.xxx.com/develop-0.0.3.box

# 添加box
vagrant box add --name develop-0.0.3 ~/data/downloads/develop-0.0.3.box

# 2.0分支对应php5 3.0开始是php7
git clone https://github.com/laravel/homestead.git -b 2.0 ~/data/vagrant/develop

# 如果共享文件夹挂载失败则需要安装一个插件
vagrant plugin install vagrant-vbguest

# 进入目录并启动
cd ~/data/vagrant/develop
bash init.sh
vagrant up

# ssh进入
vagrant ssh

# 添加redis扩展
wget https://pecl.php.net/get/redis-2.2.8.tgz
tar zxf redis-2.2.8.tgz
cd redis-2.2.8/
phpize
./configure
make
sudo make install
cd /etc/php5/mods-available/
# 新建redis.ini加上一句extension=redis.so
echo 'extension=redis.so' > redis.ini
cd /etc/php5/fpm/conf.d
sudo ln -s ../../mods-available/redis.ini 20-redis.ini
cd /etc/php5/cli/conf.d
sudo ln -s ../../mods-available/redis.ini 20-redis.ini

```

