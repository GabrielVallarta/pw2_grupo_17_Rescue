<?php

declare(strict_types=1);

use Salle\PixSalle\Controller\API\BlogAPIController;
use Salle\PixSalle\Controller\ChangePasswordController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Controller\ProfileController;
use Salle\PixSalle\Controller\LandingPageController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\UserSessionController;
use Slim\App;

function addRoutes(App $app): void
{
    $app->get('/', LandingPageController::class . ':showLandingPage')->setName('landingPage');
    $app->get('/sign-in', UserSessionController::class . ':showSignInForm')->setName('signIn');
    $app->post('/sign-in', UserSessionController::class . ':signIn');
    $app->get('/sign-up', SignUpController::class . ':showSignUpForm')->setName('signUp');
    $app->post('/sign-up', SignUpController::class . ':signUp');
    $app->get('/profile', ProfileController::class . ':showProfileForm')->setName('profile');
    $app->post('/profile', ProfileController::class . ':editProfile');
    $app->get('/profile/changePassword', ChangePasswordController::class . ':showChangePasswordForm')->setName('changePassword');
    $app->post('/profile/changePassword', ChangePasswordController::class . ':changePassword');
    $app->get('/user/membership', MembershipController::class . ':showMembership')->setName('changePlan');
    $app->post('/user/membership', MembershipController::class . ':changePlan');
    $app->get('/explore', ExploreController::class . ':show')->setName('explore');
}
