<?php

namespace Tests\Feature;

use App\Models\Episode;
use App\Models\Podcast;
use Database\Factories\PodcastFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EpisodeManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_episode_can_be_created_by_a_signed_in_user()
    {
        $this->withoutExceptionHandling();
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());
        $data = $this->data();

        $this->post('/podcasts/' . $podcast->id . '/episodes', $data);

        $episode = Episode::query()->first();
        $this->assertCount(1, Episode::all());
        $this->assertEquals($podcast->id, $episode->podcast_id);
        $this->assertEquals($this->data()['title'], $episode->title);
        $this->assertEquals($this->data()['description'], $episode->description);
        $this->assertEquals('episodes/' . $data['file']->hashName(), $episode->file);
        Storage::assertExists('episodes/' . $data['file']->hashName());
    }

    /** @test */
    public function a_guest_cannot_create_episodes()
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());
        Auth::logout();

        $response = $this->post('/podcasts/' . $podcast->id . '/episodes', $this->data());

        $response->assertRedirect('/login');
        $this->assertCount(0, Episode::all());
    }

    /** @test */
    public function an_episode_can_be_updated_by_a_signed_in_user()
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());
        $this->post('/podcasts/' . $podcast->id . '/episodes', $this->data());
        $episode = Episode::query()->first();
        $file = UploadedFile::fake()->create('ep2.mp3', 100, 'audio/mpeg');
        $data = [
            'title' => 'My updated episode name',
            'description' => 'My updated episode desc',
            'file' => $file,
        ];

        $this->patch('/podcasts/' . $podcast->id . '/episodes/' . $episode->id, $data);

        $episode->refresh();

        $this->assertEquals($podcast->id, $episode->podcast_id);
        $this->assertEquals($data['title'], $episode->title);
        $this->assertEquals($data['description'], $episode->description);
        $this->assertEquals('episodes/' . $file->hashName(), $episode->file);
        Storage::assertExists('episodes/' . $file->hashName());
    }

    /** @test */
    public function a_user_cannot_update_others_episodes()
    {
        $user = (new UserFactory())->create();
        $user2 = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());
        $this->post('/podcasts/' . $podcast->id . '/episodes', $this->data());
        Auth::logout();
        $this->actingAs($user2);
        $episode = Episode::query()->first();
        $file = UploadedFile::fake()->create('ep2.mp3', 100, 'audio/mpeg');
        $data = [
            'title' => 'My updated episode name',
            'description' => 'My updated episode desc',
            'file' => $file,
        ];

        $response = $this->patch('/podcasts/' . $podcast->id . '/episodes/' . $episode->id, $data);

        $response->assertStatus(404);
        $episode->refresh();
        $this->assertEquals($podcast->id, $episode->podcast_id);
        $this->assertNotEquals($data['title'], $episode->title);
        $this->assertNotEquals($data['description'], $episode->description);
        $this->assertNotEquals('episodes/' . $file->hashName(), $episode->file);
    }

    /** @test */
    public function a_guest_cannot_update_episodes()
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());
        $this->post('/podcasts/' . $podcast->id . '/episodes', $this->data());
        Auth::logout();
        $episode = Episode::query()->first();
        $file = UploadedFile::fake()->create('ep2.mp3', 100, 'audio/mpeg');
        $data = [
            'title' => 'My updated episode name',
            'description' => 'My updated episode desc',
            'file' => $file,
        ];

        $response = $this->patch('/podcasts/' . $podcast->id . '/episodes/' . $episode->id, $data);

        $response->assertRedirect('/login');
        $episode->refresh();
        $this->assertEquals($podcast->id, $episode->podcast_id);
        $this->assertNotEquals($data['title'], $episode->title);
        $this->assertNotEquals($data['description'], $episode->description);
        $this->assertNotEquals('episodes/' . $file->hashName(), $episode->file);
    }

    /**
     * @test
     * @dataProvider invalidEpisodes
     */
    public function a_user_cant_store_an_invalid_episode($invalidData, $invalidFields)
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());

        $response = $this->post('/podcasts/' . $podcast->id . '/episodes', $invalidData);

        $response->assertSessionHasErrors($invalidFields);
        $this->assertCount(0, Episode::all());
    }

    /**
     * @test
     * @dataProvider invalidEpisodes
     */
    public function a_user_cant_update_an_episode_with_invalid_data($invalidData, $invalidFields)
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());
        $this->post('/podcasts/' . $podcast->id . '/episodes', $this->data());
        $episode = Episode::query()->first();

        $response = $this->patch('/podcasts/' . $podcast->id . '/episodes/' . $episode->id, $invalidData);

        $this->assertCount(1, Episode::all());
        $response->assertSessionHasErrors($invalidFields);
    }

    /** @test */
    public function a_description_should_be_safe_from_xss_attacks()
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());

        $this->post('/podcasts/' . $podcast->id . '/episodes', array_merge($this->data(), [
            'description' => 'An XSS attack <script>alert(0)</script> Cool Text'
        ]));

        $this->assertCount(1, Episode::all());
        $this->assertEquals('An XSS attack alert(0) Cool Text', Episode::query()->first()->description);
    }

    /** @test */
    public function a_description_should_be_safe_from_more_advanced_xss_attacks()
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());

        $this->post('/podcasts/' . $podcast->id . '/episodes', array_merge($this->data(), [
            'description' => 'An advanced XSS attack<META HTTP-EQUIV=”refresh” CONTENT=”0;url=data:text/html;base64,PHNjcmlwdD5hbGVydCgndGVzdDMnKTwvc2NyaXB0Pg”>'
        ]));

        $this->assertCount(1, Episode::all());
        $this->assertEquals('An advanced XSS attack', Episode::query()->first()->description);
    }

    /** @test */
    public function a_user_can_delete_an_episode()
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());
        $this->post('/podcasts/' . $podcast->id . '/episodes', $this->data());
        $this->post('/podcasts/' . $podcast->id . '/episodes', array_merge($this->data(), ['title' => 'My second episode']));
        $episode = Episode::query()->first();

        $this->delete('/podcasts/' . $podcast->id . '/episodes/' . $episode->id);

        $this->assertCount(1, Episode::all());
        $this->assertDatabaseMissing(Episode::class, $this->data());
    }

    /** @test */
    public function a_user_cannot_delete_others_episodes()
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());
        $this->post('/podcasts/' . $podcast->id . '/episodes', $this->data());
        $episode = Episode::query()->first();
        Auth::logout();
        $user2 = (new UserFactory())->create();
        $this->actingAs($user2);

        $response = $this->delete('/podcasts/' . $podcast->id . '/episodes/' . $episode->id);

        $response->assertStatus(404);
        $this->assertCount(1, Episode::all());
    }

    /** @test */
    public function a_guest_cannot_delete_an_episode()
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $podcast = $user->podcasts()->create($this->podcastData());
        $this->post('/podcasts/' . $podcast->id . '/episodes', $this->data());
        $episode = Episode::query()->first();
        Auth::logout();

        $response = $this->delete('/podcasts/' . $podcast->id . '/episodes/' . $episode->id);

        $response->assertRedirect('/login');
        $this->assertCount(1, Episode::all());
    }

    /**
     * Invalid data provider
     */
    public static function invalidEpisodes()
    {
        return [
            [
                // Empty title
                [
                    'title' => '',
                    'description' => 'Awesome episode about new tech',
                    'file' => UploadedFile::fake()->create('ep1.mp3', 100, 'audio/mpeg'),
                ],
                [
                    'title'
                ]
            ],
            [
                // Invalid and dangerous title
                [
                    'title' => 'the best-podcast #1 <script>alert(0)</script>',
                    'description' => 'Awesome episode about new tech',
                    'file' => UploadedFile::fake()->create('ep1.mp3', 100, 'audio/mpeg'),
                ],
                [
                    'title'
                ]
            ],
            [
                // Empty description
                [
                    'title' => 'the best podcast 1',
                    'description' => '',
                    'file' => UploadedFile::fake()->create('ep1.mp3', 100, 'audio/mpeg'),
                ],
                [
                    'description'
                ]
            ],
            [
                // Empty file
                [
                    'title' => 'the best podcast 1',
                    'description' => 'the best-podcast #1 < $ %',
                    'file' => '',
                ],
                [
                    'file'
                ]
            ],
            [
                // Invalid file type
                [
                    'title' => 'the best podcast 1',
                    'description' => 'the best-podcast #1 < $ %',
                    'file' => UploadedFile::fake()->create('ep1.mp4', 100, 'video/mp4'),
                ],
                [
                    'file'
                ]
            ],
        ];
    }

    /**
     * @return string[]
     */
    protected function data(): array
    {
        Storage::fake('episodes');

        return [
            'title' => 'Episode number 1',
            'description' => 'Awesome episode about new tech',
            'file' => UploadedFile::fake()->create('ep1.mp3', 100, 'audio/mpeg'),
        ];
    }

    /**
     * @return string[]
     */
    protected function podcastData(): array
    {
        return [
            'name' => 'Best podcast',
            'description' => 'Awesome podcast about new tech',
        ];
    }
}
