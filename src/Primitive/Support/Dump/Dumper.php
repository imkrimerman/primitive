<?php namespace im\Primitive\Support\Dump;

use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;

/**
 * Class Dumper
 *
 * @package im\Primitive\Support\Dump
 * @author Taylor Otwel
 */
class Dumper {

    /**
     * Dump a value with elegance.
     *
     * @param  mixed  $value
     * @return void
     */
    public function dump($value)
    {
        $dumper = 'cli' === PHP_SAPI ? new CliDumper : new HtmlDumper;
        $dumper->dump((new VarCloner)->cloneVar($value));
    }
}
