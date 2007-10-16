<?PHP
/*
 * sendverificationeamil.php - Author: Greg von Beck
 *
 * Copyright (C) 2001 PlaneShift Team (info@planeshift.it,
 * http://www.planeshift.it)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation (version 2 of the License)
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * Creation Date : 10/6/03
 * Description : This page creates and sends out a verification e-mail.
 */
?>

<?PHP
// send email

function sendVerificationEmail($email, $verificationid,$forgot="no")
{  
    
$path = "http://" . $_SERVER['HTTP_HOST'];
$path = $path . substr($_SERVER['REQUEST_URI'],0,strrpos($_SERVER['REQUEST_URI'],"/")+1);
$path = $path . "verifyaccount.php?username=" . $email . "&verificationid=" . $verificationid;

if ($forgot=="yes") {
$path = $path . "&forgot=yes";
}

$messagebody = "Welcome to the PlaneShift Crystal Blue internal testing!\r\n" .
               "\r\n" .
	       "Your username is : " . $email . "\r\n" .
	       "Your Verification ID is : " . $verificationid .
	       "\r\nPlease note that you will not be asked for this ID because it's in the ".
	       "link below, it's only for evidence that you got this mail and that you own this account\r\n" .
	       "\r\n";

if ($forgot=="yes") {
        $messagebody = $messagebody . "Use the link to reset your password.\r\n";
} else {
  $messagebody = $messagebody . "Use the link to verify your account so you can begin playing.\r\n";
}
	       $messagebody = $messagebody . "\r\n" .
	       $path . "\r\n" .
	       "\r\n" .
	       "The PlaneShift Dev Team";

$headers = "From: PlaneShift <noreply@planeshift.it>\r\n" .
           "Reply-To: PlaneShift <noreply@planeshift.it>\r\n";
           
$subject = "PlaneShift Account Verification";
mail($email, $subject, $messagebody, $headers, "-fbounce_verify");
}
?>

