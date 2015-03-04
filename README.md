PrintNode-PHP
=============

Connect any printer to your application with our PrintNode Client and easy to use JSON API. 

www.printnode.com

This quick start guide covers using the PHP API library. It shows how to find which Computers and Printers you have available for printing and how you can submit PrintJobs using the provided PHP API libraries.

Step 1: Sign Up
Before you can use the API, you will need to sign up to PrintNode account, and make a new API key. To help get you started, you will get 50 free prints when you sign up.

Step 2: Add a computer and printer
To have somewhere to print to you need to download and install the PrintNode desktop client on a computer with some printers. You can download the PrintNode Client installer here. It should be intuitive to setup but for more detailed instructions please see here.

Step 3: Download the PHP Library
You can download the client from our Github account. If you have a git client installed locally, you can clone our repository from the Github website. Alternatively, you can also download archives of the files. You can view our Github account by clicking here.

Step 4: Include the PHP Library
The PrintNode PHP API is compatible with a PHP 5.2.0 and above. It comes with it's own spl_autoload_register compatible autoloader which makes using the API in your own code very simple. The only additional requirement for using the PrintNode PHP API is the PHP cURL extension. For instructions on installing the PHP cURL extension, please see your Operating System's documentation.

To include all the PrintNode PHP API classes in your own code, all you need to do is include/require the Loader.php in your code:

    // Include the PrintNode API. You may also use require here.
    include 'PrintNode/Loader.php';
 
    // Register the PrintNode autoloader.
    PrintNode\Loader::init();
 
    // Rest of my code below here
  
  
Step 5: Open a connection to PrintNode
You first need to establish a connection to PrintNode. To do this you will need an instance of PrintNode\Credentials populated with your PrintNode API Key username and password.

    $credentials = new PrintNode\Credentials(
        'myApiKey.123',
        '123a456b7cd89efab89cd0efa12bcd3e45fab678'
    );
  
Hint: Your API username is in the format description.integer, where description is the name given to the API key when you created it, followed by a dot (.) and an integer. All this information is provided for you when you create your API Key.


Step 6: Get a list of computers, printers or printjobs which are available.
To get a list of computers, printers or printjobs, create a new PrintNode\Request object, passing it your credentials as the argument to it's constructor.

    $request = new PrintNode\Request($credentials)

Hint: Before you can get a list of computers or printers, you must have successfully connected using the PrintNode Client software. If you have not yet connected with the client software you will not receive any results from the API.

Call the getComputers, getPrinters() or getPrintJobs() method on the object:

    $computers = $request->getComputers(); 
    $printers = $request->getPrinters(); 
    $printJobs = $request->getPrintJobs();
  
Hint: The return value from these methods is always an array containing 0 or more instances of PrintNode\Entities\Computer, PrintNode\Entities\Printer or PrintNode\Entities\PrintJob depending on the method called. You can iterate over this array however you please, for example you might use a while or foreach loop.


Step 7: Send a PrintJob to Printnode.
PrintNode currently only accepts PDF documents. You can make your own PDFs or your can download one of our sample files from here.

To print something, you need to create a new instance of PrintNode\PrintJob:

    $printJob = new PrintNode\PrintJob();
  
You can then populate this object with the information about the print-job and add the base64-encoded content of, or the URI to your PDF. To do this use the properties as defined on the object. In this example, we're going to print a a base64-encoded PDF named invoice.pdf:

    $printJob->printer = $printers[1]; 
    $printJob->contentType = 'pdf_base64'; 
    $printJob->content = base64_encode(file_get_contents('invoice.pdf')); 
    $printJob->source = 'My App/1.0'; 
    $printJob->title = 'Test PrintJob from My App/1.0';
  
Hint: The PrintNode PHP API comes complete with PHPDoc comments. If you have an editor that supports PHPDoc code completion, you should see hints for the properties and method names on each of the objects.
Once you have populated the object, all that's left to do is submit it:

    $response = $request->post($printJob);

The response returned from the post method is an instance of PrintNode\Response. It contains methods for retrieving the response headers, body and HTTP status-code and message.

    // Returns an array contain the keys 'code' and 'message'
    $status = $response->getStatus();
 
    // Returns the HTTP status code.
    $statusCode = $response->getStatusCode();
 
    // Returns the HTTP status message.
    $statusMessage = $response->getStatusMessage();
 
    // Returns an array of HTTP headers.
    $headers = $response->getHeaders();
 
    // Return the response body.
    $content = $response->getContent();
