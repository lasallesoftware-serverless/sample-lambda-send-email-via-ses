<?php

/**
 * This file is part of the Lasalle Software Serverless Sending email via SES sample PHP Lambda function.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  (c) 2021-2023 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://phpserverlessproject.com
 * @link       https://packagist.org/packages/lasallesoftware-serverless/sample-lambda-send-email-via-ses
 * @link       https://github.com/lasallesoftware-serverless/sample-lambda-send-email-via-ses
 *
 */

if (isset($_SERVER['LAMBDA_TASK_ROOT'])) {
    // For Lambda
    require_once './../vendor/autoload.php';
} else {1
    // For your local development
    require_once '/var/task/vendor/autoload.php';
}


// Set the vars
$region = 'us-east-1';   // same region that you specified in the serverless.yml
$version = '2010-12-01';

// This email address must be verified with Amazon SES, regardless of your SES being in the sandbox.
$sender_email = 'sender@email.com';

// IMPORTANT: If your account is still in the sandbox, these addresses must be verified.
// https://docs.aws.amazon.com/ses/latest/DeveloperGuide/request-production-access.html
$recipient_emails = ['recipient1@email.com', 'recipient2@email.com'];

// Email parameters
$subject = 'Email subject line';
$plaintext_body = 'Email body in plain text format.' ;
$html_body =  '<h1>Email body in HTML format</h1>'.
              '<p>This email was sent via AWS SES.</p>';
              '<p>This email was sent from your sample-lambda-send-email-via-ses PHP Lambda function.</p>';
$char_set = 'UTF-8';


// Create the SES client
if (isset($_SERVER['LAMBDA_TASK_ROOT'])) {

    // For Lambda
    $SesClient = new \Aws\Ses\SesClient([
        'region'  => $region,
        'version' => $version,
    ]);

} else {

    // For your local development
    $SesClient = new \Aws\Ses\SesClient([
        'profile' => 'default',
        'region'  => $region,
        'version' => $version,
    ]);

}


// Send the email
// https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sesv2-2019-09-27.html#sendemail
try {
    $result =  $SesClient->sendEmail([
        'Destination' => [
            'ToAddresses' => $recipient_emails,
        ],
        'ReplyToAddresses' => [$sender_email],
        'Source' => $sender_email,
        'Message' => [
          'Body' => [
              'Html' => [
                  'Charset' => $char_set,
                  'Data' => $html_body,
              ],
              'Text' => [
                  'Charset' => $char_set,
                  'Data' => $plaintext_body,
              ],
          ],
          'Subject' => [
              'Charset' => $char_set,
              'Data' => $subject,
          ],
        ],
    ]);
    var_dump($result);

} catch (\Aws\Exception\AwsException $e) {
    
    if (isset($_SERVER['LAMBDA_TASK_ROOT'])) {
        
        // for Lambda, log the error message
        error_log($e->getMessage());

    } else {

         // for your local development, output error message
        var_dump($e->getMessage());
    }
}