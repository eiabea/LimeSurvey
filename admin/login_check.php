<?php
/*
<<<<<<< HEAD
 * LimeSurvey
 * Copyright (C) 2007 The LimeSurvey Project Team / Carsten Schmitz
 * All rights reserved.
 * License: GNU/GPL License v2 or later, see LICENSE.php
 * LimeSurvey is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 *
 * $Id$
 */
=======
* LimeSurvey
* Copyright (C) 2007 The LimeSurvey Project Team / Carsten Schmitz
* All rights reserved.
* License: GNU/GPL License v2 or later, see LICENSE.php
* LimeSurvey is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*
* $Id$
*/
>>>>>>> refs/heads/stable_plus


if (!isset($dbprefix) || isset($_REQUEST['dbprefix'])) {die("Cannot run this script directly");}
if (!isset($action)) {$action=returnglobal('action');}

<<<<<<< HEAD



/*
 * New feature since version 1.81: One time passwords
 * The user can call the limesurvey login at /limesurvey/admin and pass username and
 * a one time password which was previously written into the users table (column one_time_pw) by
 * an external application.
 * Furthermore there is a setting in config-defaults which has to be turned on (default = off)
 * to enable the usage of one time passwords.
 */

//check if data was passed by URL
if(isset($_GET['user']) && isset($_GET['onepass']))
{
    //take care of passed data
    $user = sanitize_user($_GET['user']);
    $pw = sanitize_paranoid_string(md5($_GET['onepass']));

    //check if setting $use_one_time_passwords exists in config file
    if(isset($use_one_time_passwords))
    {
        //$use_one_time_passwords switched OFF but data was passed by URL: Show error message
        if($use_one_time_passwords === false)
        {
            //create an error message
            $loginsummary = "<br />".$clang->gT("Data for username and one time password was received but the usage of one time passwords is disabled at your configuration settings. Please add the following line to config.php to enable one time passwords: ")."<br />";
            $loginsummary .= '<br /><em>$use_one_time_passwords = true;</em><br />';
            $loginsummary .= "<br /><br /><a href='$scriptname'>".$clang->gT("Continue")."</a><br />&nbsp;\n";
        }
        //Data was passed, using one time passwords is enabled
        else
        {
            //check if user exists in DB
            $query = "SELECT uid, users_name, password, one_time_pw, dateformat, full_name, htmleditormode FROM ".db_table_name('users')." WHERE users_name=".$connect->qstr($user);
            $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC; //Checked
            $result = $connect->SelectLimit($query, 1) or safe_die ($query."<br />".$connect->ErrorMsg());
            if(!$result)
            {
                echo "<br />".$connect->ErrorMsg();
            }
            if ($result->RecordCount() < 1)
            {
                // wrong or unknown username
                $loginsummary = sprintf($clang->gT("No one-time password found for user %s"),htmlspecialchars($user))."<br />";
                if ($sessionhandler=='db')
                {
                    adodb_session_regenerate_id();
                }
                else
                {
                    session_regenerate_id();
                }
            }
            else
            {
                //get one time pw from db
                $srow = $result->FetchRow();
                $otpw = $srow['one_time_pw'];

                //check if passed password and one time password from database DON'T match
                if($pw != $otpw)
                {
                    //no match -> warning
                    $loginsummary = "<p>".$clang->gT("Passed single-use password was wrong or user doesn't exist")."<br />";
                    $loginsummary .= "<br /><br /><a href='$scriptname'>".$clang->gT("Continue")."</a><br />&nbsp;\n";
                }
                //both passwords match
                else
                {

                    //delete one time password in database
                    $uquery = "UPDATE ".db_table_name('users')."
					SET one_time_pw=''
					WHERE users_name='".db_quote($user)."'";

                    $uresult = $connect->Execute($uquery);

                    //data necessary for following functions
                    $_SESSION['user'] = $srow['users_name'];
                    $_SESSION['checksessionpost'] = sRandomChars(10);
                    $_SESSION['loginID'] = $srow['uid'];
                    $_SESSION['dateformat'] = $srow['dateformat'];
                    $_SESSION['htmleditormode'] = $srow['htmleditormode'];
                    $_SESSION['full_name'] = $srow['full_name'];
                    GetSessionUserRights($_SESSION['loginID']);

                    // Check if the user has changed his default password
                    if (strtolower($srow['password'])=='password')
                    {
                        $_SESSION['pw_notify']=true;
						$_SESSION['flashmessage']=$clang->gT("Warning: You are still using the default password ('password'). Please change your password and re-login again.");
                    }
                    else
                    {
                        $_SESSION['pw_notify']=false;
                    }

                    //delete passed information
                    unset($_GET['user']);
                    unset($_GET['onepass']);

                }	//else -> passwords match

            }	//else -> password found

        }	//else -> one time passwords enabled

    }	//else -> one time passwords set

}	//else -> data was passed by URL





// check data for login
if( isset($_POST['user']) && isset($_POST['password']) ||
($action == "forgotpass") || ($action == "login") ||
($action == "logout") ||
($useWebserverAuth === true && !isset($_SESSION['loginID'])) )
{
    include("usercontrol.php");
}




