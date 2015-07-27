# Examples Using the PrintNode PHP Library
These example shows how to submit a remote printjob from your PHP application to a remote computer and control PrintNode Client accounts.

## Prerequisites
You must install dependencies, add your PrintNode credentials and download example PDF before start using this example:

```bash
composer install
cp credentials.php.dist credentials.php
nano credentials.php
wget -O a4_portrait.pdf https://app.printnode.com/testpdfs/a4_portrait.pdf
```

## Submiting A Printjob

```bash
php example-1-submitting-a-printjob.php
```

## Creating PrintNode Child Account


```bash
php example-2-creating-a-child-account.php
```

## Manipulating PrintNode Client Accounts

```bash
example-3-manipulating-child-accounts.php
```



