cd /localDisk/kamu/mobil

cp ./android/app/build/outputs/apk/release/my-release-key.keystore ./my-release-key.keystore

rm -rf /localDisk/kamu/mobil/android

ionic build
npx cap add android
npx cap copy android
npx cap sync android

cd android
export ANDROID_HOME=/usr/lib/android-sdk
export PATH=$ANDROID_HOME/cmdline-tools/tools/bin:$PATH
./gradlew assembleRelease

cp ./../my-release-key.keystore ./app/build/outputs/apk/release/my-release-key.keystore
cd app/build/outputs/apk/release

#kozidbim.2643
#keytool -genkey -v -keystore my-release-key.keystore -alias alias_name -keyalg RSA -keysize 2048 -validity 10000

echo "kozidbim.2643" | jarsigner -verbose -sigalg SHA1withRSA -digestalg SHA1 -keystore my-release-key.keystore app-release-unsigned.apk alias_name