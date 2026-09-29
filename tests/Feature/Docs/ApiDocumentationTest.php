<?php

namespace Tests\Feature\Docs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_openapi_document_is_available(): void
    {
        $this->getJson('/docs/api.json')
            ->assertOk()
            ->assertJsonPath('info.version', '1.0.0')
            ->assertJsonStructure([
                'openapi',
                'info' => [
                    'title',
                    'version',
                    'description',
                ],
                'paths',
            ]);
    }

    public function test_openapi_document_includes_helpdesk_resources(): void
    {
        $paths = $this->getJson('/docs/api.json')
            ->assertOk()
            ->json('paths');

        $this->assertArrayHasKey('/tickets', $paths);
        $this->assertArrayHasKey('/users', $paths);
        $this->assertArrayHasKey('/categories', $paths);
        $this->assertArrayHasKey('/slas', $paths);
        $this->assertArrayHasKey('/comments', $paths);
        $this->assertArrayHasKey('/attachments', $paths);
        $this->assertArrayHasKey('/ticket-histories', $paths);
    }

    public function test_docs_ui_is_available(): void
    {
        $this->get('/docs/api')
            ->assertOk();
    }
}
