<?php


namespace App\Repositories\Concretes;


use App\Http\Requests\NewCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Car;
use App\Repositories\Abstracts\CarRepositoryInterface;
use Exception;
use Throwable;

class CarRepository implements CarRepositoryInterface
{
    protected Car $car;
    /**
     * CarRepositoryInterface constructor.
     * @param Car $car
     */
    public function __construct(Car $car)
    {
        $this->car = $car;
    }

    /**
     * Creates pagination for the object and adds the ability to orderby, sort and the items per page
     * @param string $orderBy
     * @param string $sort
     * @param int $itemsPerPage
     * @return string
     * @throws Throwable
     */
    public function paginate(string $orderBy = 'id', string $sort = 'desc', int $itemsPerPage = 15): string
    {
        try {
            return Car::orderBy($orderBy, $sort)->paginate($itemsPerPage)->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }


    /**
     * find car by model
     * @param Car $car
     * @return Car
     */
    public function fetch(Car $car): Car
    {
        return Car::where('id', $car->id)->firstOrFail();
    }

    /**
     * Creates an abstract layer for updating the model
     *
     * @param UpdateCarRequest $request
     * @param Car $car
     * @return Car
     * @throws Exception
     */
    public function update(UpdateCarRequest $request, Car $car): Car
    {
        try {
            return tap($car)->update($request->all());
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }

    /**
     * Creates an abstract layer for creating the model
     *
     * @param NewCarRequest $request
     * @return Car
     * @throws Exception
     */
    public function generate(NewCarRequest $request): Car
    {
        try {
            $user = auth()->user();
            return $user->cars()->create($request->all());
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }

    /**
     * Creates an abstract layer for destroying the model
     *
     * @param Car $car
     * @return bool
     * @throws Throwable
     */
    public function delete(Car $car): bool
    {
        return $car->deleteOrFail();
    }
}
