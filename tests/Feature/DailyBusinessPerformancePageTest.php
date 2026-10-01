<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\TestDataSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * DailyBusinessPerformance is a heavily-customised custom page, and its previous
 * breakage took down the whole panel including /admin/login, because Filament
 * resolves every page class at boot. These tests assert the page renders and that
 * the panel still boots.
 */
class DailyBusinessPerformancePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Model::withoutEvents(function (): void {
            $this->seed(TestDataSeeder::class);
        });

        $this->actingAs(User::where('email', 'seidm2031@gmail.com')->firstOrFail());
    }

    public function test_page_renders(): void
    {
        $component = Livewire::test(\App\Filament\Pages\DailyBusinessPerformance::class);

        $component->assertOk();

        $html = $component->html();
        $this->assertStringContainsString('Business Date', $html);
        $this->assertStringContainsString('District', $html);
    }

    public function test_business_date_defaults_to_yesterday(): void
    {
        $component = Livewire::test(\App\Filament\Pages\DailyBusinessPerformance::class);

        // $data is protected, so read it through Livewire's state bag. The
        // exact shape of the form state is Filament's business, so assert the
        // value is present and correct rather than pinning the whole array.
        $data = (array) $component->get('data');

        $this->assertArrayHasKey('businessDate', $data, 'The form should be seeded on mount.');
        $this->assertSame(
            now()->subDay()->toDateString(),
            $data['businessDate'],
            'The default must be yesterday, never today.'
        );
    }

    /**
     * The fatal this guards against: a page class that cannot be constructed
     * breaks panel boot, so even the login page 500s.
     */
    public function test_every_panel_page_class_can_be_instantiated(): void
    {
        $failures = [];

        foreach (\Filament\Facades\Filament::getPanel('admin')->getPages() as $class) {
            try {
                new $class();
            } catch (\Throwable $e) {
                $failures[] = $class . ' => ' . $e->getMessage();
            }
        }

        $this->assertSame([], $failures, "Unloadable panel pages:\n" . implode("\n", $failures));
    }

    public function test_login_page_still_works(): void
    {
        // An authenticated user is redirected away from /admin/login, which is
        // correct. What matters is that it is not a 500 - that is the failure
        // mode this guards against.
        $this->get('/admin/login')->assertStatus(302);
    }
}
