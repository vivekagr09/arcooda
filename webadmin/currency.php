<?php
/******************************************************************************
 ** Title.........: Show Currency
 ** Version.......:
 ** Author........: Stephen Jang <c980722@studentmail.newcastle.edu.au>
 ** Filename......: currency.php
 ** Last changed..: 11/19/00
 ** Notes.........: Functions which allow for the translation of currencies to
 **                 be displayed and currencies to be converted and updated
 **                 into database
 *****************************************************************************/

/**
 * @deprecated use echo formatHTMLOptions(getCurrencyList(false), $selected)
 *
 * @param string $selected
 *
 * @return void
 */
function showCurrencyList($selected = "")
{
    echo formatHTMLOptions(getCurrencyList(false), $selected);
}

if (!function_exists('showPriceFrom')) {
    /**
     * @deprecated use convertCurrency($price, $fromCode, $toCode)
     *
     * @param string $price
     * @param string $fromCode
     * @param string $toCode
     *
     * @return string
     */
    function showPriceFrom($price, $fromCode = 'USD', $toCode = 'USD')
    {
        return convertCurrency($price, $fromCode, $toCode);
    }
}
