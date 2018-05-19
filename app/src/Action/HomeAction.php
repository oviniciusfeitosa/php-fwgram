<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use GuzzleHttp\Client;

final class HomeAction
{
    private $view;
    private $logger;

    public function __construct(Twig $view, LoggerInterface $logger)
    {
        $this->view = $view;
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $client = new Client();

        define("CLIENT_ID", "YOUR CLIENT ID");
        define("CLIENT_SECRET", "YOUR CLIENT SECRET");
        define("REDIRECT_URL", "YOUR REDIRECT URL");


        $this->logger->info("Home page action dispatched");

        $this->view->render($response, 'home.twig');
        return $response;
    }
}