<?php

namespace App\Repositories\Abstracts;

use App\Http\Requests\NewCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Car;
use Throwable;

interface CarRepositoryInterface
{
    /**
     * CarRepositoryInterface constructor.
     * @param Car $car
     */
    public function __construct(Car $car);

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
     * @param Car $car
     * @return Car
     * @throws Throwable
     */
    public function fetch(Car $car): Car;

    /**
     * Creates an abstract layer for updating the model
     *
     * @param UpdateCarRequest $request
     * @param Car $car
     * @return Car
     * @throws Throwable
     */
    public function update(UpdateCarRequest $request, Car $car): Car;

    /**
     * Creates an abstract layer for creating the model
     *
     * @param NewCarRequest $request
     * @return Car
     * @throws Throwable
     */
    public function generate(NewCarRequest $request): Car;

    /**
     * Creates an abstract layer for destroying the model
     *
     * @param Car $car
     * @return bool
     */
    public function delete(Car $car): bool;

}
