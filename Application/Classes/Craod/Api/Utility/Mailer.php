<?php

namespace Craod\Api\Utility;

use Craod\Api\Model\User;
use Craod\Api\Exception\MailSendingException;

/**
 * The mailer utility
 *
 * @package Craod\Api\Utility
 */
class Mailer {

	/**
	 * Send an email using the SMTP configuration
	 *
	 * @param array $from An associative array with an email as a key and a name as a value
	 * @param User[]|array $to Either an associative array with emails as keys and names as values or an array of Users
	 * @param string $subject
	 * @param string $body
	 * @return string
	 * @throws MailSendingException
	 */
	public static function sendMail(array $from, array $to, $subject, $body) {
		$mail = new \PHPMailer();
		$mail->isSMTP();
		foreach (Settings::get('Craod.Api.mail.settings') as $property => $value) {
			$mail->{$property} = $value;
		}

		$fromMails = array_keys($from);
		$fromMail = $fromMails[0];

		$mail->setFrom($fromMail, $from[$fromMail]);
		foreach ($to as $email => $nameOrUser) {
			if ($nameOrUser instanceof User) {
				$email = $nameOrUser->getEmail();
				$name = $nameOrUser->getFirstName() . ' ' . $nameOrUser->getLastName();
			} else {
				$name = $nameOrUser;
			}
			$mail->addAddress($email, $name);
		}
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AltBody = $body;
		$mail->Helo = 'craod.com';
		$result = $mail->send();
		if (!$result) {
			throw new MailSendingException($mail->ErrorInfo, 1450849408);
		}
	}
}