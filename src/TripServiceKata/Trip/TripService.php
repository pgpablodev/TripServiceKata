<?php

namespace TripServiceKata\Trip;

use TripServiceKata\User\User;
use TripServiceKata\User\UserSession;
use TripServiceKata\Exception\UserNotLoggedInException;

class TripService
{
    /**
     * @return Trip[]
     * @throws UserNotLoggedInException
     */
    public function getTripsByUser(User $user): array{
        $tripList = array();
        $loggedUser = $this->getLoggedUser();
        
        if($loggedUser === null){
            throw new UserNotLoggedInException();
        }

        if(!$this->isFriend($user, $loggedUser)){
            return [];
        }

        return $this->findTripsByUser($user);
    }

    private function isFriend(User $user, User $loggedUser): bool{
        foreach ($user->getFriends() as $friend) {
            if ($friend == $loggedUser) {
                return true;
            }
        }

        return false;
    }

    protected function findTripsByUser(User $user): array{
        return TripDAO::findTripsByUser($user);
    }
}
