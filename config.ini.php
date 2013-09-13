; <?php exit(); __halt_compiler();

[plink]
; putty link only
; do not set for ssh

; path=./plink   ; linux
; path=plink.exe ; windows

[branding]
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
fqdn[0]=eth1.ipv6core1.nsw.bigair.net.au
fqdn[1]=125.253.108.85
fqdn[2]=125.253.99.60
