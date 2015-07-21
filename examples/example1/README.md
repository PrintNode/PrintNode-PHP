# Example #1
This example shows how to submit a remote printjob from your PHP application to a remote computer.

## Prerequisites
You must install dependencies, add your PrintNode credentials and download example PDF before start using this example:

```bash
composer install
cp credentials.php.dist credentials.php
nano credentials.php
wget -O a4_portrait.pdf https://app.printnode.com/testpdfs/a4_portrait.pdf
```

## Running 

```bash
php index.php
```
