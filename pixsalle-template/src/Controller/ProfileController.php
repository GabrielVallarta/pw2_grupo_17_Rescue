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

final class ProfileController
{
    private Twig $twig;
    private ValidatorService $validator;
    private UserRepository $userRepository;

    private const UPLOADS_DIR = __DIR__ . '/../../public/uploads';
    private const UNEXPECTED_ERROR = "An unexpected error occurred uploading the file '%s'...";
    private const INVALID_EXTENSION_ERROR = "The received file extension '%s' is not valid";
    private const ALLOWED_EXTENSIONS = ['jpg', 'png', 'JPG'];

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
    public function showProfileForm(Request $request, Response $response): Response
    {

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if(isset($_SESSION['user_id'])){

            $dataSet['username'] = $this->userRepository->getUsername($_SESSION['user_id']);
            $dataSet['email'] = $this->userRepository->getMail($_SESSION['user_id']);
            $dataSet['phone'] = $this->userRepository->getPhone($_SESSION['user_id']);

            return $this->twig->render(
                $response,
                'profile.twig',
                [
                    'formAction' => $routeParser->urlFor('profile'),
                    'formData' => $dataSet,
                    'formEmail' => $dataSet['email'],
                    'signUpLink' => $routeParser->urlFor('signUp'),
                    'signInLink' => $routeParser->urlFor('signIn')
                ]
            );
        }else{
            //TODO Falta afegir el missatge d'error
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }
    }

    public function editProfile(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $uploadedFiles = $request->getUploadedFiles();

        $errors = [];
        $filePath = '';

        //Nom d'usuari controlat
        if(!ctype_alnum($data['username'])){
            $errors['username'] = 'L usuari ha de contenir nomes caracters alfanumerics';
        }

        //Numero de telefon controlat
        if(preg_match('/^[0-9]{8}+$/', $data['phone'])){
            $errors['phone'] = 'El numero de telefon ha de tenir 9 numeros';
        }else{
            if($data['phone'][0] != '6'){
                $errors['phone'] = 'El numero de telefon ha de començar per 6';
            }
        }

        $uploadedFile = $uploadedFiles['imageFile'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $errors['file'] = sprintf(self::UNEXPECTED_ERROR, $uploadedFile->getClientFilename());
        }else{
            $name = $uploadedFile->getClientFilename();
            $fileInfo = pathinfo($name);
            $format = $fileInfo['extension'];

            if (!$this->isValidFormat($format)) {
                $errors['file'] = sprintf(self::INVALID_EXTENSION_ERROR, $format);
            }else{
                if($uploadedFile->getSize() > 1000000){
                    $errors['file'] = 'File is so big';
                }else{
                    $uploadedFile->moveTo(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $name);
                    $filePath = self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $name;
                }
            }
        }

        if(count($errors) > 0) {
            $dataSetEmail = $this->userRepository->getMail($_SESSION['user_id']);

            return $this->twig->render(
                $response,
                'profile.twig',
                [
                    'formAction' => $routeParser->urlFor('profile'),
                    'formData' => $data,
                    'formErrors' => $errors,
                    'formEmail' => $dataSetEmail,
                    'signUpLink' => $routeParser->urlFor('signUp'),
                    'signInLink' => $routeParser->urlFor('signIn')
                ]
            );

        }else{
            $this->userRepository->updateUser($_SESSION['user_id'], $data['username'], $data['phone'], $filePath);

            return $this->twig->render(
                $response,
                'profile.twig',
                [
                    'formAction' => $routeParser->urlFor('profile'),
                    'formData' => $data,
                    'formErrors' => $errors,
                    'signUpLink' => $routeParser->urlFor('signUp'),
                    'signInLink' => $routeParser->urlFor('signIn')
                ]
            );
        }

    }

    private function isValidFormat(string $extension): bool
    {
        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }
}