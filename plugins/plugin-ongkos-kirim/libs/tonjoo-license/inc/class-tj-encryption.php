<?php

if ( ! class_exists( 'TJ_Encryption' ) ) {

	/**
	 * Tonjoo encryption class
	 */
	class TJ_Encryption {

		/**
		 * Encode strings
		 *
		 * @param  string $string   String to encode.
		 * @param  string $key      Encryption key.
		 * @return string           Encoded string.
		 */
		public static function encode( $string, $key = '' ) {
			$key = self::get_key( $key );
			$enc = self::_xor_encode( $string, $key );
			return base64_encode( $enc );
		}

		/**
		 * Get key
		 *
		 * @param  string $key Key.
		 * @return string      Key.
		 */
		public static function get_key( $key = '' ) {
			if ( '' === $key ) {
				if ( defined( '_SALT' ) ) {
					$key = _SALT;
				} else {
					$key = "'*#@jh$%[*H@nb]+)@Dhl;,E';";
				}
			}
			return md5( $key );
		}

		/**
		 * Set encryption key
		 *
		 * @param string $key Key.
		 */
		public function set_key( $key = '' ) {
			$this->encryption_key = $key;
		}

		/**
		 * XOR Encode
		 *
		 * @param  string $string String to encode.
		 * @param  string $key    Encryiption key.
		 * @return string         Encrypted string.
		 */
		public static function _xor_encode( $string, $key ) {
			$rand = '';
			while ( strlen( $rand ) < 32 ) {
				$rand .= mt_rand( 0, mt_getrandmax() );
			}
			$rand = self::hash( $rand );
			$enc = '';
			for ( $i = 0; $i < strlen( $string ); $i++ ) {
				$enc .= substr( $rand, ($i % strlen( $rand )), 1 ) . (substr( $rand, ($i % strlen( $rand )), 1 ) ^ substr( $string, $i, 1 ));
			}
			return self::_xor_merge( $enc, $key );
		}

		/**
		 * XOR Decode
		 *
		 * @param  string $string String to decode.
		 * @param  string $key    Encryption key.
		 * @return string         Decoded string.
		 */
		public static function _xor_decode( $string, $key ) {
			$string = self::_xor_merge( $string, $key );
			$dec = '';
			for ( $i = 0; $i < strlen( $string ); $i++ ) {
				$dec .= (substr( $string, $i++, 1 ) ^ substr( $string, $i, 1 ));
			}
			return $dec;
		}

		/**
		 * XOR Merge
		 *
		 * @param  string $string String to merge.
		 * @param  string $key    Encryption key.
		 * @return string         Merged string.
		 */
		public static function _xor_merge( $string, $key ) {
			$hash = self::hash( $key );
			$str = '';
			for ( $i = 0; $i < strlen( $string ); $i++ ) {
				$str .= substr( $string, $i, 1 ) ^ substr( $hash, ($i % strlen( $hash )), 1 );
			}
			return $str;
		}

		/**
		 * Set Hash
		 *
		 * @param string $type Hash type.
		 */
		public static function set_hash( $type = 'sha1' ) {
			$this->_hash_type = ( 'sha1' !== $type && 'md5' !== $type ) ? 'sha1' : $type;
		}

		/**
		 * Hash the string
		 *
		 * @param  string $str Plain string.
		 * @return string      Hash.
		 */
		public static function hash( $str ) {
			return self::sha1( $str );
		}

		/**
		 * SHA1 hash
		 *
		 * @param  string $str Plain string.
		 * @return string      Hash.
		 */
		public static function sha1( $str ) {
			if ( ! function_exists( 'sha1' ) ) {
				if ( ! function_exists( 'mhash' ) ) {
					$sh = _class( 'sha' );
					return $sh->generate( $str );
				} else {
					return bin2hex( mhash( MHASH_SHA1, $str ) );
				}
			} else {
				return sha1( $str );
			}
		}
	}
}
