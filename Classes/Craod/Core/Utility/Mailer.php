<?php

namespace Craod\Core\Utility;

use Craod\Core\Exception\MailSendingException;

/**
 * The mailer utility
 *
 * @package Craod\Core\Utility
 */
class Mailer {

	/**
	 * Parameters to include in the mail when sending (Helo, for example)
	 *
	 * @var array
	 */
	protected static $parameters;

	/**
	 * Send an email using the SMTP configuration
	 *
	 * @param array $from An associative array with an email as a key and a name as a value
	 * @param array $to An associative array with emails as keys and names as values
	 * @param string $subject
	 * @param string $body
	 * @return string
	 * @throws MailSendingException
	 */
	public static function sendMail(array $from, array $to, $subject, $body) {
		$mail = new \PHPMailer();
		$mail->isSMTP();
		foreach (Settings::get('Craod.Core.Mailer.settings') as $property => $value) {
			$mail->{$property} = $value;
		}

		$fromMails = array_keys($from);
		$fromMail = $fromMails[0];

		$mail->setFrom($fromMail, $from[$fromMail]);
		foreach ($to as $email => $name) {
			$mail->addAddress($email, $name);
		}
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AltBody = $body;

		foreach (Settings::get('Craod.Core.Mailer.parameters', []) as $option => $value) {
			$mail->{ucfirst($option)} = $value;
		}
		$result = $mail->send();
		if (!$result) {
			throw new MailSendingException($mail->ErrorInfo, 1450849408);
		}
	}
}