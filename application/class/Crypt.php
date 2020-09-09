<?php
namespace Ads;

final class Crypt
{
	const
		CIPHER_METHOD = 'aes-256-ctr',
		CRYPT_KEY_LENGTH = 32,
		HASH_ALGORITHM = 'sha256',
        HASH_LENGTH = 64;

	public function __construct()
	{
		if (PHP_VERSION_ID < 50600) {
			throw new CryptException('PHP 5.6.0 or newer is required.');
		} elseif (!extension_loaded('openssl')) {
			throw new CryptException('Missing OpenSSL PHP module.');
		} elseif (!in_array(self::CIPHER_METHOD, openssl_get_cipher_methods(), true)) {
			throw new CryptException('Missing OpenSSL encryption method ' . self::CIPHER_METHOD . '.');
		}
	}

	/**
	 * @param  string
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return string
	 * @throws CryptException
	 */
	public function encryptBinary($text, $encryptKey, $signatureKey, $iv = null)
	{
		if (strlen($encryptKey) !== self::CRYPT_KEY_LENGTH) {
			throw new CryptException('Encryption key needs to be ' . self::CRYPT_KEY_LENGTH . ' bytes long, but ' . strlen($encryptKey) . ' given.');
		}

		if ($iv === null) {
			$iv = self::randomBytes(openssl_cipher_iv_length(self::CIPHER_METHOD));
		} elseif (($ivLen = strlen($iv)) !== ($ivNeedLen = openssl_cipher_iv_length(self::CIPHER_METHOD))) {
			throw new CryptException("IV needs to be exactly $ivNeedLen bytes long, $ivLen given.");
		}

		$ciphered = $iv . openssl_encrypt(
			$text,
			self::CIPHER_METHOD,
			$encryptKey,
			OPENSSL_RAW_DATA,
			$iv
		);

		return hash_hmac(self::HASH_ALGORITHM, $ciphered, $signatureKey) . $ciphered;
	}


	/**
	 * @param  string
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return string
	 * @throws CryptException
	 */
	public function encrypt($text, $encryptKey, $signatureKey, $iv = null)
	{
		return base64_encode($this->encryptBinary($text, $encryptKey, $signatureKey, $iv));
	}


	/**
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return string
	 * @throws CryptException
	 */
	public function decryptBinary($text, $encryptKey, $signatureKey)
	{
		if (strlen($text) < self::HASH_LENGTH) {
			throw new CryptException('Signed encrypted message is only ' . strlen($text) . ' bytes long, expected more.');
		}

		$hmac = substr($text, 0, self::HASH_LENGTH);
		$text = substr($text, self::HASH_LENGTH);

		if (!hash_equals(hash_hmac(self::HASH_ALGORITHM, $text, $signatureKey), $hmac)) {
			throw new CryptException('Message signature does not match.');
		}

		$ivLength = openssl_cipher_iv_length(self::CIPHER_METHOD);
		if (strlen($text) < $ivLength) {
			throw new CryptException('Encrypted message is only ' . strlen($text) . ' bytes long, expected more.');
		}

		$iv = substr($text, 0, $ivLength);
		$text = substr($text, $ivLength);

		$decrypted = openssl_decrypt(
			$text,
			self::CIPHER_METHOD,
			$encryptKey,
			OPENSSL_RAW_DATA,
			$iv
		);

		if ($decrypted === false) {
			throw new CryptException('Message decryption failed.');
		}

		return $decrypted;
	}


	/**
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return string
	 * @throws CryptException
	 */
	public function decrypt($text, $encryptKey, $signatureKey)
	{
		return $this->decryptBinary(base64_decode($text), $encryptKey, $signatureKey);
	}


	private static function randomBytes($length)
	{
		if ($length < 1) {
			throw new CryptException('Length must be greater then zero.');
		}

		if (!defined('PHP_WINDOWS_VERSION_BUILD') && is_readable('/dev/urandom')) {
			return file_get_contents('/dev/urandom', false, null, -1, $length);
		}

		$bytes = openssl_random_pseudo_bytes($length, $secure);
		if ($secure !== true) {
			throw new CryptException('Random bytes are not cryptographically strong.');
		}

		return $bytes;
	}
}


class CryptException extends \RuntimeException
{
}