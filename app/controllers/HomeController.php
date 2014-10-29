<?php

use \im\Primitive\Container;
use \im\Primitive\String;

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function test()
	{

		// ------------------------------------------------------------------------------
		// Container TEST
		// ------------------------------------------------------------------------------
        $_ = array(
            1,2,3,
            array('id' => 1, 'mas' => 0),
            array('name' => 'Garry', array( 'id' => 1, 'get me')),
            array('id' => 1),
            array('parent' => 'def-1')
        );
		$tester = array( 1, 2 );

		$container = new Container( $_ );

        $container->where([ 'id' => 1 ])->dump();

//		$container->push('pushed');
//		$container->push('pushed', 25);
//		$container->unshift('unshifted');
//		$pop      = $container->pop();
//		$shift    = $container->shift();
//		$found    = $container->find(3);
//		$has      = $container->has(3);
//		$hasKey   = $container->hasKey(5);
//		$first    = $container->first();
//		$last     = $container->last();
//		$firstKey = $container->firstKey();
//		$lastKey  = $container->lastKey();
//		$container->push( array(5,5,8,90) );
//		$container->unique();
//		$keys   = $container->keys();
//		$values = $container->values();
//		$container->save();
//		$shuffled = $container->shuffle()->all();
//		$container->revert();
//		$filtered = $container->filter('is_int');
//		$implode  = $container->implode('__');
//		$container->save();
//		$container->flip();
//		$rand      = $container->rand();
//		$container->encrypt();
//		$container->decrypt();
//		$container->flip();
//		$delByKey = $container->forget(1);
//		$delByVal = $container->forget('pushed');
//		$container->clean();
//		$container->revert();
//		$container->reverse();
//		$afterFirst = $container->pre('first');
//		$beforeLast = $container->pre('last');
//		$all     	= $container->all();
//		$isEmpty 	= $container->isEmpty();
//		$isAssoc 	= $container->isAssoc();
//		$json    	= $container->toJson();

		// ------------------------------------------------------------------------------
		// String TEST
		// ------------------------------------------------------------------------------

		$string = new String('FooBar');

//		$string->camel();
//		$string->say('<br>','<br>')->revert();
//		$string->dashed();
//		$string->say('<br>','<br>')->revert();
//		$string->snake();
//		$string->say('<br>','<br>')->revert();
//		$string->append('Var', ' ');
//		$string->say('<br>','<br>')->revert();
//		$string->prepand('Before', ' ');
//		$string->say('<br>','<br>')->revert();
//		$string->lower();
//		$string->say('<br>','<br>')->revert();
//		$string->lower('first');
//		$string->say('<br>','<br>')->revert();
//		$string->upper();
//		$string->say('<br>','<br>')->revert();
//		$string->lower()->upper('first');
//		$string->say('<br>','<br>')->revert();
//		$string->lower()->upper('words');
//		$string->say('<br>','<br>')->revert();
//		$string->lower()->upper();
//		$string->say('<br>','<br>')->revert();
//		$has = $string->has('b', false);
//		$string->replace('Bar', '')->say()->revert();
//		$beginsWith = $string->startsWith('fo');
//		$endsWith   = $string->endsWith('r');
//		$explode = $string->explode(' ');
//		$string->encrypt();
//		$string->decrypt();
//		$string->eq('first=value&arr[]=foo+bar');
//		$vars = $string->toVars();

		return '1';
	}

}
