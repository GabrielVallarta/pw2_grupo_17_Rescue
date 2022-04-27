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

final class ChangePasswordController
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
    public function showChangePasswordForm(Request $request, Response $response): Response
    {

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if(isset($_SESSION['user_id'])){

            return $this->twig->render(
                $response,
                'changePassword.twig',
                [
                    'formAction' => $routeParser->urlFor('changePassword')
                ]
            );
        }else{
            //TODO Falta afegir el missatge d'error
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }
    }

    public function changePassword(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $errors = [];
        $success['password'] = 'La contrasenya ha estat cambiada';

        $old_password = $this->userRepository->getPassword($_SESSION['user_id']);

        if($old_password != md5($data['old'])){
            $errors['password'] = 'La contrasenya anterior no coincideix';
        }else{
            $errors['password'] = $this->validator->validatePassword($data['new']);
            if ($errors['password'] == '') {
                unset($errors['password']);
                if($data['new'] != $data['confirm']){
                    $errors['password'] = 'La nova contrasenya no coincideix';
                }
            }
        }

        if(count($errors) > 0) {
            return $this->twig->render(
                $response,
                'changePassword.twig',
                [
                    'formAction' => $routeParser->urlFor('changePassword'),
                    'formErrors' => $errors,
                ]
            );

        }else{
            $this->userRepository->updatePassword($_SESSION['user_id'], md5($data['new']));
            return $this->twig->render(
                $response,
                'changePassword.twig',
                [
                    'formAction' => $routeParser->urlFor('changePassword'),
                    'formErrors' => $success
                ]
            );
        }

    }
}