<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>Jupiter Ionosphere/Magnetosphere Online Mapping Tool - Beta Version</title>
<link rel="stylesheet" href="style.css" type="text/css" />
<link rel="stylesheet" href="layout.css" type="text/css" media="screen" />
</head>
<body>

<?

echo '<h3>Thank you for requesting information about our Jupiter auroral mapping IDL mapping code.</h3> <p>';
echo '<p>';


    echo 'A download link will shortly be sent to ' .$_POST['username'];
    echo ' at ' .$_POST['email_contact'];

    echo '<p>';

    echo 'Please e-mail mvogt@bu.edu with any questions or if you do not receive the download link.';

    echo '<p>';

    echo 'Finally, please let Marissa Vogt know if you share this code with colleagues or students so that she can keep track of who is using the code and send out updates and error fixes as appropriate.';

    echo '<p>';


if ($username_set == 1) {
    $subject = 'Download link for Jupiter auroral mapping IDL code, requested by' .$_POST['username'];
}
else
{
    $subject = 'Download link for Jupiter auroral mapping IDL code';
}

$message .= "<p>Message sent to ";
$message .= $_POST['username'];
$message .= " (";
$message .= $_POST['email_contact'];
$message .= ")";


    $message .= "<p>Thank you for requesting information about the Jupiter auroral mapping IDL code.";
    $message .= "<p>";

    $message .= "You can download the IDL mapping code at: https://drive.google.com/open?id=1Xo0q63qKOcC_aUE5j4j_0VggPQr07Hw-";
    $message .= "<br>";
    $message .= "This program is current as of May 2020.";

    $message .= "<p>If you share this code with colleagues or students, please let Marissa Vogt know so that she can keep track of who is using the code and send out updates and error fixes as appropriate.";

$message .= "<p>When using these results in a presentation or publication, please cite: <br>";
$message .= "Vogt, Marissa F., Margaret G. Kivelson, Krishan K. Khurana, Raymond J. Walker, Bertrand Bonfond, Denis Grodent, and Aikaterini Radioti (2010), Improved mapping of Jupiters auroral features to magnetospheric sources, J. Geophys. Res., 116, A03220, doi:10.1029/2010JA016148.<br>";
$message .= "and<br>";
$message .= "Vogt, Marissa F., Emma J. Bunce, Margaret G. Kivelson, Krishan K. Khurana, Raymond J. Walker, Aikaterini Radioti, Bertrand Bonfond, and Denis Grodent (2015), Magnetosphere-ionosphere mapping at Jupiter: Quantifying the effects of using different internal field models, J. Geophys. Res. Space Physics, doi:10.1002/2014JA020729.<p>";


$message .= "<p>Please send any comments or questions to mvogt@bu.edu";



// Send
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: marissav@ucla.edu <marissav@ucla.edu>' . "\r\n";
$headers .= 'Reply-To: mvogt@bu.edu' . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion();


if (isset($_POST['email_contact']))
{
	mail($_POST['email_contact'], $subject, $message, $headers);
}

mail('marissav@ucla.edu', $subject, $message, $headers);
mail('mvogt@bu.edu', $subject, $message, $headers);


echo '<p><a href="index.html">Back to mapping form</a>';
echo '<br>';
echo '<a href="http://sites.bu.edu/marissavogt/">Back to Marissa Vogts website at BU</a>';

?>
