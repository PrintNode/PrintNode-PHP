# Example #2 - Manipulating PrintNode Child Accounts
This example shows how to use the PHP Library to control PrintNode Client accounts.  If you are integrating PrintNode cloud printing into your own product you will want multiple accounts - one for each of your customers.  You will need to have an Intergrator account for this example to work.  

## Prerequisites
You must install:

 - dependencies, write your credentials and download example PDF before start using this example:

```bash
composer install
cp credentials.php.dist credentials.php
nano credentials.php
wget -O a4_portrait.pdf https://app.printnode.com/testpdfs/a4_portrait.pdf
```
