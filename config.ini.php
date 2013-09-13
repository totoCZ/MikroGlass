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
customText="<h3><a href='//github.com/TomHetmer/MikroGlass/releases'>Download</a> Something to say? tom@runtimeapp.com (tell us about your install!)</h3>"

[login]
user=admin

; only for putty link
; leave empty for ssh on linux
password=

[servers]
fqdn[0]=78.156.159.65
fqdn[1]=demo.mt.lv
fqdn[2]=demo2.mt.lv
