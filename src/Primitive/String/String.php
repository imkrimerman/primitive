<?php namespace im\Primitive\String;

use ArrayAccess;
use Countable;
use im\Primitive\Support\Str;
use im\Primitive\Support\Contracts\ArrayableInterface;
use im\Primitive\Container\Container;
use im\Primitive\String\Exceptions\StringException;
use im\Primitive\String\Exceptions\UnexpectedArgumentValueException;
use Symfony\Component\Yaml\Dumper;

class String implements Countable, ArrayAccess {

    protected $string;


    /**
     * @param string $string
     */
    public function __construct($string = '')
    {
        if ( ! is_string($string))
        {
            $this->string = '';
        }

        $this->string = $string;
    }

    public function set($string)
    {
        $this->string = $this->getStringable($string);

        return $this;
    }

    public function get()
    {
        return $this->string;
    }

    /**
     * @param        $string
     * @param string $delimiter
     *
     * @return $this
     */
    public function append($string, $delimiter = ' ')
    {
        $string = $this->getStringable($string);

        if ($this->isStringable($string) && $this->isStringable($delimiter))
        {
            $this->string .= "{$delimiter}{$string}";
        }

        return $this;
    }



    /**
     * @param        $string
     * @param string $delimiter
     *
     * @return $this
     */
    public function prepend($string, $delimiter = ' ')
    {
        $string = $this->getStringable($string);

        if ($this->isStringable($string) && $this->isStringable($delimiter))
        {
            $this->string = "{$string}{$delimiter}{$this->string}";
        }

        return $this;
    }

    /**
     * @param bool $firstLetter
     *
     * @return $this
     */
    public function lower($firstLetter = false)
    {
        if ($firstLetter)
        {
            return new static(lcfirst($this->string));
        }

        return new static(mb_strtolower($this->string));
    }


    /**
     * @param null $what
     *
     * @return $this
     */
    public function upper($what = null)
    {
        if ($what === 'first')
        {
            return new static(ucfirst($this->string));
        }
        elseif ($what === 'words')
        {
            return new static(ucwords($this->string));
        }

        return new static(mb_strtoupper($this->string));
    }

    public function title()
    {
        return new static(Str::title($this->string));
    }

    /**
     * @return $this
     */
    public function camel()
    {
        return new static(Str::camel($this->string));
    }


    /**
     * @return $this
     */
    public function dashed()
    {
        $string = preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $this->string);

