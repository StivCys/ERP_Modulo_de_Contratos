<?php

namespace App\Shared\Contracts;

interface CsvReportInterface
{
    public function headers(): array;
    public function data(): iterable; // Generator, cursor(), lazy()
    public function filename(): string; // já deve vir com extensão e ser único
}