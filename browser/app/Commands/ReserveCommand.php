<?php

namespace App\Commands;

use Laravel\Dusk\Browser;
use LaravelZero\Framework\Commands\Command;
use NunoMaduro\LaravelConsoleDusk\ConsoleBrowser;

class ReserveCommand extends Command
{
    protected $signature = 'reserve';
    protected $description = 'Automatically reserves open spin classes.';

    public function handle(): void
    {
        $this->browse(function (ConsoleBrowser $browser) {
            $page = $browser->getOriginalBrowser();

            $page
                ->visit('https://.../portal/login')
                ->waitForText('Member Portal')
                ->type('email', env('EMAIL'))
                ->type('password', env('PASSWORD'))
                ->clickAtXPath('//button[@type = "submit"]');
            $this->info('Logged in.');

            $page
                ->visit('https://.../account/book/class')
                ->waitFor('.class-booking-list');

            $this->info('Navigated to class booking page.');

            $this->info('Clicking on open classes...');

            $bookingButtons = $page
                ->waitFor('.class-booking')
                ->elements('.class-booking:not(.disabled)');

            if (empty($bookingButtons)) {
                $this->error('No open classes found.');
                return;
            }

            foreach ($bookingButtons as $booking) {
                $booking->click();
                $this->info('Clicked on class.');
            }

            $this->info('Reserving class...');

            $page
                ->waitFor('.bookallclass')
                ->within('.bookallclass', function (Browser $page) {
                    $page->click('.submit_btn');
                    $this->info('Class reserved.');
                });

            $page
                ->waitForText('You will be charged')
                ->waitFor('#submit_btn')
                ->click('#submit_btn')
                ->waitForText('Class Booked');

            $this->info('Classes booked.');
        });
    }
}
