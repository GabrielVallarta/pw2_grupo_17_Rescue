<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class LandingPageController
{
    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    public function showLandingPage(Request $request, Response $response): Response {

        //return $this->twig->render($response, 'landing-page.twig');
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if(!isset($_SESSION['email'])){
            return $this->twig->render(
                $response,
                'landing-page.twig',
                [
                    'signUpLink' => $routeParser->urlFor('signUp'),
                    'landingLink' => $routeParser->urlFor('landingPage'),
                    'signInLink' => $routeParser->urlFor('signIn')
                ]
            );
        }else{
            return $this->twig->render(
                $response,
                'landing-page.twig',
                [
                    'signUpLink' => $routeParser->urlFor('signUp'),
                    'landingLink' => $routeParser->urlFor('landingPage'),
                    'signInLink' => $routeParser->urlFor('signIn'),
                    'username' => $_SESSION['email']
                ]
            );
        }
    }

    //TODO: ADD This call of SignIn when button pressed from header
    public function signIn(Request $request, Response $response)
    {


    }
}