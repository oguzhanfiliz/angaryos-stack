cp ./android/app/build/outputs/apk/release/my-release-key.keystore ./my-release-key.keystore

cd /localDisk/kamu/mobil/android
export ANDROID_HOME=/usr/lib/android-sdk
export PATH=$ANDROID_HOME/cmdline-tools/tools/bin:$PATH
./gradlew assembleRelease

cp ./../my-release-key.keystore ./app/build/outputs/apk/release/my-release-key.keystore
cd app/build/outputs/apk/release

echo "xxxxxxxxxx" | jarsigner -verbose -sigalg SHA1withRSA -digestalg SHA1 -keystore my-release-key.keystore app-release-unsigned.apk alias_name
zipalign -f -v 4 app-release-unsigned.apk app-release.apk


#version
#mobil/android/app/build.gradle
#mobil/package.json ???bu olmayabilir???