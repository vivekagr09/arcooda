<?php
namespace app\models;

use app\models\TwigUrlRule;

/**
 * UrlManager handles HTTP request parsing and creation of URLs based on a set of rules.
 *
 * Rules are classes implementing the [[UrlRuleInterface]], by default that is [[UrlRule]].
 * For nesting rules, there is also a [[GroupUrlRule]] class.
 *
 * For more details and usage information on UrlManager, see the [guide article on routing](guide:runtime-routing).
 *
 * @property string $baseUrl The base URL that is used by [[createUrl()]] to prepend to created URLs.
 * @property string $hostInfo The host info (e.g. `http://www.example.com`) that is used by
 * [[createAbsoluteUrl()]] to prepend to created URLs.
 * @property string $scriptUrl The entry script URL that is used by [[createUrl()]] to prepend to created
 * URLs.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TwigUrlManager
{
    /**
     * @var array the rules for creating and parsing URLs. Each element in the array
     * is the configuration array for creating a single URL rule. The configuration will
     * be merged with [[ruleConfig]] first before it is used for creating the rule object.
     *
     * A special shortcut format can be used if a rule only specifies [[TwigUrlRule::pattern|pattern]]
     * and [[TwigUrlRule::route|route]]: `'pattern' => 'route'`. That is, instead of using a configuration
     * array, one can use the key to represent the pattern and the value the corresponding route.
     * For example, `'post/<id:\d+>' => 'post/view'`.
     *
     * For RESTful routing the mentioned shortcut format also allows you to specify the
     * [[TwigUrlRule::verb|HTTP verb]] that the rule should apply for.
     * You can do that  by prepending it to the pattern, separated by space.
     * For example, `'PUT post/<id:\d+>' => 'post/update'`.
     * You may specify multiple verbs by separating them with comma
     * like this: `'POST,PUT post/index' => 'post/create'`.
     * The supported verbs in the shortcut format are: GET, HEAD, POST, PUT, PATCH and DELETE.
     * Note that [[TwigUrlRule::mode|mode]] will be set to PARSING_ONLY when specifying verb in this way
     * so you normally would not specify a verb for normal GET request.
     *
     * Here is an example configuration for RESTful CRUD controller:
     *
     * ```php
     * [
     *     'dashboard' => 'site/index',
     *
     *     'POST <controller:[\w-]+>' => '<controller>/create',
     *     '<controller:[\w-]+>s' => '<controller>/index',
     *
     *     'PUT <controller:[\w-]+>/<id:\d+>'    => '<controller>/update',
     *     'DELETE <controller:[\w-]+>/<id:\d+>' => '<controller>/delete',
     *     '<controller:[\w-]+>/<id:\d+>'        => '<controller>/view',
     * ];
     * ```
     *
     * Note that if you modify this property after the UrlManager object is created, make sure
     * you populate the array with rule objects instead of rule configurations.
     */
    public $rules = [];

    private $_ruleCache;


    /**
     * Initializes UrlManager.
     */
    public function init()
    {
        if (!empty($this->rules)) {
            $this->rules = $this->buildRules($this->rules);
        }
    }

    /**
     * Adds additional URL rules.
     *
     * This method will call [[buildRules()]] to parse the given rule declarations and then append or insert
     * them to the existing [[rules]].
     *
     * @param array $rules the new rules to be added. Each array element represents a single rule declaration.
     * Please refer to [[rules]] for the acceptable rule format.
     * @param bool $append whether to add the new rules by appending them to the end of the existing rules.
     */
    public function addRules($rules, $append = true)
    {
        $rules = $this->buildRules($rules);
        if ($append) {
            $this->rules = array_merge($this->rules, $rules);
        } else {
            $this->rules = array_merge($rules, $this->rules);
        }
    }

    /**
     * Builds URL rule objects from the given rule declarations.
     *
     * @param array $ruleDeclarations the rule declarations. Each array element represents a single rule declaration.
     * Please refer to [[rules]] for the acceptable rule formats.
     * @return TwigUrlRule[] the rule objects built from the given rule declarations
     */
    protected function buildRules($ruleDeclarations)
    {
        $builtRules = [];
        $verbs = 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS';
        foreach ($ruleDeclarations as $key => $rule) {
            if (is_string($rule)) {
                $rule = ['route' => $rule];
                if (preg_match("/^((?:($verbs),)*($verbs))\\s+(.*)$/", $key, $matches)) {
                    $rule['verb'] = explode(',', $matches[1]);
                    $key = $matches[4];
                }
                $rule['pattern'] = $key;
            }
            if (is_array($rule)) {
                $rule = new TwigUrlRule($rule);
            }
            $builtRules[] = $rule;
        }

        return $builtRules;
    }

    /**
     * Parses the user request.
     * @param Request $request the request component
     * @return array|bool the route and the associated parameters. The latter is always empty
     */
    public function parseRequest()
    {
        /* @var $rule TwigUrlRule */
        foreach ($this->rules as $rule) {
            $result = $rule->parseRequest();
            if ($result !== false) {
                return $result;
            }
        }

        return false;
    }

    /**
     * Creates a URL using the given route and query parameters.
     *
     * You may specify the route as a string, e.g., `site/index`. You may also use an array
     * if you want to specify additional query parameters for the URL being created. The
     * array format must be:
     *
     * ```php
     * // generates: /index.php?r=site%2Findex&param1=value1&param2=value2
     * ['site/index', 'param1' => 'value1', 'param2' => 'value2']
     * ```
     *
     * If you want to create a URL with an anchor, you can use the array format with a `#` parameter.
     * For example,
     *
     * ```php
     * // generates: /index.php?r=site%2Findex&param1=value1#name
     * ['site/index', 'param1' => 'value1', '#' => 'name']
     * ```
     *
     * The URL created is a relative one. Use [[createAbsoluteUrl()]] to create an absolute URL.
     *
     * Note that unlike [[\yii\helpers\Url::toRoute()]], this method always treats the given route
     * as an absolute route.
     *
     * @param string|array $params use a string to represent a route (e.g. `site/index`),
     * or an array to represent a route with query parameters (e.g. `['site/index', 'param1' => 'value1']`).
     * @return string the created URL
     */
    public function createUrl($params)
    {
        $params = (array) $params;
        $anchor = isset($params['#']) ? '#' . $params['#'] : '';
        unset($params['#']);

        $route = trim($params[0], '/');
        unset($params[0]);

        $baseUrl = $this->getBaseUrl();

        $cacheKey = $route . '?';
        foreach ($params as $key => $value) {
            if ($value !== null) {
                $cacheKey .= $key . '&';
            }
        }

        $url = $this->getUrlFromCache($cacheKey, $route, $params);
        if ($url === false) {
            /* @var $rule UrlRule */
            foreach ($this->rules as $rule) {
                if (in_array($rule, $this->_ruleCache[$cacheKey], true)) {
                    // avoid redundant calls of `TwigUrlRule::createUrl()` for rules checked in `getUrlFromCache()`
                    // @see https://github.com/yiisoft/yii2/issues/14094
                    continue;
                }
                $url = $rule->createUrl($this, $route, $params);
                if ($this->canBeCached($rule)) {
                    $this->setRuleToCache($cacheKey, $rule);
                }
                if ($url !== false) {
                    break;
                }
            }
        }

        if ($url !== false) {
            if (strpos($url, '://') !== false) {
                if ($baseUrl !== '' && ($pos = strpos($url, '/', 8)) !== false) {
                    return substr($url, 0, $pos) . $baseUrl . substr($url, $pos) . $anchor;
                }

                return $url . $baseUrl . $anchor;
            } elseif (strncmp($url, '//', 2) === 0) {
                if ($baseUrl !== '' && ($pos = strpos($url, '/', 2)) !== false) {
                    return substr($url, 0, $pos) . $baseUrl . substr($url, $pos) . $anchor;
                }

                return $url . $baseUrl . $anchor;
            }

            $url = ltrim($url, '/');
            return "$baseUrl/{$url}{$anchor}";
        }

        if (!empty($params) && ($query = http_build_query($params)) !== '') {
            $route .= '?' . $query;
        }

        $route = ltrim($route, '/');
        return "$baseUrl/{$route}{$anchor}";
    }

    /**
     * Returns the value indicating whether result of [[createUrl()]] of rule should be cached in internal cache.
     *
     * @param UrlRuleInterface $rule
     * @return bool `true` if result should be cached, `false` if not.
     * @since 2.0.12
     * @see getUrlFromCache()
     * @see setRuleToCache()
     * @see TwigUrlRule::getCreateUrlStatus()
     */
    protected function canBeCached($rule)
    {
        return
            // if rule does not provide info about create status, we cache it every time to prevent bugs like #13350
            // @see https://github.com/yiisoft/yii2/pull/13350#discussion_r114873476
            !method_exists($rule, 'getCreateUrlStatus') || ($status = $rule->getCreateUrlStatus()) === null
            || $status === TwigUrlRule::CREATE_STATUS_SUCCESS
            || $status & TwigUrlRule::CREATE_STATUS_PARAMS_MISMATCH;
    }

    /**
     * Get URL from internal cache if exists.
     * @param string $cacheKey generated cache key to store data.
     * @param string $route the route (e.g. `site/index`).
     * @param array $params rule params.
     * @return bool|string the created URL
     * @see createUrl()
     * @since 2.0.8
     */
    protected function getUrlFromCache($cacheKey, $route, $params)
    {
        if (!empty($this->_ruleCache[$cacheKey])) {
            foreach ($this->_ruleCache[$cacheKey] as $rule) {
                /* @var $rule UrlRule */
                if (($url = $rule->createUrl($this, $route, $params)) !== false) {
                    return $url;
                }
            }
        } else {
            $this->_ruleCache[$cacheKey] = [];
        }

        return false;
    }

    /**
     * Store rule (e.g. [[UrlRule]]) to internal cache.
     * @param $cacheKey
     * @param UrlRuleInterface $rule
     * @since 2.0.8
     */
    protected function setRuleToCache($cacheKey, $rule)
    {
        $this->_ruleCache[$cacheKey][] = $rule;
    }

    /**
     * Creates an absolute URL using the given route and query parameters.
     *
     * This method prepends the URL created by [[createUrl()]] with the [[hostInfo]].
     *
     * Note that unlike [[\yii\helpers\Url::toRoute()]], this method always treats the given route
     * as an absolute route.
     *
     * @param string|array $params use a string to represent a route (e.g. `site/index`),
     * or an array to represent a route with query parameters (e.g. `['site/index', 'param1' => 'value1']`).
     * @param string|null $scheme the scheme to use for the URL (either `http`, `https` or empty string
     * for protocol-relative URL).
     * If not specified the scheme of the current request will be used.
     * @return string the created URL
     * @see createUrl()
     */
    public function createAbsoluteUrl($params, $scheme = null)
    {
        $params = (array) $params;
        $url = $this->createUrl($params);
        if (strpos($url, '://') === false) {
            $hostInfo = $this->getHostInfo();
            if (strncmp($url, '//', 2) === 0) {
                $url = substr($hostInfo, 0, strpos($hostInfo, '://')) . ':' . $url;
            } else {
                $url = $hostInfo . $url;
            }
        }

        return $url;
        //return Url::ensureScheme($url, $scheme);
    }

    /**
     * Returns the base URL that is used by [[createUrl()]] to prepend to created URLs.
     * @return string the base URL that is used by [[createUrl()]] to prepend to created URLs.
     */
    public function getBaseUrl()
    {
        return ''; //TODO: Seems to need the entry script path relative to document root?
    }

    /**
     * Returns the entry script URL that is used by [[createUrl()]] to prepend to created URLs.
     * @return string the entry script URL that is used by [[createUrl()]] to prepend to created URLs.
     */
    public function getScriptUrl()
    {
        return '/'; //TODO: Seems to need the entry script path relative to document root?
    }

    /**
     * Returns the host info that is used by [[createAbsoluteUrl()]] to prepend to created URLs.
     * @return string the host info (e.g. `http://www.example.com`) that is used by [[createAbsoluteUrl()]] to prepend to created URLs.
     */
    public function getHostInfo()
    {
        return 'https://www.arcooda.com';
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
