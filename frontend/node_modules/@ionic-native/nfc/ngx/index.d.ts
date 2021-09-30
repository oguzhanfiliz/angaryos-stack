import { IonicNativePlugin } from '@ionic-native/core';
import { Observable } from 'rxjs';
export interface NdefEvent {
    tag: NdefTag;
}
export interface NdefRecord {
    id: any[];
    payload: number[];
    tnf: number;
    type: number[];
}
/**
 * @deprecated use NfcTag
 */
export interface NdefTag {
    canMakeReadOnly: boolean;
    id: number[];
    isWritable: boolean;
    maxSize: number;
    ndefMessage: NdefRecord[];
    techTypes: string[];
    type: string;
}
export interface NfcTag {
    id?: number[];
    canMakeReadOnly?: boolean;
    isWritable?: boolean;
    maxSize?: number;
    ndefMessage?: NdefRecord[];
    techTypes?: string[];
    type?: string;
}
export interface ScanOptions {
    /**
     * If true, keep the scan session open so write can be called
     * after reading. The default value is false.
     */
    keepSessionOpen?: boolean;
}
/**
 * @name NFC
 * @description
 * The NFC plugin allows you to read and write NFC tags. You can also beam to, and receive from, other NFC enabled devices.
 *
 * Use to
 * - read data from NFC tags
 * - write data to NFC tags
 * - send data to other NFC enabled devices
 * - receive data from NFC devices
 *
 * This plugin uses NDEF (NFC Data Exchange Format) for maximum compatibilty between NFC devices, tag types, and operating systems.
 *
 * @usage
 * ```typescript
 * import { NFC, Ndef } from '@ionic-native/nfc/ngx';
 *
 * constructor(private nfc: NFC, private ndef: Ndef) { }
 *
 * ...
 *
 * // Read NFC Tag - Android
 * // Once the reader mode is enabled, any tags that are scanned are sent to the subscriber
 *  let flags = this.nfc.FLAG_READER_NFC_A | this.nfc.FLAG_READER_NFC_V;
 *  this.readerMode$ = this.nfc.readerMode(flags).subscribe(
 *      tag => console.log(JSON.stringify(tag)),
 *      err => console.log('Error reading tag', err)
 *  );
 *
 * // Read NFC Tag - iOS
 * // On iOS, a NFC reader session takes control from your app while scanning tags then returns a tag
 * try {
 *     let tag = await this.nfc.scanNdef();
 *     console.log(JSON.stringify(tag));
 *  } catch (err) {
 *      console.log('Error reading tag', err);
 *  }
 *
 * ```
 *
 * For more details on NFC tag operations see https://github.com/chariotsolutions/phonegap-nfc
 */
