FROM node:14.17.6

#RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.35.3/install.sh | bash
#RUN source ~/.nvm/nvm.sh
#RUN nvm install v14
RUN npm install -g @ionic/cli@6.17.1

RUN mkdir /usr/src/app 
WORKDIR /usr/src/app

CMD ["./start.sh"]