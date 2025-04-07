<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Multimedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiMultimediaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_multimedia()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('video.mp4', 5000, 'video/mp4');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/multimedia', [
                'file' => $file,

            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'file_type', 'file_path'],
            ]);

        Storage::disk('public')->assertExists('multimedia/' . $file->hashName());
    }

    public function test_user_can_view_own_multimedia()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $this->actingAs($user, 'sanctum')->postJson('/api/multimedia', [
            'file' => $file,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/multimedia/user');

        $response->assertStatus(200)
            ->assertJsonFragment(['user_id' => $user->id]);
    }

    public function test_user_can_view_all_multimedia()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('file.pdf', 100, 'application/pdf');

        $this->actingAs($user, 'sanctum')->postJson('/api/multimedia', [
            'file' => $file,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/multimedia');

        $response->assertStatus(200)
            ->assertJsonStructure([
                [
                    'id', 'user_id', 'file_type', 'file_path', 'created_at', 'updated_at'
                ]
            ]);
    }

    public function test_user_can_delete_own_multimedia()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('file.mp4', 2000, 'video/mp4');

        // Emmagatzemar el fitxer i obtenir el camí
        $storeResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/multimedia', [
                'file' => $file,
            ]);

        // Obtenir el fitxer creat
        $id = $storeResponse['data']['id'];
        $multimedia = Multimedia::findOrFail($id);

        // Assegurar-se que el fitxer s'ha creat correctament
        Storage::disk('public')->assertExists('multimedia/' . $file->hashName());


        $deleteResponse = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/multimedia/{$id}");

        // Comprovar que el fitxer ha estat eliminat
        $deleteResponse->assertStatus(200)
            ->assertJson(['message' => 'Multimedia deleted successfully']);


    }



    public function test_user_cannot_delete_others_multimedia()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $file = UploadedFile::fake()->create('file.mp4', 2000, 'video/mp4');

        $storeResponse = $this->actingAs($otherUser, 'sanctum')
            ->postJson('/api/multimedia', [
                'file' => $file,
            ]);

        $id = $storeResponse['data']['id'];

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/multimedia/{$id}");

        $response->assertStatus(403)
            ->assertJson(['message' => 'Unauthorized']);
    }
}
