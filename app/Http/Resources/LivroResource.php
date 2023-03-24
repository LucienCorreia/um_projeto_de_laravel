<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Indice;

class LivroResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'titulo' => $this->titulo,
            'usuario_publicador' => new UsuarioResource($this->usuario),
            'indices' => $this->indices($request->titulo_do_indice),
        ];
    }

    private function indices(?String $searchIndice): array {
        $indices = [];

        foreach ($this->indices->whereNull('indice_pai_id') as $indice) {
            $indices[] = [
                'titulo' => $indice->titulo,
                'pagina' => $indice->pagina,
                'subindices' => ($searchIndice == $indice->titulo) ? [] : $this->subindices($indice, $searchIndice),
            ];
        }

        return $indices;
    }

    private function subindices(Indice $indice, ?String $searchIndice): array {
        $indice->load('subindices');

        $subindices = [];

        foreach ($indice->subindices as $subindice) {
            $subindices[] = [
                'titulo' => $subindice->titulo,
                'pagina' => $subindice->pagina,
                'subindices' => ($searchIndice == $subindice->titulo) ? [] : $this->subindices($subindice, $searchIndice),
            ];
        }

        return $subindices;
    }
}
