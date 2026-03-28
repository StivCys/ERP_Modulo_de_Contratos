<?php

namespace App\Domains\Contrato\Api;

use App\Domains\Contrato\Resources\ContratoResource;
use App\Http\Controllers\Controller;
use App\Domains\Contrato\Services\ContratoService;
use App\Domains\Contrato\Actions\CreateContrato;
use App\Domains\Contrato\Actions\UpdateContrato;
use App\Domains\Contrato\Actions\DeleteContrato;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContratoApiController extends Controller
{
    public function __construct(
        protected ContratoService $contratoService
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $contratos = $this->contratoService->listarComRelacionamentos();

        return ContratoResource::collection($contratos);
    }

    public function store(Request $request, CreateContrato $action): JsonResponse
    {
        $contrato = $action->execute($request->all());

        return response()->json(
            (new ContratoResource($contrato->load(['cliente', 'items.servico']))),
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $contrato = $this->contratoService->buscarPorIdComRelacionamentos($id);

        return response()->json([
            'contrato' => new ContratoResource($contrato),
            'historico' => $this->contratoService->obterHistorico($id),
        ]);
    }

    public function update(Request $request, int $id, UpdateContrato $action): JsonResponse
    {
        $data = array_merge($request->all(), ['id' => $id]);

        $contrato = $action->execute($data);

        return response()->json(
            new ContratoResource($contrato->load(['cliente', 'items.servico']))
        );
    }

    public function destroy(int $id, DeleteContrato $action): JsonResponse
    {
        $action->execute($id);

        return response()->json(null, 204);
    }
}