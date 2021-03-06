<?php

namespace Shunt\Parser;

use Shunt\Parser;
use Shunt\Context;


class FunctionParserTest extends \PHPUnit_Framework_TestCase
{

    public function testFunctionCallWithOneParam()
    {
        $context = new Context();
        $context->defFunction(
            'myfunc',
            function ($param1) {
                return $param1;
            }
        );

        $equation = '12 + myfunc(100) * 2';
        $actual = Parser::parse($equation, $context);

        $expected = 212;
        $this->assertEquals($expected, $actual);
    }

    public function testFunctionCallWithoutParam()
    {
        $context = new Context();
        $context->defFunction(
            'myfunc',
            function () {
                return 1;
            }
        );

        $equation = '12 + myfunc() * 2';
        $actual = Parser::parse($equation, $context);

        $expected = 14;
        $this->assertEquals($expected, $actual);
    }

    public function testFunctionCallWithTwoParams()
    {
        $context = new Context();
        $context->defFunction(
            'myfunc',
            function ($param1, $param2) {
                return ($param1 + $param2);
            }
        );

        $equation = '12+myfunc(100,50)*2';
        $actual = Parser::parse($equation, $context);

        $expected = 312;
        $this->assertEquals($expected, $actual);

        $equation = 'myfunc(1,-2)';
        $actual = Parser::parse($equation, $context);

        $expected = -1;
        $this->assertEquals($expected, $actual);
    }

    public function testFunctionCallWithDefaultParamValue()
    {
        $context = new Context();
        $context->defFunction(
            'myfunc',
            function ($param1, $param2 = 33) {
                return ($param1 + $param2);
            }
        );

        $equation = '12+myfunc(100)*2';
        $actual = Parser::parse($equation, $context);

        $expected = 278;
        $this->assertEquals($expected, $actual);

        $equation = '12+myfunc(100,44)*2';
        $actual = Parser::parse($equation, $context);

        $expected = 300;
        $this->assertEquals($expected, $actual);
    }

    public function testWrapPHPFunction()
    {
        $context = new Context();
        $context->defFunction('abs');

        $equation = 'abs(100)';
        $actual = Parser::parse($equation, $context);

        $expected = 100;
        $this->assertEquals($expected, $actual);

        $equation = 'abs(-100)';
        $actual = Parser::parse($equation, $context);

        $expected = 100;
        $this->assertEquals($expected, $actual);
    }

    public function testWrapPHPFunctionWithoutParam()
    {
        $context = new Context();
        $context->defFunction('pi');

        $equation = 'pi()';
        $actual = Parser::parse($equation, $context);

        $expected = 3.1415926535898;
        $this->assertEquals($expected, $actual);
    }

    public function testWrapPHPFunctionWithoutParamNested()
    {
        $context = new Context();
        $context->defFunction('pi');
        $context->defFunction('min');

        $equation = 'min(pi(),0)';
        $actual = Parser::parse($equation, $context);

        $expected = 0;
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \Shunt\Exception\ParseError
     */
    public function testParserExceptionMissingClosingBracket()
    {
        $context = new Context();
        $context->defFunction('pi');

        $equation = 'pi(';
        $actual = Parser::parse($equation, $context);
    }

    /**
     * @expectedException \Shunt\Exception\ParseError
     */
    public function testParserExceptionSurplusClosingBracket()
    {
        $context = new Context();
        $context->defFunction('pi');

        $equation = 'pi())';
        $actual = Parser::parse($equation, $context);
    }

}
