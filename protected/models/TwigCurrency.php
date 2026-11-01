<?php
namespace app\models;

/*
default:
'[symbol_e][price_inc_gst_e] <span style="font-size:60%">[currency_e]</span>'

cart.twig:
'[symbol][price_inc_gst]'
'[symbol][price_inc_gst]'
'[symbol][price_inc_gst] [currency]'

product-card.twig:
'[symbol_e][price_inc_gst_e]'

product-page.twig:
'[symbol][price_inc_gst]'
'[symbol_e][price_inc_gst_e] <span style="font-size:60%">[currency_inc_gst_e]</span>'

dropcart.twig:
'[symbol][price_inc_gst] [currency]'

widget-products.twig:
'[symbol][price_inc_gst]'
*/

class TwigCurrency
{
    /** @var bool */
    private $is_zero = true;

    /**
     * @param string $amount
     * @param string $currency
     */
    public function __construct($amount = null, $currency = null, $precision = null)
    {
        $this->setAmount($amount);
        $this->setCurrency($currency);
        $this->setPrecision($precision);
    }



    /** @var string */
    private $alternate = '<span style="color:#dc3545">Please Ask</span>';

    /**
     * @return string|null
     */
    public function getAlternate()
    {
        return $this->alternate;
    }

    /**
     * @param string|null $alternate
     *
     * @return TwigCurrency
     */
    public function setAlternate($alternate = null)
    {
        if (!is_null($alternate)) {
            $this->alternate  = $alternate;
            $this->out_amount = null;
            $this->string     = null;
        }
        return $this;
    }



    /** @var string|null */
    private $amount = null;

    private $out_amount = null;

    /**
     * @return string
     */
    public function getAmount()
    {
        //Return cached result
        if (!is_null($this->out_amount)) {
            return $this->out_amount;
        }

        //Return zero result
        $this->out_amount = bcadd('0.0000', '0.0000', $this->precision);
        if ($this->is_zero) {
            return $this->out_amount;
        }

        //Return unconverted USD result
        if ('USD' === $this->currency) {
            $this->out_amount = bcadd('0.0000', $this->amount, $this->precision);
            return $this->out_amount;
        }

        //Return converted USD result
        $exRates = getExchangeRateList();
        if (array_key_exists($this->currency, $exRates)) {
            $this->out_amount = bcmul($exRates[$this->currency], $this->amount, $this->precision);
        }
        return $this->out_amount;
    }

    /**
     * @param string|null $amount
     *
     * @return TwigCurrency
     */
    public function setAmount($amount = null)
    {
        if (!is_null($amount)) {
            $this->amount     = $amount;
            $this->out_amount = null;
            $this->string     = null;
            $this->is_zero    = bccomp($this->amount ?? '0.0000', '0.0000', $this->precision) == 0 || is_null($this->currency);
        }

        return $this;
    }



    /** @var string|null */
    private $currency = 'USD';

    /**
     * @return string
     */
    public function getCurrency()
    {
        global $_GLOBALS;

        /*
        return $_GLOBALS['member']['currency'] ?? $_GLOBALS['visitor']['currency'] ?? $_GLOBALS['site_options']['DEFAULT_CURRENCY'] ?? $this->currency;
        */
        return 'USD';
    }

    /**
     * @param string|null $currency
     *
     * @return TwigCurrency
     */
    public function setCurrency($currency = null)
    {
        if (!is_null($currency)) {
            $this->currency   = $currency;
            $this->out_amount = null;
            $this->string     = null;
        }

        return $this;
    }



    /** @var int */
    private $precision = 2;

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @param int $precision
     *
     * @return void
     */
    public function setPrecision($precision = null)
    {
        if (!is_null($precision)) {
            $this->precision  = $precision;
            $this->out_amount = null;
            $this->string     = null;
            $this->is_zero    = bccomp($this->amount ?? '0.0000', '0.0000', $this->precision) == 0 || is_null($this->currency);
        }

        return $this;
    }



    /**
     * @return string
     */
    public function getSymbol()
    {
        return getCurrencySymbol($this->getCurrency());
    }



    /** @var bool */
    private $taxable = true;

    /**
     * @return bool
     */
    public function getTaxable()
    {
        return $this->taxable;
    }

    /**
     * @param bool $taxable
     *
     * @return TwigCurrency
     */
    public function setTaxable($taxable)
    {
        $this->taxable = $taxable;
        $this->string  = null;

        return $this;
    }



    /** @var string|null */
    private $string = null;

    /**
     * @return string
     */
    public function __tostring()
    {
        //Return cached result
        if (!is_null($this->string)) {
            return $this->string;
        }

        if ($this->is_zero) {
            return $this->string = $this->alternate;
        }

        return $this->format('[symbol_e][price_inc_gst_e] <span style="font-size:60%">[currency_e]</span>');
    }



    private $parts;

    public function parts()
    {
        global $_GLOBALS;

        if (!is_null($this->parts)) {
            return $this->parts;
        }

        $target_country  = 'US';
        $target_currency = 'USD';
        /*
        $target_country  = $_GLOBALS['member']['country'] ?? $_GLOBALS['visitor']['country'] ?? 'US';
        $target_currency = $this->getCurrency();
        */
        $target_price    = convertCurrency($this->amount, $this->currency, $target_currency);

        $parts = [
            'price'            => $target_price,
            'price_inc_gst'    => $target_price,
            'currency'         => $target_currency,
            'currency_inc_gst' => $target_currency,
            'symbol'           => $this->getSymbol(),
        ];
        /*
        if ('AU' == $target_country && $this->taxable) {
            $parts['price_inc_gst'] = includeGST($parts['price']);
            $parts['currency_inc_gst'] .= ' inc GST';
        }
        */

        foreach ($parts as $k => $v) {
            $parts[$k . '_e'] = htmlspecialchars($v);
        }

        $this->parts = [];
        foreach ($parts as $k => $v) {
            $this->parts['[' . $k . ']'] = $v;
        }

        return $this->parts;
    }

    public function format($str)
    {
        return ($this->is_zero) ? $this->getAlternate() : strtr($str, $this->parts());
    }

    public function multiplied($qty = 1)
    {
        if (1 == $qty || $this->is_zero) {
            return $this;
        }

        /*
        $result = clone $this;
        $result->amount = bcmul($this->amount, strval($qty), 2);
        return $result;
        */

        return new TwigCurrency(
            bcmul($this->amount, strval($qty), 6),
            $this->currency,
            $this->precision
        );
    }

    public function added($add)
    {
        if (is_null($this->amount) || is_null($add->amount)) {
            return new TwigCurrency();
        }
        $new_precision = max($this->precision, $add->getPrecision());
        $new_currency  = $this->getCurrency();
        $new_amount    = bcadd($this->getAmount(), $add->getAmount(), $new_precision);
        return new TwigCurrency($new_amount, $new_currency, $new_precision);
    }
}
