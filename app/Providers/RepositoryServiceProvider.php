<?php

namespace App\Providers;

use App\Interfaces\AddressBookInterface;
use App\Interfaces\AuthInterface;
use App\Interfaces\ClosureInterface;
use App\Interfaces\CouponsInterface;
use App\Interfaces\PagesInterface;
use App\Interfaces\ReservationInterface;
use App\Interfaces\SlideShowsInterface;
use App\Interfaces\TimeSlotInterface;
use App\Interfaces\UserInterface;
use App\Repositories\AddressBookRepository;
use App\Repositories\AuthRepository;
use App\Repositories\ClosureRepository;
use App\Repositories\CouponsRepository;
use App\Repositories\PagesRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\SlideShowsRepository;
use App\Repositories\TimeSlotRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthInterface::class, AuthRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(TimeSlotInterface::class, TimeSlotRepository::class);
        $this->app->bind(ReservationInterface::class, ReservationRepository::class);
        $this->app->bind(AddressBookInterface::class, AddressBookRepository::class);
        $this->app->bind(SlideShowsInterface::class, SlideShowsRepository::class);
        $this->app->bind(CouponsInterface::class, CouponsRepository::class);
        $this->app->bind(ClosureInterface::class, ClosureRepository::class);
        $this->app->bind(PagesInterface::class, PagesRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
