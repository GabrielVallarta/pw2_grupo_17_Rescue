<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Model\User;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;

use DateTime;

final class MembershipController
{
    private Twig $twig;
    private ValidatorService $validator;
    private UserRepository $userRepository;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->validator = new ValidatorService();
    }

    /**
     * Renders the form
     */
    public function showMembership(Request $request, Response $response): Response
    {

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if($this->userRepository->getPlan($_SESSION['user_id']) == 'cool'){
            $plan = true;
        }else{
            $plan = false;
        }

        if(isset($_SESSION['user_id'])){
            return $this->twig->render(
                $response,
                'membership.twig',
                [
                    'formAction' => $routeParser->urlFor('changePlan'),
                    'formMembershipFirst' => $plan,
                    'formMembershipSecond' => !$plan,
                    'signUpLink' => $routeParser->urlFor('signUp'),
                    'signInLink' => $routeParser->urlFor('signIn')
                ]
            );
        }else{
            //TODO Falta afegir el missatge d'error
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }
    }

    public function changePlan(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if(isset($data['cool'])){
            $this->userRepository->updateMembershipPlan($_SESSION['user_id'],'cool');
        }else{
            $this->userRepository->updateMembershipPlan($_SESSION['user_id'],'active');
        }

        return $response->withHeader('Location','/profile')->withStatus(302);



    }
}