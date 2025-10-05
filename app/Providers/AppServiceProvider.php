<?php

namespace App\Providers;

use Filament\Actions\Action;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
        Model::unguard();
        Model::shouldBeStrict();

        $this->configureFilamentDefaults();
    }

    protected function configureFilamentDefaults(): void
    {
        Table::configureUsing(function (Table $table) {
            $table->defaultDateTimeDisplayFormat('Y/m/d');
        });

        Action::configureUsing(function (Action $action) {
            $action->iconPosition(IconPosition::After);
        });
    }
}