export declare class NFC extends IonicNativePlugin {
    FLAG_READER_NFC_A: number;
    FLAG_READER_NFC_B: number;
    FLAG_READER_NFC_F: number;
    FLAG_READER_NFC_V: number;
    FLAG_READER_NFC_BARCODE: number;
    FLAG_READER_SKIP_NDEF_CHECK: number;
    FLAG_READER_NO_PLATFORM_SOUNDS: number;
    /**
     * Read NFC tags sending the tag data to the success callback.
     * See https://github.com/chariotsolutions/phonegap-nfc#nfcreadermode
     *
     * @param flags
     * @returns {Observable<any>}
     */
    readerMode(flags: number): Observable<NfcTag>;
    /**
     * Function scanNdef starts the NFCNDEFReaderSession allowing iOS to scan NFC tags.
     * https://github.com/chariotsolutions/phonegap-nfc#nfcscanndef
     */
    scanNdef(options?: ScanOptions): Promise<NfcTag>;
    /**
     * Function scanTag starts the NFCTagReaderSession allowing iOS to scan NFC tags.
     *
     * You probably want *scanNdef* for reading NFC tags on iOS. Only use scanTag if you need the tag UID.
     * https://github.com/chariotsolutions/phonegap-nfc#nfcscantag
     */
    scanTag(options?: ScanOptions): Promise<NfcTag>;
    /**
     * Function cancelScan stops the NFCReaderSession returning control to your app.
     * https://github.com/chariotsolutions/phonegap-nfc#nfccancelscan
     */
    cancelScan(): Promise<any>;
    /**
     * Connect to the tag and enable I/O operations to the tag from this TagTechnology object.
     * https://github.com/chariotsolutions/phonegap-nfc#nfcconnect
     *
     * @param tech The tag technology class name e.g. android.nfc.tech.IsoDep
     * @param timeout The transceive(byte[]) timeout in milliseconds [optional]
     */
    connect(tech: string, timeout?: number): Promise<any>;
    /**
     * Close TagTechnology connection.
     * https://github.com/chariotsolutions/phonegap-nfc#nfcclose
     */
    close(): Promise<any>;
    /**
     * Send raw command to the tag and receive the response.
     * https://github.com/chariotsolutions/phonegap-nfc#nfctransceive
     *
     * Example code https://github.com/chariotsolutions/phonegap-nfc#tag-technology-functions-1
     *
     * @param data an ArrayBuffer or string of hex data e.g. '00 A4 04 00 07 D2 76 00 00 85 01 00'
     */
    transceive(data: string | ArrayBuffer): Promise<ArrayBuffer>;
    /**
     * Starts the NFCNDEFReaderSession allowing iOS to scan NFC tags.
     * @deprecated use scanNdef or scanTag
     *
     * @param onSuccess
     * @param onFailure
     * @returns {Observable<any>}
     */
    beginSession(onSuccess?: Function, onFailure?: Function): Observable<any>;
    /**
     * Registers an event listener for any NDEF tag.
     * @param onSuccess
     * @param onFailure
     * @returns {Observable<any>}
     */
    addNdefListener(onSuccess?: Function, onFailure?: Function): Observable<NdefEvent>;
    /**
     * Registers an event listener for tags matching any tag type.
     * @param onSuccess
     * @param onFailure
     * @returns {Observable<any>}
     */
    addTagDiscoveredListener(onSuccess?: Function, onFailure?: Function): Observable<any>;
    /**
     * Registers an event listener for NDEF tags matching a specified MIME type.
     * @param mimeType
     * @param onSuccess
     * @param onFailure
     * @returns {Observable<any>}
     */
    addMimeTypeListener(mimeType: string, onSuccess?: Function, onFailure?: Function): Observable<any>;
    /**
     * Registers an event listener for formatable NDEF tags.
     * @param onSuccess
     * @param onFailure
     * @returns {Observable<any>}
     */
    addNdefFormatableListener(onSuccess?: Function, onFailure?: Function): Observable<any>;
    /**
     * Writes an NdefMessage(array of ndef records) to a NFC tag.
     * @param message {any[]}
     * @returns {Promise<any>}
     */
    write(message: any[]): Promise<any>;
    /**
     * Makes a NFC tag read only. **Warning** this is permanent.
     * @returns {Promise<any>}
     */
    makeReadOnly(): Promise<any>;
    /**
     * Shares an NDEF Message(array of ndef records) via peer-to-peer.
     * @param message An array of NDEF Records.
     * @returns {Promise<any>}
     */
    share(message: any[]): Promise<any>;
    /**
     * Stop sharing NDEF data via peer-to-peer.
     * @returns {Promise<any>}
     */
    unshare(): Promise<any>;
    /**
     * Erase a NDEF tag
     */
    erase(): Promise<any>;
    /**
     * Send a file to another device via NFC handover.
     * @param uris A URI as a String, or an array of URIs.
     * @returns {Promise<any>}
     */
    handover(uris: string[]): Promise<any>;
    /**
     * Stop sharing NDEF data via NFC handover.
     * @returns {Promise<any>}
     */
    stopHandover(): Promise<any>;
    /**
     * Opens the device's NFC settings.
     * @returns {Promise<any>}
     */
    showSettings(): Promise<any>;
    /**
     * Check if NFC is available and enabled on this device.
     * @returns {Promise<any>}
     */
    enabled(): Promise<any>;
    /**
     * @{ NFC } class utility methods
     * for use with
     */
    /**
     * Convert byte array to string
     * @param bytes {number[]}
     * @returns {string}
     */
    bytesToString(bytes: number[]): string;
    /**
     * Convert string to byte array.
     * @param str {string}
     * @returns {number[]}
     */
    stringToBytes(str: string): number[];
    /**
     * Convert byte array to hex string
     *
     * @param bytes {number[]}
     * @returns {string}
     */
    bytesToHexString(bytes: number[]): string;
}
/**
 * @hidden
 */
export declare class Ndef extends IonicNativePlugin {
    TNF_EMPTY: number;
    TNF_WELL_KNOWN: number;
    TNF_MIME_MEDIA: number;
    TNF_ABSOLUTE_URI: number;
    TNF_EXTERNAL_TYPE: number;
    TNF_UNKNOWN: number;
    TNF_UNCHANGED: number;
    TNF_RESERVED: number;
    record(tnf: number, type: number[] | string, id: number[] | string, payload: number[] | string): NdefRecord;
    textRecord(text: string, languageCode?: string, id?: number[] | string): NdefRecord;
    uriRecord(uri: string, id?: number[] | string): NdefRecord;
    absoluteUriRecord(uri: string, payload: number[] | string, id?: number[] | string): NdefRecord;
    mimeMediaRecord(mimeType: string, payload: string): NdefRecord;
    smartPoster(ndefRecords: any[], id?: number[] | string): NdefRecord;
    emptyRecord(): NdefRecord;
    androidApplicationRecord(packageName: string): NdefRecord;
    encodeMessage(ndefRecords: any): any;
    decodeMessage(bytes: any): any;
    decodeTnf(tnf_byte: any): any;
    encodeTnf(mb: any, me: any, cf: any, sr: any, il: any, tnf: any): any;
    tnfToString(tnf: any): string;
    textHelper: TextHelper;
    uriHelper: UriHelper;
}
/**
 * @hidden
 */
export declare class NfcUtil extends IonicNativePlugin {
    toHex(i: number): string;
    toPrintable(i: number): string;
    bytesToString(i: number[]): string;
    stringToBytes(s: string): number[];
    bytesToHexString(bytes: number[]): string;
    isType(record: NdefRecord, tnf: number, type: number[] | string): boolean;
    arrayBufferToHexString(buffer: ArrayBuffer): string;
    hexStringToArrayBuffer(hexString: string): ArrayBuffer;
}
export declare class TextHelper extends IonicNativePlugin {
    decodePayload(data: number[]): string;
    encodePayload(text: string, lang: string): number[];
}
export declare class UriHelper extends IonicNativePlugin {
    decodePayload(data: number[]): string;
    encodePayload(uri: string): number[];
}
