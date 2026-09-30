<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModalFormsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<int, string>
     */
    protected function listPages(): array
    {
        return [
            '/admin/users',
            '/admin/banking-types',
            '/admin/user-groups',
            '/admin/daily-account-openings',
            '/admin/daily-deposit-performances',
            '/admin/branches',
            '/admin/districts',
            '/admin/annual-plans',
            '/admin/kpis',
            '/admin/permissions',
        ];
    }

    public function test_create_and_edit_routes_are_removed(): void
    {
        foreach ($this->listPages() as $page) {
            foreach (['/create', '/1/edit'] as $suffix) {
                $status = $this->get($page . $suffix)->getStatusCode();

                $this->assertTrue(
                    in_array($status, [404, 302], true),
                    "Expected {$page}{$suffix} to be gone, got {$status}",
                );
            }
        }
    }

    public function test_list_pages_render_without_create_or_edit_links(): void
    {
        $this->seed();

        $user = User::where('email', 'admin@bpms.com')->firstOrFail();
        $user->syncRoles(['admin']);

        $failures = [];

        foreach ($this->listPages() as $page) {
            $response = $this->actingAs($user)->get($page);

            if ($response->getStatusCode() !== 200) {
                $failures[] = "{$page} => {$response->getStatusCode()}";

                continue;
            }

            if (str_contains($response->getContent(), 'href="' . $page . '/create"')) {
                $failures[] = "{$page} => still links to /create";
            }

            if (str_contains($response->getContent(), $page . '/1/edit')) {
                $failures[] = "{$page} => still links to /edit";
            }
        }

        $this->assertSame([], $failures, "Problems:\n" . implode("\n", $failures));
    }
}
