<?php
namespace app\models;

/**
 * UrlRule represents a rule used by [[UrlManager]] for parsing and generating URLs.
 *
 * To define your own URL parsing and creation logic you can extend from this class
 * and add it to [[TwigUrlManager::rules]] like this:
 *
 * ```php
 * 'rules' => [
 *     ['class' => 'MyUrlRule', 'pattern' => '...', 'route' => 'site/index', ...],
 *     // ...
 * ]
 * ```
 *
 * @property-read null|int $createUrlStatus Status of the URL creation after the last [[createUrl()]] call.
 * `null` if rule does not provide info about create status. This property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TwigUrlRule
{
    /**
     * Set [[mode]] with this value to mark that this rule is for URL parsing only.
     */
    const PARSING_ONLY = 1;
    /**
     * Set [[mode]] with this value to mark that this rule is for URL creation only.
     */
    const CREATION_ONLY = 2;
    /**
     * Represents the successful URL generation by last [[createUrl()]] call.
     * @see $createStatus
     * @since 2.0.12
     */
    const CREATE_STATUS_SUCCESS = 0;
    /**
     * Represents the unsuccessful URL generation by last [[createUrl()]] call, because rule does not support
     * creating URLs.
     * @see $createStatus
     * @since 2.0.12
     */
    const CREATE_STATUS_PARSING_ONLY = 1;
    /**
     * Represents the unsuccessful URL generation by last [[createUrl()]] call, because of mismatched route.
     * @see $createStatus
     * @since 2.0.12
     */
    const CREATE_STATUS_ROUTE_MISMATCH = 2;
    /**
     * Represents the unsuccessful URL generation by last [[createUrl()]] call, because of mismatched
     * or missing parameters.
     * @see $createStatus
     * @since 2.0.12
     */
    const CREATE_STATUS_PARAMS_MISMATCH = 4;

    /**
     * @var string the name of this rule. If not set, it will use [[pattern]] as the name.
     */
    public $name;
    /**
     * On the rule initialization, the [[pattern]] matching parameters names will be replaced with [[placeholders]].
     * @var string the pattern used to parse and create the path info part of a URL.
     * @see host
     * @see placeholders
     */
    public $pattern;
    /**
     * @var string the pattern used to parse and create the host info part of a URL (e.g. `http://example.com`).
     * @see pattern
     */
    public $host;
    /**
     * @var string the route to the controller action
     */
    public $route;
    /**
     * @var mixed[] the default GET parameters (name => value) that this rule provides.
     * When this rule is used to parse the incoming request, the values declared in this property
     * will be injected into $_GET.
     */
    public $defaults = [];
    /**
     * @var string the URL suffix used for this rule.
     * For example, ".html" can be used so that the URL looks like pointing to a static HTML page.
     * If not set, the value of [[TwigUrlManager::suffix]] will be used.
     */
    public $suffix;
    /**
     * @var int a value indicating if this rule should be used for both request parsing and URL creation,
     * parsing only, or creation only.
     * If not set or 0, it means the rule is both request parsing and URL creation.
     * If it is [[PARSING_ONLY]], the rule is for request parsing only.
     * If it is [[CREATION_ONLY]], the rule is for URL creation only.
     */
    public $mode;
    /**
     * @var bool a value indicating if parameters should be url encoded.
     */
    public $encodeParams = true;

    /**
     * @var int|null status of the URL creation after the last [[createUrl()]] call.
     * @since 2.0.12
     */
    protected $createStatus;
    /**
     * @var string[] list of placeholders for matching parameters names. Used in [[parseRequest()]], [[createUrl()]].
     * On the rule initialization, the [[pattern]] parameters names will be replaced with placeholders.
     * This array contains relations between the original parameters names and their placeholders.
     * The array keys are the placeholders and the values are the original names.
     *
     * @see parseRequest()
     * @see createUrl()
     * @since 2.0.7
     */
    protected $placeholders = [];

    /**
     * @var string|null the template for generating a new URL. This is derived from [[pattern]] and is used in generating URL.
     */
    private $_template;
    /**
     * @var string the regex for matching the route part. This is used in generating URL.
     */
    private $_routeRule;
    /**
     * @var string[] list of regex for matching parameters. This is used in generating URL.
     */
    private $_paramRules = [];
    /**
     * @var string[] list of parameters used in the route.
     */
    private $_routeParams = [];

    /**
     * @return string
     * @since 2.0.11
     */
    public function __toString()
    {
        $str = '';
        if (null !== $this->host && strrpos($this->name, $this->host) === false) {
            $str .= $this->host . '/';
        }
        $str .= $this->name;

        if ('' === $str) {
            return '/';
        }

        return $str;
    }

    /**
     * Initializes this rule.
     *
     * @return void
     */
    public function init()
    {
        if (null === $this->pattern) {
            return;
        }
        if (null === $this->route) {
            return;
        }
        if (null === $this->name) {
            $this->name = $this->pattern;
        }

        $this->preparePattern();
    }

    /**
     * Process [[$pattern]] on rule initialization.
     *
     * @return void
     */
    private function preparePattern()
    {
        $this->pattern = $this->trimSlashes($this->pattern);
        $this->route   = trim($this->route, '/');

        if (null !== $this->host) {
            $this->host    = rtrim($this->host, '/');
            $this->pattern = rtrim($this->host . '/' . $this->pattern, '/');
        } elseif ('' === $this->pattern) {
            $this->_template = '';
            $this->pattern   = '#^$#u';

            return;
        } elseif (($pos = strpos($this->pattern, '://')) !== false) {
            if (($pos2 = strpos($this->pattern, '/', $pos + 3)) !== false) {
                $this->host = substr($this->pattern, 0, $pos2);
            } else {
                $this->host = $this->pattern;
            }
        } elseif (strncmp($this->pattern, '//', 2) === 0) {
            if (($pos2 = strpos($this->pattern, '/', 2)) !== false) {
                $this->host = substr($this->pattern, 0, $pos2);
            } else {
                $this->host = $this->pattern;
            }
        } else {
            $this->pattern = '/' . $this->pattern . '/';
        }

        if (strpos($this->route, '<') !== false && preg_match_all('/<([\w._-]+)>/', $this->route, $matches)) {
            foreach ($matches[1] as $name) {
                $this->_routeParams[$name] = "<$name>";
            }
        }

        $this->translatePattern(true);
    }

    /**
     * Prepares [[$pattern]] on rule initialization - replace parameter names by placeholders.
     *
     * @param bool $allowAppendSlash Defines position of slash in the param pattern in [[$pattern]].
     * If `false` slash will be placed at the beginning of param pattern. If `true` slash position will be detected
     * depending on non-optional pattern part.
     *
     * @return void
     */
    private function translatePattern($allowAppendSlash)
    {
        $tr = [
            '.' => '\\.',
            '*' => '\\*',
            '$' => '\\$',
            '[' => '\\[',
            ']' => '\\]',
            '(' => '\\(',
            ')' => '\\)',
        ];

        $tr2                 = [];
        $requiredPatternPart = $this->pattern;
        $oldOffset           = 0;
        if (preg_match_all('/<([\w._-]+):?([^>]+)?>/', $this->pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            $appendSlash = false;
            foreach ($matches as $match) {
                $name                             = $match[1][0];
                $pattern                          = isset($match[2][0]) ? $match[2][0] : '[^\/]+';
                $placeholder                      = 'a' . hash('crc32b', $name); // placeholder must begin with a letter
                $this->placeholders[$placeholder] = $name;
                if (array_key_exists($name, $this->defaults)) {
                    $length              = strlen($match[0][0]);
                    $offset              = $match[0][1];
                    $requiredPatternPart = str_replace("/{$match[0][0]}/", '//', $requiredPatternPart);
                    if (
                        $allowAppendSlash
                        && ($appendSlash || 1 === $offset)
                        && (($offset - $oldOffset) === 1)
                        && isset($this->pattern[$offset + $length])
                        && '/' === $this->pattern[$offset + $length] && isset($this->pattern[$offset + $length + 1])
                    ) {
                        // if pattern starts from optional params, put slash at the end of param pattern
                        // @see https://github.com/yiisoft/yii2/issues/13086
                        $appendSlash    = true;
                        $tr["<$name>/"] = "((?P<$placeholder>$pattern)/)?";
                    } elseif (
                        $offset > 1
                        && '/' === $this->pattern[$offset - 1] && (!isset($this->pattern[$offset + $length]) || '/' === $this->pattern[$offset + $length])
                    ) {
                        $appendSlash    = false;
                        $tr["/<$name>"] = "(/(?P<$placeholder>$pattern))?";
                    }
                    $tr["<$name>"] = "(?P<$placeholder>$pattern)?";
                    $oldOffset     = $offset + $length;
                } else {
                    $appendSlash   = false;
                    $tr["<$name>"] = "(?P<$placeholder>$pattern)";
                }

                if (isset($this->_routeParams[$name])) {
                    $tr2["<$name>"] = "(?P<$placeholder>$pattern)";
                } else {
                    $this->_paramRules[$name] = '[^\/]+' === $pattern ? '' : "#^$pattern$#u";
                }
            }
        }

        // we have only optional params in route - ensure slash position on param patterns
        if ($allowAppendSlash && trim($requiredPatternPart, '/') === '') {
            $this->translatePattern(false);
            return;
        }

        $this->_template = preg_replace('/<([\w._-]+):?([^>]+)?>/', '<$1>', $this->pattern);
        $this->pattern   = '#^' . trim(strtr($this->_template, $tr), '/') . '$#u';

        // if host starts with relative scheme, then insert pattern to match any
        if (strncmp($this->host, '//', 2) === 0) {
            $this->pattern = substr_replace($this->pattern, '[\w]+://', 2, 0);
        }

        if (!empty($this->_routeParams)) {
            $this->_routeRule = '#^' . strtr($this->route, $tr2) . '$#u';
        }
    }

    /**
     * Parses the given request and returns the corresponding route and parameters.
     * @return mixed[]|bool the parsing result. The route and the parameters are returned as an array.
     * If `false`, it means this rule cannot be used to parse this path info.
     */
    public function parseRequest()
    {
        if (self::CREATION_ONLY === $this->mode) {
            return false;
        }

        $suffix   = $this->suffix;
        $pathInfo = explode('?', $_SERVER['REQUEST_URI'], 2);
        $pathInfo = ltrim(reset($pathInfo), '/');
        if ('' !== $suffix && '' !== $pathInfo) {
            $n = strlen($suffix);
            if (substr_compare($pathInfo, $suffix, -$n, $n) === 0) {
                $pathInfo = substr($pathInfo, 0, -$n);
                if ('' === $pathInfo) {
                    // suffix alone is not allowed
                    return false;
                }
            } else {
                return false;
            }
        }

        if (null !== $this->host) {
            $pathInfo = 'https://www.arcooda.com' . ('' === $pathInfo ? '' : '/' . $pathInfo);
        }

        if (!preg_match($this->pattern, $pathInfo, $matches)) {
            return false;
        }
        $matches = $this->substitutePlaceholderNames($matches);

        foreach ($this->defaults as $name => $value) {
            if (!isset($matches[$name]) || '' === $matches[$name]) {
                $matches[$name] = $value;
            }
        }
        $params = $this->defaults;
        $tr     = [];
        foreach ($matches as $name => $value) {
            if (isset($this->_routeParams[$name])) {
                $tr[$this->_routeParams[$name]] = $value;
                unset($params[$name]);
            } elseif (isset($this->_paramRules[$name])) {
                $params[$name] = $value;
            }
        }
        if (null !== $this->_routeRule) {
            $route = strtr($this->route, $tr);
        } else {
            $route = $this->route;
        }

        return [$route, $params];
    }

    /**
     * Creates a URL according to the given route and parameters.
     * @param string $route the route. It should not have slashes at the beginning or the end.
     * @param array $params the parameters
     * @return string|bool the created URL, or `false` if this rule cannot be used for creating this URL.
     */
    public function createUrl($route, $params)
    {
        if (self::PARSING_ONLY === $this->mode) {
            $this->createStatus = self::CREATE_STATUS_PARSING_ONLY;
            return false;
        }

        $tr = [];

        // match the route part first
        if ($route !== $this->route) {
            if (null !== $this->_routeRule && preg_match($this->_routeRule, $route, $matches)) {
                $matches = $this->substitutePlaceholderNames($matches);
                foreach ($this->_routeParams as $name => $token) {
                    if (isset($this->defaults[$name]) && strcmp($this->defaults[$name], $matches[$name]) === 0) {
                        $tr[$token] = '';
                    } else {
                        $tr[$token] = $matches[$name];
                    }
                }
            } else {
                $this->createStatus = self::CREATE_STATUS_ROUTE_MISMATCH;
                return false;
            }
        }

        // match default params
        // if a default param is not in the route pattern, its value must also be matched
        foreach ($this->defaults as $name => $value) {
            if (isset($this->_routeParams[$name])) {
                continue;
            }
            if (!isset($params[$name])) {
                // allow omit empty optional params
                // @see https://github.com/yiisoft/yii2/issues/10970
                if (in_array($name, $this->placeholders) && strcmp($value, '') === 0) {
                    $params[$name] = '';
                } else {
                    $this->createStatus = self::CREATE_STATUS_PARAMS_MISMATCH;
                    return false;
                }
            }
            if (strcmp($params[$name], $value) === 0) {
                // strcmp will do string conversion automatically
                unset($params[$name]);
                if (isset($this->_paramRules[$name])) {
                    $tr["<$name>"] = '';
                }
            } elseif (!isset($this->_paramRules[$name])) {
                $this->createStatus = self::CREATE_STATUS_PARAMS_MISMATCH;
                return false;
            }
        }

        // match params in the pattern
        foreach ($this->_paramRules as $name => $rule) {
            if (isset($params[$name]) && !is_array($params[$name]) && ('' === $rule || preg_match($rule, $params[$name]))) {
                $tr["<$name>"] = $this->encodeParams ? urlencode($params[$name]) : $params[$name];
                unset($params[$name]);
            } elseif (!isset($this->defaults[$name]) || isset($params[$name])) {
                $this->createStatus = self::CREATE_STATUS_PARAMS_MISMATCH;
                return false;
            }
        }

        $url = $this->trimSlashes(strtr($this->_template, $tr));
        if (null !== $this->host) {
            $pos = strpos($url, '/', 8);
            if (false !== $pos) {
                $url = substr($url, 0, $pos) . preg_replace('#/+#', '/', substr($url, $pos));
            }
        } elseif (strpos($url, '//') !== false) {
            $url = preg_replace('#/+#', '/', trim($url, '/'));
        }

        if ('' !== $url) {
            $url .= $this->suffix;
        }

        if (!empty($params) && ($query = http_build_query($params)) !== '') {
            $url .= '?' . $query;
        }

        $this->createStatus = self::CREATE_STATUS_SUCCESS;
        return $url;
    }

    /**
     * Returns status of the URL creation after the last [[createUrl()]] call.
     *
     * @return null|int Status of the URL creation after the last [[createUrl()]] call. `null` if rule does not provide
     * info about create status.
     * @see $createStatus
     * @since 2.0.12
     */
    public function getCreateUrlStatus()
    {
        return $this->createStatus;
    }

    /**
     * Returns list of regex for matching parameter.
     * @return string[] parameter keys and regexp rules.
     *
     * @since 2.0.6
     */
    protected function getParamRules()
    {
        return $this->_paramRules;
    }

    /**
     * Iterates over [[placeholders]] and checks whether each placeholder exists as a key in $matches array.
     * When found - replaces this placeholder key with a appropriate name of matching parameter.
     * Used in [[parseRequest()]], [[createUrl()]].
     *
     * @param string[] $matches result of `preg_match()` call
     * @return string[] input array with replaced placeholder keys
     * @see placeholders
     * @since 2.0.7
     */
    protected function substitutePlaceholderNames($matches)
    {
        foreach ($this->placeholders as $placeholder => $name) {
            if (isset($matches[$placeholder])) {
                $matches[$name] = $matches[$placeholder];
                unset($matches[$placeholder]);
            }
        }

        return $matches;
    }

    /**
     * Trim slashes in passed string. If string begins with '//', two slashes are left as is
     * in the beginning of a string.
     *
     * @param string $string
     * @return string
     */
    private function trimSlashes($string)
    {
        if (strncmp($string, '//', 2) === 0) {
            return '//' . trim($string, '/');
        }

        return trim($string, '/');
    }

    /**
     * @param mixed[]|null $args
     */
    public function __construct($args)
    {
        if (is_null($args)) {
            return;
        }
        foreach ($args as $k => $v) {
            $this->$k = $v;
        }
        $this->init();
    }
}
