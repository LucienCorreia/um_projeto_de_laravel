<?php

namespace Tests\Feature;

use App\Models\Livro;
use App\Models\Usuario;
use Database\Seeders\LivrosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LivrosTest extends TestCase
{
    use RefreshDatabase;

    public function test_criar_livro(): void
    {
        $usuario = Usuario::factory()->create();

        $json = [
            'titulo' => 'Teste',
            'indices' => [
                [
                    'titulo' => 'Indice 1',
                    'pagina' => 1,
                    'subindices' => [
                        [
                            'titulo' => 'subindice 1',
                            'pagina' => 1,
                        ],
                        [
                            'titulo' => 'subindice 2',
                            'pagina' => 2,
                        ],
                    ],
                ],
                [
                    'titulo' => 'Indice 2',
                    'pagina' => 3,
                ]
            ],
        ];

        $response = $this->actingAs($usuario)
            ->post('/v1/livros', $json);

        $response->assertStatus(201);
        $this->assertDatabaseHas('livros', [
            'titulo' => 'Teste',
        ]);
        $this->assertDatabaseHas('indices', [
            'titulo' => 'Indice 1',
            'pagina' => 1,
        ]);
        $this->assertDatabaseHas('indices', [
            'titulo' => 'Indice 2',
            'pagina' => 3,
        ]);
        $this->assertDatabaseHas('indices', [
            'titulo' => 'subindice 1',
            'pagina' => 1,
        ]);
        $this->assertDatabaseHas('indices', [
            'titulo' => 'subindice 2',
            'pagina' => 2,
        ]);
    }

    public function test_listar_livros(): void
    {
        $usuario = Usuario::factory()->create();

        $response = $this->actingAs($usuario)
            ->get('/v1/livros');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'titulo',
                    'usuario_publicador' => [
                        'id',
                        'nome',
                    ],
                    'indices' => [
                        '*' => [
                            'titulo',
                            'pagina',
                            'subindices' => [
                                '*' => [
                                    'titulo',
                                    'pagina',
                                ],
                            ],
                        ],
                    ]
                ],
            ],
        ]);
    }

    public function test_pesquisar_por_titulo_livro(): void
    {
        $usuario = Usuario::factory()->create();

        $response = $this->actingAs($usuario)
            ->get('/v1/livros?titulo=Teste');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'titulo',
                    'usuario_publicador' => [
                        'id',
                        'nome',
                    ],
                    'indices' => [
                        '*' => [
                            'titulo',
                            'pagina',
                            'subindices' => [
                                '*' => [
                                    'titulo',
                                    'pagina',
                                ],
                            ],
                        ],
                    ]
                ],
            ],
        ]);

        $response = $this->actingAs($usuario)
            ->get('/v1/livros?titulo=Nenhum');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => []
        ]);
    }

    public function test_pesquisar_por_titulo_indice(): void
    {
        $usuario = Usuario::factory()->create();

        $response = $this->actingAs($usuario)
            ->get('/v1/livros?titulo_do_indice=Teste');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'titulo',
                    'usuario_publicador' => [
                        'id',
                        'nome',
                    ],
                    'indices' => [
                        '*' => [
                            'titulo',
                            'pagina',
                            'subindices' => [
                                '*' => [
                                    'titulo',
                                    'pagina',
                                ],
                            ],
                        ],
                    ]
                ],
            ],
        ]);

        $response = $this->actingAs($usuario)
            ->get('/v1/livros?titulo_do_indice=NULL');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => []
        ]);
    }

    public function test_xml(): void
    {
        $usuario = Usuario::factory()->create();

        $this->seed(LivrosSeeder::class);

        $livro = Livro::first();

        $response = $this->actingAs($usuario)
            ->post("/v1/livros/" . $livro->id. "/importar-indices-xml");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
    }
}
