<?php

namespace App\Casts;

use App\DTO\ExtraFields;
use App\DTO\ExtraFieldsCastWrapper;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ExtraFieldsCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?ExtraFields
    {
        if (! isset($attributes[$key])) {
            return null;
        }

        $data = Json::decode($attributes[$key]);

        if (! is_array($data)) {
            return null;
        }

        $data = ExtraFieldsCastWrapper::from($data);

        return $data->getExtraFieldsDTO();
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (! $value instanceof ExtraFields) {
            throw new InvalidArgumentException('The given value is not an ExtraFields instance.');
        }

        $data = new ExtraFieldsCastWrapper(
            className: get_class($value),
            data: $value->toArray(),
        );

        return $data->toJson();
    }
}
