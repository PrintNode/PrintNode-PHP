echo "Installing printnode-php via composer"
composer install
echo "copying credentials.php.dist to credentials.php"
cp credentials.php.dist credentials.php
echo "Enter apikey for testing:"
read apikey
sed -i 's/myApiKey.123/'$apikey'/g' credentials.php
echo "Getting test pdf"
wget -O a4_portrait.pdf https://app.printnode.com/testpdfs/a4_portrait.pdf
echo "Now you can run php index.php and see the results."

