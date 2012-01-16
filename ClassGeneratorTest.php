<?php

use Mockista\MethodInterface;

require_once __DIR__ . "/bootstrap.php";

class ClassGeneratorTest_Empty
{
}

class ClassGeneratorTest_Method
{
	function abc($a, $def = 123, $ghi = 'a')
	{
	}
}

interface ClassGeneratorTest_Interface
{
	function ai(Array $ax = array(1, 2, 3));
}

class ClassGeneratorTest extends KDev_Test
{
	function prepare()
	{
		$this->object = new Mockista\ClassGenerator;
	}

	function mockNoMethods()
	{
		$mock = Mockista\mock();
		$mock->methods()->once->andReturn(array());
		return $mock;
	}

	function testEmptyClass()
	{
		$emptyClass = '<?php
class ClassGeneratorTest_Empty_Generated extends ClassGeneratorTest_Empty
{
	public $mockista;

	function __call($name, $args)
	{
		return call_user_func_array(array($this->mockista, $name), $args);
	}
}
';
		$this->object->setMethodFinder($this->mockNoMethods);
		$this->assertEquals($emptyClass, $this->object->generate("ClassGeneratorTest_Empty", "ClassGeneratorTest_Empty_Generated"));
	}

	function testMethod()
	{
		$classIncludingMethod = '<?php
class ClassGeneratorTest_Method_Generated extends ClassGeneratorTest_Method
{
	public $mockista;

	function __call($name, $args)
	{
		return call_user_func_array(array($this->mockista, $name), $args);
	}

	function abc($a, $def = 123, $ghi = \'a\')
	{
		return call_user_func_array(array($this->mockista, \'abc\'), func_get_args());
	}
}
';
		$this->object->setMethodFinder(new Mockista\MethodFinder);
		$this->assertEquals($classIncludingMethod, $this->object->generate("ClassGeneratorTest_Method", "ClassGeneratorTest_Method_Generated"));

	}

	function testInterface()
	{
		$interfaceBasedClass = '<?php
class ClassGeneratorTest_Interface_Generated implements ClassGeneratorTest_Interface
{
	public $mockista;

	function __call($name, $args)
	{
		return call_user_func_array(array($this->mockista, $name), $args);
	}

	function ai(Array $ax = array (  0 => 1,  1 => 2,  2 => 3,))
	{
		return call_user_func_array(array($this->mockista, \'ai\'), func_get_args());
	}
}
';
		$this->object->setMethodFinder(new Mockista\MethodFinder);
		$this->assertEquals($interfaceBasedClass, $this->object->generate("ClassGeneratorTest_Interface", "ClassGeneratorTest_Interface_Generated"));
	}
}
