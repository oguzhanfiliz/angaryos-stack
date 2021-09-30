# cordova-plugin-mock-gps-checker

This is a cordova plugin to avoid mock locations

This plugin get mock location in Android api < 18 AND api >= 18

Plugin works in Ionic

## Supported Platforms

- Android API all versions

## Installation

Cordova local build:
    cordova plugin add <GIT URL PATH>




## Usage in javascript

```js
document.addEventListener("deviceready", onDeviceReady, false);

function onDeviceReady() {
  window.plugins.mockgpschecker.check(successCallback, errorCallback);
}

function successCallback(result) {
  console.log(result); // true - enabled, false - disabled
}

function errorCallback(error) {
  console.log(error);
}
```

## Usage in typescript

```ts

  (<any>window).plugins.mockgpschecker.check((a) => this.successCallback(a), (b) => this.errorCallback(b));


  successCallback(result) {
    console.log(result); // true - enabled, false - disabled
  }

  errorCallback(error) {
    console.log(error);
  }

```
