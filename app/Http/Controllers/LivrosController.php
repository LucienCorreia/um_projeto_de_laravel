<?php

namespace App\Http\Controllers;

use DOMDocument;
use DOMElement;

use App\Http\Requests\StoreLivroRequest;
use App\Models\Livro;
use App\Models\Indice;
use Illuminate\Http\Request;
use App\Http\Resources\LivroResource;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response as HttpResponse;

class LivrosController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $livros = Livro::with(['usuario', 'indices', 'indices.subindices'])
            ->search($request->titulo)
            ->searchByIndice($request->titulo_do_indice)
            ->get();

        return LivroResource::collection($livros);
    }

    public function store(StoreLivroRequest $request): JsonResponse
    {
        $newLivro = new Livro;

        $newLivro->titulo = $request->titulo;
        $newLivro->usuario()->associate(auth()->user());

        $newLivro->save();

        foreach ($request->indices as $indice) {
            $newIndice = new Indice;

            $newIndice->titulo = $indice['titulo'];
            $newIndice->pagina = $indice['pagina'];
            $newIndice->livro()->associate($newLivro);

            $newIndice->save();

            if (key_exists('subindices', $indice)) {
                $this->saveSubIndices($indice['subindices'], $newIndice);
            }
        }

        return response()->json(new LivroResource($newLivro->load('indices')), 201);
    }

    private function saveSubIndices(?array $subindices, ?Indice $indice): void
    {
        foreach ($subindices as $subindice) {

            $newSubIndice = new Indice;

            $newSubIndice->titulo = $subindice['titulo'];
            $newSubIndice->pagina = $subindice['pagina'];
            $newSubIndice->livro()->associate($indice->livro);
            $newSubIndice->indicePai()->associate($indice);

            $newSubIndice->save();

            if (key_exists('subindices', $subindice)) {
                $this->saveSubIndices($subindice['subindices'], $newSubIndice);
            }
        }
    }

    public function showXml(Livro $livro, Request $request): HttpResponse
    {

        $livro->load('indices');

        $livroNew = (new LivroResource($livro))->toArray($request);

        $xml = new DOMDocument(encoding: 'UTF-8');

        foreach ($livroNew['indices'] as $indice) {            
            $element = $xml->appendChild($xml->createElement('indice'));
            $itemElement = $xml->createElement('item');
            $itemElement->setAttribute('pagina', $indice['pagina']);
            $itemElement->setAttribute('titulo', $indice['titulo']);
            $element->appendChild($itemElement);

            if (key_exists('subindices', $indice) && count($indice['subindices']) > 0) {
                $this->addNewElementInXML($xml, $itemElement, $indice['subindices']);
            }
        }

        return response($xml->saveXML(), 200, ['Content-Type' => 'text/xml']);
    }

    private function addNewElementInXML(DOMDocument $xml, DOMElement $element, array $indice): void
    {
        foreach($indice as $subindice) {
            $itemElement = $xml->createElement('item');
            $itemElement->setAttribute('pagina', $subindice['pagina']);
            $itemElement->setAttribute('titulo', $subindice['titulo']);
    
            $element->appendChild($itemElement);

            if (key_exists('subindices', $subindice) && count($subindice['subindices']) > 0) {
                $this->addNewElementInXML($xml, $itemElement, $subindice['subindices']);
            }
        }
    }
}
