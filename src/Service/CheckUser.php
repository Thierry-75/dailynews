<?php

namespace App\Service;


class CheckUser
{

    static function confirmEmail($user)
    {
        if(!$user === null){
            if($user->isVerified() === false){
                return true;
            }
        }
    }

    static function fullCivility($user)
    {
        if(!$user === null){
            if($user->isVerified() === true && $user->isCompleted() === false ){
                   return true; 
            }
        }
    }
}