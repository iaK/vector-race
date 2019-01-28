<?php

function create($model, ...$params)
{
    $number = isset($params[1]) ? $params[1] : 1;
    $args = $params[0] ?? [];

    $models = factory($model, $number)->create($args);

    return oneOrCollection($models);
}

function make($model, ...$params)
{
    $number = isset($params[1]) ? $params[1] : 1;
    $args = $params[0] ?? [];

    $models = factory($model, $number)->make($args);

    return oneOrCollection($models);
}

function oneOrCollection($models)
{
    return $models->count() == "1" ? $models->first() : $models;
}
