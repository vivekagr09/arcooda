<?php
namespace app\models;

use app\models\TwigUrlManager;

/**
 * BaseUrl provides concrete implementation for [[Url]].
 *
 * Do not use BaseUrl. Use [[Url]] instead.
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @since 2.0
 */
class TwigUrl
{
    /**
     * @var \yii\web\UrlManager URL manager to use for creating URLs
     * @since 2.0.8
     */
    public static $urlManager;


    /**
     * Creates a URL for the given route.
     *
     * This method will use [[\yii\web\UrlManager]] to create a URL.
     *
     * You may specify the route as a string, e.g., `site/index`. You may also use an array
     * if you want to specify additional query parameters for the URL being created. The
     * array format must be:
     *
     * ```php
     * // generates: /index.php?r=site/index&param1=value1&param2=value2
     * ['site/index', 'param1' => 'value1', 'param2' => 'value2']
     * ```
     *
     * If you want to create a URL with an anchor, you can use the array format with a `#` parameter.
     * For example,
     *
     * ```php
     * // generates: /index.php?r=site/index&param1=value1#name
     * ['site/index', 'param1' => 'value1', '#' => 'name']
     * ```
     *
     * A route may be either absolute or relative. An absolute route has a leading slash (e.g. `/site/index`),
     * while a relative route has none (e.g. `site/index` or `index`). A relative route will be converted
     * into an absolute one by the following rules:
     *
     * - If the route is an empty string, the current [[\yii\web\Controller::route|route]] will be used;
     * - If the route contains no slashes at all (e.g. `index`), it is considered to be an action ID
     *   of the current controller and will be prepended with [[\yii\web\Controller::uniqueId]];
     * - If the route has no leading slash (e.g. `site/index`), it is considered to be a route relative
     *   to the current module and will be prepended with the module's [[\yii\base\Module::uniqueId|uniqueId]].
     *
     * Starting from version 2.0.2, a route can also be specified as an alias. In this case, the alias
     * will be converted into the actual route first before conducting the above transformation steps.
     *
     * Below are some examples of using this method:
     *
     * ```php
     * // /index.php?r=site%2Findex
     * echo Url::toRoute('site/index');
     *
     * // /index.php?r=site%2Findex&src=ref1#name
     * echo Url::toRoute(['site/index', 'src' => 'ref1', '#' => 'name']);
     *
     * // http://www.example.com/index.php?r=site%2Findex
     * echo Url::toRoute('site/index', true);
     *
     * // https://www.example.com/index.php?r=site%2Findex
     * echo Url::toRoute('site/index', 'https');
     *
     * // /index.php?r=post%2Findex     assume the alias "@posts" is defined as "post/index"
     * echo Url::toRoute('@posts');
     * ```
     *
     * @param string|array $route use a string to represent a route (e.g. `index`, `site/index`),
     * or an array to represent a route with query parameters (e.g. `['site/index', 'param1' => 'value1']`).
     * @param bool|string $scheme the URI scheme to use in the generated URL:
     *
     * - `false` (default): generating a relative URL.
     * - `true`: returning an absolute base URL whose scheme is the same as that in [[\yii\web\UrlManager::$hostInfo]].
     * - string: generating an absolute URL with the specified scheme (either `http`, `https` or empty string
     *   for protocol-relative URL).
     *
     * @return string the generated URL
     */
    public static function toRoute($route, $scheme = false)
    {
        $route = (array) $route;
        $route[0] = static::normalizeRoute($route[0]);

        if ($scheme !== false) {
            return static::getUrlManager()->createAbsoluteUrl($route, is_string($scheme) ? $scheme : null);
        }

        return static::getUrlManager()->createUrl($route);
    }

    /**
     * Normalizes route and makes it suitable for UrlManager. Absolute routes are staying as is
     * while relative routes are converted to absolute ones.
     *
     * A relative route is a route without a leading slash, such as "view", "post/view".
     *
     * - If the route is an empty string, the current [[\yii\web\Controller::route|route]] will be used;
     * - If the route contains no slashes at all, it is considered to be an action ID
     *   of the current controller and will be prepended with [[\yii\web\Controller::uniqueId]];
     * - If the route has no leading slash, it is considered to be a route relative
     *   to the current module and will be prepended with the module's uniqueId.
     *
     * Starting from version 2.0.2, a route can also be specified as an alias. In this case, the alias
     * will be converted into the actual route first before conducting the above transformation steps.
     *
     * @param string $route the route. This can be either an absolute route or a relative route.
     * @return string normalized route suitable for UrlManager
     */
    protected static function normalizeRoute($route)
    {
        if (strncmp($route, '/', 1) === 0) {
            // absolute route
            return ltrim($route, '/');
        }

        return $route;
    }

