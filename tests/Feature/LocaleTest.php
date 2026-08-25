<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_defaults_to_russian_validation_messages(): void
    {
        $response = $this->postJson('/register', []);

        $response->assertJsonFragment([
            'name' => ['Поле имя обязательно для заполнения.'],
        ]);
    }

    public function test_switching_locale_persists_in_session_and_changes_validation_messages(): void
    {
        $this->get('/locale/en')->assertRedirect();
        $this->assertSame('en', session('locale'));

        $response = $this->postJson('/register', []);

        $response->assertJsonFragment([
            'name' => ['The name field is required.'],
        ]);
    }

    public function test_switching_to_an_unsupported_locale_is_rejected(): void
    {
        $this->get('/locale/fr')->assertNotFound();
    }
}
