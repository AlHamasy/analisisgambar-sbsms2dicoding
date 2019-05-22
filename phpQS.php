<?php
/**----------------------------------------------------------------------------------
* Microsoft Developer & Platform Evangelism
*
* Copyright (c) Microsoft Corporation. All rights reserved.
*
* THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND,
* EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE IMPLIED WARRANTIES
* OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
*----------------------------------------------------------------------------------
* The example companies, organizations, products, domain names,
* e-mail addresses, logos, people, places, and events depicted
* herein are fictitious.  No association with any real company,
* organization, product, domain name, email address, logo, person,
* places, or events is intended or should be inferred.
*----------------------------------------------------------------------------------
**/

/** -------------------------------------------------------------
# Azure Storage Blob Sample - Demonstrate how to use the Blob Storage service.
# Blob storage stores unstructured data such as text, binary data, documents or media files.
# Blobs can be accessed from anywhere in the world via HTTP or HTTPS.
#
# Documentation References:
#  - Associated Article - https://docs.microsoft.com/en-us/azure/storage/blobs/storage-quickstart-blobs-php
#  - What is a Storage Account - http://azure.microsoft.com/en-us/documentation/articles/storage-whatis-account/
#  - Getting Started with Blobs - https://azure.microsoft.com/en-us/documentation/articles/storage-php-how-to-use-blobs/
#  - Blob Service Concepts - http://msdn.microsoft.com/en-us/library/dd179376.aspx
#  - Blob Service REST API - http://msdn.microsoft.com/en-us/library/dd135733.aspx
#  - Blob Service PHP API - https://github.com/Azure/azure-storage-php
#  - Storage Emulator - http://azure.microsoft.com/en-us/documentation/articles/storage-use-emulator/
#
**/

require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=".'asadwebstorage'.";AccountKey=".'LneTLXrh9Oo7ffoUPI2t4KacWivUt3F6nU8knCmTue9LNQxRTWJD3FBF/LY7tTL2f0OGbwaMwfhM3+9AO2c3Tw==';

// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);

// $fileToUpload = "HelloWorld.txt";


// Check if image file is a actual image or fake image
if(isset($_POST)) {

  $target_dir = "uploads/";
  $target_file = $target_dir . basename($_FILES["inputImage"]["name"]);
  $fileToUpload = $target_file;
  $uploadOk = 1;

  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  move_uploaded_file($_FILES["inputImage"]["tmp_name"], $target_file);

  // die();

    $check = $target_file;
    if($check !== false) {
 $createContainerOptions = new CreateContainerOptions();
 $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
$containerName = "blooobbb";

try {
        // Create container.
        // $blobClient->createContainer($containerName, $createContainerOptions);
        // Getting local file so that we can upload it to Azure
        $myfile = fopen($fileToUpload, "r") or die("Unable to open file!");
        fclose($myfile);

        # Upload file as a block blob
        // echo "Uploading BlockBlob: ".PHP_EOL;
        // echo $fileToUpload;
        // echo "<br />";

        $content = fopen($fileToUpload, "r");
        //Upload blob
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
        // List blobs.
        $listBlobsOptions = new ListBlobsOptions();
        $listBlobsOptions->setPrefix($fileToUpload);
        // echo "These are the blobs present in the container: ";
        do{
            $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
            foreach ($result->getBlobs() as $blob)
            {
                echo $blob->getUrl();
            }

            $listBlobsOptions->setContinuationToken($result->getContinuationToken());
        } while($result->getContinuationToken());
        // echo "<br />";
        // Get blob.
        $blob = $blobClient->getBlob($containerName, $fileToUpload);
        // echo "<br />";
    }
    catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
    catch(InvalidArgumentTypeException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }


    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
