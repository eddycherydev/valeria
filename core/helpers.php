<?php


use Core\View;

function section(string $name)
{
    View::startSection($name);
}

function endsection()
{
    View::endSection();
}

function yieldSection(string $name)
{
    View::yield($name);
}

function includeView(string $view, array $data = [])
{
    View::include($view, $data);
}

function e($value): string
{
    return View::e($value);
}