<?php

function generateRandom(int $lenght = 12): string
{
    $words = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle(str_repeat($words, $lenght)), 0, $lenght);
}