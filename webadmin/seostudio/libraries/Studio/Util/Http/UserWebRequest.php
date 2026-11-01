<?php

namespace Studio\Util\Http;

use Exception;

class UserWebRequest extends WebRequest {

	protected $downloaded = 0;
	protected $downloadLimit = 0;

    /**
     * Constructs a new `SafeWebRequest` instance for the given URL.
     *
     * @param string $uri
	 * @param int $limit The number of bytes to limit the response to. If not provided, uses configured default.
     */
    public function __construct($uri, $limit = 0) {
		global $studio;
		parent::__construct($uri);

		if (!$studio) {
			throw new Exception('Cannot instantiate an instance of UserWebRequest outside of the Studio context');
		}

        $this->uri = $uri;
		$this->downloadLimit = $limit > 0 ? $limit : (int) ($studio->getopt('webrequest-download-limit', 8) * 1048576);

        $this->setOption(CURLOPT_SSL_VERIFYHOST, false);
        $this->setOption(CURLOPT_SSL_VERIFYPEER, false);
		$this->setMaxRedirects(8);

		$this->setOption(CURLOPT_WRITEFUNCTION, function($ch, $content) use ($studio) {
			$numBytes = strlen($content);

			if (($this->downloaded += $numBytes) >= $this->downloadLimit) {
				$limit = (int) $studio->getopt('webrequest-download-limit', 8);
				throw new UserResponseTooBigException($this, "Resource exceeds download limit of $limit MB");
			}

            if (is_null($this->bodyBuffer)) {
                $this->bodyBuffer = '';
            }

			$this->bodyBuffer .= $content;
			return $numBytes;
		});
    }

    /**
     * Returns a callback which parses response headers and populates them into the given `$headers` array.
     *
     * @param array $headers
     * @param string[] $httpVersion
     * @return callback
     */
    protected function createHeaderParser(&$headers, &$httpVersion, &$httpMessage) {
		global $studio;

        return (function($handle, $header) use (&$headers, &$httpVersion, &$httpMessage, &$studio) {
            $length = strlen($header);
            $header = explode(':', $header, 2);

			if (($this->downloaded += $length) >= $this->downloadLimit) {
				$limit = (int) $studio->getopt('webrequest-download-limit', 8);
				throw new UserResponseTooBigException($this, "Resource exceeds download limit of $limit MB");
			}

            if (count($header) < 2) {
                if (preg_match('/^HTTP\/(\d\.\d)\s*(.*)$/i', $header[0], $matches)) {
                    $httpVersion = $matches[1];
                    $httpMessage = trim($matches[2]);
                }

                return $length;
            }

            $headerName = strtolower(trim($header[0]));
            $headerValue = trim($header[1]);

            if (!isset($headers[$headerName])) {
                $headers[$headerName] = $headerValue;
            }
            else {
                if (!is_array($headers[$headerName])) {
                    $headers[$headerName] = [$headers[$headerName]];
                }

                $headers[$headerName][] = $headerValue;
            }

            return $length;
        });
    }

    /**
     * Returns a new CURL resource configured for the given parameters.
     *
     * @param string $uri
     * @param array $options
     * @param array $headers
     */
    protected function createHandle($uri, $options, $headers) {
		global $studio;

		$parsed = @parse_url($uri);

		if (!$parsed || !isset($parsed['host'])) {
			throw new Exception('Encountered a malformed URL during redirect: ' . $uri);
		}

        $ch = curl_init($uri);

        // Security: Enforce local hostname policy
		if ($studio->getopt('allow-local-hostnames') !== 'On') {
            $remoteHost = $parsed['host'];
            $remoteAddress = DNS::get($remoteHost);

			if (filter_var($remoteAddress, FILTER_VALIDATE_IP) !== false) {
                if (filter_var($remoteAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                    $publicLink = $studio->getopt('public-url');
                    $publicParsed = @parse_url($publicLink);
                    $allowRequest = false;

                    if ($publicParsed && isset($publicParsed['host'])) {
                        $publicHost = str_replace('www.', '', $publicParsed['host']);
                        $allowed = '/^([a-z0-9\-]+\.)*' . preg_quote($publicHost) . '$/i';

                        if (preg_match($allowed, $remoteHost)) {
                            $allowRequest = true;
                        }
                    }

                    if (!$allowRequest) {
                        throw new Exception('Invalid website');
                    }
                }
            }

            curl_setopt($ch, CURLOPT_RESOLVE, DNS::getResolveArray());
		}

        // Set the options
        curl_setopt_array($ch, $options);

        // Set the headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Set the IP address
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);

        // Return the handle resource
        return $ch;
    }

}
