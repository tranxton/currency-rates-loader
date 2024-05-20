<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\Models\CurrencyRate;

use App\SharedKernel\Enums\Currency\CurrencyCodeEnum;
use App\SharedKernel\Enums\DefaultDateTimeFormatEnum;
use App\SharedKernel\Services\Guid\Casts\GuidCaster;
use App\SharedKernel\Services\Guid\Dto\Guid;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Database\Factories\Contexts\PaymentIntent\CurrencyCoordinator\CurrencyRateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Entity "Currency Rate"
 *
 * @property Guid             $guid
 *
 * @property CurrencyCodeEnum $base_code
 * @property CurrencyCodeEnum $target_code
 * @property int              $rate
 *
 * @property CarbonImmutable  $actual_at
 * @property Carbon           $created_at
 */
class CurrencyRate extends Model
{
    use HasFactory;

    public $incrementing  = false;
    public $timestamps    = false;
    protected $primaryKey = 'guid';
    protected $keyType    = 'string';

    protected $dates = [
        'actual_at',
        'created_at',
    ];

    protected $casts = [
        'guid'       => GuidCaster::class,
        'base'       => CurrencyCodeEnum::class,
        'target'     => CurrencyCodeEnum::class,
        'actual_at'  => 'immutable_datetime:' . DefaultDateTimeFormatEnum::Date->value,
        'created_at' => 'timestamp',
    ];

    protected static function newFactory(): CurrencyRateFactory
    {
        return CurrencyRateFactory::new();
    }
}