        return (new static($string))->lower();
    }



    /**
     * @param string $delimiter
     *
     * @return $this
     */
    public function snake($delimiter = '_')
    {
        return new static(Str::snake($this->string, $delimiter));
    }



    /**
     * @return $this
     */
    public function studly()
    {
        return new static(Str::studly($this->value));
    }


    /**
     * @param      $string
     *
     * @return bool
     */
    public function has($string)
    {
        return Str::contains($this->string, $string);
    }


    /**
     * @param $search
     * @param $replace
     *
     * @return static
     * @throws \im\Primitive\String\Exceptions\UnexpectedArgumentValueException
     */
    public function replace($search, $replace)
    {
        if ($this->isStringable($search) || $this->isArrayable($search) && $this->isStringable($replace))
        {
            return new static(
                str_replace($this->getSearchable($search), $this->getStringable($replace), $this->string)
            );
        }

        throw new UnexpectedArgumentValueException('Unexpected arguments');
    }



    /**
     * @param $needles
     *
     * @return bool
     */
    public function startsWith($needles)
    {
        return Str::startsWith($this->string, $needles);
    }



    /**
     * @param $needles
     *
     * @return bool
     */
    public function endsWith($needles)
    {
        return Str::endsWith($this->string, $needles);
    }


    /**
     * @param $delimiter
     *
     * @return \im\Primitive\Container\Container
     */
    public function explode($delimiter)
    {
        return a(explode($delimiter, $this->string));
    }


    /**
     * @param       $delimiter
     * @param array $array
     *
     * @return $this
     * @throws \im\Primitive\String\Exceptions\UnexpectedArgumentValueException
     */
    public function implode($delimiter, $array)
    {
        if ($this->isArrayable($array))
        {
            $this->string = implode($delimiter, $this->getArrayable($array));

            return $this;
        }

        throw new UnexpectedArgumentValueException('Argument 2 should be array, Container or instance of Arrayable');
    }




    public function trim($what = null)
    {
        if ($what === 'front')
        {
            return new static(ltrim($this->string));
        }
        elseif ($what === 'back')
        {
            return new static(rtrim($this->string));
        }
        elseif ($what === 'all')
        {
            return $this->replace(' ', '');
        }

        return new static(trim($this->string));
    }




    public function repeat($quantity = 2)
    {
        return new static(str_repeat($this->string, (int) $quantity));
    }



    public function shuffle()
    {
        return new static(str_shuffle($this->string));
    }


    /**
     * @param int $length
     *
     * @return \im\Primitive\Container\Container
     */
    public function split($length = 1)
    {
        return a(str_split($this->string, $length));
    }


    public function wordSplit()
    {
        return a(str_word_count($this->string, 2));
    }



    /**
     * @return static
     */
    public function strip()
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
        $this->string = base64_decode($base);

        return $this;
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
        $this->string = html_entity_decode($entities, $flags, $encoding);

        return $this;
    }


    public function md5()
    {
        return new static(md5($this->string));
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

        echo $this->getStringable($before), $this->string, $this->isStringable($after);

        return $this;
    }

    public function dump($die = false)
    {
        (new Dumper())->dump($this->string);

        if ($die) die;
    }

    /**
     * @param        $offset
     * @param        $length
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
        return new static(Str::limit($this->string, $limit, $end));
    }


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

    public function reset()
    {
        $this->string = '';
    }

    /**
     * @param int    $decimals
     * @param string $decimal_delimiter
     * @param string $thousands_delimiter
     *
     * @return $this
     * @throws \im\Primitive\String\Exceptions\StringException
     */
    public function float($decimals = 2, $decimal_delimiter = '.', $thousands_delimiter = ' ')
    {
        if (is_numeric($this->string))
        {
            return new static(
                number_format((float) $this->string, $decimals, $decimal_delimiter, $thousands_delimiter)
            );
        }

        throw new StringException('String is not numeric');
    }



    /**
     * @return static
     */
    public function compress()
    {
        return new static(gzcompress($this->string));
    }


    /**
     * @param $string
     *
     * @return $this
     */
    public function uncompress($string)
    {
        $this->string = gzuncompress($this->getStringable($string));

        return $this;
    }



    /**
     * @return $this
     */
    public function encrypt()
    {
        return $this->compress()->base64();
    }



    /**
     * @return $this
     */
    public function decrypt($string)
    {
        $this->string = $this->fromBase64($string)->uncompress();

        return $this;
    }



    /**
     * @return $this
     */
    public function save()
    {
        $this->clone = $this->string;

        return $this;
    }



    /**
     * @return $this
     */
    public function revert()
    {
        $this->string = $this->clone;
        $this->measure();

        return $this;
    }



    /**
     * @param null $string
     *
     * @return int|String
     */
    private function measure($string = null)
    {
        if ($string === null)
        {
            $this->length = mb_strlen($this->string);
        }

        return ($string === null) ? $this : mb_strlen($string);
    }



    public function all()
    {
        return $this->string;
    }



    /**
     * @return bool
     */
    public function isEmpty()
    {
        return ! (bool) $this->length;
    }



    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return (bool) $this->length;
    }



    /**
     * @param $string
     *
     * @return bool
     */
    public function isJson($string = '')
    {
        if (empty($string))
        {
            $string = $this->string;
        }

        if (is_string($string))
        {
            return is_array(json_decode($string, true));
        }

        return false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->string;
    }

    protected function isStringable($value)
    {
        return is_string($value) || $value instanceof String;
    }

    protected function getStringable($string)
    {
        if ($string instanceof String)
        {
            return $string->all();
        }

        return $string;
    }

    protected function isArrayable($value)
    {
        return is_array($value) || $value instanceof Container || $value instanceof ArrayableInterface;
    }

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
            $this->string = $value;
        }
        // TODO implement assign by offset
//        $this->set($offset, $value);
    }


    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }


    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        //TODO implement unset by offset
//        $this->forget($offset);
    }


    /**
     * @param mixed $offset
     *
     * @throws OffsetNotExistsException
     * @return null
     */
    public function offsetGet($offset)
    {
        if ($this->has($offset))
        {
            return $this->string[$offset];
        }

        throw new OffsetNotExistsException('Offset: ' . $offset . ' not exists');
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
        return $this->length;
    }
}
