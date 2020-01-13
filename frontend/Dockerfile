FROM node

RUN mkdir /usr/src/app 
WORKDIR /usr/src/app

COPY . /usr/src/app/

RUN npm install -g @angular/cli

CMD ["./start.sh"]