// login form
if(!isset($_SESSION['loginID']) && $action != "forgotpass" && ($action != "logout" || ($action == "logout" && !isset($_SESSION['loginID'])))) // && $action != "login")	// added by Dennis
{
    if($action == "forgotpassword")
    {
        $loginsummary = '

			<form class="form44" name="forgotpassword" id="forgotpassword" method="post" action="'.$homeurl.'/admin.php" >
				<p><strong>'.$clang->gT('You have to enter user name and email.').'</strong></p>

				<ul>
						<li><label for="user">'.$clang->gT('Username').'</label><input name="user" id="user" type="text" size="60" maxlength="60" value="" /></li>
						<li><label for="email">'.$clang->gT('Email').'</label><input name="email" id="email" type="text" size="60" maxlength="60" value="" /></li>
						<p><input type="hidden" name="action" value="forgotpass" />
						<input class="action" type="submit" value="'.$clang->gT('Check Data').'" />
						<p><a href="'.$scriptname.'">'.$clang->gT('Main Admin Screen').'</a>
			</form>
            <p>&nbsp;</p>
';
    }
    elseif (!isset($loginsummary))
    { // could be at login or after logout
        $refererargs=''; // If this is a direct access to admin.php, no args are given
        // If we are called from a link with action and other args set, get them
        if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'])
        {
            $refererargs = html_escape($_SERVER['QUERY_STRING']);
        }







        //include("database.php");
        $sIp = $_SERVER['REMOTE_ADDR'];
        $query = "SELECT * FROM ".db_table_name('failed_login_attempts'). " WHERE ip='$sIp';";
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $result = $connect->query($query) or safe_die ($query."<br />".$connect->ErrorMsg());
        $bCannotLogin = false;
        $intNthAttempt = 0;
        if ($result!==false && $result->RecordCount() >= 1)
        {
            $field = $result->FetchRow();
            $intNthAttempt = $field['number_attempts'];
            if ($intNthAttempt>=$maxLoginAttempt){
                $bCannotLogin = true;
            }

            $iLastAttempt = strtotime($field['last_attempt']);

            if (time() > $iLastAttempt + $timeOutTime){
                $bCannotLogin = false;
                $query = "DELETE FROM ".db_table_name('failed_login_attempts'). " WHERE ip='$sIp';";
                $result = $connect->query($query) or safe_die ($query."<br />".$connect->ErrorMsg());
            }

        }
        $loginsummary ="";
        if (!$bCannotLogin)
        {
            if (!isset($logoutsummary))
            {
                $loginsummary = "<form name='loginform' id='loginform' method='post' action='$homeurl/admin.php' ><p><strong>".$clang->gT("You have to login first.")."</strong><br />	<br />";
            }
            else
            {
                $loginsummary = "<form name='loginform' id='loginform' method='post' action='$homeurl/admin.php' ><br /><strong>".$logoutsummary."</strong><br />	<br />";
            }

            $loginsummary .= "
                                                            <ul>
                                                                            <li><label for='user'>".$clang->gT("Username")."</label>
                                                                            <input name='user' id='user' type='text' size='40' maxlength='40' value='' /></li>
                                                                            <li><label for='password'>".$clang->gT("Password")."</label>
                                                                            <input name='password' id='password' type='password' size='40' maxlength='40' /></li>
                                        <li><label for='loginlang'>".$clang->gT("Language")."</label>
                                        <select id='loginlang' name='loginlang' style='width:216px;'>\n";
            $loginsummary .='<option value="default" selected="selected">'.$clang->gT('Default').'</option>';
            $lan=array();
            foreach (getlanguagedata(true) as $langkey=>$languagekind)
            {
				array_push($lan,$langkey);
			}

			foreach (getlanguagedata(true) as $langkey=>$languagekind)
            {
				//The following conditional statements select the browser language in the language drop down box and echoes the other options.
                $loginsummary .= "\t\t\t\t<option value='$langkey'>".$languagekind['nativedescription']." - ".$languagekind['description']."</option>\n";
            }
            $loginsummary .= "\t\t\t</select>\n"
            . "</li>
                                    </ul>
                                                                            <p><input type='hidden' name='action' value='login' />
                                                                            <input type='hidden' name='refererargs' value='".$refererargs."' />
                                                                            <input class='action' type='submit' value='".$clang->gT("Login")."' /><br />&nbsp;\n<br/>";
        }
        else{
            $loginsummary .= "<p>".sprintf($clang->gT("You have exceeded you maximum login attempts. Please wait %d minutes before trying again"),($timeOutTime/60))."<br /></p>";
        }

        if ($display_user_password_in_email === true)
        {
            $loginsummary .= "<p><a href='$scriptname?action=forgotpassword'>".$clang->gT("Forgot Your Password?")."</a><br />&nbsp;\n";
        }
        $loginsummary .= "                                                </form><br /><p>";
        $loginsummary .= "                                                <script type='text/javascript'>\n";
        $loginsummary .= "                                                  document.getElementById('user').focus();\n";
        $loginsummary .= "                                                </script>\n";
    }
}
=======
// check data for login
if(isset($_POST['user']) && isset($_POST['password']) || ($action == "forgotpass") || ($action == "login") || ($action == "logout"))	// added by Dennis
{
	include("usercontrol.php");
}


