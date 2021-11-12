<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SendMailTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://0.0.0.0:8025/')
                ->waitForText('Aerosmith')
                ->assertSee('Aerosmith')
                ->clickLink('MailHog')
                ->waitForText('Jim')
                ->assertSee('Mail Test')
                ->assertSee('Enable Jim')
                ->assertSee('saul@hudson.com');
        });
    }
}
