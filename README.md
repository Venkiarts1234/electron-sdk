# electron-sdk
BAFTA Electron Software Development Kits

## PHP

Example code can be found under `S2Sv2-PHP-SDK / examples`

For neatness sake I split the HTML and JavaScript out into separate files within the `html/` and `js/` folders.

### Uploading

So far there are 2 examples for uploading:

#### UploadWithElectronLogin.php

The purpose of this script is to demonstrate a fully customisable customer hosted script for uploading into BAFTA Electron with a server side login.

Username and password can be provided in this script or left blank to default to the credentials in S2Sv2-PHP-SDK/config.php
		
You can optionally enter a custom path location and filename in the 'path' and 'file_name' input text boxes.

#### UploadWithUploadLink.php

The purpose of this script is to demonstrate a fully customisable customer hosted script for uploading into BAFTA Electron with Upload Link details.

No login is required for this script as is the nature of an upload link.

## API Documentation

For horribly out-of-date API documentation, visit https://docs.baftaelectron.com
