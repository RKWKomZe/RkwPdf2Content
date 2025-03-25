# Installation
Ensure you have Java Runtime Environement installed on the server.
It has been tested successfully with version 18 on Ubuntu 22.04 LTS
```
apt install openjdk-18-jre-headless
```
## DDEV
Add the java-sdk to your DDEV config.yaml. 
Version 17 of "openjdk" has been tested successfully with TYPO3 10.4. and Debian 12 (bookworm).
```
hooks:
  post-start:
    # for rkw_pdf2content; openjdk-17 is currently the default version for Debian 12
    - exec: sudo apt install openjdk-17-jre-headless
```
