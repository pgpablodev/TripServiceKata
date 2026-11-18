<?php

namespace Test\TripServiceKata\Trip;

use PHPUnit\Framework\TestCase;
use TripServiceKata\Trip\TripService;
use TripServiceKata\Trip\Trip;
use TripServiceKata\User\User;
use TripServiceKata\Exception\UserNotLoggedInException;

class TripServiceTest extends TestCase
{
    /**
     * @var TripService
     */
    private TripService $tripService;

    protected function setUp(): void{
        $this->tripService = new TestableTripService(null);
    }

    /** @test */
    public function it_throws_an_exception_when_user_is_not_logged_in(): void{
        $user = new User('Pablo');

        $this->expectException(UserNotLoggedInException::class);

        $this->tripService->getTripsByUser($user);
    }

    /** @test */
    public function it_returns_no_trips_when_logged_user_is_not_a_friend(): void{
        $loggedUser = new User('Logged user');
        $owner = new User('Trip owner');

        $this->tripService = new TestableTripService($loggedUser);

        $trips = $this->tripService->getTripsByUser($owner);

        $this->assertSame([], $trips);
        $this->assertSame(0, $this->tripService->getFindTripsCalls());
    }

    /** @test */
    public function it_returns_trips_when_logged_user_is_a_friend(): void{
        $loggedUser = new User('Logged user');
        $owner = new User('Trip owner');
        $owner->addFriend($loggedUser);

        $trip = new Trip();

        $this->tripService = new TestableTripService($loggedUser);
        $this->tripService->setTrips([$trip]);

        $trips = $this->tripService->getTripsByUser($owner);

        $this->assertSame([$trip], $trips);
        $this->assertSame(1, $this->tripService->getFindTripsCalls());
        $this->assertSame($owner, $this->tripService->getRequestedUser());
    }
}

class TestableTripService extends TripService{
    private ?User $loggedUser;

    private int $findTripsCalls = 0;
    private ?User $requestedUser = null;

    /** @var Trip[] */
    private array $trips = [];

    public function __construct(?User $loggedUser)
    {
        $this->loggedUser = $loggedUser;
    }

    protected function getLoggedUser(): ?User
    {
        return $this->loggedUser;
    }

    public function getFindTripsCalls(): int
    {
        return $this->findTripsCalls;
    }

    public function getRequestedUser(): ?User
    {
        return $this->requestedUser;
    }

    public function setTrips(array $trips): void
    {
        $this->trips = $trips;
    }

    public function findTripsByUser(User $user): array
    {
        $this->findTripsCalls++;
        $this->requestedUser = $user;

        return $this->trips;
    }
}
