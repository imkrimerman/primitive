<?php namespace im\Primitive\String;

use JWT;
use Countable;
use Traversable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use BadMethodCallException;
use InvalidArgumentException;

use Stringy\Stringy;
use Stringy\StaticStringy;
use im\Primitive\Support\Str;
use im\Primitive\Int\Int;
use im\Primitive\Bool\Bool;
use im\Primitive\Float\Float;
use im\Primitive\Container\Container;
use im\Primitive\Support\Abstracts\Type;
use im\Primitive\Support\Contracts\StringContract;
use im\Primitive\Support\Traits\SliceableTrait;
use im\Primitive\Support\Traits\StringCheckerTrait;
use im\Primitive\String\Exceptions\StringException;

/**
 * Class String
 * String manipulation class with support of multi-byte
 *
 * @package im\Primitive\String
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
class String extends Type implements StringContract, Countable, ArrayAccess, IteratorAggregate {

    use SliceableTrait;
    use StringCheckerTrait;

    /**
     * Storing value.
     * @var string
     */
    protected $string;

    /**
     * Construct String Type.
     *
     * @param mixed $string
     */
    public function __construct($string = '')
    {
        $this->initialize($string);
    }

    /**
     * Magic get method to support method calls (without parameters) as variables.
     *
     * @param string|StringContract $value
     * @return mixed
     */
    public function __get($value)
    {
        $value = $this->retrieveValue($value);

        if (method_exists($this, $value))
        {
            return $this->{$value}();
        }

        throw new BadMethodCallException('No method: ' . $value . ' found');
    }

    /**
     * Setter.
     * It can set from string, numeric, bool, array, object that implements __toString,
     * ArrayableContract, ContainerContract, BooleanContract, IntegerContract, FloatContract.
     *
     * @param mixed $string
     * @return $this
     */
    public function set($string)
    {
        $this->string = $this->retrieveValue($string);

        return $this;
    }

    /**
     * Getter. Alias for value method.
     *
     * @return string
     */
    public function get()
    {
        return $this->value();
    }

    /**
     * Return storing value.
     *
     * @return string
     */
    public function value()
    {
        return $this->string;
    }

    /**
     * Return length of string.
     *
     * @return int
     */
    public function length()
    {
        return $this->measure();
    }

    /**
     * Return Container of all chars.
     *
     * @return \im\Primitive\Container\Container
     */
    public function chars()
    {
        return new Container(StaticStringy::chars($this->string));
    }

    /**
     * Return String with appended delimiter and content.
     *
     * @param mixed $string
     * @param mixed $delimiter
     * @return $this|static
     */
    public function append($string, $delimiter = '')
    {
        if ($this->isValidStringAndDelimiter($string, $delimiter))
        {
            return new static($this->string.$this->retrieveValue($delimiter).$this->retrieveValue($string));
        }

        return $this;
    }

    /**
     * Return String with prepended content and delimiter.
     *
     * @param mixed $string
     * @param mixed $delimiter
     * @return $this|static
     */
    public function prepend($string, $delimiter = '')
    {
        if ($this->isValidStringAndDelimiter($string, $delimiter))
        {
            return new static($this->retrieveValue($string).$this->retrieveValue($delimiter).$this->string);
        }

        return $this;
    }

    /**
     * Return String all lower case.
     *
     * @return static
     */
    public function lower()
    {
        return new static(mb_strtolower($this->string));
    }

    /**
     * Return String with lower case first letter.
     *
     * @return static
     */
    public function lowerFirst()
    {
        return new static(StaticStringy::lowerCaseFirst($this->string));
    }

    /**
     * Return String with all upper case.
     *
     * @return static
     */
    public function upper()
    {
        return new static(mb_strtoupper($this->string));
    }

    /**
     * Return String with upper case first letter.
     *
     * @return static
     */
    public function upperFirst()
    {
        return new static(StaticStringy::upperCaseFirst($this->string));
    }

    /**
     * Return camel case String with upper case first letter.
     *
     * @return static
     */
    public function upperCamel()
    {
        return $this->camel()->upperFirst();
    }

    /**
     * Return Title Case String. (<- like this)
     *
     * @return static
     */
    public function title()
    {
        return new static(StaticStringy::toTitleCase($this->string));
    }

    /**
     * Return camelCase String.
     *
     * @return static
     */
    public function camel()
    {
        return new static(Str::camel($this->string));
    }

    /**
     * Return dashed-case String.
     *
     * @return static
     */
    public function dashed()
    {
        return new static(Str::dashed($this->string));
    }

    /**
     * Return snake_case String.
     *
     * @return static
     */
    public function snake()
    {
        return new static(StaticStringy::underscored($this->string));
    }

    /**
     * Return StudlyCase String.
     *
     * @return static
     */
    public function studly()
    {
        return new static(Str::studly($this->string));
    }

    /**
     * Return String with swapped case.
     * UpperCase -> uPPERcASE
     *
     * @return static
     */
    public function swapCase()
    {
        return new static(StaticStringy::swapCase($this->string));
    }

    /**
     * Return capitalized first word of the string, replaces underscores with
     * spaces, and strips '_id'.
     *
     * @return static
     */
    public function humanize()
    {
        return new static(StaticStringy::humanize($this->string));
    }

    /**
     * Return a trimmed string with the first letter of each word capitalized.
     * Ignores the case of other letters, preserving any acronyms. Also accepts
     * an arrayable, $ignore, allowing you to list words not to be capitalized.
     *
     * @param mixed $ignore
     * @return static
     */
    public function titleize($ignore = null)
    {
        return new static(StaticStringy::titleize($this->string, $this->getArrayable($ignore)));
    }

    /**
     * Return true if the string contains $needle, false otherwise. By default,
     * the comparison is case-sensitive, but can be made insensitive by setting
     * $caseSensitive to false.
     *
     * @param mixed $needle
     * @param mixed $caseSensitive
     * @return bool
     */
    public function has($needle, $caseSensitive = true)
    {
        return StaticStringy::contains(
            $this->string, $this->retrieveValue($needle), $this->getBoolable($caseSensitive)
        );
    }

    /**
     * Return true if the string contains any $needles, false otherwise. By
     * default, the comparison is case-sensitive, but can be made insensitive
     * by setting $caseSensitive to false.
     *
     * @param mixed $strings
     * @param mixed $caseSensitive
     * @return bool
     */
    public function hasAny($strings, $caseSensitive = true)
    {
        return StaticStringy::containsAny(
            $this->string, $this->getArrayable($strings), $this->getBoolable($caseSensitive)
        );
    }

    /**
     * Return true if the string contains all $needles, false otherwise. By
     * default, the comparison is case-sensitive, but can be made insensitive
     * by setting $caseSensitive to false.
     *
     * @param mixed $strings
     * @param mixed $caseSensitive
     * @return bool
     */
    public function hasAll($strings, $caseSensitive = true)
    {
        return StaticStringy::containsAll(
            $this->string, $this->getArrayable($strings), $this->getBoolable($caseSensitive)
        );
    }

    /**
     * Trim the string and replaces consecutive whitespace characters with a
     * single space. This includes tabs and newline characters, as well as
     * multi-byte whitespace such as the thin space and ideographic space.
     *
     * @return static
     */
    public function collapseWhitespace()
    {
        return $this->replaceRegex('[[:space:]]+', ' ')->trim();
    }

    /**
     * Return an ASCII version of the String. A set of non-ASCII characters are
     * replaced with their closest ASCII counterparts, and the rest are removed.
     *
     * @return static
     */
    public function toAscii()
    {
        return new static(StaticStringy::toAscii($this->string));
    }

    /**
     * Convert each tab in the string to some number of spaces, as defined by
     * $tabLength. By default, each tab is converted to 4 consecutive spaces.
     *
     * @param int $tabLength
     * @return static
     */
    public function toSpaces($tabLength = 4)
    {
        return new static(StaticStringy::toSpaces($this->string, $this->getIntegerable($tabLength)));
    }

    /**
     * Convert each occurrence of some consecutive number of spaces, as
     * defined by $tabLength, to a tab. By default, each 4 consecutive spaces
     * are converted to a tab.
     *
     * @param int $tabLength
     * @return static
     */
    public function toTabs($tabLength = 4)
    {
        return new static(StaticStringy::toTabs($this->string, $this->getIntegerable($tabLength)));
    }

    /**
     * Surround String with given string.
     *
     * @param mixed $surround
     * @return static
     */
    public function surround($surround)
    {
        return new static(StaticStringy::surround($this->string, $this->retrieveValue($surround)));
    }

    /**
     * Insert $insert into the String at the $index provided.
     *
     * @param mixed $insert
     * @param mixed $index
     * @return static
     */
    public function insert($insert, $index)
    {
        return new static(StaticStringy::insert(
            $this->string, $this->retrieveValue($insert), $this->getIntegerable($index))
        );
    }

    /**
     * Return a reversed string. A multi-byte version of strrev().
     *
     * @return static
     */
    public function reverse()
    {
        return new static(StaticStringy::reverse($this->string));
    }

    /**
     * Return char at provided $index
     *
     * @param mixed $index
     * @return static
     */
    public function at($index)
    {
        return new static(StaticStringy::at($this->string, $this->getIntegerable($index)));
    }

    /**
     * Return first $length chars
     *
     * @param mixed $length
     * @return static
     */
    public function first($length)
    {
        return new static(StaticStringy::first($this->string, $this->getIntegerable($length)));
    }

    /**
     * Return last $length chars
     *
     * @param mixed $length
     * @return static
     */
    public function last($length)
    {
        return new static(StaticStringy::last($this->string, $this->getIntegerable($length)));
    }

    /**
     * Ensure that the String begins with $string. If it doesn't, it's
     * prepended.
     *
     * @param mixed $string
     * @return static
     */
    public function ensureLeft($string)
    {
        return new static(StaticStringy::ensureLeft($this->string, $this->retrieveValue($string)));
    }

    /**
     * Ensure that the String ends with $substring. If it doesn't, it's
     * appended.
     *
     * @param mixed $string
     * @return static
     */
    public function ensureRight($string)
    {
        return new static(StaticStringy::ensureRight($this->string, $this->retrieveValue($string)));
    }

    /**
     * Remove the prefix $string from left, if present.
     *
     * @param mixed $string
     * @return static
     */
    public function removeLeft($string)
    {
        return new static(StaticStringy::removeLeft($this->string, $this->retrieveValue($string)));
    }

    /**
     * Remove the prefix $string from right, if present.
     *
     * @param mixed $string
     * @return static
     */
    public function removeRight($string)
    {
        return new static(StaticStringy::removeRight($this->string, $this->retrieveValue($string)));
    }

    /**
     * Replaces all occurrences of $search by $replacement.
     *
     * @param mixed $search
     * @param string|StringContract $replace
     * @return static
     */
    public function replace($search, $replace)
    {
        $string = Stringy::create($this->string);

        $replace = $this->retrieveValue($replace);

        foreach ((array) $this->getSearchable($search) as $find)
        {
            $string = $string->replace($find, $replace);
        }

        return new static((string) $string);
    }

    /**
     * Replaces all occurrences of $pattern by $replacement. An alias
     * for mb_ereg_replace().
     *
     * @param string|StringInterface $pattern
     * @param string|StringInterface $replace
     * @return static
     */
    public function replaceRegex($pattern, $replace)
    {
        return new static(StaticStringy::regexReplace(
            $this->string, $this->retrieveValue($pattern), $this->retrieveValue($replace))
        );
    }

    /**
     * Determine if String starts with a given needles.
     * The comparison is case-sensitive, but can be made insensitive
     * by setting $caseSensitive to false.
     *
     * @param mixed $needles
     * @param bool $caseSensitive
     * @return bool
     */
    public function startsWith($needles, $caseSensitive = true)
    {
        return Str::startsWith(
            $this->string, $this->getSearchable($needles), $this->getBoolable($caseSensitive, true)
        );
    }

    /**
     * Determine if String ends with a given needles.
     *
     * @param mixed $needles
     * @return bool
     */
    public function endsWith($needles)
    {
        return Str::endsWith($this->string, $this->getSearchable($needles, []));
    }

    /**
     * Return true if String matches the supplied pattern, false otherwise.
     *
     * @param string|StringContract $pattern
     * @return bool
     */
    public function is($pattern)
    {
        return (bool) Str::matches($this->retrieveValue($pattern), $this->string);
    }

    /**
     * Cap a String with a single instance of a given $cap.
     *
     * @param string|StringContract $cap
     * @return static
     */
    public function finish($cap)
    {
        return new static(Str::finish($this->string, $this->retrieveValue($cap)));
    }

    /**
     * Limit the number of words in a String.
     *
     * @param int|IntegerContract $limit
     * @param string|StringContract $end
     * @return static
     */
    public function limitWords($limit, $end = '...')
    {
        return new static(Str::words($this->string, $this->getIntegerable($limit), $this->retrieveValue($end)));
    }

    /**
     * Parse a Class@method style callback into class and method.
     * Return Container with parsed.
     *
     * @param string|StringContract $default
     * @return \im\Primitive\Container\Container
     */
    public function parseCallback($default = '')
    {
        return new Container(Str::parseCallback($this->string, $this->retrieveValue($default)));
    }

    /**
     * Generates random Unique User Identifier
     *
     * @return static
     */
    public function uuid()
    {
        return new static(sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        ));
    }

    /**
     * Generate a more truly "random" alpha-numeric String.
     *
     * @param int|IntegerContract $length
     * @return static
     */
    public function random($length = 16)
    {
        return new static(Str::random($this->getIntegerable($length)));
    }

    /**
     * Generate a "random" alpha-numeric String.
     *
     * @param int|IntegerContract $length
     * @return static
     */
    public function quickRandom($length = 16)
    {
        return new static(Str::quickRandom($this->getIntegerable($length)));
    }

    /**
     * Generate a URL friendly "slug".
     *
     * @param string|StringContract $delimiter
     * @return static
     */
    public function slug($delimiter = '-')
    {
        return new static(Str::slug($this->string, $this->retrieveValue($delimiter)));
    }

    /**
     * Split a String by string
     *
     * @param string|StringContract $delimiter
     * @return \im\Primitive\Container\Container
     */
    public function split($delimiter)
    {
        return new Container(explode($this->retrieveValue($delimiter), $this->string));
    }

    /**
     * Join arrayable elements with a String
     *
     * @param string|StringContract $glue
     * @param mixed $array
     * @return $this
     * @throws InvalidArgumentException
     */
    public function join($glue, $array)
    {
        if ($this->isArrayable($array))
        {
            $this->string = implode($this->retrieveValue($glue), $this->getArrayable($array));

            return $this;
        }

        throw new InvalidArgumentException('Argument 2 should be array, Container or instance of Arrayable');
    }

    /**
     * Strip whitespace (or other characters) from a String
     * Can trim:
     *      'front' from the beginning
     *      'back' from the end
     *      'all' all whitespace chars will be replace
     *       null from the end and beginning
     *
     * @param null|string|StringContract $what
     * @return static
     */
    public function trim($what = null)
    {
        switch ($this->retrieveValue($what))
        {
            case 'front':
                return new static(ltrim($this->string));
            case 'back':
                return new static(rtrim($this->string));
            case 'all':
                return $this->replaceRegex('[[:space:]]+', '');
            default:
                return new static(trim($this->string));
        }
    }

    /**
     * Repeat a String
     *
     * @param int|IntegerContract $quantity
     * @return static
     */
    public function repeat($quantity = 2)
    {
        return new static(str_repeat($this->string, $this->getIntegerable($quantity)));
    }

    /**
     * A multi-byte str_shuffle() function. It returns a string with its
     * characters in random order.
     *
     * @return static
     */
    public function shuffle()
    {
        return new static(StaticStringy::shuffle($this->string));
    }

    /**
     * Strip HTML and PHP tags from a String.
     * You can use the optional second parameter to specify tags which should
     * not be stripped.
     *
     * @param null|mixed $allowed
     * @return static
     */
    public function stripTags($allowed = null)
    {
        return new static(strip_tags($this->string, $this->getArrayable($allowed)));
    }

    /**
     * Encodes String with MIME base64
     *
     * @return static
     */
    public function base64()
    {
        return new static(base64_encode($this->string));
    }

    /**
     * Decodes String encoded with MIME base64
     *
     * @param string|StringContract $base
     * @return $this
     */
    public function fromBase64($base)
    {
        return $this->initialize(base64_decode($this->retrieveValue($base)));
    }

    /**
     * Convert all applicable characters to HTML entities.
     *
     * @param int $flags
     * @param string|StringContract $encoding
     * @return static
     */
    public function toEntities($flags = ENT_QUOTES, $encoding = 'UTF-8')
    {
        return new static(htmlentities($this->string, $flags, $this->retrieveValue($encoding)));
    }

    /**
     * Convert all HTML entities to their applicable characters.
     * First optional argument can be specified to decode and construct from $entities.
     *
     * @param string|StringContract|null $entities
     * @param int $flags
     * @param string|StringContract $encoding
     * @return $this
     */
    public function fromEntities($entities = null, $flags = ENT_QUOTES, $encoding = 'UTF-8')
    {
        if ( ! is_null($entities))
        {
            $this->string = html_entity_decode(
                $this->retrieveValue($entities), $flags, $this->retrieveValue($encoding)
            );

            return $this;
        }

        return new static(html_entity_decode($this->string, $flags, $this->retrieveValue($encoding)));
    }

    /**
     * Echo string
     *
     * @param string|StringContract $before
     * @param string|StringContract $after
     * @return $this
     */
    public function say($before = '', $after = '')
    {
        echo $this->retrieveValue($before), $this->string, $this->retrieveValue($after);

        return $this;
    }

    /**
     * Get part of String
     *
     * @param int|IntegerContract $offset
     * @param int|IntegerContract|null $length
     * @param string|StringContract $encoding
     * @return static
     */
    public function cut($offset, $length = null, $encoding = 'UTF-8')
    {
        return new static(
            mb_substr(
                $this->string,
                $this->getIntegerable($offset),
                $this->getIntegerable($length),
                $this->retrieveValue($encoding)
            )
        );
    }

    /**
     * Limit the number of characters in a String.
     *
     * @param int|IntegerContract $limit
     * @param string|StringContract $end
     * @return static
     */
    public function limit($limit = 100, $end = '...')
    {
        return new static(
            Str::limit($this->string, $this->getIntegerable($limit), $this->retrieveValue($end))
        );
    }

    /**
     * Truncates the string to a given length, while ensuring that it does not
     * split words. If word will be in the middle of limit, minus one word will be returned.
     *
     * @param int|IntegerContract $limit
     * @param string|StringContract $end
     * @return static
     */
    public function limitSafe($limit = 100, $end = '...')
    {
        return new static(
            StaticStringy::safeTruncate($this->string, $this->getIntegerable($limit)).$this->retrieveValue($end)
        );
    }

    /**
     * Parse GET data.
     *
     * @return \im\Primitive\Container\Container
     */
    public function toVars()
    {
        $vars = [];

        return mb_parse_str($this->string, $vars) && ! is_null($vars) ? new Container($vars) : new Container;
    }

    /**
     * Clean String.
     * Strip tags, convert all entities and trim.
     *
     * @return $this
     */
    public function clean()
    {
        return $this->stripTags()->toEntities()->trim();
    }

    /**
     * Reset to an empty String.
     *
     * @return $this
     */
    public function reset()
    {
        $this->string = '';

        return $this;
    }

    /**
     * Compress a String.
     *
     * @return static
     */
    public function compress()
    {
        return new static(gzcompress($this->string));
    }

    /**
     * Decompress a String.
     * If optional argument $string specified than it will
     * decompress and construct from it.
     *
     * @param null|string|StringContract $string
     * @return $this
     */
    public function decompress($string = null)
    {
        $string = $this->isStringable($string) ? $this->retrieveValue($string) : $this->string;

        $this->string = gzuncompress($string);

        return $this;
    }

    /**
     * Encrypt and sign a String into a JWT token.
     *
     * @param string|StringContract $key to encrypt with
     * @param int|IntegerContract $expires timestamp
     * @return static
     */
    public function encrypt($key, $expires)
    {
        $payload = [
            'exp' => $this->getIntegerable($expires),
            'string' => $this->string
        ];

        return JWT::encode($payload, $this->getStringable($key));
    }

    /**
     * Construct from proper encrypted String with JWT.
     *
     * @param string|StringContract $encrypted
     * @param string|StringContract $key
     * @return $this
     */
    public function fromEncrypted($encrypted, $key)
    {
        $encrypted = $this->retrieveValue($encrypted);

        $key = $this->retrieveValue($key);

        if ($this->isEncryptedString($encrypted, $key))
        {
            $data = JWT::decode($encrypted, $key);

            return $this->initialize($data->string);
        }

        throw new BadMethodCallException('Expected encrypted String, got: ' . $encrypted);
    }

    /**
     * Return value. Alias for value method.
     *
     * @return string
     */
    public function all()
    {
        return $this->get();
    }

    /**
     * Get file contents, if String is proper file path.
     *
     * @return static
     * @throws \im\Primitive\String\Exceptions\StringException
     */
    public function contents()
    {
        if ($this->isFile())
        {
            return new static(file_get_contents($this->string));
        }

        throw new StringException('Not is file: '.$this->string);
    }

    /**
     * Check if String is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return ! (bool) $this->length();
    }

    /**
     * Check if String is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return (bool) $this->length();
    }

    /**
     * Check if String has only alpha chars.
     *
     * @return bool
     */
    public function isAlpha()
    {
        return $this->is('^[[:alpha:]]*$');
    }

    /**
     * Check if String has only alphanumeric chars.
     *
     * @return bool
     */
    public function isAlphanumeric()
    {
        return $this->is('^[[:alnum:]]*$');
    }

    /**
     * Check if String has only whitespace chars.
     *
     * @return bool
     */
    public function isWhitespaces()
    {
        return $this->is('^[[:space:]]*$');
    }

    /**
     * Check if String is hexadecimal.
     *
     * @return bool
     */
    public function isHex()
    {
        return $this->is('^[[:xdigit:]]*$');
    }

    /**
     * Check if String is in lower case.
     *
     * @return bool
     */
    public function isLower()
    {
        return $this->is('^[[:lower:]]*$');
    }

    /**
     * Check if String is in upper case.
     *
     * @return bool
     */
    public function isUpper()
    {
        return $this->is('^[[:upper:]]*$');
    }

    /**
     * Check if String is Unique User Identifier generated by String.
     * If optional $uuid argument is specified, it will check it not inner value.
     *
     * @param null|string|StringContract $uuid
     * @return bool
     */
    public function isUuid($uuid = null)
    {
        if (is_null($uuid)) $uuid = $this->string;

        $uuid = $this->retrieveValue($uuid);

        return (bool) preg_match(
            '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid
        );
    }

    /**
     * Check if String is proper json.
     *
     * @return bool
     */
    public function isJson()
    {
        return Str::isJson($this->string);
    }

    /**
     * Check if String is proper file path.
     *
     * @return bool
     */
    public function isFile()
    {
        return Str::isFile($this->string);
    }

    /**
     * Check if String is proper serialized.
     *
     * @return bool
     */
    public function isSerialized()
    {
        return Str::isSerialized($this->string);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->value();
    }

    /**
     * Convert String Type to Bool Type.
     *
     * @return \im\Primitive\Bool\Bool
     */
    public function toBool()
    {
        return new Bool($this->value());
    }

    /**
     * Convert String Type to Int Type.
     *
     * @return \im\Primitive\Int\Int
     */
    public function toInt()
    {
        return new Int($this->value());
    }

    /**
     * @return \im\Primitive\Float\Float
     */
    public function toFloat()
    {
        return new Float($this->value());
    }

    /**
     * Convert String Type to Container Type.
     * If String is proper json, serialized or file path with json or serialized
     * than it will grab contents decode it and construct Container from it.
     *
     * @return \im\Primitive\Container\Container
     */
    public function toContainer()
    {
        $value = $this->value();

        if ( ! $this->isFile() || ! $this->isJson() || ! $this->isSerialized())
        {
            $value = [$value];
        }

        return new Container($value);
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        unset($this->string);
    }

    /**
     * Measure String length.
     *
     * @return int
     */
    protected function measure()
    {
        return mb_strlen($this->string);
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize($value)
    {
        if ( ! $this->isStringable($value))
        {
            throw new InvalidArgumentException('Argument 1 should be string or object implementing __toString');
        }

        $this->string = $this->retrieveValue($value);

        return $this;
    }


    /**
     * Check if given $string and $delimiter is Stringable.
     *
     * @param mixed $string
     * @param mixed $delimiter
     * @return bool
     */
    protected function isValidStringAndDelimiter($string, $delimiter)
    {
        return $this->isStringable($string) && $this->isStringable($delimiter);
    }

    /**
     * {@inheritdoc}
     */
    protected function retrieveValue($value)
    {
        return $this->getStringable($value, $this->getDefault());
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefault()
    {
        return '';
    }

    /*
    |--------------------------------------------------------------------------
    | ArrayAccess
    |--------------------------------------------------------------------------
    */
    /**
     * (PHP 5 &gt;= 5.0.0)
     * Offset to set. Sets chars to given index.
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset
     * The offset to assign the value to.
     * @param mixed $value
     * The value to set.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->append($value, '');
        }

        if ($this->offsetExists($offset))
        {
            $this->string = (string) $this->chars()->set($offset, $this->retrieveValue($value))->join();
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)
     * Whether a offset exists.
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset
     * An offset to check for.
     *
     * @return boolean true on success or false on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        $length = $this->length();
        $offset = $this->getIntegerable($offset);

        if ($offset >= 0) return ($length > $offset);

        return ($length >= abs($offset));
    }

    /**
     * (PHP 5 &gt;= 5.0.0)
     * Offset to unset.
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     * The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset))
        {
            $this->string = (string) $this->chars()->forget($offset)->join();
        }
    }

    /**
     * Get offset. Implements python-like string slices.
     * If slice (['2:4:1']) is not present it will return char at offset,
     * otherwise slices the string if the offset
     * contains at least a single colon. Slice notation follow the format
     * "start:stop:step". If no colon is present, returns the character at the
     * given index. Offsets may be negative to count from the last character in
     * the string. Throws an exception if the index does not exist, more than 3
     * slice args are given, or the step is 0.
     *
     * @param int|string|StringContract $args
     * @return \im\Primitive\String\String
     */
    public function offsetGet($args)
    {
        if ( ! $this->isStringable($args, true) || strpos($args, ':') === false)
        {
            return $this->at($args);
        }

        return $this->slice($args);
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */
    /**
     * (PHP 5 &gt;= 5.1.0)
     * Count chars of a String.
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
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
     * (PHP 5 &gt;= 5.0.0)
     * Retrieve an external iterator.
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance
     */
    public function getIterator()
    {
        return new ArrayIterator($this->chars()->all());
    }
}
