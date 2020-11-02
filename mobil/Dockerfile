FROM node

RUN mkdir /usr/src/app 
WORKDIR /usr/src/app

COPY . /usr/src/app/

RUN npm install -g @ionic/cli

CMD ["./start.sh"]
