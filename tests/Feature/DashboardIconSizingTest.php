<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\TestDataSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Icon sizing guard.
 *
 * This page renders inside the Filament admin panel, which loads only
 * vendor/filament/filament/dist/theme.css. Tailwind v4 is JIT, and that
 * stylesheet was compiled from Filament's own templates, so it does not contain
 * .w-3/.h-3/.w-4/.h-4/.w-5/.h-5 or any other utility used only by this app.
 * The app's own CSS is not registered on the panel either, because
 * AdminPanelProvider declares no ->viteTheme() call.
 *
 * <x-heroicon-*> emits a bare <svg viewBox="..."> with no width/height
 * attributes. With no sizing rule anywhere the browser falls back to 100% x
 * 100% of the container and every icon renders full-screen.
 *
 * The fix is a set of explicit size classes defined in the view's own <style>
 * block, which this test locks in. Asserting against the view SOURCE is
 * deliberate: the rendered HTML of this page is very large, and diffing it on
 * failure stalls PHPUnit.
 */
class DashboardIconSizingTest extends TestCase
{
    use RefreshDatabase;

    private const VIEW = 'resources/views/filament/pages/daily-business-performance.blade.php';

    protected function setUp(): void
    {
        parent::setUp();

        Model::withoutEvents(function (): void {
            $this->seed(TestDataSeeder::class);
        });

        $this->actingAs(User::where('email', 'seidm2031@gmail.com')->firstOrFail());
    }

    private function viewSource(): string
    {
        $path = base_path(self::VIEW);

        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }

    /**
     * Extracts the class attribute from every <x-heroicon-*> usage.
     *
     * @return array<int, string>
     */
    private function iconClasses(string $source): array
    {
        preg_match_all('/<x-heroicon-[^>]*\bclass="([^"]*)"/', $source, $matches);

        return $matches[1];
    }

    public function test_the_view_uses_at_least_one_heroicon(): void
    {
        // Guard the guard: if the page ever drops its icons entirely, the
        // assertions below would pass vacuously.
        $this->assertNotEmpty(
            $this->iconClasses($this->viewSource()),
            'Expected the page to still use <x-heroicon-*> components.'
        );
    }

    public function test_no_icon_relies_on_a_tailwind_size_utility(): void
    {
        // These are the classes that broke the page. They do not exist in the
        // panel's stylesheet, so an icon carrying one is unsized.
        $forbidden = ['w-3', 'h-3', 'w-4', 'h-4', 'w-5', 'h-5', 'w-6', 'h-6'];

        $offenders = [];

        foreach ($this->iconClasses($this->viewSource()) as $class) {
            $tokens = preg_split('/\s+/', trim($class));

            foreach ((array) $tokens as $token) {
                if (in_array($token, $forbidden, true)) {
                    $offenders[] = $class;
                    break;
                }
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "Icons must not depend on Tailwind size utilities: they are absent "
            . "from the Filament panel stylesheet, which leaves the SVG unsized "
            . "and rendering full-screen. Offending classes:\n"
            . implode("\n", $offenders)
        );
    }

    public function test_every_icon_class_is_defined_by_the_view_stylesheet(): void
    {
        $source = $this->viewSource();

        foreach ($this->iconClasses($source) as $class) {
            foreach (preg_split('/\s+/', trim($class)) ?: [] as $token) {
                // Skip presentational classes with no sizing responsibility,
                // e.g. text-gray-400 or inline style flags.
                if (str_starts_with($token, 'text-')) {
                    continue;
                }

                $this->assertStringContainsString(
                    '.' . $token,
                    $source,
                    "Icon class '{$token}' is used but never defined, so the SVG "
                    . "has no size and will render full-screen."
                );
            }
        }
    }

    /**
     * @dataProvider iconSizeClasses
     */
    public function test_each_icon_size_class_sets_both_width_and_height(string $class): void
    {
        $source = $this->viewSource();

        $this->assertMatchesRegularExpression(
            '/\.' . preg_quote($class, '/') . '\s*(,[^{]*)?\{[^}]*\bwidth\s*:/',
            $source,
            "'.{$class}' must set a width; height alone still stretches SVGs."
        );

        $this->assertMatchesRegularExpression(
            '/\.' . preg_quote($class, '/') . '\s*(,[^{]*)?\{[^}]*\bheight\s*:/',
            $source,
            "'.{$class}' must set a height; width alone still stretches SVGs."
        );
    }

    /** @return array<string, array{string}> */
    public static function iconSizeClasses(): array
    {
        return [
            'sm' => ['fi-dash-icon-sm'],
            'md' => ['fi-dash-icon-md'],
            'lg' => ['fi-dash-icon-lg'],
        ];
    }

    public function test_kpi_icons_are_sized(): void
    {
        // .kpi-icon has padding but no size, so its child SVG needs one too.
        $this->assertMatchesRegularExpression(
            '/\.kpi-icon\s+svg\s*\{[^}]*\bwidth\s*:[^}]*\bheight\s*:/s',
            $this->viewSource(),
            '.kpi-icon svg needs an explicit width and height.'
        );
    }
}