    /**
     * Creates a URL based on the given parameters.
     *
     * This method is very similar to [[toRoute()]]. The only difference is that this method
     * requires a route to be specified as an array only. If a string is given, it will be treated as a URL.
     * In particular, if `$url` is
     *
     * - an array: [[toRoute()]] will be called to generate the URL. For example:
     *   `['site/index']`, `['post/index', 'page' => 2]`. Please refer to [[toRoute()]] for more details
     *   on how to specify a route.
     * - a string with a leading `@`: it is treated as an alias, and the corresponding aliased string
     *   will be returned.
     * - an empty string: the currently requested URL will be returned;
     * - a normal string: it will be returned as is.
     *
     * When `$scheme` is specified (either a string or `true`), an absolute URL with host info (obtained from
     * [[\yii\web\UrlManager::$hostInfo]]) will be returned. If `$url` is already an absolute URL, its scheme
     * will be replaced with the specified one.
     *
     * Below are some examples of using this method:
     *
     * ```php
     * // /index.php?r=site%2Findex
     * echo Url::to(['site/index']);
     *
     * // /index.php?r=site%2Findex&src=ref1#name
     * echo Url::to(['site/index', 'src' => 'ref1', '#' => 'name']);
     *
     * // /index.php?r=post%2Findex     assume the alias "@posts" is defined as "/post/index"
     * echo Url::to(['@posts']);
     *
     * // the currently requested URL
     * echo Url::to();
     *
     * // /images/logo.gif
     * echo Url::to('@web/images/logo.gif');
     *
     * // images/logo.gif
     * echo Url::to('images/logo.gif');
     *
     * // http://www.example.com/images/logo.gif
     * echo Url::to('@web/images/logo.gif', true);
     *
     * // https://www.example.com/images/logo.gif
     * echo Url::to('@web/images/logo.gif', 'https');
     *
     * // //www.example.com/images/logo.gif
     * echo Url::to('@web/images/logo.gif', '');
     * ```
     *
     *
     * @param array|string $url the parameter to be used to generate a valid URL
     * @param bool|string $scheme the URI scheme to use in the generated URL:
     *
     * - `false` (default): generating a relative URL.
     * - `true`: returning an absolute base URL whose scheme is the same as that in [[\yii\web\UrlManager::$hostInfo]].
     * - string: generating an absolute URL with the specified scheme (either `http`, `https` or empty string
     *   for protocol-relative URL).
     *
     * @return string the generated URL
     */
    public static function to($url = '', $scheme = false)
    {
        if (is_array($url)) {
            return static::toRoute($url, $scheme);
        }

        if ($scheme === false) {
            return $url;
        }

        if (static::isRelative($url)) {
            // turn relative URL into absolute
            $url = static::getUrlManager()->getHostInfo() . '/' . ltrim($url, '/');
        }

        return static::ensureScheme($url, $scheme);
    }

    /**
     * Normalize URL by ensuring that it use specified scheme.
     *
     * If URL is relative or scheme is not string, normalization is skipped.
     *
     * @param string $url the URL to process
     * @param string $scheme the URI scheme used in URL (e.g. `http` or `https`). Use empty string to
     * create protocol-relative URL (e.g. `//example.com/path`)
     * @return string the processed URL
     * @since 2.0.11
     */
    public static function ensureScheme($url, $scheme)
    {
        if (static::isRelative($url) || !is_string($scheme)) {
            return $url;
        }

        if (strpos($url, '//') === 0) {
            // e.g. //example.com/path/to/resource
            return $scheme === '' ? $url : "$scheme:$url";
        }

        if (($pos = strpos($url, '://')) !== false) {
            if ($scheme === '') {
                $url = substr($url, $pos + 1);
            } else {
                $url = $scheme . substr($url, $pos);
            }
        }

        return $url;
    }

    /**
     * Returns a value indicating whether a URL is relative.
     * A relative URL does not have host info part.
     * @param string $url the URL to be checked
     * @return bool whether the URL is relative
     */
    public static function isRelative($url)
    {
        return strncmp($url, '//', 2) && strpos($url, '://') === false;
    }

    /**
     * @return \yii\web\UrlManager URL manager used to create URLs
     * @since 2.0.8
     */
    protected static function getUrlManager()
    {
        global $_GLOBALS;
        return static::$urlManager ?: $_GLOBALS['url_manager'];
    }
}
