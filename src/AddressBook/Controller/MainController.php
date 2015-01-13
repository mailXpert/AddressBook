<?php
/**
 * address_book.
 * Date: 13/01/15
 */

namespace AddressBook\Controller;


class MainController extends BaseController
{

    public function index()
    {
        return $this->render('Main/index.html.twig');
    }

    public function login()
    {
        return $this->render('Main/login.html.twig');
    }
}