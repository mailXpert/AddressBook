<?php
/**
 * address_book.
 * Date: 13/01/15
 */

namespace AddressBook\Controller;


use \GuzzleHttp\Client;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class MainController extends BaseController
{

    public function index()
    {
        return $this->render('Main/index.html.twig');
    }

    public function login()
    {
        // The $this->getRequest()->get('code') is just the framework "shortcut" for $_REQUEST['code']
        if ($code = $this->getRequest()->get('code')) {

            $client = new Client();
            $response = $client->get(
            // $this->getParameter() function retrieve some global variables
                $this->getParameter('mailxpert_oauth_uri') . 'token',
                [
                    'query' => [
                        'client_id' => $this->getParameter('client_id'),
                        'client_secret' => $this->getParameter('client_secret'),
                        'redirect_uri' => $this->getParameter('redirect_uri'),
                        'grant_type' => 'authorization_code',
                        'code' => $code
                    ]
                ]
            );

            if ($json = $response->getBody()->getContents()) {
                $token = json_decode($json, true);

                if (array_key_exists('access_token', $token)) {
                    // $this->setToken() store the $token in session
                    $this->setToken($token);
                    $this->redirect('/');
                }
            }
        }

        $loginButton = sprintf('%sauth?client_id=%s&response_type=code&redirect_uri=%s', $this->getParameter('mailxpert_oauth_uri'), $this->getParameter('client_id'), urlencode($this->getParameter('redirect_uri')));

        return $this->render('Main/login.html.twig', ['loginButton' => $loginButton]);
    }

    public function contactLists()
    {
        $token = $this->getToken();

        $client = new Client(['base_url' => $this->getParameter('mailxpert_api_uri'), 'defaults' => ['headers' => ['Authorization' => 'Bearer ' . $token['access_token']]]]);

        $response = $client->get('contact_lists');

        $data = json_decode($response->getBody()->getContents(), true);

        $contactLists = $data['data'];

        return $this->render('Main/contactLists.html.twig', ['contactLists' => $contactLists]);
    }

    public function createContactList()
    {
        if ('' != $name = trim($this->getRequest()->get('name'))) {
            $token = $this->getToken();

            $client = new Client(['base_url' => $this->getParameter('mailxpert_api_uri'), 'defaults' => ['headers' => ['Authorization' => 'Bearer ' . $token['access_token']]]]);

            $response = $client->post(
                'contact_lists',
                [
                    'body' => json_encode(['name' => $name])
                ]
            );

            // Will return simething like "/v2.0/contact_lists/3"
            $location = $response->getHeader('Location');

            $this->redirect('/contact_lists');
        }

        return $this->render('Main/createContactList.html.twig');
    }

    public function deleteContactList()
    {
        if ($id = $this->getRouteParameter('id')) {

            $token = $this->getToken();

            $client = new Client(['base_url' => $this->getParameter('mailxpert_api_uri'), 'defaults' => ['headers' => ['Authorization' => 'Bearer ' . $token['access_token']]]]);

            $client->delete(['contact_lists/{id}', ['id' => $id]]);
        }

        $this->redirect('/contact_lists');
    }

    public function contactList()
    {
        if ($id = $this->getRouteParameter('id')) {

            $token = $this->getToken();

            $client = new Client(['base_url' => $this->getParameter('mailxpert_api_uri'), 'defaults' => ['query' => ['Authorization' => 'Bearer ' . $token['access_token']]]]);

            $response = $client->get(['contact_lists/{id}', ['id' => $id]]);

            $data = json_decode($response->getBody()->getContents(), true);

            $contactList = $data['data'];

            $response = $client->get('contacts', ['query' => ['contact_list_id' => $id]]);

            $data = json_decode($response->getBody()->getContents(), true);

            $contacts = $data['data'];

            return $this->render('Main/contactList.html.twig', ['contactList' => $contactList, 'contacts' => $contacts]);
        } else {
            header("HTTP/1.0 404 Not Found - No ID found");
            exit;
        }
    }
}