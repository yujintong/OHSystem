sudo apt-get install -qq libboost-all-dev build-essential gcc g++ libmysql++-dev libbz2-dev libgmp3-dev
cd src/bncsutil/src/bncsutil
make
sudo make install
cd ../../../StormLib/stormlib
make
sudo make install
cd ../../
make
cd ../
mkdir bot
cp src/ghost++ bot/
cp config/* bot -R
cp plugins bot/ -R 
