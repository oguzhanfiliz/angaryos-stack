var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
import { IonicNativePlugin, cordova, cordovaPropertyGet, cordovaPropertySet } from '@ionic-native/core';
import { Observable } from 'rxjs';
var NFCOriginal = /** @class */ (function (_super) {
    __extends(NFCOriginal, _super);
    function NFCOriginal() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    NFCOriginal.prototype.readerMode = function (flags) { return cordova(this, "readerMode", { "observable": true, "clearFunction": "disableReaderMode", "clearWithArgs": false }, arguments); };
    NFCOriginal.prototype.scanNdef = function (options) { return cordova(this, "scanNdef", { "sync": true }, arguments); };
    NFCOriginal.prototype.scanTag = function (options) { return cordova(this, "scanTag", { "sync": true }, arguments); };
    NFCOriginal.prototype.cancelScan = function () { return cordova(this, "cancelScan", { "sync": true }, arguments); };
    NFCOriginal.prototype.connect = function (tech, timeout) { return cordova(this, "connect", { "sync": true }, arguments); };
    NFCOriginal.prototype.close = function () { return cordova(this, "close", { "sync": true }, arguments); };
    NFCOriginal.prototype.transceive = function (data) { return cordova(this, "transceive", { "sync": true }, arguments); };
    NFCOriginal.prototype.beginSession = function (onSuccess, onFailure) { return cordova(this, "beginSession", { "observable": true, "successIndex": 0, "errorIndex": 3, "clearFunction": "invalidateSession", "clearWithArgs": true }, arguments); };
    NFCOriginal.prototype.addNdefListener = function (onSuccess, onFailure) { return cordova(this, "addNdefListener", { "observable": true, "successIndex": 0, "errorIndex": 3, "clearFunction": "removeNdefListener", "clearWithArgs": true }, arguments); };
    NFCOriginal.prototype.addTagDiscoveredListener = function (onSuccess, onFailure) { return cordova(this, "addTagDiscoveredListener", { "observable": true, "successIndex": 0, "errorIndex": 3, "clearFunction": "removeTagDiscoveredListener", "clearWithArgs": true }, arguments); };
    NFCOriginal.prototype.addMimeTypeListener = function (mimeType, onSuccess, onFailure) { return cordova(this, "addMimeTypeListener", { "observable": true, "successIndex": 1, "errorIndex": 4, "clearFunction": "removeMimeTypeListener", "clearWithArgs": true }, arguments); };
    NFCOriginal.prototype.addNdefFormatableListener = function (onSuccess, onFailure) { return cordova(this, "addNdefFormatableListener", { "observable": true, "successIndex": 0, "errorIndex": 3 }, arguments); };
    NFCOriginal.prototype.write = function (message) { return cordova(this, "write", {}, arguments); };
    NFCOriginal.prototype.makeReadOnly = function () { return cordova(this, "makeReadOnly", {}, arguments); };
    NFCOriginal.prototype.share = function (message) { return cordova(this, "share", {}, arguments); };
    NFCOriginal.prototype.unshare = function () { return cordova(this, "unshare", {}, arguments); };
    NFCOriginal.prototype.erase = function () { return cordova(this, "erase", {}, arguments); };
    NFCOriginal.prototype.handover = function (uris) { return cordova(this, "handover", {}, arguments); };
    NFCOriginal.prototype.stopHandover = function () { return cordova(this, "stopHandover", {}, arguments); };
    NFCOriginal.prototype.showSettings = function () { return cordova(this, "showSettings", {}, arguments); };
    NFCOriginal.prototype.enabled = function () { return cordova(this, "enabled", {}, arguments); };
    NFCOriginal.prototype.bytesToString = function (bytes) { return cordova(this, "bytesToString", { "sync": true }, arguments); };
    NFCOriginal.prototype.stringToBytes = function (str) { return cordova(this, "stringToBytes", { "sync": true }, arguments); };
    NFCOriginal.prototype.bytesToHexString = function (bytes) { return cordova(this, "bytesToHexString", { "sync": true }, arguments); };
    Object.defineProperty(NFCOriginal.prototype, "FLAG_READER_NFC_A", {
        get: function () { return cordovaPropertyGet(this, "FLAG_READER_NFC_A"); },
        set: function (value) { cordovaPropertySet(this, "FLAG_READER_NFC_A", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NFCOriginal.prototype, "FLAG_READER_NFC_B", {
        get: function () { return cordovaPropertyGet(this, "FLAG_READER_NFC_B"); },
        set: function (value) { cordovaPropertySet(this, "FLAG_READER_NFC_B", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NFCOriginal.prototype, "FLAG_READER_NFC_F", {
        get: function () { return cordovaPropertyGet(this, "FLAG_READER_NFC_F"); },
        set: function (value) { cordovaPropertySet(this, "FLAG_READER_NFC_F", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NFCOriginal.prototype, "FLAG_READER_NFC_V", {
        get: function () { return cordovaPropertyGet(this, "FLAG_READER_NFC_V"); },
        set: function (value) { cordovaPropertySet(this, "FLAG_READER_NFC_V", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NFCOriginal.prototype, "FLAG_READER_NFC_BARCODE", {
        get: function () { return cordovaPropertyGet(this, "FLAG_READER_NFC_BARCODE"); },
        set: function (value) { cordovaPropertySet(this, "FLAG_READER_NFC_BARCODE", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NFCOriginal.prototype, "FLAG_READER_SKIP_NDEF_CHECK", {
        get: function () { return cordovaPropertyGet(this, "FLAG_READER_SKIP_NDEF_CHECK"); },
        set: function (value) { cordovaPropertySet(this, "FLAG_READER_SKIP_NDEF_CHECK", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NFCOriginal.prototype, "FLAG_READER_NO_PLATFORM_SOUNDS", {
        get: function () { return cordovaPropertyGet(this, "FLAG_READER_NO_PLATFORM_SOUNDS"); },
        set: function (value) { cordovaPropertySet(this, "FLAG_READER_NO_PLATFORM_SOUNDS", value); },
        enumerable: false,
        configurable: true
    });
    NFCOriginal.pluginName = "NFC";
    NFCOriginal.plugin = "phonegap-nfc";
    NFCOriginal.pluginRef = "nfc";
    NFCOriginal.repo = "https://github.com/chariotsolutions/phonegap-nfc";
    NFCOriginal.platforms = ["Android", "iOS", "Windows"];
    return NFCOriginal;
}(IonicNativePlugin));
var NFC = new NFCOriginal();
export { NFC };
var NdefOriginal = /** @class */ (function (_super) {
    __extends(NdefOriginal, _super);
    function NdefOriginal() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    NdefOriginal.prototype.record = function (tnf, type, id, payload) { return cordova(this, "record", { "sync": true }, arguments); };
    NdefOriginal.prototype.textRecord = function (text, languageCode, id) { return cordova(this, "textRecord", { "sync": true }, arguments); };
    NdefOriginal.prototype.uriRecord = function (uri, id) { return cordova(this, "uriRecord", { "sync": true }, arguments); };
    NdefOriginal.prototype.absoluteUriRecord = function (uri, payload, id) { return cordova(this, "absoluteUriRecord", { "sync": true }, arguments); };
    NdefOriginal.prototype.mimeMediaRecord = function (mimeType, payload) { return cordova(this, "mimeMediaRecord", { "sync": true }, arguments); };
    NdefOriginal.prototype.smartPoster = function (ndefRecords, id) { return cordova(this, "smartPoster", { "sync": true }, arguments); };
    NdefOriginal.prototype.emptyRecord = function () { return cordova(this, "emptyRecord", { "sync": true }, arguments); };
    NdefOriginal.prototype.androidApplicationRecord = function (packageName) { return cordova(this, "androidApplicationRecord", { "sync": true }, arguments); };
    NdefOriginal.prototype.encodeMessage = function (ndefRecords) { return cordova(this, "encodeMessage", { "sync": true }, arguments); };
    NdefOriginal.prototype.decodeMessage = function (bytes) { return cordova(this, "decodeMessage", { "sync": true }, arguments); };
    NdefOriginal.prototype.decodeTnf = function (tnf_byte) { return cordova(this, "decodeTnf", { "sync": true }, arguments); };
    NdefOriginal.prototype.encodeTnf = function (mb, me, cf, sr, il, tnf) { return cordova(this, "encodeTnf", { "sync": true }, arguments); };
    NdefOriginal.prototype.tnfToString = function (tnf) { return cordova(this, "tnfToString", { "sync": true }, arguments); };
    Object.defineProperty(NdefOriginal.prototype, "TNF_EMPTY", {
        get: function () { return cordovaPropertyGet(this, "TNF_EMPTY"); },
        set: function (value) { cordovaPropertySet(this, "TNF_EMPTY", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NdefOriginal.prototype, "TNF_WELL_KNOWN", {
        get: function () { return cordovaPropertyGet(this, "TNF_WELL_KNOWN"); },
        set: function (value) { cordovaPropertySet(this, "TNF_WELL_KNOWN", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NdefOriginal.prototype, "TNF_MIME_MEDIA", {
        get: function () { return cordovaPropertyGet(this, "TNF_MIME_MEDIA"); },
        set: function (value) { cordovaPropertySet(this, "TNF_MIME_MEDIA", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NdefOriginal.prototype, "TNF_ABSOLUTE_URI", {
        get: function () { return cordovaPropertyGet(this, "TNF_ABSOLUTE_URI"); },
        set: function (value) { cordovaPropertySet(this, "TNF_ABSOLUTE_URI", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NdefOriginal.prototype, "TNF_EXTERNAL_TYPE", {
        get: function () { return cordovaPropertyGet(this, "TNF_EXTERNAL_TYPE"); },
        set: function (value) { cordovaPropertySet(this, "TNF_EXTERNAL_TYPE", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NdefOriginal.prototype, "TNF_UNKNOWN", {
        get: function () { return cordovaPropertyGet(this, "TNF_UNKNOWN"); },
        set: function (value) { cordovaPropertySet(this, "TNF_UNKNOWN", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NdefOriginal.prototype, "TNF_UNCHANGED", {
        get: function () { return cordovaPropertyGet(this, "TNF_UNCHANGED"); },
        set: function (value) { cordovaPropertySet(this, "TNF_UNCHANGED", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NdefOriginal.prototype, "TNF_RESERVED", {
        get: function () { return cordovaPropertyGet(this, "TNF_RESERVED"); },
        set: function (value) { cordovaPropertySet(this, "TNF_RESERVED", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NdefOriginal.prototype, "textHelper", {
        get: function () { return cordovaPropertyGet(this, "textHelper"); },
        set: function (value) { cordovaPropertySet(this, "textHelper", value); },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(NdefOriginal.prototype, "uriHelper", {
        get: function () { return cordovaPropertyGet(this, "uriHelper"); },
        set: function (value) { cordovaPropertySet(this, "uriHelper", value); },
        enumerable: false,
        configurable: true
    });
    NdefOriginal.pluginName = "NFC";
    NdefOriginal.plugin = "phonegap-nfc";
    NdefOriginal.pluginRef = "ndef";
    return NdefOriginal;
}(IonicNativePlugin));
var Ndef = new NdefOriginal();
export { Ndef };
var NfcUtilOriginal = /** @class */ (function (_super) {
    __extends(NfcUtilOriginal, _super);
    function NfcUtilOriginal() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    NfcUtilOriginal.prototype.toHex = function (i) { return cordova(this, "toHex", { "sync": true }, arguments); };
    NfcUtilOriginal.prototype.toPrintable = function (i) { return cordova(this, "toPrintable", { "sync": true }, arguments); };
    NfcUtilOriginal.prototype.bytesToString = function (i) { return cordova(this, "bytesToString", { "sync": true }, arguments); };
    NfcUtilOriginal.prototype.stringToBytes = function (s) { return cordova(this, "stringToBytes", { "sync": true }, arguments); };
    NfcUtilOriginal.prototype.bytesToHexString = function (bytes) { return cordova(this, "bytesToHexString", { "sync": true }, arguments); };
    NfcUtilOriginal.prototype.isType = function (record, tnf, type) { return cordova(this, "isType", { "sync": true }, arguments); };
    NfcUtilOriginal.prototype.arrayBufferToHexString = function (buffer) { return cordova(this, "arrayBufferToHexString", { "sync": true }, arguments); };
    NfcUtilOriginal.prototype.hexStringToArrayBuffer = function (hexString) { return cordova(this, "hexStringToArrayBuffer", { "sync": true }, arguments); };
    NfcUtilOriginal.pluginName = "NFC";
    NfcUtilOriginal.plugin = "phonegap-nfc";
    NfcUtilOriginal.pluginRef = "util";
    return NfcUtilOriginal;
}(IonicNativePlugin));
var NfcUtil = new NfcUtilOriginal();
export { NfcUtil };
var TextHelper = /** @class */ (function (_super) {
    __extends(TextHelper, _super);
    function TextHelper() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    TextHelper.prototype.decodePayload = function (data) {
        return;
    };
    TextHelper.prototype.encodePayload = function (text, lang) {
        return;
    };
    return TextHelper;
}(IonicNativePlugin));
export { TextHelper };
var UriHelper = /** @class */ (function (_super) {
    __extends(UriHelper, _super);
    function UriHelper() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    UriHelper.prototype.decodePayload = function (data) {
        return;
    };
    UriHelper.prototype.encodePayload = function (uri) {
        return;
    };
    return UriHelper;
}(IonicNativePlugin));
export { UriHelper };
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5kZXguanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyIuLi8uLi8uLi8uLi9zcmMvQGlvbmljLW5hdGl2ZS9wbHVnaW5zL25mYy9pbmRleC50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7O0FBQ0EsT0FBTyxzRUFBdUQsTUFBTSxvQkFBb0IsQ0FBQztBQUN6RixPQUFPLEVBQUUsVUFBVSxFQUFFLE1BQU0sTUFBTSxDQUFDOztJQW1HVCx1QkFBaUI7Ozs7SUE4QnhDLHdCQUFVLGFBQUMsS0FBYTtJQVN4QixzQkFBUSxhQUFDLE9BQXFCO0lBVzlCLHFCQUFPLGFBQUMsT0FBcUI7SUFTN0Isd0JBQVU7SUFZVixxQkFBTyxhQUFDLElBQVksRUFBRSxPQUFnQjtJQVN0QyxtQkFBSztJQWFMLHdCQUFVLGFBQUMsSUFBMEI7SUFtQnJDLDBCQUFZLGFBQUMsU0FBb0IsRUFBRSxTQUFvQjtJQWlCdkQsNkJBQWUsYUFBQyxTQUFvQixFQUFFLFNBQW9CO0lBaUIxRCxzQ0FBd0IsYUFBQyxTQUFvQixFQUFFLFNBQW9CO0lBa0JuRSxpQ0FBbUIsYUFBQyxRQUFnQixFQUFFLFNBQW9CLEVBQUUsU0FBb0I7SUFlaEYsdUNBQXlCLGFBQUMsU0FBb0IsRUFBRSxTQUFvQjtJQVVwRSxtQkFBSyxhQUFDLE9BQWM7SUFRcEIsMEJBQVk7SUFVWixtQkFBSyxhQUFDLE9BQWM7SUFTcEIscUJBQU87SUFRUCxtQkFBSztJQVVMLHNCQUFRLGFBQUMsSUFBYztJQVN2QiwwQkFBWTtJQVNaLDBCQUFZO0lBU1oscUJBQU87SUFhUCwyQkFBYSxhQUFDLEtBQWU7SUFTN0IsMkJBQWEsYUFBQyxHQUFXO0lBVXpCLDhCQUFnQixhQUFDLEtBQWU7MEJBalNoQyxrQ0FBaUI7Ozs7OzswQkFFakIsa0NBQWlCOzs7Ozs7MEJBRWpCLGtDQUFpQjs7Ozs7OzBCQUVqQixrQ0FBaUI7Ozs7OzswQkFFakIsd0NBQXVCOzs7Ozs7MEJBRXZCLDRDQUEyQjs7Ozs7OzBCQUUzQiwrQ0FBOEI7Ozs7Ozs7Ozs7O2NBckhoQztFQXFHeUIsaUJBQWlCO1NBQTdCLEdBQUc7O0lBeVRVLHdCQUFpQjs7OztJQW1CekMscUJBQU0sYUFBQyxHQUFXLEVBQUUsSUFBdUIsRUFBRSxFQUFxQixFQUFFLE9BQTBCO0lBSzlGLHlCQUFVLGFBQUMsSUFBWSxFQUFFLFlBQXFCLEVBQUUsRUFBc0I7SUFLdEUsd0JBQVMsYUFBQyxHQUFXLEVBQUUsRUFBc0I7SUFLN0MsZ0NBQWlCLGFBQUMsR0FBVyxFQUFFLE9BQTBCLEVBQUUsRUFBc0I7SUFLakYsOEJBQWUsYUFBQyxRQUFnQixFQUFFLE9BQWU7SUFLakQsMEJBQVcsYUFBQyxXQUFrQixFQUFFLEVBQXNCO0lBS3RELDBCQUFXO0lBS1gsdUNBQXdCLGFBQUMsV0FBbUI7SUFLNUMsNEJBQWEsYUFBQyxXQUFnQjtJQUs5Qiw0QkFBYSxhQUFDLEtBQVU7SUFLeEIsd0JBQVMsYUFBQyxRQUFhO0lBS3ZCLHdCQUFTLGFBQUMsRUFBTyxFQUFFLEVBQU8sRUFBRSxFQUFPLEVBQUUsRUFBTyxFQUFFLEVBQU8sRUFBRSxHQUFRO0lBSy9ELDBCQUFXLGFBQUMsR0FBUTswQkE3RXBCLDJCQUFTOzs7Ozs7MEJBRVQsZ0NBQWM7Ozs7OzswQkFFZCxnQ0FBYzs7Ozs7OzBCQUVkLGtDQUFnQjs7Ozs7OzBCQUVoQixtQ0FBaUI7Ozs7OzswQkFFakIsNkJBQVc7Ozs7OzswQkFFWCwrQkFBYTs7Ozs7OzBCQUViLDhCQUFZOzs7Ozs7MEJBb0VaLDRCQUFVOzs7Ozs7MEJBR1YsMkJBQVM7Ozs7Ozs7OztlQXJmWDtFQThaMEIsaUJBQWlCO1NBQTlCLElBQUk7O0lBbUdZLDJCQUFpQjs7OztJQUU1Qyx1QkFBSyxhQUFDLENBQVM7SUFLZiw2QkFBVyxhQUFDLENBQVM7SUFLckIsK0JBQWEsYUFBQyxDQUFXO0lBS3pCLCtCQUFhLGFBQUMsQ0FBUztJQUt2QixrQ0FBZ0IsYUFBQyxLQUFlO0lBS2hDLHdCQUFNLGFBQUMsTUFBa0IsRUFBRSxHQUFXLEVBQUUsSUFBdUI7SUFLL0Qsd0NBQXNCLGFBQUMsTUFBbUI7SUFLMUMsd0NBQXNCLGFBQUMsU0FBaUI7Ozs7a0JBdGlCMUM7RUFpZ0I2QixpQkFBaUI7U0FBakMsT0FBTzs7SUEwQ1ksOEJBQWlCOzs7O0lBQy9DLGtDQUFhLEdBQWIsVUFBYyxJQUFjO1FBQzFCLE9BQU87SUFDVCxDQUFDO0lBQ0Qsa0NBQWEsR0FBYixVQUFjLElBQVksRUFBRSxJQUFZO1FBQ3RDLE9BQU87SUFDVCxDQUFDO3FCQWpqQkg7RUEyaUJnQyxpQkFBaUI7OztJQVNsQiw2QkFBaUI7Ozs7SUFDOUMsaUNBQWEsR0FBYixVQUFjLElBQWM7UUFDMUIsT0FBTztJQUNULENBQUM7SUFDRCxpQ0FBYSxHQUFiLFVBQWMsR0FBVztRQUN2QixPQUFPO0lBQ1QsQ0FBQztvQkExakJIO0VBb2pCK0IsaUJBQWlCIiwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgSW5qZWN0YWJsZSB9IGZyb20gJ0Bhbmd1bGFyL2NvcmUnO1xuaW1wb3J0IHsgQ29yZG92YSwgQ29yZG92YVByb3BlcnR5LCBJb25pY05hdGl2ZVBsdWdpbiwgUGx1Z2luIH0gZnJvbSAnQGlvbmljLW5hdGl2ZS9jb3JlJztcbmltcG9ydCB7IE9ic2VydmFibGUgfSBmcm9tICdyeGpzJztcbmRlY2xhcmUgbGV0IHdpbmRvdzogYW55O1xuXG4vLyB0YWcgc2hvdWxkIGJlIE5mY1RhZywgYnV0IGtlZXBpbmcgYXMgTmRlZlRhZyB0byBhdm9pZCBicmVha2luZyBleGlzdGluZyBjb2RlXG5leHBvcnQgaW50ZXJmYWNlIE5kZWZFdmVudCB7XG4gIHRhZzogTmRlZlRhZztcbn1cblxuZXhwb3J0IGludGVyZmFjZSBOZGVmUmVjb3JkIHtcbiAgaWQ6IGFueVtdO1xuICBwYXlsb2FkOiBudW1iZXJbXTtcbiAgdG5mOiBudW1iZXI7XG4gIHR5cGU6IG51bWJlcltdO1xufVxuXG4vKipcbiAqIEBkZXByZWNhdGVkIHVzZSBOZmNUYWdcbiAqL1xuZXhwb3J0IGludGVyZmFjZSBOZGVmVGFnIHtcbiAgY2FuTWFrZVJlYWRPbmx5OiBib29sZWFuO1xuICBpZDogbnVtYmVyW107XG4gIGlzV3JpdGFibGU6IGJvb2xlYW47XG4gIG1heFNpemU6IG51bWJlcjtcbiAgbmRlZk1lc3NhZ2U6IE5kZWZSZWNvcmRbXTtcbiAgdGVjaFR5cGVzOiBzdHJpbmdbXTtcbiAgdHlwZTogc3RyaW5nO1xufVxuXG5leHBvcnQgaW50ZXJmYWNlIE5mY1RhZyB7XG4gIGlkPzogbnVtYmVyW107XG4gIGNhbk1ha2VSZWFkT25seT86IGJvb2xlYW47XG4gIGlzV3JpdGFibGU/OiBib29sZWFuO1xuICBtYXhTaXplPzogbnVtYmVyO1xuICBuZGVmTWVzc2FnZT86IE5kZWZSZWNvcmRbXTtcbiAgdGVjaFR5cGVzPzogc3RyaW5nW107XG4gIHR5cGU/OiBzdHJpbmc7XG59XG5cbmV4cG9ydCBpbnRlcmZhY2UgU2Nhbk9wdGlvbnMge1xuICAvKipcbiAgICogSWYgdHJ1ZSwga2VlcCB0aGUgc2NhbiBzZXNzaW9uIG9wZW4gc28gd3JpdGUgY2FuIGJlIGNhbGxlZFxuICAgKiBhZnRlciByZWFkaW5nLiBUaGUgZGVmYXVsdCB2YWx1ZSBpcyBmYWxzZS5cbiAgICovXG4gIGtlZXBTZXNzaW9uT3Blbj86IGJvb2xlYW47XG59XG5cbi8qKlxuICogQG5hbWUgTkZDXG4gKiBAZGVzY3JpcHRpb25cbiAqIFRoZSBORkMgcGx1Z2luIGFsbG93cyB5b3UgdG8gcmVhZCBhbmQgd3JpdGUgTkZDIHRhZ3MuIFlvdSBjYW4gYWxzbyBiZWFtIHRvLCBhbmQgcmVjZWl2ZSBmcm9tLCBvdGhlciBORkMgZW5hYmxlZCBkZXZpY2VzLlxuICpcbiAqIFVzZSB0b1xuICogLSByZWFkIGRhdGEgZnJvbSBORkMgdGFnc1xuICogLSB3cml0ZSBkYXRhIHRvIE5GQyB0YWdzXG4gKiAtIHNlbmQgZGF0YSB0byBvdGhlciBORkMgZW5hYmxlZCBkZXZpY2VzXG4gKiAtIHJlY2VpdmUgZGF0YSBmcm9tIE5GQyBkZXZpY2VzXG4gKlxuICogVGhpcyBwbHVnaW4gdXNlcyBOREVGIChORkMgRGF0YSBFeGNoYW5nZSBGb3JtYXQpIGZvciBtYXhpbXVtIGNvbXBhdGliaWx0eSBiZXR3ZWVuIE5GQyBkZXZpY2VzLCB0YWcgdHlwZXMsIGFuZCBvcGVyYXRpbmcgc3lzdGVtcy5cbiAqXG4gKiBAdXNhZ2VcbiAqIGBgYHR5cGVzY3JpcHRcbiAqIGltcG9ydCB7IE5GQywgTmRlZiB9IGZyb20gJ0Bpb25pYy1uYXRpdmUvbmZjL25neCc7XG4gKlxuICogY29uc3RydWN0b3IocHJpdmF0ZSBuZmM6IE5GQywgcHJpdmF0ZSBuZGVmOiBOZGVmKSB7IH1cbiAqXG4gKiAuLi5cbiAqXG4gKiAvLyBSZWFkIE5GQyBUYWcgLSBBbmRyb2lkXG4gKiAvLyBPbmNlIHRoZSByZWFkZXIgbW9kZSBpcyBlbmFibGVkLCBhbnkgdGFncyB0aGF0IGFyZSBzY2FubmVkIGFyZSBzZW50IHRvIHRoZSBzdWJzY3JpYmVyXG4gKiAgbGV0IGZsYWdzID0gdGhpcy5uZmMuRkxBR19SRUFERVJfTkZDX0EgfCB0aGlzLm5mYy5GTEFHX1JFQURFUl9ORkNfVjtcbiAqICB0aGlzLnJlYWRlck1vZGUkID0gdGhpcy5uZmMucmVhZGVyTW9kZShmbGFncykuc3Vic2NyaWJlKFxuICogICAgICB0YWcgPT4gY29uc29sZS5sb2coSlNPTi5zdHJpbmdpZnkodGFnKSksXG4gKiAgICAgIGVyciA9PiBjb25zb2xlLmxvZygnRXJyb3IgcmVhZGluZyB0YWcnLCBlcnIpXG4gKiAgKTtcbiAqXG4gKiAvLyBSZWFkIE5GQyBUYWcgLSBpT1NcbiAqIC8vIE9uIGlPUywgYSBORkMgcmVhZGVyIHNlc3Npb24gdGFrZXMgY29udHJvbCBmcm9tIHlvdXIgYXBwIHdoaWxlIHNjYW5uaW5nIHRhZ3MgdGhlbiByZXR1cm5zIGEgdGFnXG4gKiB0cnkge1xuICogICAgIGxldCB0YWcgPSBhd2FpdCB0aGlzLm5mYy5zY2FuTmRlZigpO1xuICogICAgIGNvbnNvbGUubG9nKEpTT04uc3RyaW5naWZ5KHRhZykpO1xuICogIH0gY2F0Y2ggKGVycikge1xuICogICAgICBjb25zb2xlLmxvZygnRXJyb3IgcmVhZGluZyB0YWcnLCBlcnIpO1xuICogIH1cbiAqXG4gKiBgYGBcbiAqXG4gKiBGb3IgbW9yZSBkZXRhaWxzIG9uIE5GQyB0YWcgb3BlcmF0aW9ucyBzZWUgaHR0cHM6Ly9naXRodWIuY29tL2NoYXJpb3Rzb2x1dGlvbnMvcGhvbmVnYXAtbmZjXG4gKi9cbkBQbHVnaW4oe1xuICBwbHVnaW5OYW1lOiAnTkZDJyxcbiAgcGx1Z2luOiAncGhvbmVnYXAtbmZjJyxcbiAgcGx1Z2luUmVmOiAnbmZjJyxcbiAgcmVwbzogJ2h0dHBzOi8vZ2l0aHViLmNvbS9jaGFyaW90c29sdXRpb25zL3Bob25lZ2FwLW5mYycsXG4gIHBsYXRmb3JtczogWydBbmRyb2lkJywgJ2lPUycsICdXaW5kb3dzJ10sXG59KVxuLyoqXG4gKiBAeyBORkMgfSBjbGFzcyBtZXRob2RzXG4gKi9cbkBJbmplY3RhYmxlKClcbmV4cG9ydCBjbGFzcyBORkMgZXh0ZW5kcyBJb25pY05hdGl2ZVBsdWdpbiB7XG4gIC8vIEZsYWdzIGZvciByZWFkZXJNb2RlXG4gIC8vIGh0dHBzOi8vZGV2ZWxvcGVyLmFuZHJvaWQuY29tL3JlZmVyZW5jZS9hbmRyb2lkL25mYy9OZmNBZGFwdGVyI0ZMQUdfUkVBREVSX05GQ19BXG4gIEBDb3Jkb3ZhUHJvcGVydHkoKVxuICBGTEFHX1JFQURFUl9ORkNfQTogbnVtYmVyO1xuICBAQ29yZG92YVByb3BlcnR5KClcbiAgRkxBR19SRUFERVJfTkZDX0I6IG51bWJlcjtcbiAgQENvcmRvdmFQcm9wZXJ0eSgpXG4gIEZMQUdfUkVBREVSX05GQ19GOiBudW1iZXI7XG4gIEBDb3Jkb3ZhUHJvcGVydHkoKVxuICBGTEFHX1JFQURFUl9ORkNfVjogbnVtYmVyO1xuICBAQ29yZG92YVByb3BlcnR5KClcbiAgRkxBR19SRUFERVJfTkZDX0JBUkNPREU6IG51bWJlcjtcbiAgQENvcmRvdmFQcm9wZXJ0eSgpXG4gIEZMQUdfUkVBREVSX1NLSVBfTkRFRl9DSEVDSzogbnVtYmVyO1xuICBAQ29yZG92YVByb3BlcnR5KClcbiAgRkxBR19SRUFERVJfTk9fUExBVEZPUk1fU09VTkRTOiBudW1iZXI7XG5cbiAgLyoqXG4gICAqIFJlYWQgTkZDIHRhZ3Mgc2VuZGluZyB0aGUgdGFnIGRhdGEgdG8gdGhlIHN1Y2Nlc3MgY2FsbGJhY2suXG4gICAqIFNlZSBodHRwczovL2dpdGh1Yi5jb20vY2hhcmlvdHNvbHV0aW9ucy9waG9uZWdhcC1uZmMjbmZjcmVhZGVybW9kZVxuICAgKlxuICAgKiBAcGFyYW0gZmxhZ3NcbiAgICogQHJldHVybnMge09ic2VydmFibGU8YW55Pn1cbiAgICovXG4gIEBDb3Jkb3ZhKHtcbiAgICBvYnNlcnZhYmxlOiB0cnVlLFxuICAgIGNsZWFyRnVuY3Rpb246ICdkaXNhYmxlUmVhZGVyTW9kZScsXG4gICAgY2xlYXJXaXRoQXJnczogZmFsc2UsXG4gIH0pXG4gIHJlYWRlck1vZGUoZmxhZ3M6IG51bWJlcik6IE9ic2VydmFibGU8TmZjVGFnPiB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgLyoqXG4gICAqIEZ1bmN0aW9uIHNjYW5OZGVmIHN0YXJ0cyB0aGUgTkZDTkRFRlJlYWRlclNlc3Npb24gYWxsb3dpbmcgaU9TIHRvIHNjYW4gTkZDIHRhZ3MuXG4gICAqIGh0dHBzOi8vZ2l0aHViLmNvbS9jaGFyaW90c29sdXRpb25zL3Bob25lZ2FwLW5mYyNuZmNzY2FubmRlZlxuICAgKi9cbiAgQENvcmRvdmEoeyBzeW5jOiB0cnVlIH0pXG4gIHNjYW5OZGVmKG9wdGlvbnM/OiBTY2FuT3B0aW9ucyk6IFByb21pc2U8TmZjVGFnPiB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgLyoqXG4gICAqIEZ1bmN0aW9uIHNjYW5UYWcgc3RhcnRzIHRoZSBORkNUYWdSZWFkZXJTZXNzaW9uIGFsbG93aW5nIGlPUyB0byBzY2FuIE5GQyB0YWdzLlxuICAgKlxuICAgKiBZb3UgcHJvYmFibHkgd2FudCAqc2Nhbk5kZWYqIGZvciByZWFkaW5nIE5GQyB0YWdzIG9uIGlPUy4gT25seSB1c2Ugc2NhblRhZyBpZiB5b3UgbmVlZCB0aGUgdGFnIFVJRC5cbiAgICogaHR0cHM6Ly9naXRodWIuY29tL2NoYXJpb3Rzb2x1dGlvbnMvcGhvbmVnYXAtbmZjI25mY3NjYW50YWdcbiAgICovXG4gIEBDb3Jkb3ZhKHsgc3luYzogdHJ1ZSB9KVxuICBzY2FuVGFnKG9wdGlvbnM/OiBTY2FuT3B0aW9ucyk6IFByb21pc2U8TmZjVGFnPiB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgLyoqXG4gICAqIEZ1bmN0aW9uIGNhbmNlbFNjYW4gc3RvcHMgdGhlIE5GQ1JlYWRlclNlc3Npb24gcmV0dXJuaW5nIGNvbnRyb2wgdG8geW91ciBhcHAuXG4gICAqIGh0dHBzOi8vZ2l0aHViLmNvbS9jaGFyaW90c29sdXRpb25zL3Bob25lZ2FwLW5mYyNuZmNjYW5jZWxzY2FuXG4gICAqL1xuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgY2FuY2VsU2NhbigpOiBQcm9taXNlPGFueT4ge1xuICAgIHJldHVybjtcbiAgfVxuXG4gIC8qKlxuICAgKiBDb25uZWN0IHRvIHRoZSB0YWcgYW5kIGVuYWJsZSBJL08gb3BlcmF0aW9ucyB0byB0aGUgdGFnIGZyb20gdGhpcyBUYWdUZWNobm9sb2d5IG9iamVjdC5cbiAgICogaHR0cHM6Ly9naXRodWIuY29tL2NoYXJpb3Rzb2x1dGlvbnMvcGhvbmVnYXAtbmZjI25mY2Nvbm5lY3RcbiAgICpcbiAgICogQHBhcmFtIHRlY2ggVGhlIHRhZyB0ZWNobm9sb2d5IGNsYXNzIG5hbWUgZS5nLiBhbmRyb2lkLm5mYy50ZWNoLklzb0RlcFxuICAgKiBAcGFyYW0gdGltZW91dCBUaGUgdHJhbnNjZWl2ZShieXRlW10pIHRpbWVvdXQgaW4gbWlsbGlzZWNvbmRzIFtvcHRpb25hbF1cbiAgICovXG4gIEBDb3Jkb3ZhKHsgc3luYzogdHJ1ZSB9KVxuICBjb25uZWN0KHRlY2g6IHN0cmluZywgdGltZW91dD86IG51bWJlcik6IFByb21pc2U8YW55PiB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgLyoqXG4gICAqIENsb3NlIFRhZ1RlY2hub2xvZ3kgY29ubmVjdGlvbi5cbiAgICogaHR0cHM6Ly9naXRodWIuY29tL2NoYXJpb3Rzb2x1dGlvbnMvcGhvbmVnYXAtbmZjI25mY2Nsb3NlXG4gICAqL1xuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgY2xvc2UoKTogUHJvbWlzZTxhbnk+IHtcbiAgICByZXR1cm47XG4gIH1cblxuICAvKipcbiAgICogU2VuZCByYXcgY29tbWFuZCB0byB0aGUgdGFnIGFuZCByZWNlaXZlIHRoZSByZXNwb25zZS5cbiAgICogaHR0cHM6Ly9naXRodWIuY29tL2NoYXJpb3Rzb2x1dGlvbnMvcGhvbmVnYXAtbmZjI25mY3RyYW5zY2VpdmVcbiAgICpcbiAgICogRXhhbXBsZSBjb2RlIGh0dHBzOi8vZ2l0aHViLmNvbS9jaGFyaW90c29sdXRpb25zL3Bob25lZ2FwLW5mYyN0YWctdGVjaG5vbG9neS1mdW5jdGlvbnMtMVxuICAgKlxuICAgKiBAcGFyYW0gZGF0YSBhbiBBcnJheUJ1ZmZlciBvciBzdHJpbmcgb2YgaGV4IGRhdGEgZS5nLiAnMDAgQTQgMDQgMDAgMDcgRDIgNzYgMDAgMDAgODUgMDEgMDAnXG4gICAqL1xuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgdHJhbnNjZWl2ZShkYXRhOiBzdHJpbmcgfCBBcnJheUJ1ZmZlcik6IFByb21pc2U8QXJyYXlCdWZmZXI+IHtcbiAgICByZXR1cm47XG4gIH1cblxuICAvKipcbiAgICogU3RhcnRzIHRoZSBORkNOREVGUmVhZGVyU2Vzc2lvbiBhbGxvd2luZyBpT1MgdG8gc2NhbiBORkMgdGFncy5cbiAgICogQGRlcHJlY2F0ZWQgdXNlIHNjYW5OZGVmIG9yIHNjYW5UYWdcbiAgICpcbiAgICogQHBhcmFtIG9uU3VjY2Vzc1xuICAgKiBAcGFyYW0gb25GYWlsdXJlXG4gICAqIEByZXR1cm5zIHtPYnNlcnZhYmxlPGFueT59XG4gICAqL1xuICBAQ29yZG92YSh7XG4gICAgb2JzZXJ2YWJsZTogdHJ1ZSxcbiAgICBzdWNjZXNzSW5kZXg6IDAsXG4gICAgZXJyb3JJbmRleDogMyxcbiAgICBjbGVhckZ1bmN0aW9uOiAnaW52YWxpZGF0ZVNlc3Npb24nLFxuICAgIGNsZWFyV2l0aEFyZ3M6IHRydWUsXG4gIH0pXG4gIGJlZ2luU2Vzc2lvbihvblN1Y2Nlc3M/OiBGdW5jdGlvbiwgb25GYWlsdXJlPzogRnVuY3Rpb24pOiBPYnNlcnZhYmxlPGFueT4ge1xuICAgIHJldHVybjtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZWdpc3RlcnMgYW4gZXZlbnQgbGlzdGVuZXIgZm9yIGFueSBOREVGIHRhZy5cbiAgICogQHBhcmFtIG9uU3VjY2Vzc1xuICAgKiBAcGFyYW0gb25GYWlsdXJlXG4gICAqIEByZXR1cm5zIHtPYnNlcnZhYmxlPGFueT59XG4gICAqL1xuICBAQ29yZG92YSh7XG4gICAgb2JzZXJ2YWJsZTogdHJ1ZSxcbiAgICBzdWNjZXNzSW5kZXg6IDAsXG4gICAgZXJyb3JJbmRleDogMyxcbiAgICBjbGVhckZ1bmN0aW9uOiAncmVtb3ZlTmRlZkxpc3RlbmVyJyxcbiAgICBjbGVhcldpdGhBcmdzOiB0cnVlLFxuICB9KVxuICBhZGROZGVmTGlzdGVuZXIob25TdWNjZXNzPzogRnVuY3Rpb24sIG9uRmFpbHVyZT86IEZ1bmN0aW9uKTogT2JzZXJ2YWJsZTxOZGVmRXZlbnQ+IHtcbiAgICByZXR1cm47XG4gIH1cblxuICAvKipcbiAgICogUmVnaXN0ZXJzIGFuIGV2ZW50IGxpc3RlbmVyIGZvciB0YWdzIG1hdGNoaW5nIGFueSB0YWcgdHlwZS5cbiAgICogQHBhcmFtIG9uU3VjY2Vzc1xuICAgKiBAcGFyYW0gb25GYWlsdXJlXG4gICAqIEByZXR1cm5zIHtPYnNlcnZhYmxlPGFueT59XG4gICAqL1xuICBAQ29yZG92YSh7XG4gICAgb2JzZXJ2YWJsZTogdHJ1ZSxcbiAgICBzdWNjZXNzSW5kZXg6IDAsXG4gICAgZXJyb3JJbmRleDogMyxcbiAgICBjbGVhckZ1bmN0aW9uOiAncmVtb3ZlVGFnRGlzY292ZXJlZExpc3RlbmVyJyxcbiAgICBjbGVhcldpdGhBcmdzOiB0cnVlLFxuICB9KVxuICBhZGRUYWdEaXNjb3ZlcmVkTGlzdGVuZXIob25TdWNjZXNzPzogRnVuY3Rpb24sIG9uRmFpbHVyZT86IEZ1bmN0aW9uKTogT2JzZXJ2YWJsZTxhbnk+IHtcbiAgICByZXR1cm47XG4gIH1cblxuICAvKipcbiAgICogUmVnaXN0ZXJzIGFuIGV2ZW50IGxpc3RlbmVyIGZvciBOREVGIHRhZ3MgbWF0Y2hpbmcgYSBzcGVjaWZpZWQgTUlNRSB0eXBlLlxuICAgKiBAcGFyYW0gbWltZVR5cGVcbiAgICogQHBhcmFtIG9uU3VjY2Vzc1xuICAgKiBAcGFyYW0gb25GYWlsdXJlXG4gICAqIEByZXR1cm5zIHtPYnNlcnZhYmxlPGFueT59XG4gICAqL1xuICBAQ29yZG92YSh7XG4gICAgb2JzZXJ2YWJsZTogdHJ1ZSxcbiAgICBzdWNjZXNzSW5kZXg6IDEsXG4gICAgZXJyb3JJbmRleDogNCxcbiAgICBjbGVhckZ1bmN0aW9uOiAncmVtb3ZlTWltZVR5cGVMaXN0ZW5lcicsXG4gICAgY2xlYXJXaXRoQXJnczogdHJ1ZSxcbiAgfSlcbiAgYWRkTWltZVR5cGVMaXN0ZW5lcihtaW1lVHlwZTogc3RyaW5nLCBvblN1Y2Nlc3M/OiBGdW5jdGlvbiwgb25GYWlsdXJlPzogRnVuY3Rpb24pOiBPYnNlcnZhYmxlPGFueT4ge1xuICAgIHJldHVybjtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZWdpc3RlcnMgYW4gZXZlbnQgbGlzdGVuZXIgZm9yIGZvcm1hdGFibGUgTkRFRiB0YWdzLlxuICAgKiBAcGFyYW0gb25TdWNjZXNzXG4gICAqIEBwYXJhbSBvbkZhaWx1cmVcbiAgICogQHJldHVybnMge09ic2VydmFibGU8YW55Pn1cbiAgICovXG4gIEBDb3Jkb3ZhKHtcbiAgICBvYnNlcnZhYmxlOiB0cnVlLFxuICAgIHN1Y2Nlc3NJbmRleDogMCxcbiAgICBlcnJvckluZGV4OiAzLFxuICB9KVxuICBhZGROZGVmRm9ybWF0YWJsZUxpc3RlbmVyKG9uU3VjY2Vzcz86IEZ1bmN0aW9uLCBvbkZhaWx1cmU/OiBGdW5jdGlvbik6IE9ic2VydmFibGU8YW55PiB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgLyoqXG4gICAqIFdyaXRlcyBhbiBOZGVmTWVzc2FnZShhcnJheSBvZiBuZGVmIHJlY29yZHMpIHRvIGEgTkZDIHRhZy5cbiAgICogQHBhcmFtIG1lc3NhZ2Uge2FueVtdfVxuICAgKiBAcmV0dXJucyB7UHJvbWlzZTxhbnk+fVxuICAgKi9cbiAgQENvcmRvdmEoKVxuICB3cml0ZShtZXNzYWdlOiBhbnlbXSk6IFByb21pc2U8YW55PiB7XG4gICAgcmV0dXJuO1xuICB9XG4gIC8qKlxuICAgKiBNYWtlcyBhIE5GQyB0YWcgcmVhZCBvbmx5LiAqKldhcm5pbmcqKiB0aGlzIGlzIHBlcm1hbmVudC5cbiAgICogQHJldHVybnMge1Byb21pc2U8YW55Pn1cbiAgICovXG4gIEBDb3Jkb3ZhKClcbiAgbWFrZVJlYWRPbmx5KCk6IFByb21pc2U8YW55PiB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgLyoqXG4gICAqIFNoYXJlcyBhbiBOREVGIE1lc3NhZ2UoYXJyYXkgb2YgbmRlZiByZWNvcmRzKSB2aWEgcGVlci10by1wZWVyLlxuICAgKiBAcGFyYW0gbWVzc2FnZSBBbiBhcnJheSBvZiBOREVGIFJlY29yZHMuXG4gICAqIEByZXR1cm5zIHtQcm9taXNlPGFueT59XG4gICAqL1xuICBAQ29yZG92YSgpXG4gIHNoYXJlKG1lc3NhZ2U6IGFueVtdKTogUHJvbWlzZTxhbnk+IHtcbiAgICByZXR1cm47XG4gIH1cblxuICAvKipcbiAgICogU3RvcCBzaGFyaW5nIE5ERUYgZGF0YSB2aWEgcGVlci10by1wZWVyLlxuICAgKiBAcmV0dXJucyB7UHJvbWlzZTxhbnk+fVxuICAgKi9cbiAgQENvcmRvdmEoKVxuICB1bnNoYXJlKCk6IFByb21pc2U8YW55PiB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgLyoqXG4gICAqIEVyYXNlIGEgTkRFRiB0YWdcbiAgICovXG4gIEBDb3Jkb3ZhKClcbiAgZXJhc2UoKTogUHJvbWlzZTxhbnk+IHtcbiAgICByZXR1cm47XG4gIH1cblxuICAvKipcbiAgICogU2VuZCBhIGZpbGUgdG8gYW5vdGhlciBkZXZpY2UgdmlhIE5GQyBoYW5kb3Zlci5cbiAgICogQHBhcmFtIHVyaXMgQSBVUkkgYXMgYSBTdHJpbmcsIG9yIGFuIGFycmF5IG9mIFVSSXMuXG4gICAqIEByZXR1cm5zIHtQcm9taXNlPGFueT59XG4gICAqL1xuICBAQ29yZG92YSgpXG4gIGhhbmRvdmVyKHVyaXM6IHN0cmluZ1tdKTogUHJvbWlzZTxhbnk+IHtcbiAgICByZXR1cm47XG4gIH1cblxuICAvKipcbiAgICogU3RvcCBzaGFyaW5nIE5ERUYgZGF0YSB2aWEgTkZDIGhhbmRvdmVyLlxuICAgKiBAcmV0dXJucyB7UHJvbWlzZTxhbnk+fVxuICAgKi9cbiAgQENvcmRvdmEoKVxuICBzdG9wSGFuZG92ZXIoKTogUHJvbWlzZTxhbnk+IHtcbiAgICByZXR1cm47XG4gIH1cblxuICAvKipcbiAgICogT3BlbnMgdGhlIGRldmljZSdzIE5GQyBzZXR0aW5ncy5cbiAgICogQHJldHVybnMge1Byb21pc2U8YW55Pn1cbiAgICovXG4gIEBDb3Jkb3ZhKClcbiAgc2hvd1NldHRpbmdzKCk6IFByb21pc2U8YW55PiB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIE5GQyBpcyBhdmFpbGFibGUgYW5kIGVuYWJsZWQgb24gdGhpcyBkZXZpY2UuXG4gICAqIEByZXR1cm5zIHtQcm9taXNlPGFueT59XG4gICAqL1xuICBAQ29yZG92YSgpXG4gIGVuYWJsZWQoKTogUHJvbWlzZTxhbnk+IHtcbiAgICByZXR1cm47XG4gIH1cbiAgLyoqXG4gICAqIEB7IE5GQyB9IGNsYXNzIHV0aWxpdHkgbWV0aG9kc1xuICAgKiBmb3IgdXNlIHdpdGhcbiAgICovXG4gIC8qKlxuICAgKiBDb252ZXJ0IGJ5dGUgYXJyYXkgdG8gc3RyaW5nXG4gICAqIEBwYXJhbSBieXRlcyB7bnVtYmVyW119XG4gICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAqL1xuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgYnl0ZXNUb1N0cmluZyhieXRlczogbnVtYmVyW10pOiBzdHJpbmcge1xuICAgIHJldHVybjtcbiAgfVxuICAvKipcbiAgICogQ29udmVydCBzdHJpbmcgdG8gYnl0ZSBhcnJheS5cbiAgICogQHBhcmFtIHN0ciB7c3RyaW5nfVxuICAgKiBAcmV0dXJucyB7bnVtYmVyW119XG4gICAqL1xuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgc3RyaW5nVG9CeXRlcyhzdHI6IHN0cmluZyk6IG51bWJlcltdIHtcbiAgICByZXR1cm47XG4gIH1cbiAgLyoqXG4gICAqIENvbnZlcnQgYnl0ZSBhcnJheSB0byBoZXggc3RyaW5nXG4gICAqXG4gICAqIEBwYXJhbSBieXRlcyB7bnVtYmVyW119XG4gICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAqL1xuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgYnl0ZXNUb0hleFN0cmluZyhieXRlczogbnVtYmVyW10pOiBzdHJpbmcge1xuICAgIHJldHVybjtcbiAgfVxufVxuLyoqXG4gKiBAaGlkZGVuXG4gKi9cbkBQbHVnaW4oe1xuICBwbHVnaW5OYW1lOiAnTkZDJyxcbiAgcGx1Z2luOiAncGhvbmVnYXAtbmZjJyxcbiAgcGx1Z2luUmVmOiAnbmRlZicsXG59KVxuLyoqXG4gKiBAZGVzY3JpcHRpb25cbiAqIFV0aWxpdHkgbWV0aG9kcyBmb3IgY3JlYXRpbmcgbmRlZiByZWNvcmRzIGZvciB0aGUgbmRlZiB0YWcgZm9ybWF0LlxuICogTW92ZSByZWNvcmRzIGludG8gYXJyYXkgYmVmb3JlIHVzYWdlLiBUaGVuIHBhc3MgYW4gYXJyYXkgdG8gbWV0aG9kcyBhcyBwYXJhbWV0ZXJzLlxuICogRG8gbm90IHBhc3MgYnl0ZXMgYXMgcGFyYW1ldGVycyBmb3IgdGhlc2UgbWV0aG9kcywgY29udmVyc2lvbiBpcyBidWlsdCBpbi5cbiAqIEZvciB1c2FnZSB3aXRoIG5mYy53cml0ZSgpIGFuZCBuZmMuc2hhcmUoKVxuICovXG5ASW5qZWN0YWJsZSgpXG5leHBvcnQgY2xhc3MgTmRlZiBleHRlbmRzIElvbmljTmF0aXZlUGx1Z2luIHtcbiAgQENvcmRvdmFQcm9wZXJ0eSgpXG4gIFRORl9FTVBUWTogbnVtYmVyO1xuICBAQ29yZG92YVByb3BlcnR5KClcbiAgVE5GX1dFTExfS05PV046IG51bWJlcjtcbiAgQENvcmRvdmFQcm9wZXJ0eSgpXG4gIFRORl9NSU1FX01FRElBOiBudW1iZXI7XG4gIEBDb3Jkb3ZhUHJvcGVydHkoKVxuICBUTkZfQUJTT0xVVEVfVVJJOiBudW1iZXI7XG4gIEBDb3Jkb3ZhUHJvcGVydHkoKVxuICBUTkZfRVhURVJOQUxfVFlQRTogbnVtYmVyO1xuICBAQ29yZG92YVByb3BlcnR5KClcbiAgVE5GX1VOS05PV046IG51bWJlcjtcbiAgQENvcmRvdmFQcm9wZXJ0eSgpXG4gIFRORl9VTkNIQU5HRUQ6IG51bWJlcjtcbiAgQENvcmRvdmFQcm9wZXJ0eSgpXG4gIFRORl9SRVNFUlZFRDogbnVtYmVyO1xuXG4gIEBDb3Jkb3ZhKHsgc3luYzogdHJ1ZSB9KVxuICByZWNvcmQodG5mOiBudW1iZXIsIHR5cGU6IG51bWJlcltdIHwgc3RyaW5nLCBpZDogbnVtYmVyW10gfCBzdHJpbmcsIHBheWxvYWQ6IG51bWJlcltdIHwgc3RyaW5nKTogTmRlZlJlY29yZCB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgQENvcmRvdmEoeyBzeW5jOiB0cnVlIH0pXG4gIHRleHRSZWNvcmQodGV4dDogc3RyaW5nLCBsYW5ndWFnZUNvZGU/OiBzdHJpbmcsIGlkPzogbnVtYmVyW10gfCBzdHJpbmcpOiBOZGVmUmVjb3JkIHtcbiAgICByZXR1cm47XG4gIH1cblxuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgdXJpUmVjb3JkKHVyaTogc3RyaW5nLCBpZD86IG51bWJlcltdIHwgc3RyaW5nKTogTmRlZlJlY29yZCB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgQENvcmRvdmEoeyBzeW5jOiB0cnVlIH0pXG4gIGFic29sdXRlVXJpUmVjb3JkKHVyaTogc3RyaW5nLCBwYXlsb2FkOiBudW1iZXJbXSB8IHN0cmluZywgaWQ/OiBudW1iZXJbXSB8IHN0cmluZyk6IE5kZWZSZWNvcmQge1xuICAgIHJldHVybjtcbiAgfVxuXG4gIEBDb3Jkb3ZhKHsgc3luYzogdHJ1ZSB9KVxuICBtaW1lTWVkaWFSZWNvcmQobWltZVR5cGU6IHN0cmluZywgcGF5bG9hZDogc3RyaW5nKTogTmRlZlJlY29yZCB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgQENvcmRvdmEoeyBzeW5jOiB0cnVlIH0pXG4gIHNtYXJ0UG9zdGVyKG5kZWZSZWNvcmRzOiBhbnlbXSwgaWQ/OiBudW1iZXJbXSB8IHN0cmluZyk6IE5kZWZSZWNvcmQge1xuICAgIHJldHVybjtcbiAgfVxuXG4gIEBDb3Jkb3ZhKHsgc3luYzogdHJ1ZSB9KVxuICBlbXB0eVJlY29yZCgpOiBOZGVmUmVjb3JkIHtcbiAgICByZXR1cm47XG4gIH1cblxuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgYW5kcm9pZEFwcGxpY2F0aW9uUmVjb3JkKHBhY2thZ2VOYW1lOiBzdHJpbmcpOiBOZGVmUmVjb3JkIHtcbiAgICByZXR1cm47XG4gIH1cblxuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgZW5jb2RlTWVzc2FnZShuZGVmUmVjb3JkczogYW55KTogYW55IHtcbiAgICByZXR1cm47XG4gIH1cblxuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgZGVjb2RlTWVzc2FnZShieXRlczogYW55KTogYW55IHtcbiAgICByZXR1cm47XG4gIH1cblxuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgZGVjb2RlVG5mKHRuZl9ieXRlOiBhbnkpOiBhbnkge1xuICAgIHJldHVybjtcbiAgfVxuXG4gIEBDb3Jkb3ZhKHsgc3luYzogdHJ1ZSB9KVxuICBlbmNvZGVUbmYobWI6IGFueSwgbWU6IGFueSwgY2Y6IGFueSwgc3I6IGFueSwgaWw6IGFueSwgdG5mOiBhbnkpOiBhbnkge1xuICAgIHJldHVybjtcbiAgfVxuXG4gIEBDb3Jkb3ZhKHsgc3luYzogdHJ1ZSB9KVxuICB0bmZUb1N0cmluZyh0bmY6IGFueSk6IHN0cmluZyB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgQENvcmRvdmFQcm9wZXJ0eSgpXG4gIHRleHRIZWxwZXI6IFRleHRIZWxwZXI7XG5cbiAgQENvcmRvdmFQcm9wZXJ0eSgpXG4gIHVyaUhlbHBlcjogVXJpSGVscGVyO1xufVxuXG4vKipcbiAqIEBoaWRkZW5cbiAqL1xuQFBsdWdpbih7XG4gIHBsdWdpbk5hbWU6ICdORkMnLFxuICBwbHVnaW46ICdwaG9uZWdhcC1uZmMnLFxuICBwbHVnaW5SZWY6ICd1dGlsJyxcbn0pXG5ASW5qZWN0YWJsZSgpXG5leHBvcnQgY2xhc3MgTmZjVXRpbCBleHRlbmRzIElvbmljTmF0aXZlUGx1Z2luIHtcbiAgQENvcmRvdmEoeyBzeW5jOiB0cnVlIH0pXG4gIHRvSGV4KGk6IG51bWJlcik6IHN0cmluZyB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgQENvcmRvdmEoeyBzeW5jOiB0cnVlIH0pXG4gIHRvUHJpbnRhYmxlKGk6IG51bWJlcik6IHN0cmluZyB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgQENvcmRvdmEoeyBzeW5jOiB0cnVlIH0pXG4gIGJ5dGVzVG9TdHJpbmcoaTogbnVtYmVyW10pOiBzdHJpbmcge1xuICAgIHJldHVybjtcbiAgfVxuXG4gIEBDb3Jkb3ZhKHsgc3luYzogdHJ1ZSB9KVxuICBzdHJpbmdUb0J5dGVzKHM6IHN0cmluZyk6IG51bWJlcltdIHtcbiAgICByZXR1cm47XG4gIH1cblxuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgYnl0ZXNUb0hleFN0cmluZyhieXRlczogbnVtYmVyW10pOiBzdHJpbmcge1xuICAgIHJldHVybjtcbiAgfVxuXG4gIEBDb3Jkb3ZhKHsgc3luYzogdHJ1ZSB9KVxuICBpc1R5cGUocmVjb3JkOiBOZGVmUmVjb3JkLCB0bmY6IG51bWJlciwgdHlwZTogbnVtYmVyW10gfCBzdHJpbmcpOiBib29sZWFuIHtcbiAgICByZXR1cm47XG4gIH1cblxuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgYXJyYXlCdWZmZXJUb0hleFN0cmluZyhidWZmZXI6IEFycmF5QnVmZmVyKTogc3RyaW5nIHtcbiAgICByZXR1cm47XG4gIH1cblxuICBAQ29yZG92YSh7IHN5bmM6IHRydWUgfSlcbiAgaGV4U3RyaW5nVG9BcnJheUJ1ZmZlcihoZXhTdHJpbmc6IHN0cmluZyk6IEFycmF5QnVmZmVyIHtcbiAgICByZXR1cm47XG4gIH1cbn1cblxuZXhwb3J0IGNsYXNzIFRleHRIZWxwZXIgZXh0ZW5kcyBJb25pY05hdGl2ZVBsdWdpbiB7XG4gIGRlY29kZVBheWxvYWQoZGF0YTogbnVtYmVyW10pOiBzdHJpbmcge1xuICAgIHJldHVybjtcbiAgfVxuICBlbmNvZGVQYXlsb2FkKHRleHQ6IHN0cmluZywgbGFuZzogc3RyaW5nKTogbnVtYmVyW10ge1xuICAgIHJldHVybjtcbiAgfVxufVxuXG5leHBvcnQgY2xhc3MgVXJpSGVscGVyIGV4dGVuZHMgSW9uaWNOYXRpdmVQbHVnaW4ge1xuICBkZWNvZGVQYXlsb2FkKGRhdGE6IG51bWJlcltdKTogc3RyaW5nIHtcbiAgICByZXR1cm47XG4gIH1cbiAgZW5jb2RlUGF5bG9hZCh1cmk6IHN0cmluZyk6IG51bWJlcltdIHtcbiAgICByZXR1cm47XG4gIH1cbn1cbiJdfQ==