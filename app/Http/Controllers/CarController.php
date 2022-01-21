<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Repositories\Abstracts\CarRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class CarController extends Controller
{
    protected CarRepositoryInterface $car;

    public function __construct(CarRepositoryInterface $car)
    {
        $this->car = $car;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return string
     * @throws Throwable
     */
    public function index(Request $request): string
    {
        try {
            return $this->car->paginate(
                $request->orderBy ?? 'id',
                $request->sort ?? 'desc',
                (int)strip_tags($request->itemsPerPage) ?? 15
            );
        }
        catch (Exception $e){
            return response()->json($e->getMessage(),422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewCarRequest $request
     * @return CarResource|JsonResponse
     * @throws Throwable
     */
    public function store(NewCarRequest $request): JsonResponse|CarResource
    {
        try {
            return new CarResource($this->car->generate($request));
        }
        catch (Exception $e){
            return response()->json($e->getMessage(),422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Car $car
     * @return CarResource|JsonResponse
     * @throws Throwable
     */
    public function show(Car $car): JsonResponse|CarResource
    {
        try {
            return new CarResource($this->car->fetch($car));
        }
        catch (Exception $e){
            return response()->json($e->getMessage(),422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCarRequest $request
     * @param Car $car
     * @return CarResource|JsonResponse
     * @throws Throwable
     */
    public function update(UpdateCarRequest $request,Car $car): JsonResponse|CarResource
    {
        try {
            return new CarResource($this->car->update($request, $car));
        }
        catch (Exception $e){
            return response()->json($e->getMessage(),422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Car $car
     * @return JsonResponse|Response
     */
    public function destroy(Car $car) : JsonResponse | Response
    {
        try{
            $this->car->delete($car);
        }
        catch (Exception $e){
            return response()->json($e->getMessage(),422);
        }
        return response()->noContent();
    }
}
