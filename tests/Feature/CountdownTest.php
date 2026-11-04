<?php

namespace Tests\Feature;

use App\Models\Countdown;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountdownTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->travelTo(CarbonImmutable::parse('2026-11-04 05:00:00', 'UTC'));
    }

    private function event(array $overrides = []): Countdown
    {
        return Countdown::create(array_merge([
            'name' => 'Peluncuran Karya', 'description' => 'Momen istimewa.',
            'target_at' => CarbonImmutable::parse('2026-11-05 02:30:00', 'UTC'),
        ], $overrides));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Peluncuran Karya', 'description' => 'Momen istimewa.',
            'date' => '2026-11-05', 'time' => '09:30',
        ], $overrides);
    }

    public function test_empty_state_and_creation_save_jakarta_input_as_utc(): void
    {
        $this->get('/')->assertOk()->assertSee('Belum ada momen yang ditandai.');
        $this->post('/countdowns', $this->payload())->assertRedirect('/');
        $event = Countdown::sole();
        $this->assertSame('2026-11-05 02:30:00', $event->getRawOriginal('target_at'));
        $this->assertSame('UTC', $event->target_at->timezoneName);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{48}$/', $event->share_token);
        $this->get('/')->assertOk()->assertSee('Peluncuran Karya')->assertSee('5 November 2026');
    }

    public function test_edit_and_delete_with_stable_share_token(): void
    {
        $event = $this->event();
        $token = $event->manage_token;
        $shareToken = $event->share_token;
        $this->get("/countdowns/$token/edit")->assertOk()->assertSee('09:30');
        $this->put("/countdowns/$token", $this->payload(['name' => 'Nama Baru', 'time' => '10:30']))->assertRedirect('/');
        $event->refresh();
        $this->assertSame('Nama Baru', $event->name);
        $this->assertSame($token, $event->manage_token);
        $this->assertSame($shareToken, $event->share_token);
        $this->assertSame('2026-11-05 03:30:00', $event->getRawOriginal('target_at'));
        $this->delete("/countdowns/$token")->assertRedirect('/');
        $this->assertDatabaseCount('countdowns', 0);
        $this->get('/c/'.$event->share_token)->assertNotFound();
    }

    public function test_invalid_and_nonfuture_dates_are_rejected(): void
    {
        foreach ([
            ['date' => '2026-11-04', 'time' => '11:59'],
            ['date' => '2026-11-04', 'time' => '12:00'],
            ['date' => '2026-02-30'], ['date' => 'bukan-tanggal'],
            ['time' => '25:61'], ['date' => ''], ['time' => ''],
            ['name' => ' '], ['name' => str_repeat('x', 101)],
            ['description' => str_repeat('x', 501)],
        ] as $invalid) {
            $this->post('/countdowns', $this->payload($invalid))->assertSessionHasErrors();
        }
        $this->assertDatabaseCount('countdowns', 0);
        $this->post('/countdowns', $this->payload(['date' => '2026-11-04', 'time' => '12:01']))->assertSessionHasNoErrors();
    }

    public function test_invalid_update_preserves_existing_event(): void
    {
        $event = $this->event();
        $this->put('/countdowns/'.$event->manage_token, $this->payload(['date' => '2020-01-01']))->assertSessionHasErrors('date');
        $this->assertSame('2026-11-05 02:30:00', $event->fresh()->getRawOriginal('target_at'));
    }

    public function test_utc_boundary_and_description_are_optional(): void
    {
        $this->post('/countdowns', $this->payload(['date' => '2026-11-05', 'time' => '00:15', 'description' => '']))->assertRedirect('/');
        $this->assertDatabaseHas('countdowns', ['target_at' => '2026-11-04 17:15:00', 'description' => null]);
    }

    public function test_shared_page_is_read_only_and_finished_events_remain_accessible(): void
    {
        $event = $this->event(['target_at' => now()->subDay()]);
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10']);
        $this->get('/c/'.$event->share_token)->assertOk()->assertSee('Acara telah dimulai.')
            ->assertDontSee('Buat Countdown')->assertDontSee('data-delete', false)->assertDontSee('data-preview-form', false);
        $this->get('/c/'.$event->id)->assertNotFound();
        $this->get('/c/not-a-valid-token')->assertNotFound();
        $this->get('/')->assertForbidden();
        $this->get('/countdowns/'.$event->manage_token.'/edit')->assertForbidden();
        $this->put('/countdowns/'.$event->manage_token, $this->payload())->assertForbidden();
        $this->delete('/countdowns/'.$event->manage_token)->assertForbidden();
        $this->post('/countdowns', $this->payload())->assertForbidden();
        $this->assertDatabaseCount('countdowns', 1);
    }

    public function test_share_token_never_authorizes_management_even_on_localhost(): void
    {
        $event = $this->event();
        $this->get('/countdowns/'.$event->share_token.'/edit')->assertNotFound();
        $this->put('/countdowns/'.$event->share_token, $this->payload())->assertNotFound();
        $this->delete('/countdowns/'.$event->share_token)->assertNotFound();
        $this->get('/c/'.$event->share_token)->assertOk()->assertDontSee($event->manage_token);
        $this->assertDatabaseCount('countdowns', 1);
    }

    public function test_external_host_and_forwarded_local_address_do_not_grant_management(): void
    {
        $this->get('http://example.com/')->assertForbidden();
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.1'])
            ->withHeader('X-Forwarded-For', '127.0.0.1')->get('/')->assertForbidden();
    }

    public function test_production_disables_anonymous_management_even_on_loopback(): void
    {
        $this->app->instance('env', 'production');
        $this->get('/')->assertForbidden();
    }

    public function test_clock_is_uncached_and_exposes_server_timestamp(): void
    {
        $response = $this->getJson('/time')->assertOk()->assertJsonPath('now', now()->getTimestampMs());
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_management_is_rate_limited(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->post('/countdowns', $this->payload())->assertRedirect('/');
        }
        $this->post('/countdowns', $this->payload())->assertStatus(429)->assertSee('Tunggu sebentar');
    }

    public function test_script_like_content_is_escaped_and_tokens_are_unique(): void
    {
        $first = $this->event(['name' => '</title><script>alert(1)</script>']);
        $second = $this->event();
        $this->assertNotSame($first->share_token, $second->share_token);
        $this->get('/c/'.$first->share_token)->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }
}
