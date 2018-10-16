<?php
declare(strict_types=1);

namespace IronBound\DB\Tests;

use PHPUnit\Framework\TestCase;

final class NullTest extends TestCase
{
    public function test_null_strategy_is_alias_for_null_mail_strategy()
    {
        $strategy = new \IronBound\WP_Notifications\Strategy\Null();

        $this->assertInstanceOf( 'IronBound\WP_Notifications\Strategy\Null_Mail', $strategy );
    }
}