// login form
if(!isset($_SESSION['loginID']) && $action != "forgotpass" && ($action != "logout" || ($action == "logout" && !isset($_SESSION['loginID'])))) // && $action != "login")	// added by Dennis
{
	if($action == "forgotpassword")
	{
		$loginsummary = "<form name='forgot' id='forgot' method='post' action='$rooturl/admin/admin.php' ><br /><strong>".$clang->gT("You have to enter user name and email.")."</strong><br />	<br />
							<table>
								<tr>
									<td><p>".$clang->gT("Username")."</p></td>
									<td><input name='user' type='text' id='user' size='40' maxlength='40' value='' /></td>
								</tr>
								<tr>
									<td><p>".$clang->gT("Email")."</p></td>
									<td><input name='email' id='email' type='text' size='40' maxlength='40' value='' /></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><input type='hidden' name='action' value='forgotpass' />
									<input class='action' type='submit' value='".$clang->gT("Check Data")."' /><br />&nbsp;\n</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><a href='$scriptname'>".$clang->gT("Main Admin Screen")."</a></td>
								</tr>
							</table>						
						</form>";	
	}
	elseif (!isset($loginsummary))
	{ // could be at login or after logout 
		$refererargs=''; // If this is a direct access to admin.php, no args are given
		// If we are called from a link with action and other args set, get them
		if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'])
		{
			$refererargs = html_escape($_SERVER['QUERY_STRING']);
		}

		$hidden_loginlang = '';
		if (isset($_POST['lang']) && $_POST['lang'])
		{
			$hidden_loginlang = "<input type='hidden' name='loginlang' value='".$_POST['lang']."' />";
		}
        
		if (!isset($logoutsummary))
		{
			$loginsummary = "<form name='login' id='login' method='post' action='$rooturl/admin/admin.php' ><br /><strong>".$clang->gT("You have to login first.")."</strong><br />	<br />";
		}
		else
		{
			$loginsummary = "<form name='login' id='login' method='post' action='$rooturl/admin/admin.php' ><br /><strong>".$logoutsummary."</strong><br />	<br />";
		}

		$loginsummary .= "
							<table>
								<tr>
									<td>".$clang->gT("Username")."</td>
									<td><input name='user' type='text' id='user' size='40' maxlength='40' value='' /></td>
								</tr>
								<tr>
									<td>".$clang->gT("Password")."</td>
									<td><input name='password' id='password' type='password' size='40' maxlength='40' /></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td align='center'><input type='hidden' name='action' value='login' />
									<input type='hidden' name='refererargs' value='".$refererargs."' />
									$hidden_loginlang
									<input class='action' type='submit' value='".$clang->gT("Login")."' /><br />&nbsp;\n</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><a href='$scriptname?action=forgotpassword'>".$clang->gT("Forgot Your Password?")."</a><br />&nbsp;\n</td>
								</tr>
							</table>
						</form>";

		// Language selection
		$loginsummary .=  "\t<form name='language' id='language' method='post' action='$rooturl/admin/admin.php' >"
		. "\t<table><tr>\n"
		. "\t\t<td align='center' >\n"
		. "\t\t\t".$clang->gT("Current Language").":\n"
		. "\t\t</td><td>\n"
		. "\t\t\t<select name='lang' onchange='form.submit()'>\n";
		foreach (getlanguagedata() as $langkey=>$languagekind)
		{
			$loginsummary .= "\t\t\t\t<option value='$langkey'";
			if (isset($_SESSION['adminlang']) && $langkey == $_SESSION['adminlang']) {$loginsummary .= " selected='selected'";}
			// in case it is a logout, session has already been killed
			if (!isset($_SESSION['adminlang']) && $langkey == $clang->getlangcode() ){$loginsummary .= " selected='selected'";}
			$loginsummary .= ">".$languagekind['description']." - ".$languagekind['nativedescription']."</option>\n";
		}
		$loginsummary .= "\t\t\t</select>\n"
		. "\t\t\t<input type='hidden' name='action' value='changelang' />\n"
		. "\t\t</td>\n"
		. "\t</tr>\n"
		. "</table>"
		. "</form><br />";
	}
}

if (isset($loginsummary)) {

	$adminoutput.= "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n"
	."\t<tr>\n"
    ."\t\t<td valign='top' align='center' bgcolor='#F8F8FF'>\n";
	
	if(isset($_SESSION['loginID']))
	{
		$adminoutput.= showadminmenu();
	}
	$adminoutput.= $loginsummary;
	
	$adminoutput.= "\t\t</td>\n";
	$adminoutput.= "\t</tr>\n";
	$adminoutput.= "</table>\n";
}

?>
>>>>>>> refs/heads/stable_plus
