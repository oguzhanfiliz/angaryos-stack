#nvm install v14

cp -rf ./../frontend/src/ ./src/

sed -i 's/import { AngularFireMessagingModule }/\/\/import { AngularFireMessagingModule }/g' ./src/aero/aero.module.ts
sed -i 's/import { AngularFireDatabaseModule }/\/\/import { AngularFireDatabaseModule }/g' ./src/aero/aero.module.ts
sed -i 's/import { AngularFireAuthModule }/\/\/import { AngularFireAuthModule }/g' ./src/aero/aero.module.ts
sed -i 's/import { AngularFireModule }/\/\/import { AngularFireModule }/g' ./src/aero/aero.module.ts

sed -i 's/AngularFireMessagingModule,/\/\/AngularFireMessagingModule,/g' ./src/aero/aero.module.ts
sed -i 's/AngularFireDatabaseModule,/\/\/AngularFireDatabaseModule,/g' ./src/aero/aero.module.ts
sed -i 's/AngularFireAuthModule,/\/\/AngularFireAuthModule,/g' ./src/aero/aero.module.ts
sed -i 's/AngularFireModule./\/\/AngularFireModule./g' ./src/aero/aero.module.ts






ionic build
#npx cap sync android
#npx cap open android