<?php

namespace Tests\Feature;

use App\Models\Podcast;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PodcastsManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_signed_in_user_can_create_a_podcast()
    {
        $this->withoutExceptionHandling();
        $user = (new UserFactory())->create();
        $this->actingAs($user);

        $response = $this->post('/podcasts', $this->data());

        $podcast = Podcast::query()->first();
        $response->assertStatus(200);
        $this->assertCount(1, Podcast::all());
        $this->assertEquals(1, $podcast->id);
        $this->assertEquals('english-podcast-1', $podcast->slug);
    }

    /** @test */
    public function a_name_is_required()
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);

        $response = $this->post('/podcasts', array_merge($this->data(), ['name' => '']));

        $response->assertSessionHasErrors('name');
        $this->assertCount(0, Podcast::all());
    }

    /** @test */
    public function a_name_should_contain_only_alpha_numeric_whitespaces_and_hyphens()
    {
        $user = (new UserFactory())->create();
        $this->actingAs($user);

        $response = $this->post('/podcasts', array_merge($this->data(), ['name' => 'the best-podcast #1 <script>alert(0)</script>']));

        $response->assertSessionHasErrors('name');
        $this->assertCount(0, Podcast::all());
    }

    /** @test */
    public function a_slug_must_be_unique()
    {
        $this->withoutExceptionHandling();
        $user = (new UserFactory())->create();
        $this->actingAs($user);

        $this->post('/podcasts', $this->data());
        $this->post('/podcasts', $this->data());
        $this->post('/podcasts', $this->data());

        $podcast = Podcast::query()->first();
        $podcast2 = Podcast::all()->get(1);
        $podcast3 = Podcast::all()->get(2);

        $this->assertCount(3, Podcast::all());
        $this->assertEquals('english-podcast-1', $podcast->slug);
        $this->assertEquals('english-podcast-2', $podcast2->slug);
        $this->assertEquals('english-podcast-3', $podcast3->slug);
    }

    /** @test */
    public function a_guest_cannot_create_podcasts()
    {
        (new UserFactory())->create(); // fake user just for the data function

        $response = $this->post('/podcasts', $this->data());

        $response->assertRedirect('/login');
        $this->assertCount(0, Podcast::all());
    }

    /** @test */
    public function a_user_can_update_his_own_product()
    {
        $this->withoutExceptionHandling();
        $user = (new UserFactory())->create();
        $this->actingAs($user);
        $this->post('/podcasts', $this->data());
        $podcast = Podcast::query()->first();

        $response = $this->patch('/podcasts/' . $podcast->id, array_merge($this->data(), [
            'name' => 'Italian podcast'
        ]));

        $podcast = $podcast->refresh();
        $response->assertStatus(200);
        $this->assertEquals(1, $podcast->id);
        $this->assertEquals('Italian podcast', $podcast->name);
        $this->assertEquals('italian-podcast', $podcast->slug);
    }

    /** @test */
    public function a_user_cannot_update_others_podcasts()
    {
        $user1 = (new UserFactory())->create();
        $user2 = (new UserFactory())->create();
        $this->actingAs($user1);
        $this->post('/podcasts', $this->data());
        $podcast = Podcast::query()->first();
        $this->actingAs($user2);

        $response = $this->patch('/podcasts/' . $podcast->id, array_merge($this->data(), [
            'name' => 'Italian podcast'
        ]));

        $response->assertStatus(404);
        $this->assertEquals(1, $podcast->id);
        $this->assertEquals($this->data()['name'], $podcast->name);
    }

    /** @test */
    public function a_guest_cannot_update_podcasts_and_should_be_redirected_to_login_page()
    {
        $user1 = (new UserFactory())->create();
        $this->actingAs($user1);
        $this->post('/podcasts', $this->data());
        $podcast = Podcast::query()->first();
        Auth::logout();

        $response = $this->patch('/podcasts/' . $podcast->id, array_merge($this->data(), [
            'name' => 'Italian podcast'
        ]));

        $response->assertRedirect('/login');
        $this->assertEquals(1, $podcast->id);
        $this->assertEquals($this->data()['name'], $podcast->name);
    }

    /** @test */
    public function a_user_can_delete_his_own_podcast()
    {
        $this->withoutExceptionHandling();
        $user1 = (new UserFactory())->create();
        $this->actingAs($user1);
        $this->post('/podcasts', $this->data());
        $this->post('/podcasts', $this->data());
        $podcast = Podcast::query()->first();

        $this->delete('/podcasts/' . $podcast->id);

        $this->assertCount(1, Podcast::all());
    }

    /** @test */
    public function a_user_cannot_delete_others_podcast()
    {
        $user1 = (new UserFactory())->create();
        $this->actingAs($user1);
        $this->post('/podcasts', $this->data());
        $podcast = Podcast::query()->first();
        $user2 = (new UserFactory())->create();
        $this->actingAs($user2);

        $response = $this->delete('/podcasts/' . $podcast->id);

        $response->assertStatus(404);
        $this->assertCount(1, Podcast::all());
    }

    /** @test */
    public function a_guest_cannot_delete_podcasts_and_should_be_redirected_to_login_page()
    {
        $user1 = (new UserFactory())->create();
        $this->actingAs($user1);
        $this->post('/podcasts', $this->data());
        $podcast = Podcast::query()->first();
        Auth::logout();

        $response = $this->delete('/podcasts/' . $podcast->id);

        $response->assertRedirect('/login');
        $this->assertCount(1, Podcast::all());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return [
            'name' => 'English podcast 1',
        ];
    }
}
