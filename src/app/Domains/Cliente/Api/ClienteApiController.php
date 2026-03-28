<?php

namespace App\Domains\Cliente\Api;

use App\Http\Controllers\Controller;
use App\Domains\Cliente\Services\ClienteService;
use App\Domains\Cliente\Actions\CreateCliente;
use App\Domains\Cliente\Actions\UpdateCliente;
use App\Domains\Cliente\Actions\DeleteCliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Domains\Cliente\Resources\ClienteResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Domains\Cliente\Requests\StoreClienteRequest;
use App\Domains\Cliente\Requests\UpdateClienteRequest;

class ClienteApiController extends Controller
{
    public function __construct(
        protected ClienteService $clienteService
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $clientes = $this->clienteService->listar();

        return ClienteResource::collection($clientes);
    }

    public function store(StoreClienteRequest $request, CreateCliente $action)
    {
        $cliente = $action->execute($request->validated());

        return response()->json(new ClienteResource($cliente), 201);
    }

    public function show(int $id): JsonResponse
    {
        $cliente = $this->clienteService->buscarPorId($id);

        return response()->json(
            new ClienteResource($cliente)
        );
    }

    public function update(UpdateClienteRequest $request, int $id, UpdateCliente $action)
    {
        $data = array_merge($request->validated(), ['id' => $id]);

        $cliente = $action->execute($data);

        return response()->json(new ClienteResource($cliente));
    }

    public function destroy(int $id, DeleteCliente $action): JsonResponse
    {
        $action->execute($id);

        return response()->json(null, 204);
    }
}