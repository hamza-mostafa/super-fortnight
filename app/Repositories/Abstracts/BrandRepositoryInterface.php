<?php

namespace App\Repositories\Abstracts;

use App\Http\Requests\FindBrandByCarRequest;
use App\Http\Requests\NewBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Throwable;

interface BrandRepositoryInterface
{
    /**
     * BrandRepositoryInterface constructor.
     * @param Brand $brand
     */
    public function __construct(Brand $brand);

    /**
     * Creates pagination for the object and adds the ability to orderby, sort and the items per page
     * @param string $orderBy
     * @param string $sort
     * @param int $itemsPerPage
     * @return string
     * @throws Throwable
     */
    public function paginate(string $orderBy, string $sort, int $itemsPerPage): string;

    /**
     * Creates an abstract layer for fetching a model
     *
     * @param Brand $brand
     * @return Model
     * @throws Throwable
     */
    public function fetch(Brand $brand): Model;

    /**
     * Creates an abstract layer for fetching a model by another
     *
     * @param FindBrandByCarRequest $request
     * @throws Throwable
     */
    public function fetchByCarModel(FindBrandByCarRequest $request);

    /**
     * Creates an abstract layer for updating the model
     *
     * @param UpdateBrandRequest $request
     * @param Brand $brand
     * @return Brand
     * @throws Throwable
     */
    public function update(UpdateBrandRequest $request, Brand $brand): Brand;

    /**
     * Creates an abstract layer for creating the model
     *
     * @param NewBrandRequest $request
     * @return Brand
     * @throws Throwable
     */
    public function generate(NewBrandRequest $request): Brand;

    /**
     * Creates an abstract layer for destroying the model
     *
     * @param Brand $brand
     * @return bool
     * @return JsonResponse
     * @throws Throwable
     */
    public function delete(Brand $brand): bool;
}
