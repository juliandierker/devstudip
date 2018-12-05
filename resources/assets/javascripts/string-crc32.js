/*jslint browser: true, bitwise: true */
(function (String) {
    'use strict';

    function makeCRCTable() {
        var c,
            crcTable = [],
            n,
            k;
        for (n = 0; n < 256; n += 1) {
            c = n;
            for (k = 0; k < 8; k += 1) {
                c = ((c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1));
            }
            crcTable[n] = c;
        }
        return crcTable;
    }

    var crcTable = makeCRCTable();

    String.prototype.crc32 = function () {
        var crc = 0 ^ (-1),
            i;

        for (i = 0; i < this.length; i += 1) {
            crc = (crc >>> 8) ^ crcTable[(crc ^ this.charCodeAt(i)) & 0xFF];
        }

        return (crc ^ (-1)) >>> 0;
    };

}(String));
