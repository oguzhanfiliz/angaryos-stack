#curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.35.3/install.sh | bash
#nvm install v12.8.0

ng build --optimization=false --output-hashing=none
chmod 777 -R ./dist
mv ./dist/light/index.aero.html ./dist/light/index.html
sed -i 's/<\/head>/<link rel="stylesheet" href="assets\/themes\/aero\/assets\/css\/style.min.css"><\/head>/g' ./dist/light/index.html