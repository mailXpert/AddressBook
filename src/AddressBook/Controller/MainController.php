<?php
/**
 * address_book.
 * Date: 13/01/15
 */

namespace AddressBook\Controller;


use \GuzzleHttp\Client;

class MainController extends BaseController
{

    public function index()
    {
        return $this->render('Main/index.html.twig');
    }

    public function login()
    {
        if ($code = $this->getRequest()->get('code')) {

            $client = new Client();
            $response = $client->get($this->getParameter('mailxpert_oauth_uri') . '/token', [
                'query' => [
                    'client_id' => $this->getParameter('client_id'),
                    'client_secret' => $this->getParameter('client_secret'),
                    'redirect_uri' => $this->getParameter('redirect_uri'),
                    'grant_type' => 'authorization_code',
                    'code' => $code
                ]
            ]);

            if ($json = $response->getBody()->getContents()) {
                $token = json_decode($json, true);

                if (array_key_exists('access_token', $token)) {
                    $this->getRequest()->getSession()->set('access_token', $token);
                    $this->redirect('/');
                }
            }
        }

        $loginButton = sprintf('%s/auth?client_id=%s&response_type=code&redirect_uri=%s', $this->getParameter('mailxpert_oauth_uri'), $this->getParameter('client_id'), urlencode($this->getParameter('redirect_uri')));

        return $this->render('Main/login.html.twig', ['loginButton' => $loginButton]);
    }
}