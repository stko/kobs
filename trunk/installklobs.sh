mkdir -p ~/bin/klobs
cp -r * ~/bin/klobs
chmod +x ~/bin/klobs/*.sh
if test -d ~/Desktop
then
      cp Klobs.desktop ~/Desktop
fi
echo Installation successfully done

