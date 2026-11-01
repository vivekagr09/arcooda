<?php

function getCurrency()
{
    $currency = 'USD';

    if (isset($_COOKIE['currency']) && '' != $_COOKIE['currency']) {
        $currency = $_COOKIE['currency'];
    }

    if (isset($_SESSION['prefs']['currency']) && '' != $_SESSION['prefs']['currency']) {
        $currency = $_SESSION['prefs']['currency'];
    }

    return $currency;
}

function setCurrency($currency)
{
    setcookie("currency", $currency, time() + 30758400, '/');
    header("Location: /redirect?page=" . urlencode($_SERVER["HTTP_REFERER"]));
}

function calPriceFlux($cost, $cost_currency, $markup, $toCurrency, $showCurrency = true)
{
    $costPrice       = showPriceFrom($cost, $cost_currency, $toCurrency);
    $new_sales_price = (1 + doubleval($markup) / 100) * $costPrice;
    $new_sales_price = sprintf("%01.2f", $new_sales_price);
    if ($showCurrency) {
        return $new_sales_price . ' ' . $toCurrency;
    } else {
        return $new_sales_price;
    }

}

function calPrice($price, $currency, $toCurrency, $showCurrency = true)
{
    if ($showCurrency) {
        return sprintf("%01.2f", showPriceFrom($price, $currency, $toCurrency)) . ' ' . $toCurrency;
    } else {
        return sprintf("%01.2f", showPriceFrom($price, $currency, $toCurrency));
    }

}

function showCurrencyList($selected = "")
{
    echo formatHTMLOptions(getCurrencyList(false), $selected);
}

if (!function_exists('showPriceFrom')) {
    function showPriceFrom($price, $fromCode = 'USD', $toCode = 'USD')
    {
        return convertCurrency($price, $fromCode, $toCode);
    }
}
