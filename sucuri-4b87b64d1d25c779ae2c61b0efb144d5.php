<?php
/* Sucuri integrity monitor
 * Integrity checking and server side scanning.
 *
 * Copyright (C) 2010, 2011, 2012 Sucuri, LLC
 * http://sucuri.net
 * Do not distribute or share.
 */

$MYMONITOR = "monitor20";
$my_sucuri_encoding = "



LyogU3VjdXJpIGludGVncml0eSBtb25pdG9yIC4gCiAqIENvbm5lY3RzIGJhY2sgaG9tZS4KICog
Q29weXJpZ2h0IChDKSAyMDEwLTIwMTMgU3VjdXJpLCBMTEMKICogRG8gbm90IGRpc3RyaWJ1dGUg
b3Igc2hhcmUuCiAqLwoKCiRTVUNVUklQV0QgPSAiNTM1ZTI2ZWIwZjMzMjRlODA1NmZlNjk1NDk3
NmIwNGM2MmViOTc3ZjJjYzUxIjsKCgppZihpc3NldCgkX0dFVFsndGVzdCddKSkKewogICAgZWNo
byAiT0s6IFN1Y3VyaTogRm91bmRcbiI7CiAgICBleGl0KDApOwp9CgoKCi8qIFZhbGlkIGFyZ3Vt
ZW50LiAqLwppZighaXNzZXQoJF9HRVRbJ3J1biddKSkKewogICAgZXhpdCgwKTsKfQoKCi8qIE11
c3QgaGF2ZSBhbiBJUCBhZGRyZXNzLiAqLwppZighaXNzZXQoJF9TRVJWRVJbJ1JFTU9URV9BRERS
J10pKQp7CiAgICBleGl0KDApOwp9Cgokb3JpZ3JlbW90ZWlwID0gJF9TRVJWRVJbJ1JFTU9URV9B
RERSJ107CgovKiBJZiBjb21pbmcgZnJvbSBjbG91ZGZsYXJlOiAqLwppZihpc3NldCgkX1NFUlZF
UlsnSFRUUF9DRl9DT05ORUNUSU5HX0lQJ10pKQp7CiAgICAkX1NFUlZFUlsnUkVNT1RFX0FERFIn
XSA9ICRfU0VSVkVSWydIVFRQX0NGX0NPTk5FQ1RJTkdfSVAnXTsKfQovKiBDbG91ZFByb3h5IGhl
YWRlcnMuICovCmVsc2UgaWYoaXNzZXQoJF9TRVJWRVJbJ0hUVFBfWF9TVUNVUklfQ0xJRU5USVAn
XSkpCnsKICAgICRfU0VSVkVSWydSRU1PVEVfQUREUiddID0gJF9TRVJWRVJbJ0hUVFBfWF9TVUNV
UklfQ0xJRU5USVAnXTsKfQovKiBNb3JlIGdhdGV3YXkgcmVxdWVzdHMuICovCmVsc2UgaWYoaXNz
ZXQoJF9TRVJWRVJbJ0hUVFBfWF9PUklHX0NMSUVOVF9JUCddKSkKewogICAgJF9TRVJWRVJbJ1JF
TU9URV9BRERSJ10gPSAkX1NFUlZFUlsnSFRUUF9YX09SSUdfQ0xJRU5UX0lQJ107Cn0KZWxzZSBp
Zihpc3NldCgkX1NFUlZFUlsnSFRUUF9DTElFTlRfSVAnXSkpCnsKICAgICRfU0VSVkVSWydSRU1P
VEVfQUREUiddID0gJF9TRVJWRVJbJ0hUVFBfQ0xJRU5UX0lQJ107Cn0KLyogUHJveHkgcmVxdWVz
dHMuICovCmVsc2UgaWYoaXNzZXQoJF9TRVJWRVJbJ0hUVFBfVFJVRV9DTElFTlRfSVAnXSkpCnsK
ICAgICRfU0VSVkVSWydSRU1PVEVfQUREUiddID0gJF9TRVJWRVJbJ0hUVFBfVFJVRV9DTElFTlRf
SVAnXTsKfQovKiBQcm94eSByZXF1ZXN0cy4gKi8KZWxzZSBpZihpc3NldCgkX1NFUlZFUlsnSFRU
UF9YX1JFQUxfSVAnXSkpCnsKICAgICRfU0VSVkVSWydSRU1PVEVfQUREUiddID0gJF9TRVJWRVJb
J0hUVFBfWF9SRUFMX0lQJ107Cn0KLyogTW9yZSBnYXRld2F5IHJlcXVlc3RzLiAqLwplbHNlIGlm
KGlzc2V0KCRfU0VSVkVSWydIVFRQX1hfRk9SV0FSREVEX0ZPUiddKSkKewogICAgJF9TRVJWRVJb
J1JFTU9URV9BRERSJ10gPSAkX1NFUlZFUlsnSFRUUF9YX0ZPUldBUkRFRF9GT1InXTsKfQoKCiRt
eWlwbGlzdCA9IGFycmF5KAonOTcuNzQuMTI3LjE3MScsCic2OS4xNjQuMjAzLjE3MicsCicxNzMu
MjMwLjEyOC4xMzUnLAonNjYuMjI4LjM0LjQ5JywKJzY2LjIyOC40MC4xODUnLAonNTAuMTE2LjMu
MTcxJywKJzUwLjExNi4zNi45MicsCicxOTguNTguOTYuMjEyJywKJzUwLjExNi42My4yMjEnLAon
MTkyLjE1NS45Mi4xMTInLAonMTkyLjgxLjEyOC4zMScsCicxOTguNTguMTA2LjI0NCcsCicxMDQu
MjM3LjE0My4yNDInLAonMTA0LjIzNy4xMzkuMjI3JywKJzI2MDA6M2MwMDo6ZjAzYzo5MWZmOmZl
YWU6ZTEwNCcsCicyNjAwOjNjMDA6OmYwM2M6OTFmZjpmZTg0OmUyNzUnLAonMjYwMDozYzAzOjpm
MDNjOjkxZmY6ZmVlNDpjOWYwJywKJzI2MDA6M2MwMjo6ZjAzYzo5MWZmOmZlZTQ6Yzk5OCcsCicy
NjAwOjNjMDA6OmYwM2M6OTFmZjpmZTg0OmUyMTgnLAonMjYwMDozYzAyOjpmMDNjOjkxZmY6ZmVk
Zjo1OGM2JywKJzI2MDA6M2MwMjo6ZjAzYzo5MWZmOmZlZGY6NTgzNScsCicyNjAwOjNjMDM6OmYw
M2M6OTFmZjpmZWRmOjZhN2EnLAonZmU4MDo6ZmNmZDphZGZmOmZlZTY6ODA4NycsCicyNjAwOjNj
MDM6OmYwM2M6OTFmZjpmZTcwOjM2Y2UnLAonMjYwMDozYzAyOjpmMDNjOjkxZmY6ZmU3MDpmMTJk
JywKJzI2MDA6M2MwMTo6ZjAzYzo5MWZmOmZlNzA6NTJiYicsCic1MC4xMTYuMzYuOTMnLAoiMTky
LjE1NS45NS4xMzkiLAoiMjYwMDozYzAyOjpmMDNjOjkxZmY6ZmU2OTo0YjY2IiwKIjI2MDA6M2Mw
MDo6ZjAzYzo5MWZmOmZlNzA6NTIxMyIsCiIyNjAwOjNjMDM6OmYwM2M6OTFmZjpmZWRiOmI5Y2Ui
LAoiMjMuMjM5LjkuMjI3IiwKIjE5OC41OC4xMTIuMTAzIiwKIjE5Mi4xNTUuOTQuNDMiLAoiMTYy
LjIxNi4xNi4zMyIsCiI0NS43OS4yMTAuNTciLAoiNDUuMzMuNzYuMTciLAoiMjYwMDozYzAwOjpm
MDNjOjkxZmY6ZmU2ZTphMDQ2IiwKIjI2MDA6M2MwMjo6ZjAzYzo5MWZmOmZlNmU6YTBkZCIsCiIy
NjAwOjNjMDM6OmYwM2M6OTFmZjpmZTZlOmEwYWMiLAopOwoKCiRpcG1hdGNoZXMgPSAwOwoKZm9y
ZWFjaCgkbXlpcGxpc3QgYXMgJG15aXApCnsKICAgIGlmKHN0cnBvcygkX1NFUlZFUlsnUkVNT1RF
X0FERFInXSwgJG15aXApICE9PSBGQUxTRSkKICAgIHsKICAgICAgICAkaXBtYXRjaGVzID0gMTsK
ICAgICAgICBicmVhazsKICAgIH0KICAgIGlmKHN0cnBvcygkb3JpZ3JlbW90ZWlwLCAkbXlpcCkg
IT09IEZBTFNFKQogICAgewogICAgICAgICRpcG1hdGNoZXMgPSAxOwogICAgICAgIGJyZWFrOwog
ICAgfQp9CgoKaWYoJGlwbWF0Y2hlcyA9PSAwKQp7CiAgICBlY2hvICJFUlJPUjogSW52YWxpZCBJ
UCBBZGRyZXNzXG4iOwogICAgZXhpdCgwKTsKfQoKCi8qIFZhbGlkIGtleS4gKi8KaWYoIWlzc2V0
KCRfUE9TVFsnc3NjcmVkJ10pKQp7CiAgICBlY2hvICJFUlJPUjogSW52YWxpZCBhcmd1bWVudFxu
IjsKICAgIGV4aXQoMCk7Cn0KCgovKiBDb25uZWN0IGJhY2sgdG8gZ2V0IHdoYXQgdG8gcnVuLiAq
LwppZighZnVuY3Rpb25fZXhpc3RzKCdjdXJsX2V4ZWMnKSB8fCBpc3NldCgkX0dFVFsnbm9jdXJs
J10pKQp7CiAgICAkcG9zdGRhdGEgPSBodHRwX2J1aWxkX3F1ZXJ5KAogICAgICAgICAgICBhcnJh
eSgKICAgICAgICAgICAgICAgICdwJyA9PiAkU1VDVVJJUFdELAogICAgICAgICAgICAgICAgJ3En
ID0+ICRfUE9TVFsnc3NjcmVkJ10sCiAgICAgICAgICAgICAgICApCiAgICAgICAgICAgICk7Cgog
ICAgJG9wdHMgPSBhcnJheSgnaHR0cCcgPT4KICAgICAgICAgICAgYXJyYXkoCiAgICAgICAgICAg
ICAgICAnbWV0aG9kJyAgPT4gJ1BPU1QnLAogICAgICAgICAgICAgICAgJ2hlYWRlcicgID0+ICdD
b250ZW50LXR5cGU6IGFwcGxpY2F0aW9uL3gtd3d3LWZvcm0tdXJsZW5jb2RlZCcsCiAgICAgICAg
ICAgICAgICAnY29udGVudCcgPT4gJHBvc3RkYXRhCiAgICAgICAgICAgICAgICApCiAgICAgICAg
ICAgICk7CgogICAgJGNvbnRleHQgPSBzdHJlYW1fY29udGV4dF9jcmVhdGUoJG9wdHMpOwogICAg
JG15X3N1Y3VyaV9lbmNvZGluZyA9IGZpbGVfZ2V0X2NvbnRlbnRzKCJodHRwczovLyRNWU1PTklU
T1Iuc3VjdXJpLm5ldC9pbW9uaXRvciIsIGZhbHNlLCAkY29udGV4dCk7CgogICAgaWYoc3RybmNt
cCgkbXlfc3VjdXJpX2VuY29kaW5nLCAiV09SS0VEOiIsNykgPT0gMCkKICAgIHsKICAgIH0KICAg
IGVsc2UKICAgIHsKICAgICAgICBlY2hvICJFUlJPUjogVW5hYmxlIHRvIGNvbXBsZXRlIChtaXNz
aW5nIGN1cmwgc3VwcG9ydCBhbmQgZmlsZV9nZXQgZmFpbGVkKS4gUGxlYXNlIGNvbnRhY3Qgc3Vw
cG9ydEBzdWN1cmkubmV0IGZvciBndWlkYW5jZS5cbiI7CiAgICAgICAgZXhpdCgxKTsKICAgIH0K
fQoKZWxzZQp7CgogICAgJGNoID0gY3VybF9pbml0KCk7CiAgICBjdXJsX3NldG9wdCgkY2gsIENV
UkxPUFRfVVJMLCAiaHR0cHM6Ly8kTVlNT05JVE9SLnN1Y3VyaS5uZXQvaW1vbml0b3IiKTsKICAg
IGN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9SRVRVUk5UUkFOU0ZFUiwgdHJ1ZSk7CiAgICBjdXJs
X3NldG9wdCgkY2gsIENVUkxPUFRfUE9TVCwgdHJ1ZSk7CiAgICBjdXJsX3NldG9wdCgkY2gsIENV
UkxPUFRfUE9TVEZJRUxEUywgInA9JFNVQ1VSSVBXRCZxPSIuJF9QT1NUWydzc2NyZWQnXSk7IAog
ICAgY3VybF9zZXRvcHQoJGNoLCBDVVJMT1BUX1NTTF9WRVJJRllQRUVSLCBmYWxzZSk7CgogICAg
JG15X3N1Y3VyaV9lbmNvZGluZyA9IGN1cmxfZXhlYygkY2gpOwogICAgY3VybF9jbG9zZSgkY2gp
OwoKICAgIGlmKHN0cm5jbXAoJG15X3N1Y3VyaV9lbmNvZGluZywgIldPUktFRDoiLDcpID09IDAp
CiAgICB7CiAgICB9CiAgICBlbHNlCiAgICB7CiAgICAgICAgaWYoJG15X3N1Y3VyaV9lbmNvZGlu
ZyA9PSBOVUxMIHx8IHN0cmxlbigkbXlfc3VjdXJpX2VuY29kaW5nKSA8IDMpCiAgICAgICAgewog
ICAgICAgICAgICAkbXlfc3VjdXJpX2VuY29kaW5nID0gIngyMzUxIjsKICAgICAgICB9CiAgICAg
ICAgZWNobyAiRVJST1I6IFVuYWJsZSB0byBjb25uZWN0IGJhY2sgdG8gU3VjdXJpIChlcnJvcjog
JG15X3N1Y3VyaV9lbmNvZGluZykuIFBsZWFzZSBjb250YWN0IHN1cHBvcnRAc3VjdXJpLm5ldCBm
b3IgZ3VpZGFuY2UuXG4iOwogICAgICAgIGV4aXQoMSk7CiAgICB9Cn0KCgokbXlfc3VjdXJpX2Vu
Y29kaW5nID0gIGJhc2U2NF9kZWNvZGUoCiAgICAgICAgICAgICAgICAgICAgICAgc3Vic3RyKCRt
eV9zdWN1cmlfZW5jb2RpbmcsIDcpKTsKCgpldmFsKAogICAgJG15X3N1Y3VyaV9lbmNvZGluZwog
ICAgKTsK

";

/* Encoded to avoid that it gets flagged by AV products or even ourselves :) */
$tempb64 =  
           base64_decode(
                          $my_sucuri_encoding);

eval(  $tempb64 
      );
exit(0); ?>    
    
