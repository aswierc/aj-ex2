<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 *   Given you have two arrays:
 *
 *   items = [2, 177];
 *   rules = [
 *   '[(13  OR 3  OR 2 )]',
 *   '[(54  OR 77 ) AND 17  AND 59  AND 36 ] OR [(2  AND 36 )]',
 *   '[(2  OR 3  OR 13 ) AND 30 ]',
 *   '[(2 )] OR [(13  OR 4 ) AND (17 )]',
 *   '[(2 )] OR [(13  OR 3 ) AND 17 ]',
 *   '[(2  AND 30 ) OR (3  AND 30 )]',
 *   ];
 *
 * @author Artur Świerc <aswierc@gmail.com>
 */
class NumberEvaluatorTest extends TestCase
{
    /**
     * @param string $rule
     * @param int    $item
     * @param bool   $expected
     *
     * @dataProvider ruleProvider
     */
    public function testEvaluateNumberCase(string $rule, int $item, bool $expected): void
    {
        $simplifyRule = \str_replace(
            ['[',']', 'AND', 'OR', $item.' '],
            ['(', ')', '&', '|', 'true '],
            $rule
        );
        $simplifyRule = \preg_replace('/[0-9]+/', 'false', $simplifyRule);

        $expression = new ExpressionLanguage();
        $evaluated = $expression->evaluate($simplifyRule);

        $this->assertEquals($expected, $evaluated, $simplifyRule);
    }

    /**
     * @return array
     */
    public function ruleProvider(): array
    {
        return [
            ['[(13  OR 3  OR 2 )]', 2, true],
            ['[(13  OR 3  OR 2 )]', 177, false],
            ['[(54  OR 77 ) AND 17  AND 59  AND 36 ] OR [(2  AND 36 )]', 2, false],
            ['[(54  OR 77 ) AND 17  AND 59  AND 36 ] OR [(2  AND 36 )]', 177, false],
            ['[(2  OR 3  OR 13 ) AND 30 ]', 2, false],
            ['[(2  OR 3  OR 13 ) AND 30 ]', 177, false],
            ['[(2 )] OR [(13  OR 4 ) AND (17 )]', 2, true],
            ['[(2 )] OR [(13  OR 4 ) AND (17 )]', 177, false],
            ['[(2  AND 30 ) OR (3  AND 30 )]', 2, false],
            ['[(2  AND 30 ) OR (3  AND 30 )]', 177, false],
        ];
    }
}
