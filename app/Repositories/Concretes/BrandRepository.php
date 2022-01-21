<?php


namespace App\Repositories\Concretes;


use App\Http\Requests\FindBrandByCarRequest;
use App\Http\Requests\NewBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use App\Models\Car;
use App\Repositories\Abstracts\BrandRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Throwable;

class BrandRepository implements BrandRepositoryInterface
{
    protected Brand $brand;
    /**
     * BrandRepositoryInterface constructor.
     * @param Brand $brand
     */
    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    /**
     * Creates pagination for the object and adds the ability to orderby, sort and the items per page
     * @param string $orderBy
     * @param string $sort
     * @param int $itemsPerPage
     * @return JsonResponse
     * @throws Throwable
     */
    public function paginate(string $orderBy = 'id', string $sort = 'desc', int $itemsPerPage = 15): string
    {
        try {
            return $this->brand->with('cars')->orderBy($orderBy, $sort)->paginate($itemsPerPage)->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }

    /**
     * Find a brand by car model
     * @param FindBrandByCarRequest $request
     * @return mixed
     * @throws Exception
     */
    public function fetchByCarModel(FindBrandByCarRequest $request): mixed
    {
        try {
            return $this->brand->whereHas('cars', function ($q) use ($request){
                $q->where('model_name', $request->model_name);
            })->first();
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }

    /**
     * find brand by model
     * @param Brand $brand
     * @return Model
     */
    public function fetch(Brand $brand) : Model
    {
        return $this->brand->with('cars')->where('id', $brand->id)->firstOrFail();
    }

    /**
     * find brand by model
     * @param FindBrandByCarRequest $request
     * @return Brand
     */
    public function fetchByName(FindBrandByCarRequest $request): Brand
    {
        return $this->brand->where('brand_name', $request->brand_name)->firstOrFail();
    }

    /**
     * Creates an abstract layer for updating the model
     *
     * @param UpdateBrandRequest $request
     * @param Brand $brand
     * @return Brand
     * @throws Exception
     */
    public function update(UpdateBrandRequest $request, Brand $brand): Brand
    {
        try {
            return tap($brand)->update($request->all());
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }

    /**
     * Creates an abstract layer for creating the model
     *
     * @param NewBrandRequest $request
     * @return Brand
     * @throws Throwable
     */
    public function generate(NewBrandRequest $request): Brand
    {
        try {
            $user = auth()->user();
            return $user->brands()->create($request->all());
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }

    /**
     * Creates an abstract layer for destroying the model
     *
     * @param Brand $brand
     * @return bool
     * @throws Throwable
     */
    public function delete(Brand $brand): bool
    {
        return $brand->deleteOrFail();
    }
}
