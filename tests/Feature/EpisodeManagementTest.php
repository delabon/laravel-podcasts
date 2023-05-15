<?php

namespace Tests\Feature;

use App\Models\Episode;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EpisodeManagementTest extends TestCase
{
    use RefreshDatabase;

    // An episode can be created by a signed-in user
    /** @test */
    public function an_episode_can_be_created_by_a_signed_in_user()
    {
        $this->withoutExceptionHandling();
        $user = (new UserFactory())->create();
        $this->actingAs($user);

        $this->post('/episodes', $this->data());

        $this->assertCount(1, Episode::all());
        $this->assertEquals($user->id, Episode::query()->first()->user_id);
        $this->assertEquals($this->data()['title'], Episode::query()->first()->title);
        $this->assertEquals($this->data()['description'], Episode::query()->first()->description);
    }

    // Validate inputs
        # A name is required
        # A name should contain only alpha, num, hyphens and whitespaces
        # A description is required
        # Prevent XSS on description
    // A guest cannot create episodes and should be redirected to the login page
    // An episode can be updated by a user
    // A user cannot update others episodes
    // A guest cannot update episodes and should be redirected to the login page
    // An episode can be deleted by a user
    // A user cannot delete others episodes
    // A guest cannot delete episodes and should be redirected to the login page
    /**
     * @return string[]
     */
    protected function data(): array
    {
        return [
            'title' => 'Episode number 1',
            'description' => 'Awesome episode about new tech',
        ];
    }
}
