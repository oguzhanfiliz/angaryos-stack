FROM node

RUN mkdir /usr/src/app 
WORKDIR /usr/src/app

COPY ./package.json /usr/src/app/package.json

RUN npm install
RUN npm install -g @angular/cli

COPY ./start.sh /usr/src/app/start.sh
CMD ["./start.sh"]