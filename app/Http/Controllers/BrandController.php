<?php

namespace App\Http\Controllers;

use App\Http\Requests\FindBrandByCarRequest;
use App\Http\Requests\NewBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Repositories\Abstracts\BrandRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class BrandController extends Controller
{
    protected BrandRepositoryInterface $brand;

    public function __construct(BrandRepositoryInterface $brand)
    {
        $this->brand = $brand;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index(Request $request): string
    {
        try {
            return $this->brand->paginate(
                $request->orderBy ?? 'id',
                $request->sort ?? 'desc',
                (int)$request->itemsPerPage ?? 15
            );
        }
        catch (Throwable $e){
            return response()->json($e->getMessage(),422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewBrandRequest $request
     * @return BrandResource|JsonResponse
     */
    public function store(NewBrandRequest $request) : BrandResource|JsonResponse
    {
        try {
            return new BrandResource($this->brand->generate($request));
        }
        catch (Throwable $e){
            return response()->json($e->getMessage(),422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Brand $brand
     * @return BrandResource|JsonResponse
     */
    public function show(Brand $brand) : BrandResource|JsonResponse
    {
        try {
            return new BrandResource($this->brand->fetch($brand));
        }
        catch (Throwable $e){
            return response()->json($e->getMessage(),422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param FindBrandByCarRequest $request
     * @return BrandResource|JsonResponse
     */
    public function fetchByCarModelOrBrand(FindBrandByCarRequest $request) : BrandResource|JsonResponse
    {
        try {
            if ($request->brand_name){
                return new BrandResource($this->brand->fetchByName($request));
            }elseif($request->model_name){
                return new BrandResource($this->brand->fetchByCarModel($request));
            }else{
                return response()->json(['data' => [
                    'message' => 'nothing found matching this data'
                ]],404);
            }
        }
        catch (Throwable $e){
            return response()->json($e->getMessage(),422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBrandRequest $request
     * @param Brand $brand
     * @return BrandResource|JsonResponse
     */
    public function update(UpdateBrandRequest $request,Brand $brand) : BrandResource|JsonResponse
    {
        try {
            return new BrandResource($this->brand->update($request, $brand));
        }
        catch (Throwable $e){
            return response()->json($e->getMessage(),422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Brand $brand
     * @return JsonResponse|Response
     */
    public function destroy(Brand $brand) : JsonResponse|Response
    {
        try{
            $this->brand->delete($brand);
        }
        catch (Throwable $e){
            return response()->json($e->getMessage(),422);
        }
        return response()->noContent();
    }
}
