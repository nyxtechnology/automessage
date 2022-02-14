<?php

namespace Tests\Unit;

use Tests\TestCase;
#use PHPUnit\Framework\TestCase;
use App\Console\Commands\SendMailSMTP;

class MailSendSmtpTest extends TestCase
{
    /**
     * @test
     *
     * @return
     */
    public function test_handle()
    {
        $mailSend = new SendMailSMTP();
        $mailSend->handle();
        $this->assertTrue(true);
    }
}
