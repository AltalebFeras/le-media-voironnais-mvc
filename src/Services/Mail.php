<?php

namespace src\Services;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../../vendor/autoload.php';

final class Mail
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }
    private function configureSMTP()
    {
        // SMTP configuration
        $this->mail->isSMTP();
        $this->mail->Host = HOST;
        $this->mail->Port = PORT;
        
        // Configure authentication based on environment
        if (defined('SMTP_AUTH') && SMTP_AUTH === true) {
            $this->mail->SMTPAuth = true;
            $this->mail->Username = USERNAME;
            $this->mail->Password = PASSWORD;
        } else {
            // For MailDev - no authentication required
            $this->mail->SMTPAuth = false;
        }

        // For email on server side only, it uses the SSL protocol
        if (IS_PROD === true) {
            $this->mail->SMTPSecure = 'ssl';
        }
    }

    private function getSalutation(): string
    {
        $time = (int) date('H');
        if ($time < 12) {
            return 'Bonjour';
        } elseif ($time < 16) {
            return 'Bon après-midi';
        }
        return 'Bonsoir';
    }

    private function getSouhait(): string
    {
        $time = (int) date('H');
        if ($time < 4 || $time >= 22) {
            return 'Bonne nuit';
        } elseif ($time < 12) {
            return 'Bonne journée';
        } elseif ($time < 16) {
            return 'Bon après-midi';
        }
        return 'Bonne soirée';
    }

    /**
     * Alternative template generator with salutation and souhait.
     * Usage: $this->generateTemplate($subject, $body)
     */
    private function generateTemplate(string $recipientName, string $subject, string $body): string
    {
        $salutation = $this->getSalutation();
        $souhait = $this->getSouhait();
        $currentYear = date('Y');
        $logoPath = DOMAIN . HOME_URL . 'assets/images/logo/logo.png';

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$subject</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%); padding: 40px 20px; text-align: center; }
        .content { padding: 40px 30px;border-left: 5px solid #3B82F6; border-right: 5px solid #3B82F6; }
        .footer { background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%); padding: 30px 20px; text-align: center; }
        .logo-container { display: inline-flex; align-items: center; }
        .logo-text { font-size: 24px; font-weight: bold; color: #ffffff; margin: 0; }
        .greeting { color: #333333; font-size: 18px; font-weight: 600; margin: 0 0 25px 0; }
        .content-body { color: #555555; font-size: 16px; line-height: 1.6; margin: 0 0 25px 0; }
        .closing { color: #333333; font-size: 16px; font-weight: 500; margin: 25px 0 0 0; }
        .team-name { color: #ffffff; font-size: 20px; font-weight: 700; margin: 0; }
        .team-tagline { color: #ffffff; font-size: 14px; margin: 8px 0 0 0; opacity: 0.9; }
        .copyright { color: #ffffff; font-size: 12px; margin: 20px 0 0 0; opacity: 0.8; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <a href="https://le-media-voironnais.feras.fr" style="text-decoration: none;">
                <div class="logo-container">
                    <p class="logo-text">
                        <span style="color: #EF4444;">Le </span><span style="color: #10B981;">Média</span><span style="color: #3B82F6;">Voironnais</span>
                    </p>
                </div>
            </a>
        </div>
        <!-- Main Content Section -->
        <div class="content">
            <p class="greeting">$salutation $recipientName,</p>
            <div class="content-body">
                $body
            </div>
            <p class="closing">$souhait !</p>
        </div>
        <!-- Footer Section -->
        <div class="footer">
            <p class="team-name">L'équipe du Média Voironnais</p>
            <p class="team-tagline">Bougez, partagez, progressez !</p>
            <p class="copyright">&copy; $currentYear Le Média Voironnais. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">

<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"><!--[if mso]>
<xml><w:WordDocument xmlns:w="urn:schemas-microsoft-com:office:word"><w:DontUseAdvancedTypographyReadingMail/></w:WordDocument>
<o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch><o:AllowPNG/></o:OfficeDocumentSettings></xml>
<![endif]--><!--[if !mso]><!-->
	<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css"><!--<![endif]-->
	<style>
		* {
			box-sizing: border-box;
		}

		body {
			margin: 0;
			padding: 0;
		}

		a[x-apple-data-detectors] {
			color: inherit !important;
			text-decoration: inherit !important;
		}

		#MessageViewBody a {
			color: inherit;
			text-decoration: none;
		}

		p {
			line-height: inherit
		}

		.desktop_hide,
		.desktop_hide table {
			mso-hide: all;
			display: none;
			max-height: 0px;
			overflow: hidden;
		}

		.image_block img+div {
			display: none;
		}

		sup,
		sub {
			font-size: 75%;
			line-height: 0;
		}

		.menu_block.desktop_hide .menu-links span {
			mso-hide: all;
		}

		@media (max-width:695px) {

			.desktop_hide table.icons-inner,
			.social_block.desktop_hide .social-table {
				display: inline-block !important;
			}

			.icons-inner {
				text-align: center;
			}

			.icons-inner td {
				margin: 0 auto;
			}

			.image_block div.fullWidth {
				max-width: 100% !important;
			}

			.menu-checkbox[type=checkbox]~.menu-links {
				display: none !important;
				padding: 5px 0;
			}

			.menu-checkbox[type=checkbox]:checked~.menu-trigger .menu-open {
				display: none !important;
			}

			.menu-checkbox[type=checkbox]:checked~.menu-links,
			.menu-checkbox[type=checkbox]~.menu-trigger {
				display: block !important;
				max-width: none !important;
				max-height: none !important;
				font-size: inherit !important;
			}

			.menu-checkbox[type=checkbox]~.menu-links>a,
			.menu-checkbox[type=checkbox]~.menu-links>span.label {
				display: block !important;
				text-align: center;
			}

			.menu-checkbox[type=checkbox]:checked~.menu-trigger .menu-close {
				display: block !important;
			}

			.mobile_hide {
				display: none;
			}

			.row-content {
				width: 100% !important;
			}

			.stack .column {
				width: 100%;
				display: block;
			}

			.mobile_hide {
				min-height: 0;
				max-height: 0;
				max-width: 0;
				overflow: hidden;
				font-size: 0px;
			}

			.desktop_hide,
			.desktop_hide table {
				display: table !important;
				max-height: none !important;
			}
		}

		#menu-r0c0m3:checked~.menu-links {
			background-color: #e27c3a !important;
		}

		#menu-r0c0m3:checked~.menu-links a,
		#menu-r0c0m3:checked~.menu-links span {
			color: #ffffff !important;
		}
	</style><!--[if mso ]><style>sup, sub { font-size: 100% !important; } sup { mso-text-raise:10% } sub { mso-text-raise:-10% }</style> <![endif]-->
</head>

<body class="body" style="background-color: #fff5eb; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;">
	<table class="nl-container" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff5eb;">
		<tbody>
			<tr>
				<td>
					<table class="row row-1" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
						<tbody>
							<tr>
								<td>
									<table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 675px; margin: 0 auto;" width="675">
										<tbody>
											<tr>
												<td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; vertical-align: top;">
													<div class="spacer_block block-1" style="height:20px;line-height:20px;font-size:1px;">&#8202;</div>
													<table class="image_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="padding-left:20px;width:100%;padding-right:0px;">
																<div class="alignment" align="center">
																	<div style="max-width: 118.125px;"><a href="http://www.example.com" target="_blank"><img src="https://d1oco4z2z1fhwp.cloudfront.net/templates/default/4856/travel-logo.png" style="display: block; height: auto; border: 0; width: 100%;" width="118.125" alt="your-ogo" title="your-ogo" height="auto"></a></div>
																</div>
															</td>
														</tr>
													</table>
													<div class="spacer_block block-3" style="height:20px;line-height:20px;font-size:1px;">&#8202;</div>
													<table class="menu_block block-4" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="color:#1a1a1a;font-family:'Montserrat', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif;font-size:13px;letter-spacing:1px;text-align:center;">
																<table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
																	<tr>
																		<td class="alignment" style="text-align:center;font-size:0px;"><!--[if !mso]><!--><input class="menu-checkbox" id="menu-r0c0m3" type="checkbox" style="display:none !important;max-height:0;visibility:hidden;"><!--<![endif]-->
																			<div class="menu-trigger" style="display:none;max-height:0px;max-width:0px;font-size:0px;overflow:hidden;"><label class="menu-label" for="menu-r0c0m3" style="height: 36px; width: 36px; display: inline-block; cursor: pointer; mso-hide: all; user-select: none; align: center; text-align: center; color: #ffffff; text-decoration: none; background-color: #e27c3a; border-radius: 50%;"><span class="menu-open" style="word-break: break-word; mso-hide: all; font-size: 26px; line-height: 31.5px;">☰</span><span class="menu-close" style="word-break: break-word; display: none; mso-hide: all; font-size: 26px; line-height: 36px;">✕</span></label></div>
																			<div class="menu-links"><!--[if mso]><table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style=""><tr style="text-align:center;"><![endif]--><!--[if mso]><td style="padding-top:10px;padding-right:5px;padding-bottom:10px;padding-left:5px"><![endif]--><a href="http://www.example.com" target="_self" style="mso-hide:false;padding-top:10px;padding-bottom:10px;padding-left:5px;padding-right:5px;display:inline-block;color:#1a1a1a;font-family:'Montserrat', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif;font-size:13px;text-decoration:none;letter-spacing:1px;">CONTACT</a><!--[if mso]></td><![endif]--><!--[if mso]><td style="padding-top:10px;padding-right:5px;padding-bottom:10px;padding-left:5px"><![endif]--><a href="http://www.example.com" target="_self" style="mso-hide:false;padding-top:10px;padding-bottom:10px;padding-left:5px;padding-right:5px;display:inline-block;color:#1a1a1a;font-family:'Montserrat', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif;font-size:13px;text-decoration:none;letter-spacing:1px;">DESTINATIONS</a><!--[if mso]></td><![endif]--><!--[if mso]><td style="padding-top:10px;padding-right:5px;padding-bottom:10px;padding-left:5px"><![endif]--><a href="http://www.example.com" target="_self" style="mso-hide:false;padding-top:10px;padding-bottom:10px;padding-left:5px;padding-right:5px;display:inline-block;color:#1a1a1a;font-family:'Montserrat', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif;font-size:13px;text-decoration:none;letter-spacing:1px;">SAFE TRAVEL</a><!--[if mso]></td><![endif]--><!--[if mso]></tr></table><![endif]--></div>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="row row-2" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
						<tbody>
							<tr>
								<td>
									<table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 675px; margin: 0 auto;" width="675">
										<tbody>
											<tr>
												<td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top;">
													<div class="spacer_block block-1" style="height:30px;line-height:30px;font-size:1px;">&#8202;</div>
													<table class="heading_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="text-align:center;width:100%;">
																<h1 style="margin: 0; color: #e27c3a; direction: ltr; font-family: Georgia, Times, 'Times New Roman', serif; font-size: 38px; font-weight: normal; letter-spacing: 1px; line-height: 1.2; text-align: center; margin-top: 0; margin-bottom: 0; mso-line-height-alt: 46px;"><em><strong>Thanksgiving</strong></em></h1>
															</td>
														</tr>
													</table>
													<div class="spacer_block block-3" style="height:15px;line-height:15px;font-size:1px;">&#8202;</div>
													<table class="heading_block block-4" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="text-align:center;width:100%;">
																<h1 style="margin: 0; color: #1a1a1a; direction: ltr; font-family: 'Montserrat', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif; font-size: 23px; font-weight: normal; letter-spacing: 2px; line-height: 1.2; text-align: center; margin-top: 0; margin-bottom: 0; mso-line-height-alt: 28px;">- IS COMING -</h1>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="row row-3" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
						<tbody>
							<tr>
								<td>
									<table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 675px; margin: 0 auto;" width="675">
										<tbody>
											<tr>
												<td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top;">
													<div class="spacer_block block-1" style="height:15px;line-height:15px;font-size:1px;">&#8202;</div>
													<table class="heading_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="text-align:center;width:100%;">
																<h1 style="margin: 0; color: #e27c3a; direction: ltr; font-family: Georgia, Times, 'Times New Roman', serif; font-size: 27px; font-weight: normal; letter-spacing: 1px; line-height: 1.2; text-align: center; margin-top: 0; margin-bottom: 0; mso-line-height-alt: 32px;"><em>For everything you are <strong>grateful</strong> for</em></h1>
															</td>
														</tr>
													</table>
													<table class="paragraph_block block-3" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad">
																<div style="color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:18px;letter-spacing:0px;line-height:1.5;text-align:center;mso-line-height-alt:27px;">
																	<p style="margin: 0; word-break: break-word;"><em><span style="word-break: break-word;">Planning, preparing and hosting.</span></em></p>
																	<p style="margin: 0; word-break: break-word;"><span style="word-break: break-word;"><strong>Thanksgiving</strong> will be different this year,</span></p>
																	<p style="margin: 0; word-break: break-word;"><span style="word-break: break-word;">and we’ve got the plan to make it easier.</span></p>
																</div>
															</td>
														</tr>
													</table>
													<table class="button_block block-4" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="padding-bottom:15px;padding-left:10px;padding-right:10px;padding-top:15px;text-align:center;">
																<div class="alignment" align="center"><a href="http://www.example.com" target="_blank" style="color:#ffffff;text-decoration:none;"><!--[if mso]>
<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"  href="http://www.example.com"  style="height:42px;width:221px;v-text-anchor:middle;" arcsize="10%" fillcolor="#e27c3a">
<v:stroke dashstyle="Solid" weight="0px" color="#e27c3a"/>
<w:anchorlock/>
<v:textbox inset="5px,0px,0px,0px">
<center dir="false" style="color:#ffffff;font-family:sans-serif;font-size:16px">
<![endif]--><span class="button" style="background-color: #e27c3a; mso-shading: transparent; border-bottom: 0px solid transparent; border-left: 0px solid transparent; border-radius: 4px; border-right: 0px solid transparent; border-top: 0px solid transparent; color: #ffffff; display: inline-block; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; font-size: 16px; font-weight: undefined; mso-border-alt: none; padding-bottom: 5px; padding-top: 5px; padding-left: 50px; padding-right: 45px; text-align: center; width: auto; word-break: keep-all; letter-spacing: normal;"><span style="word-break: break-word; line-height: 32px;"><strong>DESTINATIONS</strong></span></span><!--[if mso]></center></v:textbox></v:roundrect><![endif]--></a></div>
															</td>
														</tr>
													</table>
													<div class="spacer_block block-5" style="height:10px;line-height:10px;font-size:1px;">&#8202;</div>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="row row-4" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
						<tbody>
							<tr>
								<td>
									<table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 675px; margin: 0 auto;" width="675">
										<tbody>
											<tr>
												<td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top;">
													<div class="spacer_block block-1" style="height:20px;line-height:20px;font-size:1px;">&#8202;</div>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="row row-5" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
						<tbody>
							<tr>
								<td>
									<table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 675px; margin: 0 auto;" width="675">
										<tbody>
											<tr>
												<td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top;">
													<table class="heading_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="text-align:center;width:100%;">
																<h1 style="margin: 0; color: #e27c3a; direction: ltr; font-family: Georgia, Times, 'Times New Roman', serif; font-size: 27px; font-weight: normal; letter-spacing: 1px; line-height: 1.2; text-align: center; margin-top: 0; margin-bottom: 0; mso-line-height-alt: 32px;"><em><strong>Get closer</strong> to your family</em><br></h1>
															</td>
														</tr>
													</table>
													<table class="paragraph_block block-2" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad">
																<div style="color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:18px;letter-spacing:0px;line-height:1.5;text-align:center;mso-line-height-alt:27px;">
																	<p style="margin: 0; word-break: break-word;">Discover our destinations and<strong> </strong>get close</p>
																	<p style="margin: 0; word-break: break-word;">to your family for Thanksgiving.</p>
																</div>
															</td>
														</tr>
													</table>
													<div class="spacer_block block-3" style="height:10px;line-height:10px;font-size:1px;">&#8202;</div>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="row row-6" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
						<tbody>
							<tr>
								<td>
									<table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 675px; margin: 0 auto;" width="675">
										<tbody>
											<tr>
												<td class="column column-1" width="33.333333333333336%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 10px; padding-left: 10px; padding-right: 10px; padding-top: 10px; vertical-align: top;">
													<table class="image_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="width:100%;padding-right:0px;padding-left:0px;">
																<div class="alignment" align="center">
																	<div class="fullWidth" style="max-width: 205px;"><a href="http://www.example.com" target="_blank"><img src="https://d1oco4z2z1fhwp.cloudfront.net/templates/default/4856/roma.png" style="display: block; height: auto; border: 0; width: 100%;" width="205" alt="ITALY" title="ITALY" height="auto"></a></div>
																</div>
															</td>
														</tr>
													</table>
													<table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad" style="padding-bottom:5px;padding-left:10px;padding-right:5px;padding-top:10px;">
																<div style="color:#e27c3a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:20px;line-height:2;text-align:left;mso-line-height-alt:40px;">
																	<p style="margin: 0; word-break: break-word;"><strong><span style="word-break: break-word;">Italy</span></strong></p>
																</div>
															</td>
														</tr>
													</table>
													<table class="paragraph_block block-3" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad">
																<div style="color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:13px;line-height:1.8;text-align:left;mso-line-height-alt:23px;">
																	<p style="margin: 0; word-break: break-word;"><span style="word-break: break-word;">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed do eiusmod tempor.</span></p>
																</div>
															</td>
														</tr>
													</table>
													<table class="text_block block-4" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad">
																<div style="font-family: sans-serif">
																	<div class style="font-size: 12px; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; mso-line-height-alt: 21.6px; color: #1a1a1a; line-height: 1.8;">
																		<p style="margin: 0; font-size: 14px; text-align: left; mso-line-height-alt: 28.8px;"><span style="word-break: break-word; font-size: 16px;"><strong><span style="word-break: break-word; color: #e27c3a;">$320</span></strong>&nbsp; &nbsp;<span style="word-break: break-word; font-size: 14px;">3 days / 2 nights</span></span></p>
																	</div>
																</div>
															</td>
														</tr>
													</table>
													<table class="button_block block-5" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="padding-bottom:15px;padding-left:10px;padding-right:10px;padding-top:15px;text-align:center;">
																<div class="alignment" align="center"><a href="http://www.example.com" target="_blank" style="color:#ffffff;text-decoration:none;"><!--[if mso]>
<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"  href="http://www.example.com"  style="height:38px;width:160px;v-text-anchor:middle;" arcsize="11%" fillcolor="#e27c3a">
<v:stroke dashstyle="Solid" weight="0px" color="#e27c3a"/>
<w:anchorlock/>
<v:textbox inset="0px,0px,0px,0px">
<center dir="false" style="color:#ffffff;font-family:sans-serif;font-size:16px">
<![endif]--><span class="button" style="background-color: #e27c3a; mso-shading: transparent; border-bottom: 0px solid transparent; border-left: 0px solid transparent; border-radius: 4px; border-right: 0px solid transparent; border-top: 0px solid transparent; color: #ffffff; display: inline-block; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; font-size: 16px; font-weight: undefined; mso-border-alt: none; padding-bottom: 5px; padding-top: 5px; padding-left: 30px; padding-right: 30px; text-align: center; width: auto; word-break: keep-all; letter-spacing: normal;"><span style="word-break: break-word; line-height: 28.8px;"><strong>BOOK NOW</strong></span></span><!--[if mso]></center></v:textbox></v:roundrect><![endif]--></a></div>
															</td>
														</tr>
													</table>
												</td>
												<td class="column column-2" width="33.333333333333336%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 10px; padding-left: 10px; padding-right: 10px; padding-top: 10px; vertical-align: top;">
													<table class="image_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="width:100%;padding-right:0px;padding-left:0px;">
																<div class="alignment" align="center">
																	<div class="fullWidth" style="max-width: 205px;"><a href="http://www.example.com" target="_blank"><img src="https://d1oco4z2z1fhwp.cloudfront.net/templates/default/4856/newyork.png" style="display: block; height: auto; border: 0; width: 100%;" width="205" alt="NEW-YORK" title="NEW-YORK" height="auto"></a></div>
																</div>
															</td>
														</tr>
													</table>
													<table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad" style="padding-bottom:5px;padding-left:10px;padding-right:5px;padding-top:10px;">
																<div style="color:#e27c3a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:20px;line-height:2;text-align:left;mso-line-height-alt:40px;">
																	<p style="margin: 0; word-break: break-word;"><strong><span style="word-break: break-word;">North America</span></strong></p>
																</div>
															</td>
														</tr>
													</table>
													<table class="paragraph_block block-3" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad">
																<div style="color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:13px;line-height:1.8;text-align:left;mso-line-height-alt:23px;">
																	<p style="margin: 0; word-break: break-word;"><span style="word-break: break-word;">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed do eiusmod tempor.</span></p>
																</div>
															</td>
														</tr>
													</table>
													<table class="text_block block-4" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad">
																<div style="font-family: sans-serif">
																	<div class style="font-size: 12px; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; mso-line-height-alt: 21.6px; color: #1a1a1a; line-height: 1.8;">
																		<p style="margin: 0; font-size: 14px; text-align: left; mso-line-height-alt: 28.8px;"><span style="word-break: break-word; font-size: 16px;"><strong><span style="word-break: break-word; color: #e27c3a;">$530</span></strong>&nbsp; &nbsp;3<span style="word-break: break-word; font-size: 14px;">&nbsp;days / 2 nights</span></span></p>
																	</div>
																</div>
															</td>
														</tr>
													</table>
													<table class="button_block block-5" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="padding-bottom:15px;padding-left:10px;padding-right:10px;padding-top:15px;text-align:center;">
																<div class="alignment" align="center"><a href="http://www.example.com" target="_blank" style="color:#ffffff;text-decoration:none;"><!--[if mso]>
<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"  href="http://www.example.com"  style="height:38px;width:160px;v-text-anchor:middle;" arcsize="11%" fillcolor="#e27c3a">
<v:stroke dashstyle="Solid" weight="0px" color="#e27c3a"/>
<w:anchorlock/>
<v:textbox inset="0px,0px,0px,0px">
<center dir="false" style="color:#ffffff;font-family:sans-serif;font-size:16px">
<![endif]--><span class="button" style="background-color: #e27c3a; mso-shading: transparent; border-bottom: 0px solid transparent; border-left: 0px solid transparent; border-radius: 4px; border-right: 0px solid transparent; border-top: 0px solid transparent; color: #ffffff; display: inline-block; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; font-size: 16px; font-weight: undefined; mso-border-alt: none; padding-bottom: 5px; padding-top: 5px; padding-left: 30px; padding-right: 30px; text-align: center; width: auto; word-break: keep-all; letter-spacing: normal;"><span style="word-break: break-word; line-height: 28.8px;"><strong>BOOK NOW</strong></span></span><!--[if mso]></center></v:textbox></v:roundrect><![endif]--></a></div>
															</td>
														</tr>
													</table>
												</td>
												<td class="column column-3" width="33.333333333333336%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 10px; padding-left: 10px; padding-right: 10px; padding-top: 10px; vertical-align: top;">
													<table class="image_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="width:100%;padding-right:0px;padding-left:0px;">
																<div class="alignment" align="center">
																	<div class="fullWidth" style="max-width: 205px;"><a href="http://www.example.com" target="_blank"><img src="https://d1oco4z2z1fhwp.cloudfront.net/templates/default/4856/spain.png" style="display: block; height: auto; border: 0; width: 100%;" width="205" alt="BARCELLONA" title="BARCELLONA" height="auto"></a></div>
																</div>
															</td>
														</tr>
													</table>
													<table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad" style="padding-bottom:5px;padding-left:10px;padding-right:5px;padding-top:10px;">
																<div style="color:#e27c3a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:20px;line-height:2;text-align:left;mso-line-height-alt:40px;">
																	<p style="margin: 0; word-break: break-word;"><strong><span style="word-break: break-word;">Spain</span></strong></p>
																</div>
															</td>
														</tr>
													</table>
													<table class="paragraph_block block-3" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad">
																<div style="color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:13px;line-height:1.8;text-align:left;mso-line-height-alt:23px;">
																	<p style="margin: 0; word-break: break-word;"><span style="word-break: break-word;">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed do eiusmod tempor.</span></p>
																</div>
															</td>
														</tr>
													</table>
													<table class="text_block block-4" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad">
																<div style="font-family: sans-serif">
																	<div class style="font-size: 12px; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; mso-line-height-alt: 21.6px; color: #1a1a1a; line-height: 1.8;">
																		<p style="margin: 0; font-size: 14px; text-align: left; mso-line-height-alt: 28.8px;"><span style="word-break: break-word; font-size: 16px;"><strong><span style="word-break: break-word; color: #e27c3a;">$420</span></strong>&nbsp; &nbsp;<span style="word-break: break-word; font-size: 14px;">3 days / 2 nights</span></span></p>
																	</div>
																</div>
															</td>
														</tr>
													</table>
													<table class="button_block block-5" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="padding-bottom:15px;padding-left:10px;padding-right:10px;padding-top:15px;text-align:center;">
																<div class="alignment" align="center"><a href="http://www.example.com" target="_blank" style="color:#ffffff;text-decoration:none;"><!--[if mso]>
<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"  href="http://www.example.com"  style="height:38px;width:160px;v-text-anchor:middle;" arcsize="11%" fillcolor="#e27c3a">
<v:stroke dashstyle="Solid" weight="0px" color="#e27c3a"/>
<w:anchorlock/>
<v:textbox inset="0px,0px,0px,0px">
<center dir="false" style="color:#ffffff;font-family:sans-serif;font-size:16px">
<![endif]--><span class="button" style="background-color: #e27c3a; mso-shading: transparent; border-bottom: 0px solid transparent; border-left: 0px solid transparent; border-radius: 4px; border-right: 0px solid transparent; border-top: 0px solid transparent; color: #ffffff; display: inline-block; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; font-size: 16px; font-weight: undefined; mso-border-alt: none; padding-bottom: 5px; padding-top: 5px; padding-left: 30px; padding-right: 30px; text-align: center; width: auto; word-break: keep-all; letter-spacing: normal;"><span style="word-break: break-word; line-height: 28.8px;"><strong>BOOK NOW</strong></span></span><!--[if mso]></center></v:textbox></v:roundrect><![endif]--></a></div>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="row row-7" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
						<tbody>
							<tr>
								<td>
									<table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 675px; margin: 0 auto;" width="675">
										<tbody>
											<tr>
												<td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top;">
													<div class="spacer_block block-1" style="height:20px;line-height:20px;font-size:1px;">&#8202;</div>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="row row-8" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff5eb;">
						<tbody>
							<tr>
								<td>
									<table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff5eb; color: #000000; width: 675px; margin: 0 auto;" width="675">
										<tbody>
											<tr>
												<td class="column column-1" width="41.666666666666664%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top;">
													<div class="spacer_block block-1" style="height:35px;line-height:35px;font-size:1px;">&#8202;</div>
													<table class="image_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="width:100%;padding-right:0px;padding-left:0px;">
																<div class="alignment" align="center">
																	<div style="max-width: 112.5px;"><img src="https://d1oco4z2z1fhwp.cloudfront.net/templates/default/4856/travel-logo.png" style="display: block; height: auto; border: 0; width: 100%;" width="112.5" alt="Your Logo" title="Your Logo" height="auto"></div>
																</div>
															</td>
														</tr>
													</table>
													<div class="spacer_block block-3" style="height:20px;line-height:20px;font-size:1px;">&#8202;</div>
													<table class="social_block block-4" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="padding-bottom:15px;padding-left:10px;padding-right:10px;padding-top:10px;text-align:center;">
																<div class="alignment" align="center">
																	<table class="social-table" width="168px" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; display: inline-block;">
																		<tr>
																			<td style="padding:0 5px 0 5px;"><a href="https://www.facebook.com" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/t-outline-circle-dark-gray/facebook@2x.png" width="32" height="auto" alt="Facebook" title="facebook" style="display: block; height: auto; border: 0;"></a></td>
																			<td style="padding:0 5px 0 5px;"><a href="https://www.twitter.com" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/t-outline-circle-dark-gray/twitter@2x.png" width="32" height="auto" alt="Twitter" title="twitter" style="display: block; height: auto; border: 0;"></a></td>
																			<td style="padding:0 5px 0 5px;"><a href="https://www.linkedin.com" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/t-outline-circle-dark-gray/linkedin@2x.png" width="32" height="auto" alt="Linkedin" title="linkedin" style="display: block; height: auto; border: 0;"></a></td>
																			<td style="padding:0 5px 0 5px;"><a href="https://www.instagram.com" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/t-outline-circle-dark-gray/instagram@2x.png" width="32" height="auto" alt="Instagram" title="instagram" style="display: block; height: auto; border: 0;"></a></td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
													</table>
												</td>
												<td class="column column-2" width="25%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top;">
													<div class="spacer_block block-1" style="height:35px;line-height:35px;font-size:1px;">&#8202;</div>
													<table class="menu_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
														<tr>
															<td class="pad" style="color:#1a1a1a;font-family:inherit;font-size:12px;letter-spacing:2px;padding-bottom:10px;padding-right:20px;padding-top:10px;text-align:center;">
																<table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
																	<tr>
																		<td class="alignment" style="text-align:center;font-size:0px;">
																			<div class="menu-links"><!--[if mso]><table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style=""><tr style="text-align:center;"><![endif]--><!--[if mso]><td style="padding-top:5px;padding-right:20px;padding-bottom:5px;padding-left:20px"><![endif]--><a href="www.example.com" target="_self" style="mso-hide:false;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;display:block;color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:12px;text-decoration:none;letter-spacing:2px;">DESTINATIONS</a><!--[if mso]></tr></td><![endif]--><!--[if mso]></tr><tr style="text-align:center;"><td style="padding-top:5px;padding-right:20px;padding-bottom:5px;padding-left:20px"><![endif]--><a href="www.example.com" target="_self" style="mso-hide:false;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;display:block;color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:12px;text-decoration:none;letter-spacing:2px;">CHECK IN</a><!--[if mso]></tr></td><![endif]--><!--[if mso]></tr><tr style="text-align:center;"><td style="padding-top:5px;padding-right:20px;padding-bottom:5px;padding-left:20px"><![endif]--><a href="www.example.com" target="_self" style="mso-hide:false;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;display:block;color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:12px;text-decoration:none;letter-spacing:2px;">SAFE TRAVEL</a><!--[if mso]></tr></td><![endif]--><!--[if mso]></tr><tr style="text-align:center;"><td style="padding-top:5px;padding-right:20px;padding-bottom:5px;padding-left:20px"><![endif]--><a href="www.example.com" target="_self" style="mso-hide:false;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;display:block;color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:12px;text-decoration:none;letter-spacing:2px;">CONTACTS</a><!--[if mso]></tr></td><![endif]--><!--[if mso]></tr><tr style="text-align:center;"><td style="padding-top:5px;padding-right:20px;padding-bottom:5px;padding-left:20px"><![endif]--><a href="www.example.com" target="_self" style="mso-hide:false;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;display:block;color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:12px;text-decoration:none;letter-spacing:2px;">FAQ</a><!--[if mso]></tr></td><![endif]--><!--[if mso]></tr></table><![endif]--></div>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
												<td class="column column-3" width="33.333333333333336%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top;">
													<div class="spacer_block block-1" style="height:35px;line-height:35px;font-size:1px;">&#8202;</div>
													<table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad" style="padding-bottom:10px;padding-left:25px;padding-right:10px;padding-top:10px;">
																<div style="color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:14px;line-height:1.2;text-align:center;mso-line-height-alt:17px;">
																	<p style="margin: 0; word-break: break-word;"><span style="word-break: break-word;"><strong>Where to find us</strong></span></p>
																</div>
															</td>
														</tr>
													</table>
													<table class="paragraph_block block-3" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad" style="padding-bottom:10px;padding-left:25px;padding-right:10px;padding-top:10px;">
																<div style="color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:12px;line-height:1.2;text-align:center;mso-line-height-alt:14px;">
																	<p style="margin: 0; word-break: break-word;">Company address here<br>+1 123 123 123</p>
																</div>
															</td>
														</tr>
													</table>
													<div class="spacer_block block-4" style="height:10px;line-height:10px;font-size:1px;">&#8202;</div>
													<table class="paragraph_block block-5" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
														<tr>
															<td class="pad" style="padding-bottom:15px;padding-left:25px;padding-right:10px;padding-top:10px;">
																<div style="color:#1a1a1a;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;font-size:12px;line-height:1.2;text-align:center;mso-line-height-alt:14px;">
																	<p style="margin: 0; word-break: break-word;"><span style="word-break: break-word;">Changed your mind? <a style="text-decoration: underline; color: #1a1a1a;" href="http://www.example.com" target="_blank" rel="noopener">Unsubscribe</a> </span></p>
																</div>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="row row-9" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff;">
						<tbody>
							<tr>
								<td>
									<table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 675px; margin: 0 auto;" width="675">
										<tbody>
											<tr>
												<td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top;">
													<div class="spacer_block block-1" style="height:10px;line-height:10px;font-size:1px;">&#8202;</div>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="row row-10" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff;">
						<tbody>
							<tr>
								<td>
									<table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 675px; margin: 0 auto;" width="675">
										<tbody>
											<tr>
												<td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top;">
													<table class="icons_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; text-align: center; line-height: 0;">
														<tr>
															<td class="pad" style="vertical-align: middle; color: #1e0e4b; font-family: 'Inter', sans-serif; font-size: 15px; padding-bottom: 5px; padding-top: 5px; text-align: center;"><!--[if vml]><table align="center" cellpadding="0" cellspacing="0" role="presentation" style="display:inline-block;padding-left:0px;padding-right:0px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;"><![endif]-->
																<!--[if !vml]><!-->
																<table class="icons-inner" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; display: inline-block; padding-left: 0px; padding-right: 0px;" cellpadding="0" cellspacing="0" role="presentation"><!--<![endif]-->
																	<tr>
																		<td style="vertical-align: middle; text-align: center; padding-top: 5px; padding-bottom: 5px; padding-left: 5px; padding-right: 6px;"><a href="https://designedwithbeefree.com/" target="_blank" style="text-decoration: none;"><img class="icon" alt="Beefree Logo" src="https://d1oco4z2z1fhwp.cloudfront.net/assets/Beefree-logo.png" height="auto" width="34" align="center" style="display: block; height: auto; margin: 0 auto; border: 0;"></a></td>
																		<td style="font-family: 'Inter', sans-serif; font-size: 15px; font-weight: undefined; color: #1e0e4b; vertical-align: middle; letter-spacing: undefined; text-align: center; line-height: normal;"><a href="https://designedwithbeefree.com/" target="_blank" style="color: #1e0e4b; text-decoration: none;">Designed with Beefree</a></td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table><!-- End -->
</body>

</html>

HTML;
    }

    public function addAttachment($filePath, $name = '')
    {
        try {
            if (!empty($name)) {
                $this->mail->addAttachment($filePath, $name);
            } else {
                $this->mail->addAttachment($filePath);
            }
        } catch (Exception $e) {
            throw new \Exception("Failed to add attachment: " . $e->getMessage());
        }
    }

    /**
     * Sends an HTML email using the configured PHPMailer instance.
     * This method sets the character encoding to UTF-8 to support multilingual content,
     * generates a styled HTML email using a template, sets sender and recipient information,
     * and optionally applies custom headers before sending the email.
     *
     * @param string $senderEmail       The sender's email address .
     * @param string $senderName   The sender's display name.
     * @param string $recipientEmail         The recipient's email address.
     * @param string $recipientName     The recipient's display name.
     * @param string $subject    The subject of the email.
     * @param string $body       The main body content of the email (before templating).
     * @param array  $headers    Optional associative array of additional headers (e.g., ['X-Priority' => '1']).
     *
     * @throws \Exception        If email sending fails or PHPMailer throws an error.
     *
     * @return void
     */

    public function sendEmail($senderEmail, $senderName, $recipientEmail, $recipientName, $subject, $body, $headers = []): bool
    {
        try {
            // Set the charset to UTF-8 for French and English compatibility
            $this->mail->CharSet = 'UTF-8';

            // Generate the template with the subject and body

            $emailContent = $this->generateTemplate($recipientName, $subject, $body);

            // Recipients
            $this->mail->setFrom($senderEmail, $senderName);
            $this->mail->addAddress($recipientEmail, $recipientName);

            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = mb_encode_mimeheader($subject, 'UTF-8');
            $this->mail->Body = $emailContent;

            // Apply custom headers
            foreach ($headers as $key => $value) {
                $this->mail->addCustomHeader($key, $value);
            }

            // Send the email
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
            throw new \Exception("Failed to send email: " . $e->getMessage());
        }
    }
}
