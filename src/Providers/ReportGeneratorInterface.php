<?php
namespace BlockSense\Providers;

interface ReportGeneratorInterface {
    public function generate(string $address): array;
}