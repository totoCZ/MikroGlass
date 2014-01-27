; <?php exit(); __halt_compiler();

[plink]
; putty link only
; do not set for ssh
; path=./plink   ; linux
; path=plink.exe ; windows

[branding]
title="MikroGlass Looking Glass DEMO"
name="MikroGlass"
companyUrl="//github.com/TomHetmer/MikroGlass/"
logo="//placehold.it/150x50&text=Demo+server"
customText="<h3><a href='//github.com/TomHetmer/MikroGlass/releases'>Download</a></h3>"

[login]
user=mguser270114

; only for putty link
; leave empty for ssh on linux
password=

[servers]
fqdn[0]=193.187.80.16
fqdn[1]=demo.mt.lv
fqdn[2]=demo2.mt.lv
