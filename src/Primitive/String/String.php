<?php namespace im\Primitive\String;

use Countable;
use OutOfBoundsException;
use Traversable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use InvalidArgumentException;

use Stringy\StaticStringy;
use im\Primitive\Support\Str;
use im\Primitive\Support\Dump\Dumper;
use im\Primitive\Support\Contracts\ArrayableInterface;
use im\Primitive\Container\Container;
use im\Primitive\String\Exceptions\StringException;
use im\Primitive\String\Exceptions\UnexpectedArgumentValueException;

class String implements Countable, ArrayAccess, IteratorAggregate {

    /**
     * @var string
     */
    protected $string;

    /**
     * @param string $string
     */
    public function __construct($string = '')
    {
        $this->initialize($string);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function __get($value)
    {
        if (method_exists($this, $value))
        {
            return $this->{$value}();
        }
    }

    /**
     * @param $string
     *
     * @return $this
     */
    public function set($string)
    {
        $this->string = $this->getStringable($string);

        return $this;
    }

    /**
     * @return string
     */
    public function get()
    {
        return (string) $this->string;
    }

    /**
     * @return string
     */
    public function value()
    {
        return $this->get();
    }

    /**
     * @return int
     */
    public function length()
    {
        return $this->measure();
    }

    /**
     * @return \im\Primitive\Container\Container
     */
    public function chars()
    {
        return a(StaticStringy::chars($this->string));
    }

    /**
     * @param string $string
     * @param string $delimiter
     *
     * @return $this
     */
    public function append($string, $delimiter = ' ')
    {
        if ($this->isStringable($string) && $this->isStringable($delimiter))
        {
            $this->string .= $this->getStringable($delimiter) . $this->getStringable($string);
        }

        return $this;
    }

    /**
     * @param string $string
     * @param string $delimiter
     *
     * @return $this
     */
    public function prepend($string, $delimiter = ' ')
    {
        if ($this->isStringable($string) && $this->isStringable($delimiter))
        {
            $this->string = $this->getStringable($string) . $this->getStringable($delimiter) . $this->string;
        }

        return $this;
    }

    /**
     * @return static
     */
    public function lower()
    {
        return new static(mb_strtolower($this->string));
    }

    /**
     * @return static
     */
    public function lowerFirst()
    {
        return new static(StaticStringy::lowerCaseFirst($this->string));
    }

    /**
     * @return static
     */
    public function upper()
    {
        return new static(mb_strtoupper($this->string));
    }

    /**
     * @return static
     */
    public function upperFirst()
    {
        return new static(StaticStringy::upperCaseFirst($this->string));
    }

    /**
     * @return static
     */
    public function upperCamel()
    {
        return $this->camel()->upperFirst();
    }

    /**
     * @return static
     */
    public function title()
    {
        return new static(StaticStringy::toTitleCase($this->string));
    }

    /**
     * @return static
     */
    public function camel()
    {
        return new static(Str::camel($this->string));
    }

    /**
     * @return static
     */
    public function dashed()
    {
        return new static(Str::dashed($this->string));
    }

    /**
     * @return static
     */
    public function underscore()
    {
        return new static(Str::underscore($this->string));
    }

    /**
     * @param string $delimiter
     *
     * @return static
     */
    public function snake($delimiter = '_')
    {
        return new static(Str::snake($this->string, $delimiter));
    }

    /**
     * @return static
     */
    public function studly()
    {
        return new static(Str::studly($this->string));
    }

    /**
     * @return static
     */
    public function swapCase()
    {
        return new static(StaticStringy::swapCase($this->string));
    }

    /**
     * @return static
     */
    public function humanize()
    {
        return new static(StaticStringy::humanize($this->string));
    }

    /**
     * @param null $ignore
     *
     * @return static
     */
    public function titleize($ignore = null)
    {
        return new static(StaticStringy::titleize($this->string, $ignore));
    }

    /**
     * @param string $string
     * @param bool $caseSensitive
     *
     * @return bool
     */
    public function has($string, $caseSensitive = true)
    {
        return StaticStringy::contains($this->string, $this->getStringable($string), $caseSensitive);
    }

    /**
     * @param array|Container|ArrayableInterface $strings
     * @param bool $caseSensitive
     *
     * @return bool
     */
    public function hasAny($strings, $caseSensitive = true)
    {
        return StaticStringy::containsAny($this->string, $this->getArrayable($strings), $caseSensitive);
    }

    /**
     * @param array|Container|ArrayableInterface $strings
     * @param bool $caseSensitive
     *
     * @return bool
     */
    public function hasAll($strings, $caseSensitive = true)
    {
        return StaticStringy::containsAll($this->string, $this->getArrayable($strings), $caseSensitive);
    }

    /**
     * @return static
     */
    public function collapseWhitespace()
    {
        return new static(StaticStringy::collapseWhitespace($this->string));
    }

    /**
     * @return static
     */
    public function toAscii()
    {
        return new static(StaticStringy::toAscii($this->string));
    }

    /**
     * @param int $tabLength
     *
     * @return static
     */
    public function toSpaces($tabLength = 4)
    {
        return new static(StaticStringy::toSpaces($this->string, $tabLength));
    }

    /**
     * @param int $tabLength
     *
     * @return static
     */
    public function toTabs($tabLength = 1)
    {
        return new static(StaticStringy::toTabs($tabLength));
    }

    /**
     * @param string $surround
     *
     * @return static
     */
    public function surround($surround)
    {
        return new static(StaticStringy::surround($this->string, $surround));
    }

    /**
     * @param string $insert
     * @param int $index
     *
     * @return static
     */
    public function insert($insert, $index)
    {
        return new static(StaticStringy::insert($this->string, $insert, $index));
    }

    /**
     * @return static
     */
    public function reverse()
    {
        return new static(StaticStringy::reverse($this->string));
    }

    /**
     * @param int $index
     *
     * @return static
     */
    public function at($index)
    {
        return new static(StaticStringy::at($this->string, $index));
    }

    /**
     * @param int $length
     *
     * @return static
     */
    public function first($length)
    {
        return new static(StaticStringy::first($this->string, $length));
    }

    /**
     * @param int $length
     *
     * @return static
     */
    public function last($length)
    {
        return new static(StaticStringy::last($this->string, $length));
    }

    /**
     * @param string $string
     *
     * @return static
     */
    public function ensureLeft($string)
    {
        return new static(StaticStringy::ensureLeft($this->string, $this->getStringable($string)));
    }

    /**
     * @param string $string
     *
     * @return static
     */
    public function ensureRight($string)
    {
        return new static(StaticStringy::ensureRight($this->string, $this->getStringable($string)));
    }

    /**
     * @param string $string
     *
     * @return static
     */
    public function removeLeft($string)
    {
        return new static(StaticStringy::removeLeft($this->string, $this->getStringable($string)));
    }

    /**
     * @param string $string
     *
     * @return static
     */
    public function removeRight($string)
    {
        return new static(StaticStringy::removeRight($this->string, $this->getStringable($string)));
    }

    /**
     * @param array|Container|ArrayableInterface|string $search
     * @param string $replace
     *
     * @return static
     */
    public function replace($search, $replace)
    {
        $string = $this->string;

        $replace = $this->getStringable($replace);

        if ($search == ' ') $search = '\s*';

        foreach ((array) $this->getSearchable($search) as $find)
        {
            $string = StaticStringy::replace($string, $find, $replace);
        }

        return new static($string);
    }

    /**
     * @param string $pattern
     * @param string $replace
     *
     * @return static
     */
    public function replaceRegex($pattern, $replace)
    {
        return new static(StaticStringy::regexReplace($this->string, $pattern, $this->getStringable($replace)));
    }

    /**
     * @param array|Container|ArrayableInterface|string $needles
     *
     * @return bool
     */
    public function startsWith($needles)
    {
        return Str::startsWith($this->string, $this->getArrayable($needles));
    }

    /**
     * @param array|Container|ArrayableInterface|string $needles
     *
     * @return bool
     */
    public function endsWith($needles)
    {
        return Str::endsWith($this->string, $this->getArrayable($needles));
    }

    /**
     * @param string $pattern
     *
     * @return bool
     */
    public function is($pattern)
    {
        return Str::is($this->getStringable($pattern), $this->string);
    }

    /**
     * @param string $cap
     *
     * @return static
     */
    public function finish($cap)
    {
        return new static(Str::finish($this->string, $this->getStringable($cap)));
    }

    /**
     * @param int    $limit
     * @param string $end
     *
     * @return static
     */
    public function words($limit, $end = '...')
    {
        return new static(Str::words($this->string, $limit, $end));
    }

    /**
     * @param string $callback
     * @param string $default
     *
     * @return \im\Primitive\Container\Container
     */
    public function parseCallback($callback, $default = '')
    {
        return a(Str::parseCallback($this->getStringable($callback), $default));
    }

    /**
     * @param int $length
     *
     * @return static
     */
    public function random($length = 16)
    {
        return new static(Str::random($length));
    }

    /**
     * @param int $length
     *
     * @return static
     */
    public function quickRandom($length = 16)
    {
        return new static(Str::quickRandom($length));
    }

    /**
     * @param string $delimiter
     *
     * @return static
     */
    public function slug($delimiter = '-')
    {
        return new static(Str::slug($this->string, $this->getStringable($delimiter)));
    }

    /**
     * @param string $delimiter
     *
     * @return \im\Primitive\Container\Container
     */
    public function explode($delimiter)
    {
        return a(explode($this->getStringable($delimiter), $this->string));
    }

    /**
     * @param string $delimiter
     * @param array  $array
     *
     * @return $this
     * @throws \im\Primitive\String\Exceptions\UnexpectedArgumentValueException
     */
    public function implode($delimiter, $array)
    {
        if ($this->isArrayable($array))
        {
            $this->string = implode($this->getStringable($delimiter), $this->getArrayable($array));

            return $this;
        }

        throw new UnexpectedArgumentValueException('Argument 2 should be array, Container or instance of Arrayable');
    }

    /**
     * @param null|string $what
     *
     * @return static
     * @throws \im\Primitive\String\Exceptions\UnexpectedArgumentValueException
     */
    public function trim($what = null)
    {
        switch ($what)
        {
            case 'front':
                return new static(ltrim($this->string));
            case 'back':
                return new static(rtrim($this->string));
            case 'all':
                return $this->replace(' ', '');
            default:
                return new static(trim($this->string));
        }
    }

    /**
     * @param int $quantity
     *
     * @return static
     */
    public function repeat($quantity = 2)
    {
        return new static(str_repeat($this->string, (int) $quantity));
    }

    /**
     * @param bool $quick
     *
     * @return static
     */
    public function shuffle($quick = true)
    {
        if ($quick)
        {
            return new static(str_shuffle($this->string));
        }

        return new static(StaticStringy::shuffle($this->string));
    }

    /**
     * @param null|array|Container|ArrayableInterface $charsAsWords
     *
     * @return \im\Primitive\Container\Container
     */
    public function wordSplit($charsAsWords = null)
    {
        return a(str_word_count($this->string, 2, $this->getArrayable($charsAsWords)));
    }

    /**
     * @return static
     */
    public function stripTags()
    {
        return new static(strip_tags($this->string));
    }

    /**
     * @return static
     */
    public function base64()
    {
        return new static(base64_encode($this->string));
    }

    /**
     * @param string $base
     *
     * @return $this
     */
    public function fromBase64($base)
    {
        return $this->initialize(base64_decode($this->getStringable($base)));
    }

    /**
     * @param int    $flags
     * @param string $encoding
     *
     * @return static
     */
    public function toEntities($flags = ENT_QUOTES, $encoding = 'UTF-8')
    {
        return new static(htmlentities($this->string, $flags, $encoding));
    }

    /**
     * @param string $entities
     * @param int    $flags
     * @param string $encoding
     *
     * @return $this
     */
    public function fromEntities($entities, $flags = ENT_QUOTES, $encoding = 'UTF-8')
    {
        $this->string = html_entity_decode($this->getStringable($entities), $flags, $encoding);

        return $this;
    }

    /**
     * Echo string
     *
     * @param string $before
     * @param string $after
     *
     * @return $this
     */
    public function say($before = '', $after = '')
    {
        if ( ! $this->isStringable($before))
        {
            $before = '';
        }

        if ( ! $this->isStringable($after))
        {
            $after = '';
        }

        echo $this->getStringable($before), $this->string, $this->getStringable($after);

        return $this;
    }

    /**
     * @param bool $die
     */
    public function dump($die = false)
    {
        (new Dumper())->dump($this->string);

        if ($die) die;
    }

    /**
     * @param int    $offset
     * @param int    $length
     * @param string $encoding
     *
     * @return static
     */
    public function cut($offset, $length, $encoding = 'UTF-8')
    {
        return new static(mb_substr($this->string, $offset, $length, $encoding));
    }

    /**
     * @param int    $limit
     * @param string $end
     *
     * @return static
     */
    public function limit($limit = 100, $end = '...')
    {
        return new static(Str::limit($this->string, $limit, $this->getStringable($end)));
    }

    /**
     * @param int    $limit
     * @param string $end
     *
     * @return static
     */
    public function limitSafe($limit = 100, $end = '...')
    {
        return new static(StaticStringy::safeTruncate($this->string, $limit).$this->getStringable($end));
    }

    /**
     * @return \im\Primitive\Container\Container
     */
    public function toVars()
    {
        $vars = [];

        parse_str($this->string, $vars);

        return a($vars);
    }

    /**
     * @return $this
     */
    public function clean()
    {
        return $this->strip()->toEntities()->trim();
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->string = '';

        return $this;
    }

    /**
     * @param int    $decimals
     * @param string $decimal_delimiter
     * @param string $thousands_delimiter
     *
     * @return static
     * @throws \im\Primitive\String\Exceptions\StringException
     */
    public function number($decimals = 2, $decimal_delimiter = '.', $thousands_delimiter = ' ')
    {
        if (is_numeric($this->string))
        {
            return new static(
                number_format(
                    (float) $this->string,
                    $decimals,
                    $this->getStringable($decimal_delimiter),
                    $this->getStringable($thousands_delimiter)
                )
            );
        }

        throw new StringException('String is not numeric');
    }

    /**
     * @return static
     */
    public function compress()
    {
        $this->string = gzcompress($this->string);

        return $this;
    }

    /**
     * @return $this
     */
    public function uncompress()
    {
        $this->string = gzuncompress($this->string);

        return $this;
    }

    /**
     * @return static
     */
    public function encrypt()
    {
        return $this->compress()->base64();
    }

    /**
     * @param string $encrypted
     *
     * @return $this
     */
    public function fromEncrypted($encrypted)
    {
        $this->string = $this->fromBase64($this->getStringable($encrypted))->uncompress();

        return $this;
    }

    /**
     * @return int
     */
    protected function measure()
    {
        return mb_strlen($this->string);
    }

    /**
     * @return string
     */
    public function all()
    {
        return $this->get();
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return ! (bool) $this->length();
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return (bool) $this->length();
    }

    /**
     * @return bool
     */
    public function isAlpha()
    {
        return StaticStringy::isAlpha($this->string);
    }

    /**
     * @return bool
     */
    public function isAlphanumeric()
    {
        return StaticStringy::isAlphanumeric($this->string);
    }

    /**
     * @return bool
     */
    public function isWhitespaces()
    {
        return StaticStringy::isBlank($this->string);
    }

    /**
     * @return bool
     */
    public function isHex()
    {
        return StaticStringy::isHexadecimal($this->string);
    }

    /**
     * @return bool
     */
    public function isSerialized()
    {
        return StaticStringy::isSerialized($this->string);
    }

    /**
     * @return bool
     */
    public function isJson()
    {
        return is_array(json_decode($this->string, true));
    }

    /**
     * @return bool
     */
    public function isLower()
    {
        return StaticStringy::isLowerCase($this->string);
    }

    /**
     * @return bool
     */
    public function isUpper()
    {
        return StaticStringy::isUpperCase($this->string);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * @param $string
     *
     * @return $this
     */
    protected function initialize($string)
    {
        if ( ! $this->isStringable($string))
        {
            throw new InvalidArgumentException('Argument 1 should be string or object implementing __toString');
        }

        $this->string = $this->getStringable($string);

        return $this;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function isStringable($value)
    {
        return is_string($value) ||
               $value instanceof String ||
               is_array($value) ||
               (is_object($value) && method_exists($value, '__toString'));
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    protected function getStringable($string)
    {
        if ($string instanceof String)
        {
            return $string->get();
        }
        elseif (is_array($string))
        {
            return (string) a($string)->implode();
        }
        elseif (is_object($string) && method_exists($string, '__toString'))
        {
            return (string) $string;
        }

        return $string;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function isArrayable($value)
    {
        return is_array($value) || $value instanceof Container || $value instanceof ArrayableInterface;
    }

    /**
     * @param $value
     *
     * @return array
     */
    protected function getArrayable($value)
    {
        if ($value instanceof Container)
        {
            $value = $value->all();
        }
        elseif ($value instanceof ArrayableInterface)
        {
            $value = $value->toArray();
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return array|mixed
     */
    protected function getSearchable($value)
    {
        if ($this->isStringable($value))
        {
            return $this->getStringable($value);
        }

        return $this->getArrayable($value);
    }

    /*
    |--------------------------------------------------------------------------
    | ArrayAccess
    |--------------------------------------------------------------------------
    */

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->append($value, '');
        }

        if ($this->offsetExists($offset))
        {
            $this->string = (string) $this->chars()->set($offset, $value)->implode();
        }
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        $length = $this->length();
        $offset = (int) $offset;

        if ($offset >= 0) return ($length > $offset);

        return ($length >= abs($offset));
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset))
        {
            $this->string = (string) $this->chars()->forget($offset)->implode();
        }
    }

    /**
     * @param mixed $offset
     *
     * @throws OffsetNotExistsException
     * @return null
     */
    public function offsetGet($offset)
    {
        $offset = (int) $offset;
        $length = $this->length();

        if (($offset >= 0 && $length <= $offset) || $length < abs($offset))
        {
            throw new OutOfBoundsException('No character exists at the index');
        }

        return mb_substr($this->string, $offset, 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->measure();
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->chars()->all());
    }
}
