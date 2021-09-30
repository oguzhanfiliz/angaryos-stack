'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

var tslib = require('tslib');
var core$1 = require('@angular/core');
var core = require('@ionic-native/core');

var NativeAudio = /** @class */ (function (_super) {
    tslib.__extends(NativeAudio, _super);
    function NativeAudio() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    NativeAudio.prototype.preloadSimple = function (id, assetPath) { return core.cordova(this, "preloadSimple", {}, arguments); };
    NativeAudio.prototype.preloadComplex = function (id, assetPath, volume, voices, delay) { return core.cordova(this, "preloadComplex", {}, arguments); };
    NativeAudio.prototype.play = function (id, completeCallback) { return core.cordova(this, "play", { "successIndex": 1, "errorIndex": 2 }, arguments); };
    NativeAudio.prototype.stop = function (id) { return core.cordova(this, "stop", {}, arguments); };
    NativeAudio.prototype.loop = function (id) { return core.cordova(this, "loop", {}, arguments); };
    NativeAudio.prototype.unload = function (id) { return core.cordova(this, "unload", {}, arguments); };
    NativeAudio.prototype.setVolumeForComplexAsset = function (id, volume) { return core.cordova(this, "setVolumeForComplexAsset", {}, arguments); };
    NativeAudio.pluginName = "NativeAudio";
    NativeAudio.plugin = "cordova-plugin-nativeaudio";
    NativeAudio.pluginRef = "plugins.NativeAudio";
    NativeAudio.repo = "https://github.com/floatinghotpot/cordova-plugin-nativeaudio";
    NativeAudio.platforms = ["Android", "Browser", "iOS"];
    NativeAudio.decorators = [
        { type: core$1.Injectable }
    ];
    return NativeAudio;
}(core.IonicNativePlugin));

exports.NativeAudio = NativeAudio;
