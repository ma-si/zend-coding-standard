<?php
/**
 * Copied from:
 *
 * @see https://github.com/consistence/coding-standard/blob/master/Consistence/Sniffs/NamingConventions/ValidVariableNameSniff.php
 */

declare(strict_types=1);

namespace ZendCodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Common;

use function in_array;
use function ltrim;

use const T_DOUBLE_COLON;
use const T_WHITESPACE;

class ValidVariableNameSniff extends AbstractVariableSniff
{
    /** @var string[] */
    private $phpReservedVars = [
        '_SERVER',
        '_GET',
        '_POST',
        '_REQUEST',
        '_SESSION',
        '_ENV',
        '_COOKIE',
        '_FILES',
        'GLOBALS',
    ];

    /**
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr) : void
    {
        $tokens = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        // If it's a php reserved var, then its ok.
        if (in_array($varName, $this->phpReservedVars, true)) {
            return;
        }

        $objOperator = $phpcsFile->findPrevious([T_WHITESPACE], $stackPtr - 1, null, true);
        if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
            return; // skip MyClass::$variable, there might be no control over the declaration
        }

        if (! Common::isCamelCaps($varName, false, true, false)) {
            $error = 'Variable "%s" is not in valid camel caps format';
            $data = [$varName];
            $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $data);
        }
    }

    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr) : void
    {
        // handled by PSR2.Classes.PropertyDeclaration
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr) : void
    {
        // handled by Squiz.Strings.DoubleQuoteUsage
    }
}
