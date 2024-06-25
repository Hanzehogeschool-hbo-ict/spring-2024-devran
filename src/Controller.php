<?php

namespace Hive;

class Controller
{
    public function __construct(protected ?Database $db, protected ?Session $session)
    {
    }
}