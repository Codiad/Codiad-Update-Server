#!/bin/sh

cd $1
git checkout upgrade.sh

git pull https://github.com/Codiad/Codiad-Update-Server.git
chown $2:$2 -R $1
chmod 744 data/
chmod 744 upgrade.sh
