curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.35.3/install.sh | bash
nvm install v12.8.0
ng build -prod --optimization=false
mv ./dist/light/index.aero.html ./dist/light/index.html

#<link rel="stylesheet" href="https://192.168.10.185/assets/themes/aero/assets/css/style.min.css"></head>
