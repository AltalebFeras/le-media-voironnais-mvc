<?php

namespace src\Controllers;

use DateTime;
use Exception;
use src\Abstracts\AbstractController;
use src\Models\User;
use src\Repositories\ContactRepository;
use src\Services\Encrypt_decrypt;
use src\Services\Mail;
use src\Services\Helper;

class ContactController extends AbstractController
{
    protected $repo;
    public function __construct()
    {
        $this->repo = new ContactRepository();
    }
}
