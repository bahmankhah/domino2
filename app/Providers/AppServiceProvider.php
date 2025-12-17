<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Observers\OrderObserver;
use App\Observers\OrderItemObserver;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\ServiceProvider;
use Filament\Infolists\Components\TextEntry;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        OrderItem::observe(OrderItemObserver::class);

        DateTimePicker::macro('localeDateTime', function () {
            if(config('app.date_type') == 'jalali'){
                $this->jalali();
            }
            return $this;
        });

        DatePicker::macro('localeDateTime', function () {
            if(config('app.date_type') == 'jalali'){
                $this->jalali();
            }
            return $this;
        });

        TextColumn::macro('localeDateTime', function (string|Closure|null $format = null, ?string $timezone = null) {
            if(config('app.date_type') == 'jalali'){
                $this->jalaliDateTime($format, $timezone);
            } else {
                $this->dateTime($format, $timezone);
            }
            return $this;
        });

        TextEntry::macro('localeDateTime', function (string|Closure|null $format = null, ?string $timezone = null) {
            if(config('app.date_type') == 'jalali'){
                $this->jalaliDateTime($format, $timezone);
            } else {
                $this->dateTime($format, $timezone);
            }
            return $this;
        });
    }
}
