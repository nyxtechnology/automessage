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
     * @return void
     */
    public function test_handle()
    {
        $_ENV["MAIL_HOST"] = "smtp.mail.yahoo.com";

        $mailSend = new SendMailSMTP();
        $mailSend->handle();
        $this->assertTrue(true);
    }
}
