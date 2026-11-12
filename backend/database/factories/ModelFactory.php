<?php

namespace Database\Factories;

use App\Models\Identity\Reader;
use App\Models\Identity\Writer;
use App\Models\Identity\Administrator;
use Database\Factories\Identity\ReaderFactory;
use Database\Factories\Identity\WriterFactory;
use Database\Factories\Identity\AdministratorFactory;

return [
    Reader::class => ReaderFactory::class,
    Writer::class => WriterFactory::class,
    Administrator::class => AdministratorFactory::class,
];
